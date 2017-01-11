<?php
class CInc_Wec_Cnt extends CCor_Cnt {

  public $mAttributes = array(
    'preview.path',
    'preview.filename'
  );

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mTitle = lan('wec-pi.menu');
    $this -> mMmKey = 'opt';

    $lUserRight = 'wec-pi';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lUserRight)) {
      $this -> setProtection('*', $lUserRight, rdNone);
    }
  }

  protected function actStd() {
    $this -> redirect('index.php?act=wec.preview');
  }

  protected function actPreview() {
    $lForm = new CHtm_Form('wec.spreview', lan('lib.preview'), FALSE);
    $lForm -> setAtt('style', 'width: 100%');

    $lAttributes = implode('","', $this -> mAttributes);
    $lQry = new CCor_Qry('SELECT code,val FROM al_sys_pref WHERE code IN ("'.$lAttributes.'") AND mand='.MID);
    foreach ($lQry as $lKey => $lValue) {
      $lForm -> setVal($lValue['code'], $lValue['val']);
    }

    $lForm -> addDef(fie('preview.path',     lan('lib.preview.path'),     'edit', NULL, array('style' => 'width: 600px')));
    $lForm -> addDef(fie('preview.filename', lan('lib.preview.filename'), 'edit', NULL, array('style' => 'width: 600px')));

    $lMenu = new CWec_Menu('preview');
    $this -> render(CHtm_Wrap::wrap($lMenu, $lForm));
  }

  protected function actSpreview() {
    $lValues = $this -> getReq('val');

    foreach ($this -> mAttributes as $lKey) {
      if (!isset($lValues[$lKey])) {
        CCor_Qry::exec('DELETE FROM al_sys_pref WHERE code="'.$lKey.'" AND mand='.MID.';');
      } else {
        CCor_Qry::exec('REPLACE INTO al_sys_pref (code,mand,grp,val) VALUES ("'.$lKey.'", '.MID.', "adm", "'.addslashes($lValues[$lKey]).'");');
      }
    }

    $this -> redirect('index.php?act=wec.preview');
  }
}