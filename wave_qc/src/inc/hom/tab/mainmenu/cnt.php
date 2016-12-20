<?php
class CInc_Hom_Tab_Mainmenu_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mCode = $this -> getReq('code');
    $this -> mTitle = lan('tab.mainmenu.'.$this -> mCode);

    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead('tab.mainmenu.'.$this -> mCode)) {
      $this -> setProtection('*', 'tab.mainmenu.'.$this -> mCode, rdRead);
    }
  }

  protected function actStd() {
    $lPag = CHtm_Page::getInstance();
    $lPag -> openProjectFile('page_tab_mainmenu.htm');

    $lTpl = new CCor_Tpl();
    $lTpl -> openProjectFile('tab_mainmenu.htm');

    $lBox = new CHom_Tab_Mainmenu_Form($this -> mCode);
    $lTpl -> setPat('swf.title', $this -> mTitle);
    $lTpl -> setPat('swf.content', $lBox -> getContent());

    $lVie = '<table cellspacing="0" cellpadding="2" border="0" id="table_assets" style="width: 100%;">';
    $lVie.= '<tbody>';
    $lVie.= '<tr>';
    $lVie.= '<td valign="top" style="width: 100%; height: 100%;">';
    $lVie.= $lTpl -> getContent();
    $lVie.= '</td>';
    $lVie.= '</tr>';
    $lVie.= '</tbody>';
    $lVie.= '</table>';

    $this -> render($lVie);
  }
}