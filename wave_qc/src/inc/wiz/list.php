<?php
class CInc_Wiz_List extends CHtm_List {

  public function __construct() {
    parent::__construct('wiz');
    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('wiz.menu');

    $this -> addColumn('more','', false, array('width' => 16));
    $this -> addColumn('name_'.LAN, 'Name', TRUE);
    $this -> mDefaultOrder = 'name_'.LAN;

    if ($this -> mCanDelete) {
      $this -> addDel();
    }

    $this -> addBtn(lan('lib.neuwiz'), "go('index.php?act=wiz.new')", '<i class="ico-w16 ico-w16-plus"></i>');

    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_wiz_master');
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);

    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);
    $this -> mMaxLines = $this -> mIte -> getCount();
    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());

    $this -> getSub();
    $this -> mFie = CCor_Res::extract('id', 'name_'.LAN, 'fie');
  }

  protected function getSub() {
    $this -> mSub = array();
    $lSql = 'SELECT * FROM al_wiz_items WHERE mand='.MID.' ORDER BY hierarchy';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lWiz = intval($lRow['wiz_id']);
      $this -> mSub[$lWiz][] = $lRow;
    }
  }

  protected function getTdMore() {
    $lWiz = $this -> getInt('id');
    if (!empty($this -> mSub[$lWiz])) {
      $lRet = '';
      $this -> mMoreId = getNum('t');
      $lRet = '<a class="nav" onclick="Flow.Std.togTr(\''.$this -> mMoreId.'\')">';
      $lRet.= '...</a>';
      return $this -> tdClass($lRet, 'w16');
    } else {
      $this -> mMoreId = NULL;
      return $this -> td();
    }
  }

  protected function afterRow() {
    $lRet = parent::afterRow();
    $lRet.= '<tr id="'.$this -> mMoreId.'" class="togtr" style="display:none">'.LF;
    $lRet.= '<td class="td1 ca">&nbsp;</td>'.LF;
    $lRet.= '<td class="td1 p8"'.$this -> getColspan().'>';
    $lRet.= $this -> getWizardTable();
    $lRet.= '</td>'.LF;
    $lRet.= '</tr>'.LF;
    return $lRet;
  }

  protected function getWizardTable() {
    $lWiz = $this -> getInt('id');
    if (!isset($this -> mSub[$lWiz])) return '';
    $lArr = $this -> mSub[$lWiz];
    if (empty($lArr)) return '';
    $lRet = '<table cellpadding="2" cellspacing="0" class="tbl w100p">'.LF;
    $lRet.= '<tr><td class="th3 w16">&nbsp;</td><td class="th3 w200 nw">'.htm(lan('wiz.fie1')).'</td><td class="th3 nw">'.htm(lan('wiz.fie2')).'</td></tr>'.LF;
    $lCtr = 1;
    foreach ($lArr as $lRow) {
      $lLnk = '<a href="index.php?act=wiz-itm.edt&amp;id='.$lWiz.'&amp;sid='.$lRow['id'].'">';
      $lRet.= '<tr>';
      $lRet.= '<td class="td1 nw r">'.$lCtr.'.</td>';
      $lRet.= '<td class="td1 nw">'.$lLnk.$this -> fieldToStr($lRow['mainfield_id']).NB.'</a></td>';
      $lRet.= '<td class="td1 nw">'.$lLnk.$this -> fieldsToStr($lRow['secondary_fields']).NB.'</a></td>';
      $lRet.= '</tr>'.LF;
      $lCtr++;
    }
    $lRet.= '</table>'.LF;
    return $lRet;
  }

  protected function fieldToStr($aId) {
    if (empty($aId)) return '';
    $lId = intval($aId);
    if (isset($this -> mFie[$lId])) {
      return htm($this -> mFie[$lId]);
    }
    return '[unknown field]';
  }

  protected function fieldsToStr($aIds) {
    if (empty($aIds)) return '';
    $lRet = array();
    $lArr = explode(',', $aIds);
    foreach ($lArr as $lFid) {
      $lRet[] = $this -> fieldToStr($lFid);
    }
    return htm(implode(', ',$lRet));
  }

}