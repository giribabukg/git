<?php
class CInc_Usg_Opt_Menu extends CCor_Ren {

  public function __construct($aId) {
    $this -> mUid = intval($aId);
  }

  protected function getCont() {
    $lRet = '<div class="tbl w400">';
    $lRet.= '<div class="cap">'.lan('usg.opt').'</div>';
    $lRet.= '<div class="frm p16">';
    $lRet.= btn(lan('usr.opt.send_new_usr'), 'validate("'.lan('usr.opt.change').'","'.lan('usr.opt.error').'","'.$this -> mUid.'");', 'img/ico/16/key.png', 'button', array('class' => 'btn w300')).BR.BR;
    $lRet.= btn(lan('usr.opt.req_new_usr'), 'go("index.php?act=usg-opt.usrchg&id='.$this -> mUid.'")', 'img/ico/16/key.png', 'button', array('class' => 'btn w300')).BR.BR;
    $lRet.= btn(lan('usg.send_new_pwd'), 'go("index.php?act=usg-opt.pwd&id='.$this -> mUid.'")', 'img/ico/16/key.png', 'button', array('class' => 'btn w300')).BR.BR;
    $lRet.= btn(lan('usg.req_pwd_change'), 'go("index.php?act=usg-opt.pwdchg&id='.$this -> mUid.'")', 'img/ico/16/key.png', 'button', array('class' => 'btn w300')).BR.BR;
    $lRet.= '</div>';
    $lRet.= '</div>';
    return $lRet;
  }
}