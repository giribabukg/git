<?php
class CInc_Cnd_List extends CHtm_List {

  /**
   * Constructor
   *
   * @access public
   */
  public function __construct() {
    parent::__construct('cnd');

    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('cnd.menu');

    $this -> addCtr();
    $this -> addColumn('name', lan('cnd.name'), true, array('width' => '80%'));
    $this -> addColumn('flags', lan('cnd.flags'), true, array('width' => '20%'));

    $this -> mStdLnk = 'index.php?act=cnd.edt&amp;id=';
    $this -> mDelLnk = 'index.php?act=cnd.del&amp;id=';

    $lUsr = CCor_Usr::getInstance();

    if ($lUsr -> canDelete('cnd')) {
      $this -> addDel();
    }

    if ($lUsr -> canInsert('cnd')) {
      $this -> addBtn(lan('cnd.new'), "go('index.php?act=cnd.new')", '<i class="ico-w16 ico-w16-plus"></i>');
    }

    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_cnd_master');
    $this -> mIte -> addCnd('mand='.MID);
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);
    $this -> mMaxLines = $this -> mIte -> getCount();

    $this -> addPanel('nav', $this -> getNavBar());
  }

  /**
   * td flags
   *
   * @access protected
   */
  protected function getTdFlags() {
    $lId = $this -> getVal('id');
    $lCurrentFlags = $this -> getVal('flags');

    $lQuery = new CCor_Qry('SELECT value,value_'.LAN.' FROM al_htb_itm WHERE mand IN (0,'.MID.') AND domain="cnd";');
    foreach ($lQuery as $lRow) {
      $lAllFlags[$lRow['value']] = $lRow['value_'.LAN];
    }

    $lResult = array();
    foreach ($lAllFlags as $lKey => $lValue) {
      if (($lCurrentFlags & $lKey) === $lKey) {
        $lResult[] = $lValue;
      }
    }

    $lReturn = '<a href="index.php?act=cnd-itm&amp;cnd_id='.$lId.'">';
    $lReturn.= implode(",", $lResult);
    $lReturn.= '</a>';

    return $this -> td($lReturn);
  }

}