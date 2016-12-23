<?php
class CInc_Hom_Mand_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    
    // Ask If user has right for this page
    $lpn = 'mand';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdRead);
    }

    $this -> mTitle = lan('lib.mand.chg');
    $this -> mMmKey = 'hom-wel';
  }

  protected function actStd() {
    $lUsr = CCor_Usr::getInstance();
    $lUmand = $lUsr -> getVal('mand');
    if (!($lUsr -> canRead('mand'))) {
 #    if (!($lUsr -> canRead('mand') && $lUmand == 0)) {
      $this -> msg('User has no right OR is not Customer User',mtUser,mlError);
      $this->redirect('index.php?act=hom-wel');
    }
    $lMen = new CHom_Menu('mand');
    $lFrm = new CHom_Mand_Form();
    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm));
  }

  protected function actPost() {
    $lVal = $this -> getReq('val');
    $lOld = $this -> getReq('old');

    $lUsr = CCor_Usr::getInstance();
    foreach ($lVal as $lKey => $lValue) {
      $lOle = (isset($lOld[$lKey])) ? $lOld[$lKey] : NULL;

      if ($lOle == $lValue) {
        continue;
      }

      $lArr = CCor_Res::extract('code', 'id', 'mand');
      if ($lUsr -> canInsert('sys-admin')) {
        $lCust = CCor_Cfg::get('cust.pfx', 'pfx');
        $lArr[$lCust] = 0;
      }
      
      if (-1 < $lValue) { // ist der Wert der Linie
        if (isset($lArr[$lValue])) {
          $lUsr -> setPref('sys.mid', $lArr[$lValue]);
        }
        $lUsr -> setPref($lKey, $lValue);
      }
      $lUsr -> loadPrefsFromDb();
    }

    $this -> redirect('index.php?act=hom-wel');
  }

  protected function actCpy() {
    $lUsr = CCor_Usr::getInstance();
    if (!(0 == MID AND $lUsr -> canRead('copy.mand'))) {
      $this->redirect('index.php?act=hom-wel');
    }
    $lMen = new CHom_Menu('copymand');
    $lFrm = new CHom_Mand_Form('lib.mand.cpy');
    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm));
  }

}