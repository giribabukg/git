<?php
class CInc_Job_Fil_View_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mTitle = lan('job-fil-view.menu');
    $this -> mMmKey = 'opt';

    $lPriv = 'job-fil-view';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lPriv)) {
      $this -> setProtection('*', $lPriv, rdNone);
    }
  }

  protected function actStd() {
    $this -> redirect('index.php?act=job-fil-view.cat');
  }

  protected function actCat() {
    $lMen = new CJob_Fil_View_Menu('cat');

    $lVie = new CJob_Fil_View_Form();
    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }

  protected function actScat() {
    $lReqOld = $this -> mReq -> getVal('old');
    $lReqVal = $this -> mReq -> getVal('val');

    $lQry = new CCor_Qry();
    foreach ($lReqVal as $lKey => $lValue) {
      $lSql = 'DELETE FROM al_sys_pref WHERE mand='.MID.' AND code="'.$lKey.'";';
      $lQry -> query($lSql);

      $lSql = 'INSERT INTO al_sys_pref SET mand='.MID.', val="'.$lValue.'", code="'.$lKey.'";';
      $lQry -> query($lSql);

      if (strpos($lKey, 'cat.switch.button')) {
        switch ($lValue) {
          case 0:
            $lSql = 'DELETE FROM al_sys_rig_usr WHERE code="'.$lKey.'" AND mand='.MID;
      	    $lQry -> query($lSql);

            $lSql = 'DELETE FROM al_usr_rig WHERE code="'.$lKey.'" AND mand='.MID;
            $lQry -> query($lSql);
      	  break;
      	  case 1:
      	    break;
          case 2:
            $lSql = 'INSERT INTO al_sys_rig_usr SET mand='.MID.', level=3, grp="adm", name_en="'.lan('lib.cat.view.switch').'", name_de="'.lan('lib.cat.view.switch').'", desc_en="'.lan('lib.cat.view.switch').'", desc_de="'.lan('lib.cat.view.switch').'", code="'.$lKey.'";';
            $lQry -> query($lSql);
      	  break;
        }
      }
    }

    $this -> redirect('index.php?act=job-fil-view.cat');
  }
}