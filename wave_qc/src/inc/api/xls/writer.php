<?php
class CInc_Api_Xls_Writer extends CCor_Obj {

  public function __construct() {
    $this->init();
  }

  protected function init() {
    require_once 'Office/PHPExcel.php';

    $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
    $cacheSettings = array('memoryCacheSize' => '8MB');
    PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

    $this->setBook(new PHPExcel());

    require_once 'Office/PHPExcel/Writer/Excel5.php';
    $this->setWriter(new PHPExcel_Writer_Excel5($this->mBook));

    $this->mCols = array();
    $this->mIte = array();
    $this->mX = 0;
    $this->mY = 1;
    $this->mOffsetX = 0;
    $this->mOffsetY = 1;

    $this->mStyleKey = 'td1';
    $this->mStyles = array();
    $this->setDefaultStyles();
  }

  public function setBook($aBook) {
    $this->mBook = $aBook;
    $this->mSheet = $this->mBook->getActiveSheet();
  }

  public function setWriter($aWriter) {
    $this->mWriter = $aWriter;
  }

  public function setDefaultStyles() {
    $this->mStyles['th1'] = $this->createStyle('FF7992af');
    $this->mStyles['th2'] = $this->createStyle('FF95B3D7');

    $this->mStyles['td1'] = $this->createStyle('00FFFFFF');
    $this->mStyles['td2'] = $this->createStyle('00F6F6F6');

    $this->mStyles['default'] = $this->createStyle('00FFFFFF');
  }

  public function setX($aX) {
    $this->mX = intval($aX);
    return $this;
  }

  public function incX() {
    $this->mX++;
    return $this;
  }

  public function setY($aY) {
    $this->mY = intval($aY);
    return $this;
  }

  public function incY() {
    $this->mY++;
    return $this;
  }

  public function setOffsetX($aOffsetX) {
    $this->mOffsetX = intval($aOffsetX);
    if ($this-> mX < $this->mOffsetX) {
      $this->mX = $this->mOffsetX;
    }
    return $this;
  }

  public function setOffsetY($aOffsetY) {
    $this->mOffsetY = intval($aOffsetY);
    if ($this->mY < $this->mOffsetY) {
      $this->mY = $this->mOffsetY;
    }
    return $this;
  }

  public function newLine() {
    $this->setX($this->mOffsetX);
    $this->incY();
    return $this;
  }

  public function setIterator($aIterator) {
    $this->mIte = $aIterator;
    return $this;
  }

  public function addField($aAlias, $aCaption) {
    $this->mCols[$aAlias] = $aCaption;
    return $this;
  }

  public function dumpCols() {
    var_export($this->mCols);
  }

  public function addCtr() {
    $this->addColumn('ctr',  '');
    return $this;
  }

  protected function createStyle($aColor = NULL) {
    if (is_null($aColor)) return array();
    $lRet = array('fill' => array(
        'type' => \PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array('argb' => $aColor)
    ));
    return $lRet;
  }

  public function setActiveStyle($aKey) {
    $this->mStyle = (isset($this->mStyles[$aKey]))
    ? $this->mStyles[$aKey]
    : $this->mStyles['default'];
    return $this;
  }

  public function writeTable() {
    $this->setActiveStyle('th1');
    $this->writeCaptions();
    $this->writeRows();

    return $this;
  }

  public function writeCaptions() {
    $this->setActiveStyle('th1');
    foreach ($this->mCols as $this->mCurKey => $this->mCurCol) {
      $this->mSheet->getColumnDimensionByColumn($this->mX)->setAutoSize(true);
      $lCaption = $this->mCurCol;
      $this->writeCell($lCaption);
      $this->incX();
    }
    $this->newLine();
    return $this;
  }

  public function setAutoSizeCurCol() {
    $this->mSheet->getColumnDimensionByColumn($this->mX)->setAutoSize(true);
  }

  public function writeRows() {
    $this->mCtr = 1;
    $this->mStyleKey = 'td1';
    if (empty($this->mIte)) return $this;
    foreach ($this->mIte as $this->mRow) {
      $this->setActiveStyle($this->mStyleKey);
      $this->writeRow();
      $this->newLine();
      $this->mCtr++;
      $this->switchStyle();
    }
    return $this;
  }

  protected function writeRow() {
    foreach ($this->mCols as $this->mCurKey => $this->mCurCol) {
      $this->mVal = (isset($this->mRow[$this->mCurKey]))
      ? $this->mRow[$this->mCurKey]
      : null;
      $this->writeCell($this->mVal);
      $this->incX();
    }
  }

  public function writeCell($aText) {
    $this->mSheet->setCellValueByColumnAndRow($this->mX, $this->mY, utf8_encode($aText));
    // $this->mSheet->getStyleByColumnAndRow($this->mX, $this->mY)->applyFromArray($this->mStyle);
  }

  public function write($aText) {
    $this->writeCell($aText);
    $this->incX();
  }

  public function writeAsString($aText) {
    $this->mSheet->getCellByColumnAndRow($this->mX, $this->mY)
    ->setValueExplicit(utf8_encode($aText), \PHPExcel_Cell_DataType::TYPE_STRING);
    // $this->mSheet->getStyleByColumnAndRow($this->mX, $this->mY)->applyFromArray($this->mStyle);
    $this->incX();
  }

  public function switchStyle() {
    $this->mStyleKey = ($this->mStyleKey == 'td1') ? 'td2' : 'td1';
    $this->setActiveStyle($this->mStyleKey);
  }

  public function downloadAs($aFilename = 'Workbook.xls') {
    // header('Content-type: application/vnd.ms-excel');
    header('Content-disposition: attachment;filename="'.$aFilename.'"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($this->mBook, 'Excel5');
    $objWriter->save('php://output');
  }

  public function getContent() {
    ob_start();
    $this->mWriter->save('php://output');
    $ret = ob_get_contents();
    ob_end_clean();
    return $ret;
  }

}