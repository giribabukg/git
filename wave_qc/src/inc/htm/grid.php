<?php
class CInc_Htm_Grid extends CHtm_Tag {

  protected $mCells = array();
  
  public function __construct($aCols = 0, $aRows = 0) {
    parent::__construct('table');
    $this -> setAtt('cellpadding', 0);
    $this -> setAtt('cellspacing', 0);
    #$this -> setAtt('border', 1);
    #$this -> setAtt('width', '100%');
    $this -> mCols = $aCols;
    $this -> mRows = $aRows;
    $this -> createCellTags();
  }
  
  public function createCellTags() {
    for ($lRow = 0; $lRow < $this -> mRows; $lRow++) {
      for ($lCol = 0; $lCol < $this -> mCols; $lCol++) {
        $lTag = new CHtm_Tag('td');
        $lTag -> setAtt('valign', 'top');
        $this -> mTags[$lCol][$lRow] = $lTag;
      }
    }
  }
  
  public function & getCellTag($aCol, $aRow) {
    $lRet = & $this -> mTags[$aCol][$aRow];
    return $lRet;
  }
  
  public function setCnt($aCol, $aRow, $aCont) {
    $this -> mCells[$aCol][$aRow] = toStr($aCont);
  }
  
  public function getCnt($aCol, $aRow) {
    $lRet = (isset($this -> mCells[$aCol][$aRow])) ? $this -> mCells[$aCol][$aRow] : '';
    return $lRet;
  }
  
  public function addCnt($aCol, $aRow, $aCont) {
    $lRet = $this -> getCnt($aCol, $aRow);
    $this -> setCnt($aCol, $aRow, $lRet.$aCont);
  }
  
  protected function getCont() {
    $lRet = $this -> getTag();
    for ($lRow = 0; $lRow < $this -> mRows; $lRow++) {
      $lRet.= '<tr>'.LF;
      for ($lCol = 0; $lCol < $this -> mCols; $lCol++) {
        $lTag = & $this -> getCellTag($lCol, $lRow);
        $lRet.= $this -> getComment('Cell ['.$lCol.']['.$lRow.']');
        $lRet.= $lTag -> getTag().LF;
        $lRet.= $this -> getCnt($lCol, $lRow);
        $lRet.= '</td>'.LF;
      } 
      $lRet.= '</tr>'.LF;
    } 
    $lRet.= $this -> getEndTag();
    return $lRet;
  }
  
}