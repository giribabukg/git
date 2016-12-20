<?php
class CInc_Hom_Tab_Mainmenu_Form extends CCor_Ren {

  public function __construct($aCode) {
    $this -> mCode = $aCode;
  }

  protected function getCont() {
    $lUsr = CCor_Usr::getInstance();
    $lUID = $lUsr -> getId();

    $lLink = CCor_Qry::getStr('SELECT link FROM al_tab_slave WHERE mand='.MID.' AND code="'.$this -> mCode.'"');
    if (!empty($lLink)) {
      $lRet= '<iframe src="'.$lLink.'" name="Tabs" scrolling="yes" frameborder="0" marginheight="0px" marginwidth="0px" height="100%" width="100%"></iframe>';
    } else {
      $lLink = CCor_Qry::getStr('SELECT link FROM al_tab_slave WHERE mand=0 AND code="'.$this -> mCode.'"');
      if (!empty($lLink)) {
        $lRet= '<iframe src="'.$lLink.'" name="Tabs" scrolling="yes" frameborder="0" marginheight="0px" marginwidth="0px" height="100%" width="100%"></iframe>';
      } else {
        $lRet= 'Currently there is nothing assigned!';
      }
    }

    return $lRet;
  }
}