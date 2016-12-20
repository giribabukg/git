<?php
class CInc_Ldt_Itm_List extends CHtm_List {
  
  public function __construct($aLid) {
    parent::__construct('ldt-itm');
    $this -> mLid = intval($aLid);
    $this -> setAtt('width', '100%');
    $this -> mStdLnk = 'index.php?act=ldt-itm.edt&amp;lid='.$this -> mLid.'&amp;id=';
    $this -> mDelLnk = 'index.php?act=ldt-itm.del&amp;lid='.$this -> mLid.'&amp;id=';
    $this -> mOrdLnk = 'index.php?act=ldt-itm.ord&amp;lid='.$this -> mLid.'&amp;fie=';
    
    $this -> mFie = CCor_Res::get('fie', 'pro,tpl,pac');
    $this -> getMaster();
    $this -> mTitle = 'Leadtime Items / '.$this -> mMas['name_'.LAN];
    
    $this -> addCtr();
    $lCol = $this -> mMas['fac_cols'];
    if (!empty($lCol)) {
      $lCol = explode(',', $lCol);
      $lCnt = 1;
      foreach ($lCol as $lVal) {
        if (isset($this -> mFie[$lVal])) {
          $lFie = $this -> mFie[$lVal];
          $lFie['alias'] = 'f'.$lCnt;
          $this -> addField($lFie);
          $lCnt++;
        }
      }
    }
    $this -> addColumn('days','Days');
    #$this -> addColumn('src', 'Source', TRUE, array('width' => '40'));
    #if ($this -> mCanDelete) {
      $this -> addDel();
    #}
    
    $this -> addBtn('Back', "go('index.php?act=ldt')", 'img/ico/16/back-hi.gif');
    $this -> addBtn('New Item', "go('index.php?act=ldt-itm.new&lid=".$this -> mLid."')", 'img/ico/16/plus.gif');
    
    $this -> getPrefs();
    
    $this -> mIte = new CCor_TblIte('al_ldt_itm');
    $this -> mIte -> addCnd('lid='.$this -> mLid);
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);
    $this -> mMaxLines = $this -> mIte -> getCount();
    $this -> addPanel('nav', $this -> getNavBar());
    
    #$this -> addPanel('vie', $this -> getViewMenu());
  }
  
  protected function getMaster() {
    $lSql = 'SELECT * FROM al_ldt_master WHERE id='.$this -> mLid;
    $lQry = new CCor_Qry($lSql);
    $this -> mMas = $lQry -> getDat();
  }
 
}