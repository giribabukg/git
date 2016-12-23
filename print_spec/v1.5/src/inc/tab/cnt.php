<?php
class CInc_Tab_Cnt extends CCor_Cnt {

  protected $mModule = 'tab_master';

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mTitle = lan($this -> mModule.'.menu');

    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead('tab')) {
      $this -> setProtection('*', 'tab', rdNone);
    }
  }

  protected function actStd() {
    $lVie = new CTab_List();
    $this -> render($lVie);
  }

  protected function actNew() {
    $lAllAvailableTabTypes = CCor_Cfg::get('tab');
    $lAlreadyUsedTabTypes = 'SELECT type FROM al_'.$this -> mModule.' WHERE mand='.MID;

    $lQry = new CCor_Qry($lAlreadyUsedTabTypes);
    $lAlreadyUsedTabTypes = explode(',', $lQry -> getImplode('type'));

    $lStillAvailableTabTypes = array_diff($lAllAvailableTabTypes, $lAlreadyUsedTabTypes);
    if (!empty($lStillAvailableTabTypes)) {
      $lFrm = new CTab_Form('tab.snew', lan($this -> mModule.'.act.new'), NULL, $lStillAvailableTabTypes);
      $this -> render($lFrm);
    } else {
      $this -> msg(lan($this -> mModule.'.act.new.error'), mtUser, mlError);
      $this -> redirect();
    }
  }

  protected function actSnew() {
    $lMod = new CTab_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> insert();
    $this -> redirect();
  }

  protected function actEdt() {
    $lId = $this -> mReq -> getInt('id');

    $lVie = new CTab_Form_Edit($lId);

    $this -> render($lVie);
  }

  protected function actSedt() {
    $lMod = new CTab_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect();
  }

  protected function actDel() {
    $lId = $this -> mReq -> getInt('id');

    $lType = CCor_Qry::getStr('SELECT type FROM al_'.$this -> mModule.' WHERE id='.$lId);
    if ($lType == 'job') {
      CCor_Qry::exec('DELETE FROM al_sys_lang WHERE mand='.MID.' AND code like "tab.'.$lType.'.%";'); # delete translations
      CCor_Qry::exec('DELETE FROM al_usr_rig WHERE mand='.MID.' AND code like "tab.'.$lType.'.%";'); # delete user settings
      CCor_Qry::exec('DELETE FROM al_sys_rig_usr WHERE mand='.MID.' AND code like "tab.'.$lType.'.%";'); # delete system settings
      CCor_Qry::exec('DELETE FROM al_'.$this -> mModule.' WHERE mand='.MID.' AND id='.$lId); # delete tab
    }
    if ($lType == 'mainmenu') {
      CCor_Qry::exec('DELETE FROM al_sys_lang WHERE mand='.MID.' AND code like "tab.'.$lType.'.%";'); # delete translations
      CCor_Qry::exec('DELETE FROM al_usr_rig WHERE mand='.MID.' AND code like "tab.'.$lType.'.%";'); # delete user settings
      CCor_Qry::exec('DELETE FROM al_sys_rig_usr WHERE mand='.MID.' AND code like "tab.'.$lType.'.%";'); # delete system settings
      CCor_Qry::exec('DELETE FROM al_'.$this -> mModule.' WHERE mand='.MID.' AND id='.$lId); # delete tab
    }
    $this -> redirect();
  }

}