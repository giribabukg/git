<?php
class CInc_Prf_List extends CHtm_List {

  public function __construct() {
    parent::__construct('prf');
    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('prf.menu');
    $this -> mIdField = 'code';

    $lArr[0] = 'Global';
    $lArr[-1] = '['.lan('lib.mand.all').']';
    $lArr+= CCor_Res::extract('id', 'name_'.LAN, 'mand');
    $this -> mArrMand = $lArr;

    $this -> addCtr();
    $this -> addColumn('mand', lan('lib.mand'), TRUE, array('width' => '50'));
    $lFie = fie('grp', lan('lib.group'), 'tselect', array('dom' => 'prf'));
    $lFie['flags'] = ffSort;
    $this -> addField($lFie);
    $this -> addColumn('code',  'Code', TRUE, array('width' => '50'));
    $this -> addColumn('name_'.LAN, 'Name', TRUE, array('width' => '100%'));
    $this -> addDel();

    $this -> addBtn(lan('lib.neupreference'), "go('index.php?act=prf.new')", 'img/ico/16/plus.gif');

    $this -> getPrefs();
    $this -> mIte = new CCor_TblIte('al_sys_pref');
    $this -> mIte -> addCnd('(mand<1 OR mand='.intval(MID).')');

    if (!empty($this -> mFil)) {
      foreach ($this -> mFil as $lKey => $lVal) {
        if (($lKey == 'mand') and (empty($lVal))) continue;
        if (($lKey == 'mand') and ($lVal == -2)) $lVal = 0;
        if (($lKey == 'grp') and (empty($lVal))) continue;
        $this -> mIte -> addCnd('`'.$lKey.'`="'.addslashes($lVal).'"');
      }
    }
    if (!empty($this -> mSer['name'])) {
      $lVal = '"%'.addslashes($this -> mSer['name']).'%"';
      $lCnd = '(name_'.LAN.' LIKE '.$lVal.' OR ';
      $lCnd.= 'code LIKE '.$lVal.')';
      $this -> mIte -> addCnd($lCnd);
    }
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);
    $this -> mMaxLines = $this -> mIte -> getCount();

    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('cp1', NB.'Project');
    $this -> addPanel('mand', $this -> getFilterMenu());
    $this -> addPanel('sca', '| '.htmlan('lib.search'));
    $this -> addPanel('ser', $this -> getSearchForm());
  }

  protected function getFilterMenu() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="prf.fil" />'.LF;

    $lRet.= '<select name="val[mand]" size="1" onchange="this.form.submit()">'.LF;
    $lArr[0] = '[all]';
    $lArr[-2] = '[global]';
    $lArr[-1] = '['.lan('lib.mand.all').']';
    $lRes = CCor_Res::extract('id', 'name_'.LAN, 'mand');
    $lArr[MID] = $lRes[MID];

    $lFil = (isset($this -> mFil['mand'])) ? $this -> mFil['mand'] : '';
    foreach ($lArr as $lKey => $lVal) {
      $lRet.= '<option value="'.htm($lKey).'" ';
      if ($lKey == $lFil) {
        $lRet.= ' selected="selected"';
      }
      $lRet.= '>'.htm($lVal).'</option>'.LF;
    }
    $lRet.= '</select>'.LF;
    $lRet.= ' Group ';

    $lRet.= '<select name="val[grp]" size="1" onchange="this.form.submit()">'.LF;
    $lArr = array();
    $lArr[0] = '[all]';
    $lRes = CCor_Res::get('htb', 'prf');
    foreach ($lRes as $lKey => $lVal) {
      $lArr[$lKey] = $lVal;
    }
    $lFil = (isset($this -> mFil['grp'])) ? $this -> mFil['grp'] : '';
    foreach ($lArr as $lKey => $lVal) {
      $lRet.= '<option value="'.htm($lKey).'" ';
      if ($lKey == $lFil) {
        $lRet.= ' selected="selected"';
      }
      $lRet.= '>'.htm($lVal).'</option>'.LF;
    }
    $lRet.= '</select>'.LF;
    $lRet.= '</form>';
    return $lRet;
  }

  protected function getSearchForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="prf.ser" />'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
    $lVal = (isset($this -> mSer['name'])) ? htm($this -> mSer['name']) : '';
    $lRet.= '<td><input type="text" name="val[name]" class="inp" value="'.$lVal.'" /></td>'.LF;
    $lRet.= '<td>'.btn(lan('lib.search'),'','','submit').'</td>';
    if (!empty($this -> mSer)) {
      $lRet.= '<td>'.btn(lan('lib.show_all'),'go("index.php?act=prf.clser")').'</td>';
    }
    $lRet.= '</tr></table>';
    $lRet.= '</form>'.LF;

    return $lRet;
  }

  protected function getTdMand() {
    $lMand = $this -> getCurInt();
    if (isset($this -> mArrMand[$lMand])) {
      $lMand = $this -> mArrMand[$lMand];
    }
    return $this -> tda($lMand);
  }

  protected function getLink() {
    if (empty($this -> mStdLnk)) {
      return '';
    } else {
      $lId = $this -> getVal('code');
      $lMid = $this -> getVal('mand');
      return $this -> mStdLnk.$lId.'&mand='.$lMid;
    }
  }

}