<?php
class CInc_Utl_Jfp_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct = 'std') {
    parent::__construct($aReq, 'utl-jfp', $aAct);
    $this -> mTitle = htm(lan('lib.param'));
  }

  protected function actStd() {
    $lSel = $this -> getReq('sel');
    $lPic = new CUtl_Jfp_Form($lSel);
    $lTyp = $this -> getReq('typ');
    if (!empty($lTyp)) {
      $lPic -> addFilter('typ', $lTyp);
    }
    $lPag = new CUtl_Page();
    $lPag -> setPat('pg.cont', $lPic -> getContent());
    $lPag -> setPat('pg.title', $this -> mTitle);
    $lPag -> setPat('pg.lib.wait', htm(lan('lib.wait')));

    echo $lPag -> getContent();
    exit;
  }

  protected function actPick() {
    $lReq = $this -> getReq('val');

    $lArr = array();
    for ($i = 0; $i < 30; $i++) {
      $lKey = $lReq['key'.$i];
      if (!empty($lKey)) {
        $lVal = $lReq['val'.$i];
        $lArr[$lKey] = $lVal;
      }
    }

    $lRet = '<script type="text/javascript">';
    $lRet.= 'Flow.Std.selCal(\''.implode(",", array_keys($lArr)).'\');';
    $lRet.= '</script>';

    $lPag = new CUtl_Page();
    $lPag -> setPat('pg.cont', '');
    $lPag -> setPat('pg.title', $this -> mTitle);
    $lPag -> setPat('pg.lib.wait', htm(lan('lib.wait')));
    $lPag -> setPat('pg.js', $lRet);

    echo $lPag -> getContent();
    exit;
  }

}