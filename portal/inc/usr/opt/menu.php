<?php
class CInc_Usr_Opt_Menu extends CCor_Ren {

  public function __construct($aId) {
    $this -> mUid = intval($aId);
  }

  protected function getCont() {
    $lRet = '<div class="tbl w400">';
    $lRet.= '<div class="cap">'.lan('usr.opt').'</div>';
    $lRet.= '<div class="frm p16">';
    $lRet.= btn(lan('usr.opt.send_new_usr'), 'validate("'.lan('usr.opt.change').'","'.lan('usr.opt.error').'","'.$this -> mUid.'");', '<i class="ico-w16 ico-w16-key"></i>', 'button', array('class' => 'btn w300')).BR.BR;
    $lRet.= btn(lan('usr.opt.req_new_usr'), 'go("index.php?act=usr-opt.usrchg&id='.$this -> mUid.'")', '<i class="ico-w16 ico-w16-key"></i>', 'button', array('class' => 'btn w300')).BR.BR;
    $lRet.= btn(lan('usr.send_new_pwd'), 'go("index.php?act=usr-opt.pwd&id='.$this -> mUid.'")', '<i class="ico-w16 ico-w16-key"></i>', 'button', array('class' => 'btn w300')).BR.BR;
    $lRet.= btn(lan('usr.req_pwd_change'), 'go("index.php?act=usr-opt.pwdchg&id='.$this -> mUid.'")', '<i class="ico-w16 ico-w16-key"></i>', 'button', array('class' => 'btn w300')).BR.BR;
    $lRet.= '</div>';
    $lRet.= '</div>';
    return $lRet;
  }
}