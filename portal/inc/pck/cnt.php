<?php
class CInc_Pck_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('pck.menu');
    $this -> mMmKey = 'opt';

    $lpn = 'pck';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }
  }

  protected function actStd() {
    $lVie = new CPck_List();
    $this -> render($lVie);
  }

  protected function actEdt() {
    $lId  = $this -> getReq('id');
    $lDom = $this -> getReq('dom');
    $lMen = new CPck_Menu($lDom, 'dat');
    $lVie = new CPck_Form_Edit($lId, $lDom);
    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
    #$this -> render($lVie);
  }

  protected function actSedt() {
    $lMod = new CPck_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect();
    #$lId = $lMod -> getVal('id');
    #$this -> redirect('index.php?act=pck.edt&id='.$lId);
  }

  protected function actNew() {
    $lVie = new CPck_Form_Base('pck.snew', lan('pck.new'));
    $this -> render($lVie);
  }

  protected function actSnew() {
    $lMod = new CPck_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> insert();
/*  if ($lMod -> insert()) {
      $lId = $lMod -> getInsertId();
      $this -> redirect('index.php?act=pck.edt&id='.$lId);
    }
*/
    $this -> redirect();
  }

  protected function actDel() {
    $lId  = $this -> getReq('id');
    $lSql = 'UPDATE al_pck_master SET del="Y" WHERE id='.$lId;
    CCor_Qry::exec($lSql);
    // TODO: delete items in pck_itm, too
    $this -> redirect();
  }

}