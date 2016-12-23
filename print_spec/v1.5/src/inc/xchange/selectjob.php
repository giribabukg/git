<?php
class CInc_Xchange_Selectjob extends CHtm_List {
  
  public function __construct($aXid) {
    parent::__construct('xchange');
    $this->mMid = MID;
    $this->mSearchField = CXchange_Jobfields::getSearchField();
    $this->mSearch = '';
    $this->mXid = intval($aXid);
    $this->loadXid($this->mXid);
    
    $this->mFie = CCor_Res::getByKey('alias', 'fie');
    $this->getIterator();
    $this->addMyColumns();
    $this->mTitle = 'Assign to Job';
    //var_dump($this->mIte);
    if (!empty($this->mSearchField)) {
      #$this -> addPanel('cap', htm(lan('lib.search')));
      $this -> addPanel('fil', $this -> getSearchForm());
    }
  }
  
  protected function togCls() {
    // do not toggle classes between td1 and td2 as user can hide some
  }
  
  public function loadXid($aXid) {
    $lSql = 'SELECT * FROM al_xchange_jobs_'.$this->mMid.' WHERE id='.$aXid;
    $lQry = new CCor_Qry($lSql);
    $this->mXJob = $lQry->getDat();
    if (!empty($this->mSearchField)) {
      $this->mSearch = $this->mXJob[$this->mSearchField];
    }
  }
  
  protected function getSearchForm() {
    $lRet = '';
    $lRet.= '<input type="text" id="app-seljob" value="'.htm($this->mSearch).'">';
    $lRet.= NB.btn(lan('lib.search'),'Flow.diff.search("#app-seljob")');
    $lRet.= NB.btn(lan('lib.show_all'),
        'jQuery("#app-seljob").val("");Flow.diff.search("#app-seljob")',
         '', 'button', array('id' => 'app-alljob', 'style' => 'display:none'));
    return $lRet;
  }
  
  protected function addMyColumns() {
    $this->addColumn('jobid', 'JobID', false);
    $lXfields = new CXchange_Jobfields();
    $lFields = $lXfields->getSelectFields();
    foreach ($lFields as $lAlias => $lCaption) {
      $this->addMyCol($lAlias);
    }
    $this->addColumn('_go', 'Open', false);
    
    $lFie = $this->mFie['src'];
    $this->mIte->addField('src', $lFie['native']);
  }
  
  protected function addMyCol($aAlias) {
    if (!isset($this->mFie[$aAlias])) {
      return;
    }
    $lFie = $this->mFie[$aAlias];

    $this->addColumn($aAlias, $lFie['name_en'], false);
    $this->mIte->addField($aAlias, $lFie['native']);
    
  }
  
  protected function getLink() {
    return 'javascript:Flow.diff.loadJob(\''.$this->getVal('src').'\',\''.$this->getVal('jobid').'\','.$this->mXid.')';
    return 'index.php?act=xchange.assign&xid='.$this->mXid.'&amp;src='.$this->getVal('src').'&amp;jid='.$this->getVal('jobid');
  }
  
  protected function getIterator() {
    $lWriter = CCor_Cfg::get('job.writer.default', 'alink');
    if ('portal' == $lWriter) {
      $lIte = new CCor_TblIte('all', $this -> mWithoutLimit);
      $lIte -> setOrder('jobnr', 'desc');
    } else {
      $this -> mIte = new CApi_Alink_Query_Getjoblist('', $this -> mWithoutLimit);
      $this -> mIte -> setOrder('jobnr', 'desc');
    }
  }
  
  protected function getCont() {
    $lRet = '<!-- start -->';
    $lRet.= parent::getCont();
    $lRet.= '<!-- end -->';
    return $lRet;
  }
  
  protected function getTdJobid() {
    $lJid = $this -> getVal('jobid');
    return $this -> tda(jid($lJid));
  }
  
  protected function getTd_Go() {
    $lJid = $this -> getVal('jobid');
    $lSrc = $this -> getVal('src');
    $lUrl = 'index.php?act=job-'.$lSrc.'.edt&jobid='.$lJid;
    $lRet = '<a href="'.htm($lUrl).'" target="_blank">Go</a>';
    return $this -> td($lRet);
  }

}