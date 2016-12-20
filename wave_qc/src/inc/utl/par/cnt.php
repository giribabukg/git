<?php
class CInc_Utl_Par_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct = 'std') {
    parent::__construct($aReq, 'utl-par', $aAct);
    $this -> mTitle = htm(lan('lib.param'));

  // Ask If user has right for this page
    $lpn = 'sys-svc';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdRead);
    }
  }

  protected function actStd() {
    $lPic = new CUtl_Par_Form($this -> getReq('sel'));

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
    for ($i = 0; $i < 12; $i++) {
      $lKey = $lReq['key'.$i];
      if ('' !== $lKey) {
        $lVal = $lReq['val'.$i];
        $lArr[$lKey] = $lVal;
      }
    }

    $lRet = '<script type="text/javascript">';
    // If $lArr empty, should not be serialized.
    if (empty($lArr)){
      $lRet.= 'Flow.Std.selCal(\'\');';
    }else{
      $lRet.= 'Flow.Std.selCal(\''.addslashes(serialize($lArr)).'\');';
    }
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