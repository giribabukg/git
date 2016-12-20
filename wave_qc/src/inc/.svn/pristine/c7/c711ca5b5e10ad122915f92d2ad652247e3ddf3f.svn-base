<?php
class CInc_Rep_Rep extends CCor_Ren {

  public function __construct() {
  }

  protected function getCont() {
    $lUsr = CCor_Usr::getInstance();
    $lUID = $lUsr -> getId();

    $lURL = CCor_Qry::getStr('SELECT val FROM al_usr_pref WHERE code="rep.url" AND mand='.MID.' AND uid='.$lUID);

    if (!empty($lURL)) {
      $lRet= '<iframe src="'.$lURL.'" name="Reports" scrolling="auto" frameborder="0" marginheight="0px" marginwidth="0px" height="100%" width="100%"></iframe>';
    } else {
      $lRet= 'Currently there is nothing assigned!';
    }

    return $lRet;
  }

}