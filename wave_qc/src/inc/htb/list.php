<?php
class CInc_Htb_List extends CHtm_List {

  public function __construct() {
    parent::__construct('htb');
    $this -> setAtt('class', 'tbl w800');
    $this -> mTitle = lan('htb.menu');

    $this -> addCtr();
    $this -> addColumn('domain',      lan('lib.code'), TRUE, array('width' => '16'));
    $this -> addColumn('description', lan('lib.description'), TRUE, array('width' => '100%'));
    $this -> addColumn('items',       lan('lib.items'), TRUE, array('width' => '16'));
    $this -> mDefaultOrder = 'description';

    if ($this -> mCanDelete) {
      $this -> addDel();
    }

    $this -> addBtn(lan('htb.new'), "go('index.php?act=htb.new')", '<i class="ico-w16 ico-w16-plus"></i>');

    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_htb_master');
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);

    if (!empty($this -> mSer)) {
      if (!empty($this -> mSer['name'])) {
        $lVal = '"%'.addslashes($this -> mSer['name']).'%"';
        $lCnd = '(description_'.LAN.' LIKE '.$lVal.' OR ';
        $lCnd.= 'description LIKE '.$lVal.' OR ';
        $lCnd.= 'domain LIKE '.$lVal.')';
        $this -> mIte -> addCnd($lCnd);
      }
    }

    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);
    $this -> mMaxLines = $this -> mIte -> getCount();
    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());
    $this -> addPanel('sca', '| '.htmlan('lib.search'));
    $this -> addPanel('ser', $this -> getSearchForm());
  }

  protected function getSearchForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="htb.ser" />'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
    $lVal = (isset($this -> mSer['name'])) ? htm($this -> mSer['name']) : '';
    $lRet.= '<td><input type="text" name="val[name]" class="inp" value="'.$lVal.'" /></td>'.LF;
    $lRet.= '<td>'.btn(lan('lib.search'),'','','submit').'</td>';
    if (!empty($this -> mSer)) {
      $lRet.= '<td>'.btn(lan('lib.show_all'),'go("index.php?act=htb.clser")').'</td>';
    }
    $lRet.= '</tr></table>';
    $lRet.= '</form>'.LF;

    return $lRet;
  }

  protected function getTdItems() {
    $lCod = $this -> getVal('domain');
    $lRet = '<a href="index.php?act=htb-itm&amp;dom='.$lCod.'">';
    $lCnt = CCor_Qry::getStr('SELECT COUNT(*) FROM al_htb_itm WHERE mand IN(0,'.MID.') AND domain="'.$lCod.'"');
    $lRet.= $lCnt;
    $lRet.= '</a>';
    return $this -> td($lRet);
  }


}