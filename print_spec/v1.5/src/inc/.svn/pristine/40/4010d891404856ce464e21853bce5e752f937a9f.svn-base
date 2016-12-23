<?php
class CInc_Ajx_Tip_His extends CCor_Ren {

  public function __construct($aId, $aName) {
    $this -> mId = intval($aId);
    $this -> mName = $aName;
  }

  protected function getCont() {
    $lRet = '<div class="th1">'.htm($this -> mName).'</div>';
    $lRet.= '<div class="frm p8" style="height:100%">';
    $lRet.= '<div>';
    $lImg = 'img/his/'.$this -> mId.'.gif';
    $lRet.= img($lImg, array('style' => 'float:left; margin-right:8px; margin-bottom:8px;'));
    if (!empty($this -> mName)) {
      $lRet.= nl2br(htm(trim($this -> mName)));
    }
    $lRet.= '<br style="clear:both" />';
    $lRet.= '</div>';
    $lRet.= '</div>';

    return $lRet;
  }

}