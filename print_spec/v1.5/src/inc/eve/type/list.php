<?php
class CInc_Eve_Type_List extends CHtm_List {

  /**
   * Registry for action types
   *
   * @var CApp_Event_Action_Registry
   */
  protected $mReg;

  public function __construct() {
    parent::__construct('eve-type');
    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('eve-type.menu');

    $this -> mIdField = 'code';

    $this -> addCtr();
    $this -> addColumn('code', 'Code', TRUE);
    $this -> addColumn('name', 'Name', TRUE);
    $this -> addColumn('fields', lan('fie.menu'), TRUE);
    $this -> mDefaultOrder = 'name';

    if ($this -> mCanDelete) {
      $this -> addDel();
    }

    $this -> addBtn(lan('eve-type.act.new'), "go('index.php?act=eve-type.new')", '<i class="ico-w16 ico-w16-plus"></i>');

    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_eve_types');
    $this -> mIte -> addCnd('mand='.MID);
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);

    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);
    $this -> mMaxLines = $this -> mIte -> getCount();
    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());
  }

  protected function getTdFields() {
    $lCod = $this -> getVal('code');
    $lRet = '<a href="index.php?act=eve-type.fields&amp;code='.$lCod.'" class="db">';
    $lRet.= $this->getVal('fields');
    $lRet.= NB.'</a>';
    return $this -> td($lRet);
  }

}