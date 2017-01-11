<?php
/**
 * Hilfstabellen unter Daten, können vom Kunden bearbeitet werden
 *
 * @package    htg
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 6 $
 * @date $Date: 2012-02-21 09:50:56 +0100 (Tue, 21 Feb 2012) $
 * @author $Author: gemmans $
 */
class CInc_Htg_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('htb.menu');
    $this -> m2Act = 'htg';

    $lpn = $this -> m2Act;// old:'htb';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    } else {// $this -> mcanRead= true;
      $this -> mcanEdt = $lUsr -> canEdit($lpn);
      $this -> mcanIns = $lUsr -> canInsert($lpn);
      $this -> mcanDel = $lUsr -> canDelete($lpn);
    }
  }

  protected function actStd() {
    $lVie = new CHtg_List();
    $this -> render($lVie);
  }

  protected function actEdt() {
    if($this -> mcanEdt){
      $lId = $this -> mReq -> getInt('id');
      $lVie = new CHtg_Form_Edit($lId);
      $this -> render($lVie);
    } else
      $this -> redirect();
  }

  protected function actSedt() {
    if($this -> mcanEdt){
      $lMod = new CHtb_Mod();
      $lMod -> getPost($this -> mReq);
      $lMod -> update();
    }
    $this -> redirect();
  }

  protected function actNew() {
    if($this -> mcanIns){
      $lVie = new CHtb_Form_Base($this -> m2Act.'.snew', lan('htb.new'));

      $this -> render($lVie);
    } else
      $this -> redirect();
  }

  protected function actSnew() {
    if($this -> mcanIns){
      $lMod = new CHtb_Mod();
      $lMod -> getPost($this -> mReq);
      $lMod -> insert();
    }
    $this -> redirect();
  }

  protected function actDel() {
    if($this -> mcanDel){
      $lId = $this -> mReq -> getInt('id');
      $lSql = 'DELETE FROM al_htb_master WHERE id="'.addslashes($lId).'"';
      CCor_Qry::exec($lSql);
      // TODO: delete items in htb_itm, too
    }
    $this -> redirect();
  }

}