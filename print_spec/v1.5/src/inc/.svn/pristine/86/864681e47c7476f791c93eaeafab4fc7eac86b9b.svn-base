<?php
class CInc_Ajx_Tip_Crp extends CCor_Ren {

  public function __construct($aId) {
    $this->mId = intval($aId);
  }

  protected function getCont() {
    if(empty($this->mId)) return;
    $lSql = 'SELECT * FROM al_crp_status WHERE mand='.MID.' AND id='.$this->mId;
    $lQry = new CCor_Qry($lSql);
    $lRow = $lQry->getDat();
    
    // see what the src is for the job
    if(THEME !== "default" && isset($lRow['crp_id'])){
      $lCodeSql = 'SELECT * FROM al_crp_master WHERE mand='.MID.' AND id='.$lRow['crp_id'];
      $lCodeQry = new CCor_Qry($lCodeSql);
      $lCodeRow = $lCodeQry->getDat();
	  
	  $lImg = CApp_Crpimage::getSrcPath($lCodeRow['code'], 'img/crp/big/'.$lRow['img'].'.gif');
    } else {
      $lImg = 'img/crp/big/'.$lRow['img'].'h.gif';
    }
    
    $lRet = '<div class="th1">'.htm($lRow['name_'.LAN]).'</div>';
    $lRet .= '<div class="frm p8" style="height:100%">';
    $lRet .= '<div>';
    $lRet .= img($lImg, array('style' => 'float:left; margin-right:8px; margin-bottom:8px;'));
    if(!empty($lRow['desc_'.LAN])){
      $lRet .= nl2br(htm(trim($lRow['desc_'.LAN])));
    }
    $lRet .= '<br style="clear:both" />';
    $lRet .= '</div>';
    $lRet .= '</div>';
    
    return $lRet;
  }

}