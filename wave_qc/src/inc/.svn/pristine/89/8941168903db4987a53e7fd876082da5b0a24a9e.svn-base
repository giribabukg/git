<?php
class CInc_Sys_Lang_List extends CHtm_List {

  public function __construct() {
    parent::__construct('sys-lang');
    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('sys-lang.menu');

    $this -> addCtr();
    $this -> addColumn('mand', lan('lib.mand'), TRUE, array('width' => '50px'));
    $this -> addColumn('code', lan('lib.code'), TRUE, array('width' => '100px'));

    $lAvailLang = CCor_Res::get('languages');
    foreach ($lAvailLang as $lLang => $lName) {
      $this -> addColumn('value_'.$lLang, lan('lan.'.$lLang), TRUE);
    }

    if ($this -> mCanDelete) {
      $this -> addDel();
    }
    if ($this -> mCanInsert) {
      $this -> addBtn(lan('lib.new_item'), "go('index.php?act=sys-lang.new')", '<i class="ico-w16 ico-w16-plus"></i>');
    }

    $this -> getPrefs();
    $this -> mIte = new CCor_TblIte('al_sys_lang');
    $this -> mIte -> addCnd('mand IN(0,'.MID.')');

    if (!empty($this -> mSer['msg'])) {
      $lVal = '"%'.addslashes($this -> mSer['msg']).'%"';
      $lSqlVal = '';
      foreach ($lAvailLang as $lLang => $lName) {
       $lSqlVal.= ' OR value_'.$lLang.' LIKE '.$lVal;
      }
      $this -> mIte -> addCnd('(code LIKE '.$lVal.$lSqlVal.')');
    }

    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);

    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);
    $this -> mMaxLines = $this -> mIte -> getCount();
    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());

    $this -> addPanel('sca', '| '.htmlan('lib.search'));
    $this -> addPanel('ser', $this -> getSearchForm());

    /*	sp�ter zur Administrierung neuer Sprachen
     $lUsr = CCor_Usr::getInstance();
    if ($lUsr -> canInsert('new-lang')) {
    $this -> addBtn(lan('new_lang'), "go('index.php?act=sys-lang.new')", 'img/ico/16/plus.gif');
    }
    */
  }

  protected function getSearchForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="sys-lang.ser" />'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
    $lVal = (isset($this -> mSer['msg'])) ? htm($this -> mSer['msg']) : '';
    $lRet.= '<td><input type="text" name="val[msg]" class="inp" value="'.$lVal.'" /></td>'.LF;
    $lRet.= '<td>'.btn(lan('lib.search'),'','','submit').'</td>';
    if (!empty($this -> mSer)) {
      $lRet.= '<td>'.btn(lan('lib.show_all'),'go("index.php?act=sys-lang.clser")').'</td>';
    }
    $lRet.= '</tr></table>';
    $lRet.= '</form>'.LF;

    return $lRet;
  }

  protected function getLink() {
    $lCode = $this -> getVal('code');
    $lMand = $this -> getVal('mand');
    return $this -> mStdLnk.$lCode.'&mid='.$lMand;
  }

  protected function getDelLink() {
    $lCode = $this -> getVal('code');
    $lMand = $this -> getVal('mand');
    return $this -> mDelLnk.$lCode.'&mid='.$lMand;
  }



}