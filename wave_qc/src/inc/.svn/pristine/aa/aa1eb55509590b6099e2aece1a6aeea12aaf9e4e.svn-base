<?php
class CInc_Ldt_List extends CHtm_List {
  
  public function __construct() {
    parent::__construct('ldt');
    $this -> setAtt('width', '100%');
    $this -> mTitle = 'Leadtimes';
    
    $lArr = array();
    $lArr['pro'] = 'Project';
    $lArr['pde'] = 'Print Development';
    $lArr['mba'] = 'MBA/MAW';
    $lArr['tpl'] = 'Variants';
    $lArr['pac'] = 'Packaging';
    $this -> mSrcArr = $lArr;
    
    $this -> mFie = CCor_Res::get('fie', 'pro,tpl,pac');
    
    $this -> addCtr();
    #$this -> addColumn('src', 'Source', TRUE, array('width' => '40'));
    $this -> addColumn('name_'.LAN, 'Name', TRUE);
    $this -> addColumn('std_val', 'Default', TRUE, array('width' => '20'));
    $this -> addColumn('fac_cols', 'Factors', TRUE);
    $this -> addColumn('items', 'Items', TRUE);
    $this -> mDefaultOrder = 'id';
    
    if ($this -> mCanDelete) {
      $this -> addDel();
    }
    
    $this -> addBtn('New Leadtime List', "go('index.php?act=ldt.new')", 'img/ico/16/plus.gif');
    
    $this -> getPrefs();
    
    $this -> mIte = new CCor_TblIte('al_ldt_master');
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);
    $this -> mMaxLines = $this -> mIte -> getCount();
    $this -> addPanel('nav', $this -> getNavBar());
    $this -> getItemCount();
    
    #$this -> addPanel('vie', $this -> getViewMenu());
  }
  
  protected function getItemCount() {
    $this -> mItmCnt = array();
    
    $lSql = 'SELECT lid,COUNT(*) AS cnt FROM al_ldt_itm GROUP BY lid';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> mItmCnt[$lRow['lid']] = $lRow['cnt'];
    }
  }
  
  protected function getTdSrc() {
    $lSrc = $this -> getCurVal();
    $lRet = (isset($this -> mSrcArr[$lSrc])) ? $this -> mSrcArr[$lSrc] : '???';
    return $this -> tda($lRet);
  }
  
  protected function getTdStd_val() {
    $lVal = $this -> getCurVal();
    #$lWk = number_format($lVal/7,1);
    #$lRet = $lWk.' weeks ('.$lVal.' days)';
    $lRet = $lVal.' days';
    return $this -> tda($lRet);
  }
  
  protected function getTdFac_cols() {
    $lRet = '';
    $lVal = $this -> getCurVal();
    $lFie = array();
    if (!empty($lVal)) {
      $lArr = explode(',', $lVal);
      foreach ($lArr as $lFid) {
        if (isset($this -> mFie[$lFid])) {
          $lDef = $this -> mFie[$lFid];
          $lFie[] = $lDef['name_'.LAN];
        }
      }
      if (!empty($lFie)) {
        $lRet.= implode(', ', $lFie);
      }
    }
    $lRet = '<a href="index.php?act=ldt.fac&amp;id='.$this -> getInt('id').'">'.htm($lRet).NB.'</a>';
    return $this -> td($lRet);
  }
  
  protected function getTdItems() {
    $lId = $this -> getInt('id');
    $lCnt = (isset($this -> mItmCnt[$lId])) ? $this -> mItmCnt[$lId] : 0;
    $lRet = '<a href="index.php?act=ldt-itm&amp;lid='.$lId.'">'.$lCnt.' Items</a>';
    return $this -> td($lRet);
  }
  
}