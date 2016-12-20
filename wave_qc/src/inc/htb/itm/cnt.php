<?php
class CInc_Htb_Itm_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('htb-itm.menu');
    $this -> mDom = $this -> getReq('dom');

    // Ask If user has right for this page
    $lpn = 'htb';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }
  }

  protected function getStdUrl() {
    return 'index.php?act='.$this -> mMod.'&dom='.$this -> mDom;
  }

  protected function actStd() {
    $lMas = new CHtb_List();
    $lVie = new CHtb_Itm_List($this -> mDom);
    $this -> render(CHtm_Wrap::wrap($lMas, $lVie));
  }

  protected function actEdt() {
    $lMas = new CHtb_List();

    $lId = $this -> getInt('id');
    $lVie = new CHtb_Itm_Form_Edit($lId, $this -> mDom);
    $this -> render(CHtm_Wrap::wrap($lMas, $lVie));
  }

  protected function actSedt() {
    $lMod = new CHtb_Itm_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> hasChanged()) {
      $lMod -> update();
      CCor_Cache::clearStatic('cor_res_htb_'.$this -> mDom);
    }
    $this -> redirect();
  }

  protected function actNew() {
    $lMas = new CHtb_List();

    $lVie = new CHtb_Itm_Form_Base('htb-itm.snew', lan('htb-itm.new'), 'htb-itm&dom='.$this -> mDom);
    $lVie -> setDom($this -> mDom);
    $this -> render(CHtm_Wrap::wrap($lMas, $lVie));
  }

  protected function actBatchNew() {
    $lMas = new CHtb_List();

    $lVie = new CHtb_Itm_Batch_Form('htb-itm.cbatch', lan("lib.addMultiple"), 'htb-itm&dom='.$this -> mDom);
    $lVie -> setDom($this -> mDom);
    $this -> render(CHtm_Wrap::wrap($lMas, $lVie));
  }

  protected function actCbatch() {
    $lVie = new CHtb_Itm_Batch_List("htb-itm.sbatch", "htb-itm.batchNew", $this->mReq, lan("fie.check"));

    $this -> render($lVie);
  }

  protected function actsbatch() {
    $lReq = $this->mReq;
    $lVal = $lReq->getVal("val");

    //Generate Array (Duplicates will be ignored)
    $lBatchData = array();
    $lLines = explode("\n", $lReq->getVal("batch"));
    foreach ($lLines as $line) {
      list($key, $value_de, $value_en) = explode(";", $line);
      $lBatchData[$key]["key"] = trim($key);
      $lBatchData[$key]["de"]  = trim($value_de);
      $lBatchData[$key]["en"]  = trim($value_en);
    }

    foreach ($lBatchData as $key => $value) {
      if(isset($lVal[$key])) {
        //Add to Helptable
        $lQry = "INSERT INTO `al_htb_itm` (`mand`,`domain`, `value`, `value_de`, `value_en`) VALUES ('".$this->getReq('mand')."','".$this->mDom."', '".$value["key"]."', '".$value["de"]."', '".$value["en"]."');";
        CCor_Qry::exec($lQry);
      }
    }

    CCor_Cache::clearStatic('cor_res_htb_'.$this -> mDom);
    $this -> redirect('index.php?act=htb-itm&dom='.$this -> mDom);
  }

  protected function actSnew() {
    $lMod = new CHtb_Itm_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> insert()) {
      CCor_Cache::clearStatic('cor_res_htb_'.$this -> mDom);
    }
    $this -> redirect('index.php?act=htb-itm.new&dom='.$this -> mDom);
  }

  protected function actDel() {
    $lId = $this -> mReq -> getInt('id');
    $lSql = 'DELETE FROM al_htb_itm WHERE mand IN(0,'.MID.') AND id="'.$lId.'"';
    CCor_Qry::exec($lSql);
    CCor_Cache::clearStatic('cor_res_htb_'.$this -> mDom);
    $this -> redirect();
  }

  protected function actCsvExp() {
    //File Export
    $lFileName = $this->mDom;

    //File
    header("Content-type: text/csv");
    header("Content-Disposition: attachement; filename=".$lFileName.".csv");
    flush();

    //Content
    $lCont = new CCor_Qry("SELECT value,value_de,value_en FROM al_htb_itm WHERE domain = '".$this->mDom . "' AND mand IN ('0','".MID."')");
    $lCont = $lCont->getAssocs();

    $lRetCsv = lan("lib.key") . "," . lan("cnd-itm.value") . " DE," . lan("cnd-itm.value") . " EN\n";
    foreach($lCont as $row) {
      $lRetCsv .= implode(';', $row) . "\n";
    }

    echo $lRetCsv;
  }
}