<?php
class CInc_Apl_Types_List extends CHtm_List {

  public function __construct() {
    parent::__construct('apl-types');
    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('apl-types.menu');

    $this -> addCtr();
    $this -> addColumn('code',  'Code', TRUE, array('width' => '50'));
    $this -> addColumn('short', 'Short', TRUE);
    $this -> addColumn('name', 'Name', TRUE);

    if ($this -> mCanDelete) {
      $this -> addDel();
    }

    $this -> addBtn(lan('apl-types.new'), "go('index.php?act=apl-types.new')", '<i class="ico-w16 ico-w16-plus"></i>');

    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_apl_types');
    $this -> mIte ->addCnd('mand='.MID);
    if (!empty($this -> mSer['name'])) {
      $lVal = '"%'.addslashes($this -> mSer['name']).'%"';
      $lCnd = '(name LIKE '.$lVal.' OR ';
      $lCnd.= 'code LIKE '.$lVal.')';
      $this -> mIte -> addCnd($lCnd);
    }
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);
    $this -> mMaxLines = $this -> mIte -> getCount();

    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('sca', '| '.htmlan('lib.search'));
    $this -> addPanel('ser', $this -> getSearchForm());
  }

  protected function getSearchForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="apl-types.ser" />'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
    $lVal = (isset($this -> mSer['name'])) ? htm($this -> mSer['name']) : '';
    $lRet.= '<td><input type="text" name="val[name]" class="inp" value="'.$lVal.'" /></td>'.LF;
    $lRet.= '<td>'.btn(lan('lib.search'),'','','submit').'</td>';
    if (!empty($this -> mSer)) {
      $lRet.= '<td>'.btn(lan('lib.show_all'),'go("index.php?act=apl-types.clser")').'</td>';
    }
    $lRet.= '</tr></table>';
    $lRet.= '</form>'.LF;

    return $lRet;
  }

}