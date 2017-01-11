<?php
/**
 * Hilfstabellen unter Daten, k�nnen vom Kunden bearbeitet werden
 *
 * @package    htg
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 23 $
 * @date $Date: 2012-03-12 21:16:57 +0100 (Mon, 12 Mar 2012) $
 * @author $Author: gemmans $
 */
class CInc_Htg_Itm_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    $this -> m2Act = 'htg';
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('htb-itm.menu');
    $this -> mDom = $this -> getReq('dom');
    $lPriv = new CCor_Usr_Priv(CCor_Usr::getAuthId(), MID, 'htg');
    if (!$lPriv -> canDo($this -> mDom, rdRead)) $this -> denyAccess();
  }

  protected function getStdUrl() {
    return 'index.php?act='.$this -> mMod.'&dom='.$this -> mDom;
  }

  protected function actStd() {
    $lVie = new CHtg_Itm_List($this -> mDom);
    $this -> render($lVie);
  }

  protected function actReset() {
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref('htg-itm.page', 0);
    $this -> actStd();
  }

  protected function actEdt() {
    $lId = $this -> mReq -> getInt('id');
    $lVie = new CHtg_Itm_Form_Edit($lId, $this -> mDom);
    $this -> render($lVie);
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
    $lVie = new CHtb_Itm_Form_Base($this -> m2Act.'-itm.snew', lan('htb-itm.new'), $this -> m2Act.'-itm&dom='.$this -> mDom);
    $lVie -> setDom($this -> mDom);
    $this -> render($lVie);
  }

  protected function actSnew() {
    $lMod = new CHtb_Itm_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> insert()) {
      CCor_Cache::clearStatic('cor_res_htb_'.$this -> mDom);
    }
    $this -> redirect('index.php?act='.$this -> m2Act.'-itm.new&dom='.$this -> mDom);
  }

  protected function actDel() {
    $lId = $this -> mReq -> getInt('id');
    $lSql = 'DELETE FROM al_htb_itm WHERE mand IN(0,'.MID.') AND id="'.$lId.'"';
    CCor_Qry::exec($lSql);
    CCor_Cache::clearStatic('cor_res_htb_'.$this -> mDom);
    $this -> redirect();
  }

}