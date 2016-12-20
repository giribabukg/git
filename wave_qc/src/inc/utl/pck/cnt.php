<?php
class CInc_Utl_Pck_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct = 'std') {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = htm(lan('pck.menu'));
    
    // Ask If user has right for this page
    $lpn = 'pck';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdRead);
    }
  }
 
  protected function actStd() {
    $lSrc = $this -> mReq -> getVal('src');
    $lJid = $this -> mReq -> getVal('jobid');
    $lLis = $this -> mReq -> getVal('lis');

    $lObj = new CUtl_Pck_List($lSrc, $lJid, $lLis);
// oder
#  $lObj = new CUtl_Pck_List($this -> mReq);

    $lPag = new CUtl_Page();
    $lPag -> setPat('pg.cont', $lObj -> getContent());
    $lPag -> setPat('pg.title', $this -> mTitle);
    $lPag -> setPat('pg.lib.wait', htm(lan('lib.wait')));
    
    echo $lPag -> getContent();
    exit;
  }
  
  protected function actPick() {
    $lReq = $this -> getReq('val');
    $lArr = array();
    for ($i=0; $i<10; $i++) {
      $lKey = $lReq['key'.$i];
      if ('' !== $lKey) {
        $lVal = $lReq['val'.$i];
        $lArr[$lKey] = $lVal;
      }
    }
    /*
            // optional typecast to save
        $lNum = intval($lNum);
        if ($lNum == $lKey) {
          $lKey = $lNum;
        }
    */

    $lRet = '<script type="text/javascript">';
    $lRet.= 'Flow.Std.selCal(\''.serialize($lArr).'\');';
    $lRet.= '</script>';

    $lPag = new CUtl_Page();
    $lPag -> setPat('pg.cont', '');
    $lPag -> setPat('pg.title', $this -> mTitle);
    $lPag -> setPat('pg.js', $lRet);

    echo $lPag -> getContent();
    exit;
  }
}