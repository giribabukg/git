<?php
class CInc_Crp_Wrap extends CCor_Ren {
  
  public function __construct($aId, $aKey, $aCont) {
    $this -> mId = intval($aId);
    $this -> mKey = $aKey;
    $this -> mCont = $aCont;
  }
  
  protected function getCont() {
    $lRet = '';
    $lRet.= '<table cellpadding="0" cellspacing="0" border="0">'.LF;
    $lRet.= '<tr><td style="padding-right:16px" valign="top">';
    $lMen = new CCrp_Menu($this -> mId, $this -> mKey);
    $lRet.= $lMen -> getContent();
    $lRet.= '</td>'.LF;
    $lRet.= '<td valign="top">';
    if (is_string($this -> mCont)) {
      $lRet.= $this -> mCont;
    } else {
      $lRet.= $this -> mCont -> getContent();
    }
    $lRet.= '</td>'.LF;
    $lRet.= '</tr></table>'.LF;
    return $lRet;
  }
  
}