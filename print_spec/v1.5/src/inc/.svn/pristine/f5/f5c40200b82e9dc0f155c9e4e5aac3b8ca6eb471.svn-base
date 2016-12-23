<?php
class CInc_Gru_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('gru.menu');
    $this -> mMmKey = 'usr';
    $lpn = 'gru';
    
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }
  }

  protected function actStd() {
    $lVie = new CGru_List();
    $this -> render($lVie);
  }

  protected function actEdt() {
    $lGid = $this -> getInt('id');
    //darf nur die Gruppen aus aktuellem Mandant und "Mandant=0" editiert werden.
    $lNam = CCor_Qry::getInt('SELECT id FROM al_gru WHERE id='.$lGid.' AND mand IN (0,'.MID.')');
    if (!$lNam){
      $this -> redirect();
    }
    $lMen = new CGru_Menu($lGid, 'dat');
    $lVie = new CGru_Form_Edit($lGid);
    $lVie -> setParam('id', $lGid);
    $this -> render(CHtm_Wrap::wrap($lMen,$lVie));
}

  protected function actSedt() {
    $lGid = $this -> getInt('id');
    $lMod = new CGru_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    CCor_Cache::clearStatic('cor_res_gru_'.MID);
    $this -> redirect('index.php?act=gru.edt&id='.$lGid);
  }

  protected function actNew() {
    $lMid = $this -> getReqInt('mid');
    $lVie = new CGru_Form_Base('gru.snew', lan('gru.new'), NULL, $lMid);
    $this -> render($lVie);
  }

  protected function actSnew() {
    
    $lUsr = CCor_Usr::getInstance();
    $lUid = $lUsr -> getId();
    $lGruArr = CCor_Res::extract('id', 'name', 'gru');
    $ParentId = $this -> mReq -> val['parent_id'];
    $lGroupName = $this -> mReq -> val['name'];
    $ParentGroupName = (isset($lGruArr[$ParentId])) ? $lGruArr[$ParentId] : ''; 
    $lQry = new CCor_Qry();
    
    $lMod = new CGru_Mod();
    $lMod -> getPost($this -> mReq);
  
    if ($lMod -> insert()) {
      $lId = $lMod -> getInsertId();
      $lGruHisSave = $lMod -> saveGruHis($lUid, $lId, date("Y-m-d"), $aTyp=14, $aSubject="Added group ($lGroupName)", $aMsg='');
      CCor_Cache::clearStatic('cor_res_gru_'.MID);
      $this -> redirect('index.php?act=gru.edt&id='.$lId);
    }
    $this -> redirect();
  }

  protected function actDel() {
    $lGid = $this -> getReqInt('id');
    $lMod = new CGru_Mod();
    $lMod -> delete($lGid);
    CCor_Cache::clearStatic('cor_res_gru_'.MID);
    $this -> redirect();
  }
}