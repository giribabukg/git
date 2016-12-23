<?php
class CInc_Chk_Itm_List extends CHtm_List {

  public function __construct($aDomain) {
    parent::__construct('chk-itm');

    $this -> mDomain = $aDomain;

    $this -> setAtt('width', '100%');

   # $this -> mName = CCor_Qry::getStr('SELECT name_'.LAN.' FROM al_chk_master WHERE domain="'.addslashes($this -> mDomain).'"');
    $lSql = 'SELECT id, name_'.LAN.' AS name FROM al_chk_master WHERE domain="'.addslashes($this -> mDomain).'"';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> mName = $lRow['name'];
      $this -> mMasterId = $lRow['id'];
    }
    $this -> mTitle = lan('chk.itm').' - '.htm($this -> mName);

    $this -> addCtr();
    $this -> addColumn('name_'.LAN, lan('lib.value'), TRUE);
    $this -> addColumn('cnd', lan('lib.condition'), TRUE, array('width' => '16'));

    $this -> mDefaultOrder = 'name_'.LAN;

    if ($this -> mCanDelete) {
      $this -> addDel();
    }

    $this -> mOrdLnk = 'index.php?act=chk-itm.ord&amp;domain='.$this -> mDomain.'&amp;fie=';
    $this -> mStdLnk = 'index.php?act=chk-itm.edt&amp;domain='.$this -> mDomain.'&amp;id=';
    $this -> mDelLnk = 'index.php?act=chk-itm.del&amp;domain='.$this -> mDomain.'&amp;id=';

    $this -> addBtn('Back', "go('index.php?act=chk')", 'img/ico/16/back-hi.gif');

    if ($this -> mCanInsert) {
      $lTemp = strval($this -> mMasterId);
      $lUrl = "go('index.php?act=chk-itm.new&master_id=".$lTemp."&domain=".$this -> mDomain."')";
      $this -> addBtn('New Item', $lUrl, 'img/ico/16/plus.gif');
    }

    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_chk_items AS chk_items');
    $this -> mIte -> addField('chk_items.id AS id');
    $this -> mIte -> addField('chk_items.name_'.LAN.' AS name_'.LAN);
    $this -> mIte -> addField('(SELECT cond.name FROM al_cond AS cond WHERE chk_items.cnd_id=cond.id) AS cnd');

    $this -> mIte -> addCnd('domain="'.addslashes($this -> mDomain).'"');

    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);

    $this -> mMaxLines = $this -> mIte -> getCount();

    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());
  }

  protected function & getViewMenuObject() {
    $lMen = new CHtm_Menu(lan('lib.opt'));

    $lMen -> addTh2(lan('lib.opt.opr'));
    $lMen -> addItem('index.php?act='.$this -> mMod.'.opr&domain='.$this -> mDomain, lan('lib.opt.opr'), 'ico/16/ord-asc-desc.gif');

    $lMen -> addTh2(lan('lib.opt.lpp'));
    $lOk = 'ico/16/ok.gif';
    $lArr = array(25, 50, 100, 200);
    foreach ($lArr as $lLpp) {
      $lImg = ($lLpp == $this -> mLpp) ? $lOk : 'd.gif';
      $lMen -> addItem($this -> mLppLnk.$lLpp, $lLpp.' '.lan('lib.opt.lines'), $lImg);
    }
    return $lMen;
  }
}