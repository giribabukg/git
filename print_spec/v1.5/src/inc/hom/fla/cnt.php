<?php
class CInc_Hom_Fla_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mTitle = lan('hom-fla.menu');
  }

  protected function actStd() {
    // page_dashboard.htm contains onload and onresize additions 
    $lPag = CHtm_Page::getInstance();
    $lPag -> openProjectFile('page_dashboard.htm');

    $lDashboard = CCor_Cfg::get('hom.wel.dashboard');
    if (!$lDashboard) {
      $this -> redirect('index.php?act=hom-wel');
    }

    $lTpl = new CCor_Tpl();
    $lTpl -> openProjectFile('dashboard.htm');

    $lBox = new CHom_Fla_Fla();
    $lTpl -> setPat('swf.title', $this -> mTitle);
    $lTpl -> setPat('swf.content', $lBox -> getContent());

    $lMen = new CHom_Menu('fla');
    $lRet = '<table cellspacing="0" cellpadding="2" border="0" id="table_dashboard">';
    $lRet.= '<tbody>';
    $lRet.= '<tr>';
    $lRet.= '<td valign="top" style="height: 100%; padding-right:16px">';
    $lRet.= $lMen -> getContent();
    $lRet.= '</td>';
    $lRet.= '<td valign="top" style="width: 100%; height: 100%;">';
    $lRet.= $lTpl -> getContent();
    $lRet.= '</td>';
    $lRet.= '</tr>';
    $lRet.= '</tbody>';
    $lRet.= '</table>';

    $this -> render($lRet);
  }

}