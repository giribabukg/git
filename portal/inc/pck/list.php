<?php
class CInc_Pck_List extends CHtm_List {

  public function __construct() {
    parent::__construct('pck');
    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('pck.menu');

    $this -> addCtr();
    $this -> addColumn('domain',      lan('lib.code'), TRUE, array('width' => '50'));
    $this -> addColumn('description_'.LAN, lan('lib.description'), TRUE, array('width' => '100%'));
    $this -> addColumn('columns',     lan('pck.columns'), TRUE, array('width' => '50'));
    $this -> addColumn('items',       lan('lib.items'), TRUE, array('width' => '50'));
    $this -> mDefaultOrder = 'description_'.LAN;

    if ($this -> mCanDelete) {
      $this -> addDel();
    }

    $this -> addBtn(lan('pck.new'), "go('index.php?act=pck.new')", '<i class="ico-w16 ico-w16-plus"></i>');

    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_pck_master');
    $this -> mIte -> addCnd('mand IN(0,'.MID.')');
    $this -> mIte -> addCnd('del="N"');
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);

    if (!empty($this -> mSer)) {
      if (!empty($this -> mSer['name'])) {
        $lVal = '"%'.addslashes($this -> mSer['name']).'%"';
        $lCnd = '(description_'.LAN.' LIKE '.$lVal.' OR ';
        $lCnd.= 'domain LIKE '.$lVal.')';
        $this -> mIte -> addCnd($lCnd);
      }
    }
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);
    $this -> mMaxLines = $this -> mIte -> getCount();
    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu()); //Optionen
    $this -> addPanel('sca', '| '.htmlan('lib.search'));
    $this -> addPanel('ser', $this -> getSearchForm());
  }

  protected function getSearchForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="pck.ser" />'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
    $lVal = (isset($this -> mSer['name'])) ? htm($this -> mSer['name']) : '';
    $lRet.= '<td><input type="text" name="val[name]" class="inp" value="'.$lVal.'" /></td>'.LF;
    $lRet.= '<td>'.btn(lan('lib.search'),'','','submit').'</td>';
    if (!empty($this -> mSer)) {
      $lRet.= '<td>'.btn(lan('lib.show_all'),'go("index.php?act=pck.clser")').'</td>';
    }
    $lRet.= '</tr></table>';
    $lRet.= '</form>'.LF;

    return $lRet;
  }

  protected function getTdItems() {
    $lCod = $this -> getVal('domain');
    $lRet = '<a href="index.php?act=pck-itm&amp;xx=1&amp;dom='.$lCod.'">';
    $lCnt = CCor_Qry::getStr('SELECT COUNT(*) FROM al_pck_items WHERE mand IN(0,'.MID.') AND domain="'.$lCod.'"');
    $lRet.= $lCnt;
    $lRet.= '</a>';
    return $this -> td($lRet);
  }

  protected function getTdColumns() {
    $lCod = $this -> getVal('domain');
    $lRet = '<a href="index.php?act=pck-col&amp;dom='.$lCod.'">';
    $lRet.= lan('pck-col.edt');
    $lRet.= '</a>';
    return $this -> td($lRet);
  }

  protected function getLink() {
    if (empty($this -> mStdLnk)) {
      return '';
    } else {
      $lCod = $this -> getVal('domain');
      $lId = $this -> getVal($this -> mIdField);
      return $this -> mStdLnk.$lId.'&amp;dom='.$lCod;
    }
  }

}