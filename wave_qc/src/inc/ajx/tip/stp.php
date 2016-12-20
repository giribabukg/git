<?php
class CInc_Ajx_Tip_Stp extends CCor_Ren {

  public function __construct($aId) {
    $this -> mId = intval($aId);
  }

  protected function getCont() {
    $lSql = 'SELECT s.name_'.LAN.' AS stepname, s.desc_'.LAN.' AS description,c.* FROM al_crp_step s,al_crp_status c WHERE c.mand='.MID.' AND c.id=s.to_id AND s.id='.$this -> mId;
    $lQry = new CCor_Qry($lSql);
    $lRow = $lQry -> getDat();

    $lRet = '<div class="th1">'.$lRow['stepname'].'</div>';
    $lRet.= '<div class="frm p8" style="height:100%">';
    $lRet.= '<div>';
#    $lImg = 'img/crp/big/'.$lRow['img'].'.gif';
#    $lRet.= img($lImg, array('style' => 'float:left; margin-right:8px; margin-bottom:8px;'));
    $lRet.= '<b>'.htm($lRow['name_'.LAN]).'</b>';
    $lRet.= '<br style="clear:both" />';
    if (!empty($lRow['description'])) {
      $lRet.= nl2br(htm(trim($lRow['description'])));
     }
    $lRet.= '</div>';
    $lRet.= '</div>';

    return $lRet;
  }

}