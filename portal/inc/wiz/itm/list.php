<?php
class CInc_Wiz_Itm_List extends CHtm_List {

  public function __construct($aWizId) {
    parent::__construct('wiz-itm');
    $this -> mWiz = intval($aWizId);
    $this -> setAtt('class', 'tbl w800');
    $this -> mTitle = lan('wiz-itm.menu');

    $this -> mStdLnk = 'index.php?act=wiz-itm.edt&amp;id='.$this -> mWiz.'&amp;sid=';
    $this -> mDelLnk = 'index.php?act=wiz-itm.del&amp;id='.$this -> mWiz.'&amp;sid=';

    $this -> addCtr();
    $this -> addColumn('mainfield_id', lan('wiz.fie1'));
    $this -> addColumn('secondary_fields', lan('wiz.fie2'));

    if ($this -> mCanDelete) {
      $this -> addDel();
    }

    $this -> addBtn(lan('lib.step'), "go('index.php?act=wiz-itm.new&id=".$this -> mWiz."')", '<i class="ico-w16 ico-w16-plus"></i>');

    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_wiz_items');
    $this -> mIte -> addCnd('mand='.intval(MID));
    $this -> mIte -> addCnd('wiz_id='.$this -> mWiz);
    $this -> mIte -> setOrder('hierarchy');

    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);
    $this -> mMaxLines = $this -> mIte -> getCount();

    $this -> mFie = CCor_Res::extract('id', 'name_'.LAN, 'fie');
  }

  protected function getTdMainfield_id() {
    $lFid = $this -> getCurInt();
    return $this -> tda($this -> fieldToStr($lFid));
  }

  protected function getTdSecondary_fields() {
    $lFid = $this -> getCurVal();
    return $this -> tda($this -> fieldsToStr($lFid));
  }

  protected function fieldToStr($aId) {
    if (empty($aId)) return '';
    $lId = intval($aId);
    if (isset($this -> mFie[$lId])) {
      return $this -> mFie[$lId];
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
    return implode(', ',$lRet);
  }

}