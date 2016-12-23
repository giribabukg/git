<?php
class CInc_Crp_Sta_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('lib.status');
    $this -> mMmKey = 'opt';

    $lpn = 'crp';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }
  }

  protected function actStd() {
    $lCrp = $this -> getReqInt('id');
    $lMen = new CCrp_Menu($lCrp, 'sta');
    $lVie = new CCrp_Sta_List($lCrp);
    $this -> render(CHtm_Wrap::wrap($lMen,$lVie));
  }

  protected function actNew() {
    $lCid = $this -> getReqInt('id');
    $lVie = new CCrp_Sta_Form_Base('crp-sta.snew', lan('crp-sta.new'), $lCid);
    $lMen = new CCrp_Menu($lCid, 'sta');
    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }
  
  protected function actSnew() {
    $lCid = $this -> getReqInt('cid');
    $lMod = new CCrp_Sta_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> insert()) {
      CCor_Cache::clearStatic('cor_res_crp_'.MID);
      CCor_Cache::clearStatic('cor_res_crp_'.MID.'_'.$lCid);
      CCor_Cache::clearStatic('cor_res_crpstep_'.MID);
      CCor_Cache::clearStatic('cor_res_crpstep_'.MID.'_'.$lCid);
    }
    $this -> redirect('index.php?act=crp-sta&id='.$lCid);
  }

  protected function actEdt() {
    $lId  = $this -> getReqInt('id');
    $lCid = $this -> getReqInt('cid');
    $lMen = new CCrp_Menu($lCid, 'sta');
    $lVie = new CCrp_Sta_Form_Edit($lId, $lCid);
    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }

  protected function actSedt() {
    #echo '<pre>---cnt.php---'.get_class().'---';var_dump($_REQUEST,$this -> mReq,'#############');echo '</pre>';
    $lCid = $this -> getReqInt('cid');
    $lMod = new CCrp_Sta_Mod();

    $lMod -> getPost($this -> mReq);
    if ($lMod -> update()) {
      CCor_Cache::clearStatic('cor_res_crp_'.MID);
      CCor_Cache::clearStatic('cor_res_crp_'.MID.'_'.$lCid);
      CCor_Cache::clearStatic('cor_res_crpstep_'.MID);
      CCor_Cache::clearStatic('cor_res_crpstep_'.MID.'_'.$lCid);
    }
    $this -> redirect('index.php?act=crp-sta&id='.$lCid);
  }

  protected function actDel() {
    $lId  = $this -> getReqInt('id');
    $lCid = $this -> getReqInt('cid');
    // Die zugehörigen Steps mitlöschen!
    $lSql = 'DELETE FROM al_crp_step WHERE mand='.MID.' AND crp_id='.$lCid.' AND (from_id='.$lId.' OR to_id='.$lId.')';
    CCor_Qry::exec($lSql);
    $lMod = new CCrp_Sta_Mod();
    if ($lMod -> delete($lId)) {
      CCor_Cache::clearStatic('cor_res_crp_'.MID);
      CCor_Cache::clearStatic('cor_res_crp_'.MID.'_'.$lCid);
      CCor_Cache::clearStatic('cor_res_crpstep_'.MID);
      CCor_Cache::clearStatic('cor_res_crpstep_'.MID.'_'.$lCid);
    }
    $this -> redirect('index.php?act=crp-sta&id='.$lCid);
  }

  // steps

  protected function actNewstp() {
    $lCid = $this -> getReqInt('id');
    $lVie = new CCrp_Sta_Stp_Form_Base('crp-sta.snewstp', lan('crp-stp.new'), $lCid);
    $lMen = new CCrp_Menu($lCid, 'sta');
    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }
  
  protected function actNewstpind() {
    $lCid = $this -> getReqInt('id');
    $lVie = new CCrp_Sta_Stp_Form_Base('crp-sta.snewstp', lan('crp-stp.new-independent'), $lCid);
    $lVie->setIndependent();
    $lMen = new CCrp_Menu($lCid, 'sta');
    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }
  
  protected function actSnewstp() {
    $lCid = $this -> getReqInt('cid');
    $lMod = new CCrp_Sta_Stp_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> insert()) {
      CCor_Cache::clearStatic('cor_res_crp_'.MID);
      CCor_Cache::clearStatic('cor_res_crp_'.MID.'_'.$lCid);
      CCor_Cache::clearStatic('cor_res_crpstep_'.MID);
      CCor_Cache::clearStatic('cor_res_crpstep_'.MID.'_'.$lCid);
    }
    $this -> redirect('index.php?act=crp-sta&id='.$lCid);
  }

  protected function actEdtstp() {
    $lId  = $this -> getReqInt('id');
    $lCid = $this -> getReqInt('cid');
    $lMen = new CCrp_Menu($lCid, 'sta');
    $lVie = new CCrp_Sta_Stp_Form_Edit($lId, $lCid);
    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }

  protected function actSedtstp() {
    $lCid = $this -> getReqInt('cid');
    $lMod = new CCrp_Sta_Stp_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> update()) {
      CCor_Cache::clearStatic('cor_res_crp_'.MID);
      CCor_Cache::clearStatic('cor_res_crp_'.MID.'_'.$lCid);
      CCor_Cache::clearStatic('cor_res_crpstep_'.MID);
      CCor_Cache::clearStatic('cor_res_crpstep_'.MID.'_'.$lCid);
    }
    $this -> redirect('index.php?act=crp-sta&id='.$lCid);
  }

  protected function actDelstp() {
    $lId  = $this -> getReqInt('id');
    $lCid = $this -> getReqInt('cid');
    $lMod = new CCrp_Sta_Stp_Mod();
    if ($lMod -> delete($lId)) {
      CCor_Cache::clearStatic('cor_res_crp_'.MID);
      CCor_Cache::clearStatic('cor_res_crp_'.MID.'_'.$lCid);
      CCor_Cache::clearStatic('cor_res_crpstep_'.MID);
      CCor_Cache::clearStatic('cor_res_crpstep_'.MID.'_'.$lCid);
    }
    $this -> redirect('index.php?act=crp-sta&id='.$lCid);
  }
  
  protected function actExport() {
    $lCid = $this -> getInt('id');
    $lAll = CCor_Res::extract('id', 'code', 'crpmaster');
    $lName = (isset($lAll[$lCid])) ? strtoupper($lAll[$lCid]).'-' : ''; 
    $lExport = new CCrp_Export($lCid);
    $lRet = $lExport->getXml();
    header('Content-type: text/xml');
    header('Content-Disposition: attachment; filename="Workflow-'.$lName.date('y-m-d H-i-s').'.xml"');
    echo $lRet;
  }
  
  protected function actImport() {
    $lCid = $this -> getInt('id');
    $lAll = CCor_Res::extract('id', 'name_'.LAN, 'crpmaster');
    $lName = (isset($lAll[$lCid])) ? $lAll[$lCid] : 'Workflow ID'.$lCid; 
    $lFrm = new CHtm_Form('crp-sta.simport', lan('crp-sta.import').' - '.$lName, 'crp-sta&id='.$lCid);
    $lFrm->addDef(fie('xml', 'XML', 'memo'));
    $lFrm->setParam('id', $lCid);
    $this->render($lFrm);
  }
  
  protected function actSimport() {
    $lVal = $this->getReq('val');
    $lXml = $lVal['xml'];
    $lCid = $this->getInt('id');
    $lImport = new CCrp_Import($lCid);
    $lImport->importXml($lXml);
    $this->redirect('index.php?act=crp-sta&id='.$lCid);
  }

}