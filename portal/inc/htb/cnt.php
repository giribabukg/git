<?php
class CInc_Htb_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('htb.menu');
    $this -> mMmKey = 'opt';

    $lpn = 'htb';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }
  }

  protected function actStd() {
    $lVie = new CHtb_List();
    $this -> render($lVie);
  }

  protected function actEdt() {
    $lId = $this -> mReq -> getInt('id');
    $lVie = new CHtb_Form_Edit($lId);
    $this -> render($lVie);
  }

  protected function actSedt() {
    $lMod = new CHtb_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect();
  }

  protected function actNew() {
    $lVie = new CHtb_Form_Base('htb.snew', lan('htb.new'));
    $this -> render($lVie);
  }

  protected function actSnew() {
    $lMod = new CHtb_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> insert();
    $this -> redirect();
  }

  protected function actDel() {
    $lId = $this -> getInt('id');
    $lQry = new CCor_Qry('SELECT domain FROM al_htb_master WHERE id='.$lId);
    $lRow = $lQry->getAssoc();
    $lDom = $lRow['domain'];
    if (empty($lDom)) $this->redirect();

    $lQry->query('DELETE FROM al_htb_master WHERE id='.$lId);
    $lQry->query('DELETE FROM al_htb_itm WHERE domain='.esc($lDom));

    CHtb_Mod::clearCache();
    $this -> redirect();
  }

}