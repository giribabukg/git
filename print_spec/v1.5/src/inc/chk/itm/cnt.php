<?php
class CInc_Chk_Itm_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mTitle = lan('chk.itm');

    $this -> mDomain = $this -> getReq('domain');
    $this -> mMasterId = $this -> getReq('master_id');

    $lPriv = 'chk-itm';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lPriv)) {
      $this -> setProtection('*', $lPriv, rdNone);
    }
  }

  protected function getStdUrl() {
    return 'index.php?act='.$this -> mMod.'&domain='.$this -> mDomain;
  }

  protected function actStd() {
    $lVie = new CChk_Itm_List($this -> mDomain);
    $this -> render($lVie);
  }

  protected function actNew() {
    $lVie = new CChk_Itm_Form_Base('chk-itm.snew', lan('chk.itm.new'), 'chk-itm&master_id='.$this -> mMasterId.'&domain='.$this -> mDomain);
    $lVie -> setDomain($this -> mDomain);
    $lVie -> setMasterId($this -> mMasterId);
    $this -> render($lVie);
  }
  
  protected function actSnew() {
    $lMod = new CChk_Itm_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> insert()) {
      CCor_Cache::clearStatic('cor_res_chk_'.$this -> mDomain);
    }
    $this -> redirect();
  }

  protected function actEdt() {
    $lId = $this -> getReqInt('id');
    $lVie = new CChk_Itm_Form_Edit($lId, $this -> mDomain);
    $this -> render($lVie);
  }

  protected function actSedt() {
    $lMod = new CChk_Itm_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> update()) {
      CCor_Cache::clearStatic('cor_res_chk_'.$this -> mDomain);
    }
    $this -> redirect();
  }

  protected function actDel() {
    $lId = $this -> mReq -> getInt('id');
    $lSql = 'DELETE FROM al_chk_items WHERE id="'.$lId.'"';
    CCor_Qry::exec($lSql);
    CCor_Cache::clearStatic('cor_res_chk_'.$this -> mDomain);
    $this -> redirect();
  }

  protected function actOpr() {
    $lVie = new CHtm_Opr($this -> mMod.'.sopr', $this -> mMod.'&domain='.$this -> mDomain);
    $lVie -> setParam('domain', $this -> mDomain);

    $lDef = CCor_Res::get('chkitems', array('domain' => $this -> mDomain));
    $this -> arraySortByKey($lDef, 'ord_no');

    $lArr = array();
    foreach ($lDef as $lFie) {
      $lArr[$lFie['id']] = $lFie['name_'.LAN];
    }
    $lVie -> setSrc($lArr);

    $lPag = CHtm_Page::getInstance();
    $lPag -> addJs($lVie -> getTooltips());

    $this -> render($lVie);
  }
  
  protected function actSopr() {
    $lSrc = $this -> mReq -> getVal('src');
    $lDom = $this -> mReq -> getVal('domain');

    if (!empty($lSrc)) {
      $lSql = 'UPDATE al_chk_items SET ord_no = CASE id ';
      foreach ($lSrc as $lKey => $lValue) {
        $lSql.= 'WHEN '.$lValue.' THEN '.$lKey.' ';
      }
      $lSql.= 'END WHERE domain = "'.$lDom.'";';

      CCor_Qry::exec($lSql);
    }
    $this -> redirect('index.php?act='.$this -> mMod.'&domain='.$lDom);
  }

  protected function arraySortByKey(&$aArray, $aKey) {
    $lOrd = array();
    $lRet = array();

    reset($aArray);
    foreach ($aArray as $lKey => $lValue) {
      $lOrd[$lKey] = $lValue[$aKey];
    }

    asort($lOrd);
    foreach ($lOrd as $lKey => $lValue) {
      $lRet[$lKey] = $aArray[$lKey];
    }

    $aArray = $lRet;
  }
}