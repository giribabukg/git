<?php
class CInc_Lan_List extends CHtm_List {

  public function __construct($aAvailLang = array()) {
    parent::__construct('lan');
    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('lang.menu');

    $lUsr = CCor_Usr::getInstance();

    $this -> mAvailLang = $aAvailLang;
    #echo '<pre>---list.php---'.get_class().'---';var_dump($lAvailLang,'#############');echo '</pre>';

    if (!empty($this -> mAvailLang)) {
      $this -> addCtr();

      // Show Copy Button
      if ($lUsr -> canInsert('lang')) {
        $this -> addColumn('cpy', '', FALSE, array('width' => 16));
      }

      $this -> addColumn('code', lan('lib.code'), TRUE, array('width' => '100px'));
      if (1 == CCor_Usr::getAuthId() AND $lUsr -> canDelete('lang')) {
        $this -> addColumn('del', lan('lib.delete'), TRUE, array('width' => '16'));
      }

      $this -> getPrefs();

      $lSearchStrg = '';
      if (!empty($this -> mSer['msg'])) {
        $lVal = '"%'.addslashes($this -> mSer['msg']).'%"';
        $lSearchStrg.= '(code LIKE '.$lVal;
      } else {
        $lVal = '';
      }
      foreach ($this -> mAvailLang as $lLang => $lName) {
        $this -> addColumn('name_'.$lLang, lan('lan.'.$lLang), TRUE);
        $lSearchStrg.=' OR name_'.$lLang.' LIKE '.$lVal;
      }
      $lSearchStrg.= ')';

      if ($lUsr -> canInsert('lang')) {//if ($this -> mCanInsert) {#arbeitet mit 'lan'
        $this -> addBtn(lan('new_lang'), "go('index.php?act=lan.new')", 'img/ico/16/plus.gif');
      }

      $this -> mIte = new CCor_TblIte('al_sys_languages');

      if (!empty($this -> mSer['msg'])) {
        $this -> mIte -> addCnd($lSearchStrg);
      }

      $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);

      $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);
      $this -> mMaxLines = $this -> mIte -> getCount();
      $this -> addPanel('nav', $this -> getNavBar());
      $this -> addPanel('vie', $this -> getViewMenu());

      $this -> addPanel('sca', '| '.htmlan('lib.search'));
      $this -> addPanel('ser', $this -> getSearchForm());
    }

  }

  protected function getSearchForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="lan.ser" />'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
    $lVal = (isset($this -> mSer['msg'])) ? htm($this -> mSer['msg']) : '';
    $lRet.= '<td><input type="text" name="val[msg]" class="inp" value="'.$lVal.'" /></td>'.LF;
    $lRet.= '<td>'.btn(lan('lib.search'),'','','submit').'</td>';
    if (!empty($this -> mSer)) {
      $lRet.= '<td>'.btn(lan('lib.show_all'),'go("index.php?act=lan.clser")').'</td>';
    }
    $lRet.= '</tr></table>';
    $lRet.= '</form>'.LF;

    return $lRet;
  }

  protected function getLink() {
    $lCode = $this -> getVal('code');
    return $this -> mStdLnk.$lCode;
  }

  protected function getCpyLink() {
    $lCode = $this -> getVal('code');
    $lRet = $this -> mCpyLnk.$lCode;
    return $lRet;
  }

  protected function getDelLink() {
    $lCode = $this -> getVal('code');
    $lRet = $this -> mDelLnk.$lCode;
    return $lRet;
  }

  protected function getTdDel() {
    $lCode = $this -> getVal('code');
    if (!in_array($lCode, array('en', 'de'))) {
      $lRet = '<td class="'.$this -> mCls.($this -> mHighlight ? 'r': '').' nw w16 ac">';
      $lRet.= '<a class="nav" href="javascript:Flow.Std.cnfDel(\''.$this -> getDelLink().'\', \''.LAN.'\')">';
      $lRet.= img('img/ico/16/del.gif');
      $lRet.= '</a>';
      $lRet.= '</td>'.LF;
      return $lRet;
    } else {
      return $this -> td('');
    }
  }

}