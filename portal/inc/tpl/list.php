<?php
class CInc_Tpl_List extends CHtm_List {

  public function __construct() {
    parent::__construct('tpl');
    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('tpl.menu');

    $this -> addColumn('more');
    $this -> addColumn('mand', lan('lib.mand'), TRUE, array('width' => '50px'));
    $this -> addColumn('lang', lan('lib.lang'), TRUE);
    $this -> addColumn('name', 'Name', TRUE);
    $this -> addColumn('subject', lan('lib.sbj'), TRUE);
    $this -> mDefaultOrder = 'name';

    $this -> addIsThisInUse();
    if ($this -> mCanDelete) {
      $this -> addDel();
    }
    $this -> addBtn(lan('lib.neutpl'), "go('index.php?act=tpl.new')", '<i class="ico-w16 ico-w16-plus"></i>');

    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_eve_tpl');
    $this -> mIte -> addCnd('mand IN(0,'.MID.')');
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);

    if (!empty($this -> mSer)) {
      $lName = (empty($this -> mSer['name'])) ? '' : trim($this -> mSer['name']);
      if (!empty($lName)) {
        $lName = esc('%'.$lName.'%');
        $lCnd = '(name LIKE '.$lName.' ';
        $lCnd.= 'OR subject LIKE '.$lName.' ';
        $lCnd.= 'OR msg LIKE '.$lName.')';
        $this -> mIte -> addCnd($lCnd);
      }
      if (!empty($this -> mSer['typ'])) {
        $this -> mTyp = $this -> mSer['typ'];
        $lCnd = '(e.typ='.esc($this -> mSer['typ']).')';
        $this -> mIte -> addCnd($lCnd);
      }
    }

    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);
    $this -> mMaxLines = $this -> mIte -> getCount();
    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());
    $this -> addPanel('cap', '|');
    $this -> addPanel('fil', $this -> getSearchForm());
  }

  protected function getSearchForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="tpl.ser" />'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;

    $lRet.= '<td>'.htm(lan('lib.search')).'</td>';
    $lVal = (isset($this -> mSer['name'])) ? htm($this -> mSer['name']) : '';
    $lRet.= '<td><input type="text" name="val[name]" class="inp" value="'.$lVal.'" /></td>'.LF;

    $lRet.= '<td>'.btn(lan('lib.search'),'','','submit').'</td>';
    if (!empty($this -> mSer)) {
      $lRet.= '<td>'.btn(lan('lib.show_all'),'go("index.php?act=tpl.clser")').'</td>';
    }
    $lRet.= '</tr></table>';
    $lRet.= '</form>'.LF;

    return $lRet;
  }

  protected function getTdMore() {
    $this -> mMoreId = getNum('t');
    $lRet = '<a class="nav" onclick="Flow.Std.togTr(\''.$this -> mMoreId.'\')">';
    $lRet.= '...</a>';
    return $this -> tdClass($lRet, 'w16');
  }

  protected function afterRow() {
    $lRet = parent::afterRow();
    $lRet.= '<tr id="'.$this -> mMoreId.'" class="togtr" style="display:none">'.LF;
    $lRet.= '<td class="td1 ca">&nbsp;</td>'.LF;
    $lRet.= '<td class="td1 p8"'.$this -> getColspan().'>';
    $lVal = trim($this -> getVal('msg'));
    $lRet.= nl2br(htm($lVal));
    $lRet.= '</td>'.LF;
    $lRet.= '</tr>'.LF;
    return $lRet;
  }

  protected function getTdInuse() {
  	$lRet = '<td class="'.$this -> mCls.' nw w16 ac">';
  	$lId = $this -> getVal($this -> mIdField);
  	$lInEveAction = CCor_Qry::getInt('SELECT id FROM al_eve_act WHERE param LIKE "%tpl%%'.$lId.'%"');
  	if (!empty($lInEveAction)) {
  		$lRet.= '<i class="ico-w16 ico-w16-flag-03"></i>';
  	}
  	else $lRet.= '<i class="ico-w16 ico-w16-flag-00"></i>';
  	$lRet.= '</td>'.LF;
  	return $lRet;
  }

}