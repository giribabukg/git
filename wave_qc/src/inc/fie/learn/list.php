<?php
class CInc_Fie_Learn_List extends CHtm_List {

  public function __construct() {
    parent::__construct('fie-learn');

    $this -> setAtt('width', '100%');
    $this -> mTblId = getNum('t');
    $this -> setAtt('id', $this -> mTblId);
    $this -> mTitle = lan('fie-learn.menu');

    $this -> addColumn('ctr');
    $this -> addColumn('sel',     '',                   FALSE, array('width' => '16'));
    $this -> addColumn('alias',   lan('lib.field'),     TRUE);
    $this -> addColumn('val',     lan('lib.value'),     TRUE, array('width' => '100%'));
    $this -> addColumn('stamp',   lan('lib.lastused'),  TRUE);
    $this -> addDel();

    $this -> getPrefs();
    $this -> mDef = CCor_Res::extract('alias', 'name_'.LAN, 'fie');

    $this -> mIte = new CCor_TblIte('al_fie_choice');
    $this -> mIte -> addCnd('mand='.MID);
    if (!empty($this -> mFil)) {
      foreach ($this -> mFil as $lKey => $lVal) {
        if (empty($lVal)) continue;
        $this -> mIte -> addCnd('`'.$lKey.'`="'.addslashes($lVal).'"');
      }
    }

    if (!empty($this -> mSer)) {
      if (!empty($this -> mSer['name'])) {
        $lVal = '"%'.addslashes($this -> mSer['name']).'%"';
        $lCnd = 'val LIKE '.$lVal.' ';
        $this -> mIte -> addCnd($lCnd);
      }
    }

    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);
    $this -> mMaxLines = $this -> mIte -> getCount();

    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());
    $this -> addPanel('cap', '| '.htmlan('lib.field'));
    $this -> addPanel('fil', $this -> getFilterMenu());
    $this -> addPanel('sca', '| '.htmlan('lib.search'));
    $this -> addPanel('ser', $this -> getSearchForm());
  }

  protected function getFilterMenu() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="fie-learn.fil" />'.LF;
    $lRet.= '<select name="val[alias]" size="1" onchange="this.form.submit()">'.LF;

    $lArr = array('' => '[all]');
    $lSql = 'SELECT DISTINCT(f1.learn) AS alias,f2.name_'.LAN.' FROM al_fie f1, al_fie f2 ';
    $lSql.= 'WHERE f1.mand='.MID.' AND f2.mand='.MID.' AND f1.learn=f2.alias ORDER BY f2.name_'.LAN;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lArr[$lRow['alias']] = $lRow['name_'.LAN];
    }

    $lFil = (isset($this -> mFil['alias'])) ? $this -> mFil['alias'] : '';
    foreach ($lArr as $lKey => $lVal) {
      $lRet.= '<option value="'.$lKey.'" ';
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
    $lRet.= '<input type="hidden" name="act" value="fie-learn.ser" />'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
    $lVal = (isset($this -> mSer['name'])) ? htm($this -> mSer['name']) : '';
    $lRet.= '<td><input type="text" name="val[name]" class="inp" value="'.$lVal.'" /></td>'.LF;
    $lRet.= '<td>'.btn(lan('lib.search'),'','','submit').'</td>';
    if (!empty($this -> mSer)) {
      $lRet.= '<td>'.btn(lan('lib.all'),'go("index.php?act=fie-learn.clser")').'</td>';
    }
    $lRet.= '</tr></table>';
    $lRet.= '</form>'.LF;

    return $lRet;
  }

  protected function getTdSel() {
    $lId = $this -> getInt('id');
    $lRet = '<input type="checkbox" value="'.$lId.'" />';
    return $this -> tdc($lRet);
  }

  protected function getTdAlias() {
    $lAli = $this -> getCurVal();
    if (isset($this -> mDef[$lAli])) {
      $lAli = $this -> mDef[$lAli];
    }
    return $this -> tda(htm($lAli));
  }

  protected function getTdStamp() {
    $lVal = $this -> getCurVal();
    $lDat = new CCor_Date($lVal);
    return $this -> tda($lDat -> getFmt(lan('lib.date.week')));
  }

  protected function getBody() {
    $lRet = $this -> getRows();

    $lJs = 'function selFieAll(aEl) {'.LF;
    $lJs.= 'var lVal = aEl.checked;';
    $lJs.= '$("'.$this -> mTblId.'").getElementsBySelector("[type=\'checkbox\']").each(function(aNod) {aNod.checked=lVal;});';
    $lJs.='}'.LF;
    $lJs.= 'function delFieAll(aEl) {'.LF;
    $lJs.= 'var lSel = new Array();';
    $lJs.= '$("'.$this -> mTblId.'").getElementsBySelector("[type=\'checkbox\']").each(function(aNod) {if ((aNod.checked) && (aNod.value!="on")) lSel.push(aNod.value)});'.LF;
    $lJs.= 'Flow.Std.cnfDel("index.php?act=fie-learn.delselected&ids=" + lSel.join(","), "'.LAN.'");';
    $lJs.='}'.LF;
    $lPag = CHtm_Page::getInstance();
    $lPag -> addJs($lJs);

    $lRet.= '<tr><td class="th2">&nbsp;</td>';
    $lRet.= '<td class="th2"><input type="checkbox" onclick="selFieAll(this)" /></td>';
    $lRet.= '<td class="th2" colspan="4">';
    $lRet.= '<a href="javascript:delFieAll(this)">'.lan('lib.del.sel').'</a>';
    $lRet.= '</td>';
    $lRet.= '</tr>';
    return $lRet;
  }

}