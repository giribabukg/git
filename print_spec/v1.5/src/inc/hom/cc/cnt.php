<?php
class CInc_Hom_Cc_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('hom-cc.menu');

    // Ask If user has right for this page
    $lpn = 'ast'; // Assets
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdRead);
    }
  }

  protected function actStd() {
    $lPag = CHtm_Page::getInstance();
    $lPag -> openProjectFile('page_assets.htm');

    $lTpl = new CCor_Tpl();
    $lTpl -> openProjectFile('assets.htm');

    $lBox = new CHom_Cc_Fla();
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