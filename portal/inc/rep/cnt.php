<?php
class CInc_Rep_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('rep.menu');
    $this -> mMod = 'rep';
    
    // Ask If user has right for this page
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($this -> mMod)) {
      $this -> setProtection('*', $this -> mMod, rdRead);
    }
  }

  protected function actStd() {
    $lRepStdClass = CCor_Cfg::get('rep.std-class', 'main');

    switch ($lRepStdClass) {
      case 'rep':
        // page_reports.htm contains onload and onresize additions 
        $lPag = CHtm_Page::getInstance();
        $lPag -> openProjectFile('page_reports.htm');

        $lTpl = new CCor_Tpl();
        $lTpl -> openProjectFile('reports.htm');

        $lBox = new CRep_Rep();
        $lTpl -> setPat('swf.title', $this -> mTitle);
        $lTpl -> setPat('swf.content', $lBox -> getContent());

        $lVie = '<table cellspacing="0" cellpadding="2" border="0" id="table_reports" style="width: 100%;">';
        $lVie.= '<tbody>';
        $lVie.= '<tr>';
        $lVie.= '<td valign="top" style="width: 100%; height: 100%;">';
        $lVie.= $lTpl -> getContent();
        $lVie.= '</td>';
        $lVie.= '</tr>';
        $lVie.= '</tbody>';
        $lVie.= '</table>';
        break;
      default:
        $lVie = new CRep_Main();
    }

    $this -> render($lVie);
  }
  
  protected function actChart() {
    $lSrc = $this -> getReq('src');
    $lChart = new CRep_Chart($lSrc);
    
    echo $lChart -> getContent();
    exit;
  }
  
  protected function actSer() {
    $lUsr = CCor_Usr::getInstance();
    $this -> mReq -> expect('val');
    $lReq = $this -> getReq('val', array());
    
    $lArr = array();
    foreach ($lReq as $lKey => $lVal) {
      if ('' === $lVal) continue;
      $lArr[$lKey] = $lVal;
    }
    $lUsr -> setPref($this -> mMod.'.ser', $lArr);
    $this -> redirect();
  }

  protected function actClser() {
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mMod.'.ser', '');
    $this -> redirect();
  }
  
   protected function actFil() {
    $lUsr = CCor_Usr::getInstance();
    $this -> mReq -> expect('val');
    $lReq = $this -> getReq('val', array());
    
    $lArr = array();
    foreach ($lReq as $lKey => $lVal) {
      if ('' === $lVal) continue;
      $lArr[$lKey] = $lVal;
    }
    
    $lUsr -> setPref($this -> mMod.'.fil', $lArr);
    $this -> redirect();
  }

  protected function actClfil() {
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mMod.'.fil', '');
    $this -> redirect();
  }
  protected function actSpr() {
    $lUsr = CCor_Usr::getInstance();
    $lVie = new CHtm_Fpr($this -> mMod.'.sspr');
    $lVie -> setTitle(lan('lib.opt.spr'));
    $lDef = CCor_Res::get('fie');

    $lArr = array();
    foreach ($lDef as $lFie) {
      $lFla = intval($lFie['flags']);
      if (bitSet($lFla, ffSearch) && bitset($lFla, ffReport)) {
        // If Jobfield has Read Flag active, ask for User READ-RIGHT (combination of 'fie_' and Alias).
        // If User has no READ-RIGHT, Jobfield not shown in the list.
        $lFieRight = 'fie_'.$lFie['alias'];
        if (bitset($lFla,ffRead) && !$lUsr -> canRead($lFieRight)){
          continue;
        }
        $lArr[$lFie['id']] = $lFie['name_'.LAN];
      }
    }

    $lVie -> setSrc($lArr);
    $lVie -> setSel($lUsr -> getPref($this -> mMod.'.sfie'));
    $lPag = CHtm_Page::getInstance();
    $lPag->addJs($lVie->getTooltips());
    $this -> render($lVie);
  }

  protected function actSspr() {
    $lDst = $this -> mReq -> getVal('dst');
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mMod.'.sfie', implode(',', $lDst));
    $this -> redirect();
  }
}