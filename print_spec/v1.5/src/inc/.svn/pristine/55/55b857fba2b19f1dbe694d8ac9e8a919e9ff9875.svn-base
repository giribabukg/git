<?php
class CInc_Ajx_Tip_Field extends CCor_Ren {

  public function __construct($aId) {
    $this -> mId = intval($aId);
  }

  protected function getCont() {
    $lSql = 'SELECT * FROM al_fie WHERE id='.$this -> mId;
    $lQry = new CCor_Qry($lSql);
    $lRow = $lQry -> getDat();

    $lDesc = trim($lRow['desc_'.LAN]);
    if (empty($lDesc)) return '';

    $lRet = '<div class="th1">'.htm($lRow['name_'.LAN]).'</div>';
    $lRet.= '<div class="frm p8" style="height:100%">';
    $lRet.= '<div>';
    $lRet.= nl2br(htm($lDesc));
    $lRet.= '<br style="clear:both" />';
    $lRet.= '</div>';
    $lRet.= '</div>';

    return $lRet;
  }

}