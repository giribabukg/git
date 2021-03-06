<?php
/**
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 6390 $
 * @date $Date: 2014-11-07 15:30:02 +0100 (Fri, 07 Nov 2014) $
 * @author $Author: jwetherill $
 */
class CInc_Job_Art_Sub_List extends CHtm_List {

  public function __construct($aJobId) {
    parent::__construct('job-art-sub');
    $this -> mJobId = $aJobId;
    $this -> mShowSubHdr = FALSE;
    $this -> mStdLnk = 'index.php?act='.$this -> mMod.'.edt&amp;jobid='.$this -> mJobId.'&amp;id=';
    $this -> mOrdLnk = 'index.php?act='.$this -> mMod.'.ord&amp;jobid='.$this -> mJobId.'&amp;fie=';
    $this -> setAtt('width', '100%');
    $this -> mCapCls = 'th1';
    $this -> mTitle = lan('job-sub.menu');

    $this -> mFie = CCor_Res::getByKey('id', 'fie');

    $this -> addColumns();
    $this -> addColumn('job_rep', lan('job-rep.menu'),FALSE, array('width' => '70'));
    $this -> addColumn('job_sec', lan('job-sec.menu'),FALSE, array('width' => '70'));
    $this -> addColumn('job_adm', lan('job-adm.menu'),FALSE, array('width' => '70'));
    $this -> addColumn('job_mis', lan('job-mis.menu'),FALSE, array('width' => '70'));
    $this -> addColumn('job_com', lan('job-com.menu'),FALSE, array('width' => '70'));
    $this -> addColumn('job_tra', lan('job-tra.menu'),FALSE, array('width' => '70'));
    $this -> getPrefs();

    $this -> getIterator();
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);
    $this -> setGroup($this -> mOrd);
    $this -> mMaxLines = $this -> mIte -> getCount();
    $this -> preLoad();

