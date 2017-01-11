<?php
class CInc_Jfl_List extends CHtm_List {

  public function __construct() {
    parent::__construct('jfl');

    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('jfl.menu');

    $this -> addCtr();
    $this -> addColumn('img',       '',               TRUE, array('width' => '16px'));
    $this -> addColumn('name_'.LAN, lan('lib.name'),  TRUE, array('width' => '100%'));
    $this -> addColumn('code',      lan('lib.code'),  TRUE, array('width' => '16px'));
    $this -> addColumn('val',       lan('lib.value'), TRUE, array('width' => '16px'));
    $this -> addColumn('mand',      lan('lib.mand'),  TRUE, array('width' => '16px'));

    $this -> mDefaultOrder = 'val';

    $this -> getPrefs();

    if ($this -> mCanInsert) {
      $this -> addBtn(lan('jfl.new'), "go('index.php?act=jfl.new')", 'img/ico/16/plus.gif');
    }

    if ($this -> mCanDelete) {
      $this -> addDel();
    }

    $this -> mIte = new CCor_TblIte('al_jfl');
    $this -> mIte -> addCnd('mand IN(0,'.MID.')');
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);

    $this -> mMaxLines = $this -> mIte -> getCount();

    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());
  }

  protected function getTdImg() {
    $lVal = $this -> getVal('val');
//    $lPath = getImgPath('img/jfl/'.$lVal.'.gif');
//    $lRet = img($lPath, array('style' => 'margin: 2px'));

    $lRet = "<i class='ico-jfl ico-jfl-".$lVal."'></i>";
    return $this -> tdClass($lRet, 'w20 h20 ac');
  }
}