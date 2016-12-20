<?php
class CInc_Hom_Cc_Fla extends CCor_Ren {

  public function __construct($aUid) {
  }

  protected function getCont() {
    $lUsr = CCor_Usr::getInstance();
    $lUID = $lUsr -> getId();

    $lURL = CCor_Cfg::get('hom.wel.assets');

    if (!empty($lURL)) {
      $lRet= '<iframe src="'.$lURL.'" name="Assets" scrolling="yes" frameborder="0" marginheight="0px" marginwidth="0px" height="100%" width="100%"></iframe>';
    } else {
      $lRet= 'Currently there is nothing assigned!';
    }

    return $lRet;
  }

}