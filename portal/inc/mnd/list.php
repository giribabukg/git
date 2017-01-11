<?php
class CInc_Mnd_List extends CHtm_List {

  public function __construct() {
    parent::__construct('mnd');

    $this -> setAtt('class', 'tbl w400');
    $this -> mTitle = lan('mnd.menu');

    $this -> addColumn('disable', lan('log.dis.menu'));
    $this -> addColumn('id', 'MandID', TRUE);
    $this -> addColumn('code', 'Code', TRUE);
    $this -> addColumn('name_'.LAN, 'Name', TRUE);
    $this -> addColumn('login', 'Login');

    $this -> mDefaultOrder = 'id';

    $this -> addBtn(lan('lib.neumnd'), "go('index.php?act=mnd.new')", '<i class="ico-w16 ico-w16-plus"></i>');
    $this -> addBtn(lan('log.enmnd'), "go('index.php?act=mnd.enableAll')", '<i class="ico-w16 ico-w16-process_doit"></i>');
    $this -> addBtn(lan('log.dismnd'), "go('index.php?act=mnd.disableAll')", '<i class="ico-w16 ico-w16-ml-4"></i>');


    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_sys_mand');
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);

    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);
    $this -> mMaxLines = $this -> mIte -> getCount();
    $this -> addPanel('nav', $this -> getNavBar());
  }

  protected function getTdLogin() {
    $lId = $this -> getVal('id');
    $lRet = NB;
    if (MID == $lId) {
      $lRet = '<a href="index.php?act=mnd.login&amp;id='.$lId.'" class="nav">';
      $lRet.= 'Login</a>';
    }

    return $this -> tdClass($lRet, 'ac');
  }
  protected function getTdDisable() {
    $lId = $this -> getVal('id');
    $lUsr = CCor_Usr::getInstance();
    if($lUsr->canEdit("mnd-dis")) {
      //With Links
      if($this->getVal('disabled') === "N") {
        $lRet = '<a href="index.php?act=mnd.disable&amp;id='.$lId.'" class="nav">';
        $lRet.= '<i class="ico-w16 ico-w16-flag-03" alt="Aktiv" data-toggle="tooltip" data-tooltip-body="'.lan("log.active").'"></i></a>';
      }
      else {
        $lRet = '<a href="index.php?act=mnd.enable&amp;id='.$lId.'" class="nav">';
        $lRet.= '<i class="ico-w16 ico-w16-flag-01" alt="Inaktiv" data-toggle="tooltip" data-tooltip-body="'.lan("log.inactive").'"></i></a>';
      }
    }
    //Without Links
    else {
        if($this->getVal('disabled') === "N") {
        $lRet.= '<i class="ico-w16 ico-w16-flag-03" alt="Aktiv" data-toggle="tooltip" data-tooltip-body="'.lan("log.active").'"></i>';
      }
      else {
        $lRet.= '<i class="ico-w16 ico-w16-flag-01" alt="Inaktiv" data-toggle="tooltip" data-tooltip-body="'.lan("log.inactive").'"></i>';
      }
    }

    return $this ->td($lRet);
  }
}