    $this -> getWizards();

  }

  protected function getWizards() {
    $this -> mWiz = array();
    $lSql = 'SELECT * FROM al_wiz_master ORDER BY name_'.LAN;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> mWiz[$lRow['id']] = $lRow['name_'.LAN];
    }
  }

  protected function getSubMenu() {
    $lMen = new CHtm_Menu('New Subjob');
    $lMen -> addTh2(lan('lib.tpl'));
    foreach ($this -> mWiz as $lKey => $lVal) {
      $lMen -> addItem('index.php?act=job-pro-sub.new&amp;jobid='.$this -> mJobId.'&amp;wiz='.$lKey, $lVal);
    }
    return $lMen -> getContent();
  }

  protected function getWizMenu() {
    $lMen = new CHtm_Menu(lan('wiz.menu'));
    $lMen -> addTh2(lan('lib.tpl'));
    foreach ($this -> mWiz as $lKey => $lVal) {
      $lMen -> addItem('index.php?act=job-pro-sub.wiz&amp;jobid='.$this -> mJobId.'&amp;wiz='.$lKey, $lVal);
    }
    return $lMen -> getContent();
  }

  protected function getIterator() {
    $this -> mIte = new CCor_TblIte('al_job_sub_'.intval(MID));
    $this -> mIte -> addCnd('jobid_art="'.addslashes($this -> mJobId).'"');
  }

  protected function preLoad() {
    $lArt = array();
    $lRep = array();
    $lSec = array();
    $lAdm = array();
    $lMis = array();
    $lCom = array();
    $lTra = array();

    $this -> mArt = array();
    $this -> mRep = array();
    $this -> mSec = array();
    $this -> mAdm = array();
    $this -> mMis = array();
    $this -> mCom = array();
    $this -> mTra = array();

    $this -> mIte = $this -> mIte -> getArray('id');
    foreach ($this -> mIte as $lRow) {
      if (!empty($lRow['jobid_rep'])) {
        $lRep[$lRow['jobid_rep']] = TRUE;
      }
      if (!empty($lRow['jobid_sec'])) {
        $lSec[$lRow['jobid_sec']] = TRUE;
      }
      if (!empty($lRow['jobid_adm'])) {
        $lAdm[$lRow['jobid_adm']] = TRUE;
      }
      if (!empty($lRow['jobid_mis'])) {
        $lMis[$lRow['jobid_mis']] = TRUE;
      }
      if (!empty($lRow['jobid_com'])) {
        $lAdm[$lRow['jobid_com']] = TRUE;
      }
      if (!empty($lRow['jobid_tra'])) {
        $lMis[$lRow['jobid_tra']] = TRUE;
      }
    }

    $lQry = new CCor_Qry();

    if (!empty($lRep)) {
      $lQry -> query('SELECT id,webstatus FROM al_job_rep WHERE id IN ('.implode(',', array_keys($lRep)).')');
      foreach ($lQry as $lRow) {
        $this -> mRep[$lRow['id']] = $lRow;
      }
    }

    if (!empty($lSec)) {
      $lQry -> query('SELECT id,webstatus FROM al_job_sec WHERE id IN ('.implode(',', array_keys($lSec)).')');
      foreach ($lQry as $lRow) {
        $this -> mSec[$lRow['id']] = $lRow;
      }
    }

    if (!empty($lAdm)) {
      $lQry -> query('SELECT id,webstatus FROM al_job_adm WHERE id IN ('.implode(',', array_keys($lAdm)).')');
      foreach ($lQry as $lRow) {
        $this -> mAdm[$lRow['id']] = $lRow;
      }
    }

    if (!empty($lMis)) {
      $lQry -> query('SELECT id,webstatus FROM al_job_mis WHERE id IN ('.implode(',', array_keys($lMis)).')');
      foreach ($lQry as $lRow) {
        $this -> mMis[$lRow['id']] = $lRow;
      }
    }

    if (!empty($lCom)) {
      $lQry -> query('SELECT jobid,webstatus FROM al_job_pdb_'.intval(MID).' WHERE src="com" AND id IN ('.implode(',', array_keys($lCom)).')');
      foreach ($lQry as $lRow) {
        $this -> mCom[$lRow['id']] = $lRow;
      }
    }

    if (!empty($lTra)) {
      $lQry -> query('SELECT jobid,webstatus FROM al_job_pdb_'.intval(MID).' WHERE src="tra" AND id IN ('.implode(',', array_keys($lTra)).')');
      foreach ($lQry as $lRow) {
        $this -> mTra[$lRow['id']] = $lRow;
      }
    }
  }

  protected function addColumns() {
    $lSql = 'SELECT DISTINCT(wiz_id) FROM al_job_sub_'.intval(MID).' WHERE jobid_art='.esc($this -> mJobId);
    $lQry = new CCor_Qry($lSql);

    $lArr = array();
    foreach ($lQry as $lRow) {
      $lArr[] = $lRow['wiz_id'];
    }

    if (empty($lArr)) return;
    $lFie = array();
    $lSql = 'SELECT * FROM al_wiz_items WHERE mand='.intval(MID).' AND wiz_id IN ('.implode(',', $lArr).')';
    $lQry -> query($lSql);
    foreach ($lQry as $lRow) {
      $lFie[$lRow['mainfield_id']] = TRUE;
      $lTmp = trim($lRow['secondary_fields']);
      if (!empty($lTmp)) {
        $lTmp = explode(',', $lTmp);
        foreach ($lTmp as $lVal) {
         $lFie[$lVal] = TRUE;
        }
      }
    }

    $lCol = array_keys($lFie);
    foreach ($lCol as $lFid) {
      if (isset($this -> mFie[$lFid])) {
        $lDef = $this -> mFie[$lFid];
        $this -> addField($lDef);
      }
    }
  }

  protected function & getViewMenuObject() {
    $lUsr = CCor_Usr::getInstance();

    $lMen = new CHtm_Menu('Options');

    $lMen -> addTh2('View Options');
    $lMen -> addItem('index.php?act='.$this -> mMod.'.fpr', lan('lib.opt.fpr'), 'ico/16/save.gif');
    $lMen -> addItem('index.php?act='.$this -> mMod.'.spr', lan('lib.opt.spr'), 'ico/16/save.gif');


    $lMen -> addTh2('Lines per page');
    $lOk = 'ico/16/ok.gif';
    $lArr = array(25,50,100,200);
    foreach ($lArr as $lLpp) {
      $lImg = ($lLpp == $this -> mLpp) ? $lOk : 'd.gif';
      $lMen -> addItem($this -> mLppLnk.$lLpp, $lLpp.' Lines', $lImg);
    }

    $lMen -> addTh2('Saved views');
    $lSql = 'SELECT id,name FROM al_usr_view WHERE ref="'.$this -> mMod.'" ';
    $lSql.= 'AND src="usr" AND src_id='.$lUsr -> getId().' AND mand='.MID.' ';
    $lSql.= 'ORDER BY name';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lMen -> addItem('index.php?act='.$this -> mMod.'.selview&amp;id='.$lRow['id'], $lRow['name'], 'ico/16/new-hi.gif');
    }
    $lMen -> addItem('index.php?act='.$this -> mMod.'.myview', 'Save current view as...', 'ico/16/save.gif');
    /*
    if ($lUsr -> isMemberOf(1)) {
      $lMen -> addItem('index.php?act=mba.sview', 'Save as Standard', 'ico/16/save-std.gif');
    }
    */
    return $lMen;
  }

  protected function getGroupHeader() {
    $lRet = '';
    if (!empty($this -> mGrp)) {
      $lNew = $this -> getVal($this -> mGrp);
      if ($lNew !== $this -> mOldGrp) {
        $lRet = TR;
        $lRet.= '<td class="tg1 ac">';
        $lId = getNum('c');
        $lRet.= '<input type="checkbox" class="grp" id="'.$lId.'" onclick="subAll(\''.$lId.'\')" />';
        $lRet.= '</td>';
        $lRet.= '<td class="tg1" colspan="'.($this -> mColCnt -1).'">';
        $lRet.= htm($lNew).NB;
        $lRet.= '</td>';
        $lRet.= _TR;
        $this -> mOldGrp = $lNew;
        $this -> mCls = 'td1';
      }
    }
    return $lRet;
  }

  protected function getTdSel() {
    $lRet = '<input type="checkbox" ';
    $lRet.= ' />';
    return $this ->tdc($lRet);
  }

  protected function getTdJob_Rep() {
    $lId = $this -> getVal('jobid_rep');
    $lSid = $this -> getInt('id');
    if (isset($this -> mRep[$lId])) {
      $lRow = $this -> mRep[$lId];
      $lSta = $lRow['webstatus'] / 10;
      $lRet = '<a href="index.php?act=job-rep.edt&amp;jobid='.$lId.'" class="nav">';
	  $lPath = CApp_Crpimage::getSrcPath('rep', 'img/crp/'.$lSta.'b.gif');
      $lRet.= img($lPath).NB.jid($lId, TRUE);
      $lRet.= '</a>';
    } else {
      $lRet = '<a href="index.php?act=job-rep.sub&amp;pid='.$this -> mJobId.'&amp;sid='.$lSid.'" class="nav">';
      $lRet.= 'Create';
      $lRet.= '</a>';
    }
    return $this -> td($lRet);
  }

  protected function getTdJob_Sec() {
    $lId = $this -> getVal('jobid_sec');
    $lSid = $this -> getInt('id');
    if (isset($this -> mSec[$lId])) {
      $lRow = $this -> mSec[$lId];
      $lSta = $lRow['webstatus'] / 10;
      $lRet = '<a href="index.php?act=job-sec.edt&amp;jobid='.$lId.'" class="nav">';
	  $lPath = CApp_Crpimage::getSrcPath('sec', 'img/crp/'.$lSta.'b.gif');
      $lRet.= img($lPath).NB.jid($lId, TRUE);
      $lRet.= '</a>';
    } else {
      $lRet = '<a href="index.php?act=job-sec.sub&amp;pid='.$this -> mJobId.'&amp;sid='.$lSid.'" class="nav">';
      $lRet.= 'Create';
      $lRet.= '</a>';
    }
    return $this -> td($lRet);
  }

  protected function getTdJob_Adm() {
    $lId = $this -> getVal('jobid_adm');
    $lSid = $this -> getInt('id');
    if (isset($this -> mAdm[$lId])) {
      $lRow = $this -> mAdm[$lId];
      $lSta = $lRow['webstatus'] / 10;
      $lRet = '<a href="index.php?act=job-adm.edt&amp;jobid='.$lId.'" class="nav">';
	  $lPath = CApp_Crpimage::getSrcPath('adm', 'img/crp/'.$lSta.'b.gif');
      $lRet.= img($lPath).NB.jid($lId, TRUE);
      $lRet.= '</a>';
    } else {
      $lRet = '<a href="index.php?act=job-adm.sub&amp;pid='.$this -> mJobId.'&amp;sid='.$lSid.'" class="nav">';
      $lRet.= 'Create';
      $lRet.= '</a>';
    }
    return $this -> td($lRet);
  }

  protected function getTdJob_Mis() {
    $lId = $this -> getVal('jobid_mis');
    $lSid = $this -> getInt('id');
    if (isset($this -> mMis[$lId])) {
      $lRow = $this -> mMis[$lId];
      $lSta = $lRow['webstatus'] / 10;
      $lRet = '<a href="index.php?act=job-mis.edt&amp;jobid='.$lId.'" class="nav">';
	  $lPath = CApp_Crpimage::getSrcPath('mis', 'img/crp/'.$lSta.'b.gif');
      $lRet.= img($lPath).NB.jid($lId, TRUE);
      $lRet.= '</a>';
    } else {
      $lRet = '<a href="index.php?act=job-mis.sub&amp;pid='.$this -> mJobId.'&amp;sid='.$lSid.'" class="nav">';
      $lRet.= 'Create';
      $lRet.= '</a>';
    }
    return $this -> td($lRet);
  }

  protected function getTdJob_Com() {
    $lId = $this -> getVal('jobid_com');
    $lSid = $this -> getInt('id');
    if (isset($this -> mCom[$lId])) {
      $lRow = $this -> mCom[$lId];
      $lSta = $lRow['webstatus'] / 10;
      $lRet = '<a href="index.php?act=job-com.edt&amp;jobid='.$lId.'" class="nav">';
	  $lPath = CApp_Crpimage::getSrcPath('com', 'img/crp/'.$lSta.'b.gif');
      $lRet.= img($lPath).NB.jid($lId, TRUE);
      $lRet.= '</a>';
    } else {
      $lRet = '<a href="index.php?act=job-com.sub&amp;pid='.$this -> mJobId.'&amp;sid='.$lSid.'" class="nav">';
      $lRet.= 'Create';
      $lRet.= '</a>';
    }
    return $this -> td($lRet);
  }

  protected function getTdJob_Tra() {
    $lId = $this -> getVal('jobid_tra');
    $lSid = $this -> getInt('id');
    if (isset($this -> mTra[$lId])) {
      $lRow = $this -> mTra[$lId];
      $lSta = $lRow['webstatus'] / 10;
      $lRet = '<a href="index.php?act=job-tra.edt&amp;jobid='.$lId.'" class="nav">';
	  $lPath = CApp_Crpimage::getSrcPath('tra', 'img/crp/'.$lSta.'b.gif');
      $lRet.= img($lPath).NB.jid($lId, TRUE);
      $lRet.= '</a>';
    } else {
      $lRet = '<a href="index.php?act=job-tra.sub&amp;pid='.$this -> mJobId.'&amp;sid='.$lSid.'" class="nav">';
      $lRet.= 'Create';
      $lRet.= '</a>';
    }
    return $this -> td($lRet);
  }

}