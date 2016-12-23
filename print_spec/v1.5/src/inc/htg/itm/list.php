<?php
/**
 * Hilfstabellen unter Daten, können vom Kunden bearbeitet werden
 *
 * @package    htg
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 23 $
 * @date $Date: 2012-03-13 04:16:57 +0800 (Tue, 13 Mar 2012) $
 * @author $Author: gemmans $
 */
class CInc_Htg_Itm_List extends CHtm_List {

  public function __construct($aDom) {
    $this -> m2Act = 'htg';
    parent::__construct('htg-itm');
    $this -> mDom = $aDom;
    $this -> setAtt('width', '100%');
    $this -> mName = CCor_Qry::getStr('SELECT description FROM al_htb_master WHERE domain="'.addslashes($this -> mDom).'"');
    $this -> mTitle = lan('htb-itm.menu').' - '.htm($this -> mName);
    $this -> getPriv($aDom);//'htg'

    $this -> addCtr();
    $this -> addColumn('value', lan('lib.key'), TRUE, array('width' => '16'));
    $this -> addColumn('value_'.LAN, lan('lib.value'), TRUE, array('width' => '100%'));
    $this -> mDefaultOrder = 'value_'.LAN;

    if ($this -> mCanDelete) {
      $this -> addDel();
    }

    $this -> mOrdLnk = 'index.php?act='.$this -> m2Act.'-itm.ord&amp;dom='.$this -> mDom.'&amp;fie=';
    if ($this -> mCanEdit)
      $this -> mStdLnk = 'index.php?act='.$this -> m2Act.'-itm.edt&amp;dom='.$this -> mDom.'&amp;id=';
    else
      $this -> mStdLnk = '';
    $this -> mDelLnk = 'index.php?act='.$this -> m2Act.'-itm.del&amp;dom='.$this -> mDom.'&amp;id=';

    $this -> addBtn(lan('lib.back'), "go('index.php?act=".$this -> m2Act."')", 'img/ico/16/back-hi.gif');
    if ($this -> mCanInsert) {
      $this -> addBtn(lan('lib.new_item'), "go('index.php?act=".$this -> m2Act."-itm.new&dom=".$this -> mDom."')", 'img/ico/16/plus.gif');
    }

    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_htb_itm');
    $this -> mIte -> addCnd('domain="'.addslashes($this -> mDom).'"');
    $this -> mIte -> addCnd('mand IN (0,'.MID.')');
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);

    $this -> mMaxLines = $this -> mIte -> getCount();
    if ($this -> mPage * $this -> mLpp > $this->mMaxLines) {
      $this->mPage  = 0;
      $this->mCtr   = 1;
      $this->mFirst = 0;
    }
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);

    $this -> addPanel('nav', $this -> getNavBar());
    #$this -> addPanel('vie', $this -> getViewMenu());
  }

  protected function getPriv($aKey = NULL) {
    $this -> mRig = 'htg';
    $lKey = (NULL === $aKey) ? $this -> mDom : $aKey;
    $lPriv = new CCor_Usr_Priv(CCor_Usr::getAuthId(), MID, $this -> mRig);
    $this -> mCanRead   = $lPriv -> canDo($aKey, rdRead);
    $this -> mCanEdit   = $lPriv -> canDo($aKey, rdEdit);
    $this -> mCanInsert = $lPriv -> canDo($aKey, rdIns);
    $this -> mCanDelete = $lPriv -> canDo($aKey, rdDel);
  }

  protected function getNavBar() {
    $lNav = new CHtm_NavBar($this -> mMod, $this -> mPage, $this -> mMaxLines, $this -> mLpp);
    $lNav -> setParam('dom', $this -> mDom);
    return $lNav -> getContent();
  }

}