<?php
class CInc_Htm_Wrap extends CCor_Ren {

  public function __construct() {
    $this -> mCols = array();
    $this -> mAtt = array();
    $this -> setAtt('cellpadding', 2);
    $this -> setAtt('cellspacing', 0);
    $this -> setAtt('border', 0);
  }

  public function addCol($aContent) {
    $this -> mCols[] = toStr($aContent);
  }

  public function setAtt($aKey, $aVal) {
    $this -> mAtt[$aKey] = $aVal;
  }

  protected function getAttributeString() {
    if (empty($this -> mAtt)) return '';
    $lRet = '';
    foreach ($this -> mAtt as $lKey => $lVal) {
      $lRet.= ' '.$lKey.'="'.htm($lVal).'"';
    }
    return $lRet;
  }

  public static function wrap($aCont) {
    $lNum = func_num_args();
    if (1 == $lNum) {
      return toStr($aCont);
    }
    $lVie = new self();
    for ($i = 0; $i < $lNum; $i++) {
      $lVie -> addCol(func_get_arg($i));
    }
    return $lVie -> getContent();
  }

  protected function getCont() {
    if (empty($this -> mCols)) {
      return '';
    }
    $lRet = '<table'.$this -> getAttributeString().'>';
    $lRet.= '<tr>'.LF;
    foreach ($this -> mCols as $lCnt) {
      $lRet.= '<td valign="top" style="padding-right:16px">'.LF;
      $lRet.= $lCnt;
      $lRet.= '</td>'.LF;
    }
    $lRet.= '</tr></table>'.LF;
    return $lRet;
  }

}