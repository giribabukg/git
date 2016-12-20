<?php
class CInc_Tab_List extends CHtm_List {

  protected $mModule = 'tab_master';

  public function __construct() {
    parent::__construct('tab');

    $this -> setAtt('width', '100%');
    $this -> mTitle = lan($this -> mModule.'.menu');

    $this -> addCtr();
    $this -> addColumn('name', lan($this -> mModule.'.name'), TRUE);
    $this -> addColumn('type', lan($this -> mModule.'.type'), TRUE);
    $this -> addColumn('count', lan($this -> mModule.'.count'), TRUE, array('width' => '16'));

    $this -> mDefaultOrder = 'name';

    if ($this -> mCanDelete) {
      $this -> addDel();
    }

    $this -> addBtn(lan($this -> mModule.'.act.new'), "go('index.php?act=tab.new')", '<i class="ico-w16 ico-w16-plus"></i>');

    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_'.$this -> mModule);
    $this -> mIte -> addCnd('mand IN (0, '.MID.')');
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);
    $this -> mMaxLines = $this -> mIte -> getCount();
    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());
  }

  protected function getTdCount() {
    $lTabType = $this -> getVal('type');
    $lRet = '<a href="index.php?act=tab-itm&amp;type='.$lTabType.'">';
    $lCnt = CCor_Qry::getStr('SELECT COUNT(*) FROM al_tab_slave WHERE mand IN (0, '.MID.') AND type="'.$lTabType.'"');
    $lRet.= $lCnt;
    $lRet.= '</a>';
    return $this -> td($lRet);
  }
}