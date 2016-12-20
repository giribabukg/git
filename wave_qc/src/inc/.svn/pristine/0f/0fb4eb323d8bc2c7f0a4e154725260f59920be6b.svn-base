<?php
class CInc_Cnd_Itm_List extends CHtm_List {

  /**
   * Constructor
   *
   * @access public
   * @param $aCndId
   */
  public function __construct($aCndId) {
    parent::__construct('cnd-itm');

    $this -> mCndId = $aCndId;

    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('cnd-itm.menu');
    
    $this -> addCtr();
    $this -> addColumn('field', lan('cnd-itm.field'), false, array('width' => '25%'));
    $this -> addColumn('operator', lan('cnd-itm.operator'), false, array('width' => '25%'));
    $this -> addColumn('value', lan('cnd-itm.value'), false, array('width' => '25%'));
    $this -> addColumn('conjunction', lan('cnd-itm.conjunction'), false, array('width' => '25%'));

    $this -> mStdLnk = 'index.php?act=cnd-itm.edt&amp;cnd_id='.$this -> mCndId.'&amp;id=';
    $this -> mDelLnk = 'index.php?act=cnd-itm.del&amp;cnd_id='.$this -> mCndId.'&amp;id=';

    $lUsr = CCor_Usr::getInstance();

    if ($lUsr -> canDelete('cnd')) {
      $this -> addDel();
    }

    if ($lUsr -> canInsert('cnd')) {
      $this -> addBtn(lan('cnd-itm.new'), "go('index.php?act=cnd-itm.new&cnd_id=".$this -> mCndId."')", 'img/ico/16/plus.gif');
    }

    $this -> addBtn(lan('cnd-itm.back'), "go('index.php?act=cnd')", 'img/ico/16/back-hi.gif', true);

    $this -> mIte = new CCor_TblIte('al_cnd_items');
    $this -> mIte -> addCnd('cnd_id='.addslashes($this -> mCndId));
  }

}