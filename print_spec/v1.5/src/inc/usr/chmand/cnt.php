<?php
class CInc_Usr_Chmand_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('usr-chmand.menu');
    $this -> mReq -> expect('id');
    $this -> mVmKey = 'chmand';

    // Ask If user has right for this page
    $lpn = 'usr-chmand';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdRead);
    }
  }

  protected function actStd() {
    $lUid = $this -> getReqInt('id');
    $lSql = 'SELECT * FROM al_usr where id = '.$lUid;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lUmand = $lRow['mand'];
    }
    $lMen = new CUsr_Menu($lUid, 'chmand');
    $lVie = new CUsr_chmand_Form($lUid, $lUmand);

    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }

  protected function actSedt() {
    $lUid = $this -> mReq -> getInt('id');
    $lSql = 'DELETE FROM al_usr_mand WHERE uid='.$lUid.' AND mand !=0';

    $lQry = new CCor_Qry($lSql);
    $lVal = $this -> mReq -> val;
    $lQry -> query('SELECT id FROM al_sys_mand');
    foreach ($lQry as $lRow) {
      $lMand = $lRow['id'];
      if (isset($lVal[$lMand])) {
        CCor_Qry::exec('INSERT INTO al_usr_mand (uid,mand) VALUES ('.$lUid.','.$lMand.')');
      }
    }
    $this -> redirect('index.php?act=usr-chmand&id='.$lUid);
  }

}