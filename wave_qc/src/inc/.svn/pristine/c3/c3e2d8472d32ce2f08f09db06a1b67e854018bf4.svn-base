<?php
class CInc_Xchange_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('xchange.menu');
    $this -> mMmKey = 'opt';

    $lpn = 'xchange';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      #$this -> setProtection('*', $lpn, rdNone);
    }
  }

  protected function actStd() {
    $lTab = new CXchange_Tabs();
    $lVie = new CXchange_List();
    $lRet = $lTab->getContent();
    $lRet.= $lVie->getContent();
    $this -> render($lRet);
  }

  protected function actEdt() {
    $lId = $this->getInt('id');
    $lView = new CXchange_View($lId);
    $this->render($lView);
  }

  protected function actDel() {
    $lId = $this->getInt('id');
    $lSql = 'UPDATE al_xchange_projects_'.MID.' SET x_status="deleted" WHERE id='.$lId;
    CCor_Qry::exec($lSql);
    $this->redirect();
  }

  protected function actUndel() {
    $lId = $this->getInt('id');
    $lSql = 'UPDATE al_xchange_projects_'.MID.' SET x_status="new" WHERE id='.$lId;
    CCor_Qry::exec($lSql);
    $this->redirect();
  }


  protected function actJobdel() {
    $lId = $this->getInt('id');
    $lSql = 'UPDATE al_xchange_jobs_'.MID.' SET x_status="deleted" WHERE id='.$lId;
    CCor_Qry::exec($lSql);
    $this -> redirect('index.php?act=xchange.job');
  }

  protected function actJobundel() {
    $lId = $this->getInt('id');
    $lSql = 'UPDATE al_xchange_jobs_'.MID.' SET x_status="new" WHERE id='.$lId;
    CCor_Qry::exec($lSql);
    $this -> redirect('index.php?act=xchange.job');
  }


  protected function actJob() {
    $lTab = new CXchange_Tabs('job');
    $lVie = new CXchange_Joblist();
    $lRet = $lTab->getContent();
    $lRet.= $lVie->getContent();
    $this -> render($lRet);
  }

  protected function actDeljob() {
    $lId = $this->getInt('id');
    $lSql = 'UPDATE al_xchange_jobs_'.MID.' SET x_status="deleted" WHERE id='.$lId;
    CCor_Qry::exec($lSql);
    $this->redirect();
  }

  protected function actUndeljob() {
    $lId = $this->getInt('id');
    $lSql = 'UPDATE al_xchange_jobs_'.MID.' SET x_status="new" WHERE id='.$lId;
    CCor_Qry::exec($lSql);
    $this->redirect();
  }

  protected function actJobfie() {
    $lParser = new CApi_Xchange_Xmlparser();
    $lRet = $lParser->getJobFieldSql();
    $this->render(nl2br($lRet));
  }

  protected function actJoblpp() {
    $this -> mReq -> expect('lpp');
    $lLpp = $this ->getInt('lpp');
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mPrf.'.job.lpp', $lLpp);
    $lUsr -> setPref($this -> mPrf.'.job.page', 0);
    $this -> redirect('index.php?act=xchange.job');
  }

  protected function actJobfil() {
    $this -> mReq -> expect('val');
    $lVal = $this -> mReq -> getVal('val');
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mPrf.'.job.fil', $lVal);
    $lUsr -> setPref($this -> mPrf.'.job.page', 0);
    $this->redirect('index.php?act=xchange.job');
  }
  
  protected function actJobser() {
    $this -> mReq -> expect('val');
    $lVal = $this -> mReq -> getVal('val');
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mPrf.'.job.ser', $lVal);
    $lUsr -> setPref($this -> mPrf.'.job.page', 0);
    $this->redirect('index.php?act=xchange.job');
  }
  
  protected function actJobclser() {
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mPrf.'.job.ser', $lVal);
    $lUsr -> setPref($this -> mPrf.'.job.page', 0);
    $this -> redirect();
  }

  protected function actJobpage() {
    $this -> mReq -> expect('page');
    $lPag = $this -> mReq -> getInt('page');
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mMod.'.job.page', $lPag);
    $this->redirect('index.php?act=xchange.job');
  }

  protected function actJobord() {
    $this -> mReq -> expect('fie');
    $lFie = $this -> mReq -> getVal('fie');
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mPrf.'.job.ord', $lFie);
    $this->redirect('index.php?act=xchange.job');
  }
  
  protected function actAssign() {
    $lXid = $this->getReq('xid');
    $lSrc = $this->getReq('src');
    $lJid = $this->getReq('jid');
    if (empty($lSrc)) { $lSrc = 'art'; }

    $lList = new CXchange_Selectjob($lXid);
    if (empty($lJid)) {
      $lDiv = '<div id="app-diff"></div>';
    } else {
      $lFac = new CJob_Fac($lSrc, $lJid);
      $lJob = $lFac->getDat();
      $lDiff = new CXchange_Diff($lXid, $lJob);
      $lDiv = '<div id="app-diff">'.$lDiff->getContent().'</div>';
    }
    $lRet = CHtm_wrap::wrap($lList, $lDiv);
    $this->render($lRet);
  }
  
  protected function actSelectjob() {
    $lXid = $this->getReq('xid');
    $lSrc = $this->getReq('src');
    $lJid = $this->getReq('jid');
  
    $lFac = new CJob_Fac($lSrc, $lJid);
    $lJob = $lFac->getDat();
  
    $lDiff = new CXchange_Diff($lXid, $lJob);
    echo $lDiff->getContent();
  }
    
  protected function actSassign() {
    $lOld = $this->getReq('old');
    $lVal = $this->getReq('val');
    $lFie = $this->getReq('fie');
    
    $lSrc = $this->getReq('src');
    $lJid = $this->getReq('jid');
    $lXid = $this->getInt('xid');
    
    $lFac = new CJob_Fac($lSrc, $lJid);
    $lMod = $lFac->getMod($lJid);
    
    foreach ($lFie as $lAlias => $lDummy) {
      $lMod->forceVal($lAlias, $lVal[$lAlias]);
      $lMod->setOld($lAlias, $lOld[$lAlias]);
    }
    $lMod->update();
    
    $lUpd['x_jobid'] = $lJid;
    $lUpd['x_src']   = 'art';
    $lUpd['x_assign_date'] = date('Y-m-d H:i:s');
    $lUpd['x_status'] = 'assigned';
    
    $lSql = 'UPDATE al_xchange_jobs_'.MID.' SET ';
    foreach ($lUpd as $lKey => $lVal) {
      $lSql.= $lKey.'='.esc($lVal).',';
    }
    $lSql = strip($lSql).' WHERE id='.$lXid;
    CCor_Qry::exec($lSql);

    #$this->redirect('index.php?act=xchange.assign&src='.$lSrc.'&jid='.$lJid.'&xid='.$lXid);
    $this->redirect('index.php?act=job-'.$lSrc.'.edt&jobid='.$lJid);
  }

}
