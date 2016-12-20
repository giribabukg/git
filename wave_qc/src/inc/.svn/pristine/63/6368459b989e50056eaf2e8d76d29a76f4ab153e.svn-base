<?php
class CInc_Api_Excel_List extends CCor_Obj {

  public function __construct() {
    $this -> init();
  }

  protected function init() {
    $this -> mCols = array();
    $this -> mIte = array();
    $this -> mX = 0;
    $this -> mY = 1;
    $this -> mOffsetX = 0;
    $this -> mOffsetY = 1;

    require_once 'Office/PHPExcel.php';
    $this -> mBook = new PHPExcel();
    $this -> mSheet = $this -> mBook -> getActiveSheet();

    $this -> mStyles = array();
    $this -> setDefaultStyles();

    $this -> mPlain = new CHtm_Fie_Plain();
  }

  public function setDefaultStyles() {
    $this -> mStyles['th1'] = $this -> createStyle('FF7992af');
    $this -> mStyles['th2'] = $this -> createStyle('FF95B3D7');

    $this -> mStyles['td1'] = $this -> createStyle('00FFFFFF');
    $this -> mStyles['td2'] = $this -> createStyle('FFDCE6F1');

    $this -> mStyles['default'] = $this -> createStyle('00FFFFFF');
  }

  public function setX($aX) {
    $this -> mX = intval($aX);
    return $this;
  }

  public function incX() {
    $this -> mX++;
    return $this;
  }

  public function setY($aY) {
    $this -> mY = intval($aY);
    return $this;
  }

  public function incY() {
    $this -> mY++;
    return $this;
  }

  public function setOffsetX($aOffsetX) {
    $this -> mOffsetX = intval($aOffsetX);
    if ($this-> mX < $this -> mOffsetX) {
      $this -> mX = $this -> mOffsetX;
    }
    return $this;
  }

  public function setOffsetY($aOffsetY) {
    $this -> mOffsetY = intval($aOffsetY);
    if ($this-> mY < $this -> mOffsetY) {
      $this -> mY = $this -> mOffsetY;
    }
    return $this;
  }

  public function newLine() {
    $this -> setX($this -> mOffsetX);
    $this -> incY();
    return $this;
  }

  public function setIterator($aIterator) {
    $this -> mIte = $aIterator;
    return $this;
  }

  public function addColumn($aAlias = '', $aCaption = '', $aFieAttr = array()) {
    if (empty($aAlias)) {
      $aAlias = getNum('col');
    }
    $lCol = new CHtm_Column($aAlias, $aCaption, false, null, $aFieAttr);
    $this -> mCols[$aAlias] = $lCol;
    return $lCol;
  }

  public function addField($aDef) {
    $lAlias = $aDef['alias'];
    $this -> mCols[$lAlias] = $aDef;
    return $this;
  }

  public function dumpCols() {
    var_export($this->mCols);
  }

  public function addCtr() {
    $this -> addColumn('ctr',  '');
    return $this;
  }

  protected function createStyle($aColor) {
    $lRet = array('fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array('argb' => $aColor)
    ));
    return $lRet;
  }

  public function setActiveStyle($aKey) {
    $this -> mStyle = (isset($this -> mStyles[$aKey])) ? $this -> mStyles[$aKey] : $this -> mStyles['default'];
    return $this;
  }

  public function writeTable() {
    $this -> setActiveStyle('th1');
    $this -> writeCaptions();
    $this -> writeRows();

    return $this;
  }

  public function writeCaptions() {
    $this -> setActiveStyle('th1');
    foreach ($this -> mCols as $this -> mCurKey => $this -> mCurCol) {
      $this -> mSheet -> getColumnDimensionByColumn($this -> mX) -> setAutoSize(true);
      $lCaption = $this -> mCurCol -> getCaption();
      $this -> writeCell($lCaption);
      $this -> incX();
    }
    $this -> newLine();
    return $this;
  }

  public function writeRows() {
    $this -> mCtr = 1;
    $this -> mStyleKey = 'td1';
    foreach ($this -> mIte as $this -> mRow) {
      $this -> setActiveStyle($this -> mStyleKey);
      $this -> writeRow();
      $this -> newLine();
      $this -> mCtr++;
      $this -> switchStyle();
    }
    return $this;
  }

  protected function writeRow() {
    foreach ($this -> mCols as $this -> mCurKey => $this -> mCurCol) {
      $this -> mVal = (isset($this -> mRow[$this -> mCurKey]))
        ? $this -> mRow[$this -> mCurKey]
        : null;
      $lPlain = $this -> mPlain ->getPlain($this->mCurCol->mFieAtt, $this->mVal);
      $this -> writeCell($lPlain);
      $this -> incX();
    }
  }

  protected function writeCell($aText) {
    $this -> mSheet -> setCellValueByColumnAndRow($this -> mX, $this -> mY, $aText);
    //$this -> mSheet -> setCellValueByColumnAndRow($this -> mX, $this -> mY, utf8_encode($aText));
    $this -> mSheet -> getStyleByColumnAndRow($this -> mX, $this -> mY) -> applyFromArray($this -> mStyle);
  }

  protected function switchStyle() {
    $this -> mStyleKey = ($this -> mStyleKey == 'td1') ? 'td2' : 'td1';
  }

  public function downloadAs($aFilename = 'Workbook.xlsx') {
    require_once 'Office/PHPExcel/Writer/Excel2007.php';
    $lWriter = new PHPExcel_Writer_Excel2007($this -> mBook);
    header('Content-type: application/vnd-ms-excel');
    header('Content-disposition: attachment;filename="'.$aFilename.'"');

    $lWriter->save('php://output');
  }

}