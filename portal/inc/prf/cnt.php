<?php
class CInc_Prf_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('prf.menu');
    $this -> mMmKey = 'usr';
    $lpn = 'prf';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }
  }

  protected function actStd() {
    $lVie = new CPrf_List();
    $this -> render($lVie);
  }

  protected function actEdt() {
    $lId = $this -> mReq -> getVal('id');
    $lMid = $this -> mReq -> getVal('mand');
    $lVie = new CPrf_Form_Edit($lId, $lMid);
    $this -> render($lVie);
  }

  protected function actSedt() {
    $lMod = new CPrf_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    

	//MOP2010 Intouch redirect to Edit-Page
	//$lId = $lMod -> getVal('code');
	//$this -> redirect('index.php?act=prf.edt&id='.$lId);    
	// MOP2010 ende
	
	$this -> redirect();
  }

  protected function actNew() {
    $lVie = new CPrf_Form_Base('prf.snew', lan('prf.new'));
    $this -> render($lVie);
  }

  protected function actSnew() {
    $lMod = new CPrf_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> insert();
	
	//MOP2010 Intouch redirect to Edit-Page
	//$lId = $lMod -> getVal('code');
	//$this -> redirect('index.php?act=prf.edt&id='.$lId);    
	// MOP2010 ende   

    $this -> redirect();
  }

  protected function actDel() {
    $lId = $this -> getReq('id');
    $lMod = new CPrf_Mod();
    $lMod -> delete($lId);
    $this -> redirect();
  }

}