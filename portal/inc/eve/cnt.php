<?php
class CInc_Eve_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('eve.menu');
    $this -> mMmKey = 'opt';

    // Ask If user has right for this page
    $lpn = 'eve';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdRead);
    }
  }

  protected function actStd() {
    $lVie = new CEve_List();
    $this -> render($lVie);
  }

  protected function actEdt() {
    $lId = $this -> getInt('id');

    $lFrm = new CEve_Form('eve.sedt', lan('eve.edt'));
    $lFrm -> load($lId);

    $lMen = new CEve_Menu($lId, 'dat');

    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm));
  }

  protected function getInfos($aVals) {
    $lRet = array();
    if (empty($aVals)) return $lRet;
    foreach ($aVals as $lKey => $lVal) {
      if ('' == $lVal) continue;
      if (substr($lKey,0,5) == 'info_') {
        $lRet[substr($lKey,5)] = $lVal;
      }
    }
    return $lRet;
  }

  protected function actSedt() {
    $lMod = new CEve_Mod();

    $lReq = $this->getVal('val');
    $lInfos = $this->getInfos($lReq);
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    if (!empty($lInfos)) {
      $lMod -> setInfos($lReq['id'], $lInfos);
    }
    $this -> redirect();
  }

  protected function actNew() {
    $lFrm = new CEve_Form('eve.snew', lan('eve.new'));
    $this -> render($lFrm);
  }

  protected function actSnew() {
    $lMod = new CEve_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> insert()) {
      $lId = $lMod->getInsertId();
      $this->redirect('index.php?act=eve.edt&id='.$lId);
    } else {
      $this -> redirect();
    }
  }

  protected function actCopy() {
    $lId = $this -> getInt('id');
    $lFrm = new CEve_Form('eve.scopy', lan('eve.edt'));
    $lFrm -> load($lId);

    $this -> render($lFrm);
  }

  protected function actScopy() {
    $lMod = new CEve_Mod();
    $lValues = $this->getVal('val');

    $lMod -> getPost($this->mReq);
    $lOldId = $lValues['id'];
    $lMod->forceVal('id', NULL);

    if ($lMod -> insert()) {
      $lId = $lMod->getInsertId();
      $lValues = $this->getVal('val');
      $lInfos = $this->getInfos($lValues);
      if (!empty($lInfos)) {
        $lMod -> setInfos($lId, $lInfos);
      }
      $lMod->copyActions($lOldId, $lId);
      $this->redirect('index.php?act=eve.edt&id='.$lId);
    } else {
      $this -> redirect();
    }
  }

  protected function actDel() {
    $lId = $this -> mReq -> getInt('id');
    $lSql = 'DELETE FROM al_eve WHERE mand='.MID.' AND id="'.addslashes($lId).'"';
    CCor_Qry::exec($lSql);
    CEve_Mod::clearCache();
    $this -> redirect();
  }
  
  protected function actXlsexp() {
    $lFileName = 'Approval workflow_';
    $lFileName.= date('Ymd_H-i-s');
    $lFileName.= '.xls';

    $lEventList = new CInc_Eve_List();
    $lIdField = $lEventList -> mIdField;
    $lEventList -> mIte -> setLimit(0, 10000);
    $lEventList -> mIte = $lEventList -> mIte -> getArray($lIdField);
    
    $lXls = $lEventList -> getExcel();#exit;
    $lXls -> downloadAs($lFileName);
  }
  
  protected function actReportexp() {
    $lFileName = 'Approval workflow_';
    $lFileName.= date('Ymd_H-i-s');
    $lFileName.= '.xls';
    
    $lEventList = new CInc_Eve_List();
    $lIdField = $lEventList -> mIdField;
    $lEventList -> mIte -> setLimit(0, 10000);
    $lEventList -> mIte = $lEventList -> mIte -> getArray($lIdField);
    
    $this -> mReq -> expect('val');
    $lReq = $this -> getReq('val', array());
    $lArr = array();
    foreach ($lReq as $lKey => $lVal) {
      if ('' === $lVal) continue;
      $lArr[$lKey] = $lVal;
    }
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mMod.'.fil', $lArr);
    $lXls = $lEventList -> getReportAplCounts($lArr);
    $lXls -> downloadAs($lFileName);
  }

}