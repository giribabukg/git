<?php
class CInc_Tab_Itm_Cnt extends CCor_Cnt {

  protected $mModule = 'tab_slave';

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mTitle = lan($this -> mModule.'.menu');
    $this -> mTabType = $this -> getReq('type');

    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead('tab')) {
      $this -> setProtection('*', 'tab', rdNone);
    }
  }

  protected function getStdUrl() {
    return 'index.php?act=tab-itm&type='.$this -> mTabType;
  }

  protected function actStd() {
    $lVie = new CTab_Itm_List($this -> mTabType);
    $this -> render($lVie);
  }

  protected function actNew() {
    $lVie = new CTab_Itm_Form_Base('tab-itm.snew', lan($this -> mModule.'.act.new'), 'tab-itm&type='.$this -> mTabType);
    $lVie -> setTabType($this -> mTabType);
    $lVie -> setTabSubType($this -> mTabType);
    $this -> render($lVie);
  }

  protected function actSnew() {
    $lMod = new CTab_Itm_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> insert();
    $lMod -> updateTable();
    $this -> redirect('index.php?act=tab-itm.new&type='.$this -> mTabType);
  }

  protected function actEdt() {
    $lID = $this -> mReq -> getInt('id');

    $lVie = new CTab_Itm_Form_Edit($lID, $this -> mTabType);
    $this -> render($lVie);
  }

  protected function actSedt() {
    $lMod = new CTab_Itm_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> hasChanged()) {
      $lMod -> update();
      $lMod -> updateTable();
    }
    $this -> redirect();
  }

  protected function actMove() {
    $lId = $this -> mReq -> getInt('id');
    $lDir = $this -> mReq -> getVal('dir');

    if ($lDir == 'down') {
      $PriorId = CCor_Qry::getInt('SELECT MAX(id) FROM al_'.$this -> mModule.' WHERE mand='.MID.' AND id<'.$lId);
    } else {
      $NextId = CCor_Qry::getInt('SELECT MAX(id) FROM al_'.$this -> mModule.' WHERE mand='.MID.' AND id<'.$lId);
    }
    $this -> redirect();
  }

  protected function actDel() {
    $lID = $this -> mReq -> getInt('id');
    $lCode = CCor_Qry::getStr('SELECT code FROM al_'.$this -> mModule.' WHERE id='.$lID);
    $lType = CCor_Qry::getStr('SELECT type FROM al_'.$this -> mModule.' WHERE id='.$lID);
    $lSubType = CCor_Qry::getStr('SELECT subtype FROM al_'.$this -> mModule.' WHERE id='.$lID);

    if ($lType == 'job') {
      CCor_Qry::exec('DELETE FROM al_sys_lang WHERE code="tab.'.$lType.'.'.$lSubType.'.'.$lCode.'";'); # delete translations
      CCor_Qry::exec('DELETE FROM al_usr_rig WHERE code="tab.'.$lType.'.'.$lSubType.'.'.$lCode.'";'); # delete user settings
      CCor_Qry::exec('DELETE FROM al_sys_rig_usr WHERE code="tab.'.$lType.'.'.$lSubType.'.'.$lCode.'";'); # delete system settings
      CCor_Qry::exec('DELETE FROM al_'.$this -> mModule.' WHERE id='.$lID); # delete tab
    }

    if ($lType == 'mainmenu') {
      CCor_Qry::exec('DELETE FROM al_sys_lang WHERE code="tab.'.$lType.'.'.$lCode.'";'); # delete translations
      CCor_Qry::exec('DELETE FROM al_usr_rig WHERE code="tab.'.$lType.'.'.$lCode.'";'); # delete user settings
      CCor_Qry::exec('DELETE FROM al_sys_rig_usr WHERE code="tab.'.$lType.'.'.$lCode.'";'); # delete system settings
      CCor_Qry::exec('DELETE FROM al_'.$this -> mModule.' WHERE id='.$lID); # delete tab
    }

    $this -> redirect();
  }
}