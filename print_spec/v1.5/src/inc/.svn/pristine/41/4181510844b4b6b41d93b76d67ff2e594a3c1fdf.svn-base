<?php
class CInc_Hom_Tab_Job_Form extends CCor_Ren {

  public function __construct($aCode) {
    $this -> mCode = $aCode;
  }

  protected function getCont() {
    $lUsr = CCor_Usr::getInstance();
    $lUID = $lUsr -> getId();

    $lLink = CCor_Qry::getStr('SELECT link FROM al_tab_slave WHERE mand='.MID.' AND code="'.$this -> mCode.'"');
    if (!empty($lLink)) {
      $lRet= '<script type="text/javascript">jQuery(function() {jQuery(\'iframe[name="Tabs"]\').css(\'height\', (document[\'body\'].offsetHeight - 32) + \'px\');})</script><div class="tbl"><div class="frm p8"><iframe src="'.$lLink.'" name="Tabs" scrolling="yes" frameborder="0" marginheight="0px" marginwidth="0px" height="100%" width="100%"></iframe></div></div>';
    } else {
      $lLink = CCor_Qry::getStr('SELECT link FROM al_tab_slave WHERE mand=0 AND code="'.$this -> mCode.'"');
      if (!empty($lLink)) {
        $lRet= '<script type="text/javascript">jQuery(function() {jQuery(\'iframe[name="Tabs"]\').css(\'height\', (document[\'body\'].offsetHeight - 32) + \'px\');})</script><div class="tbl"><div class="frm p8"><iframe src="'.$lLink.'" name="Tabs" scrolling="yes" frameborder="0" marginheight="0px" marginwidth="0px" height="100%" width="100%"></iframe></div></div>';
      } else {
        $lRet= 'Currently there is nothing assigned!';
      }
    }

    return $lRet;
  }
}