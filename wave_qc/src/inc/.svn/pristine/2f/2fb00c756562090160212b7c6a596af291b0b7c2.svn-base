<?php
class CInc_Arc_Pro_Sub_List extends CHtm_List {

  public function __construct($aJobId) {
    parent::__construct('arc-pro-sub');
    $this -> mJobId = intval($aJobId);

    $this -> mShowSubHdr = FALSE;
    $this -> mStdLnk = 'index.php?act='.$this -> mMod.'.edt&amp;jobid='.$this -> mJobId.'&amp;id=';
    $this -> mOrdLnk = 'index.php?act='.$this -> mMod.'.ord&amp;jobid='.$this -> mJobId.'&amp;fie=';
    $this -> mDelLnk = 'index.php?act='.$this -> mMod.'.del&amp;jobid='.$this -> mJobId.'&amp;id=';
    $this -> setAtt('width', '100%');
    $this -> mCapCls = 'th1';
    $this -> mTitle = lan('job-pro-sub.menu');
    $this -> mImg = 'img/ico/40/'.LAN.'/job-pro.gif';
    
    $this -> mFie = CCor_Res::getByKey('id', 'fie');

    $this -> addColumns();
    $this -> addColumn('job_art', lan('job-art.item'),FALSE, array('width' => '70'));
    $this -> addColumn('job_rep', lan('job-rep.item'),FALSE, array('width' => '70'));
    $this -> addColumn('job_sec', lan('job-sec.item'),FALSE, array('width' => '70'));
    $this -> addColumn('job_adm', lan('job-adm.item'),FALSE, array('width' => '70'));
    $this -> addColumn('job_mis', lan('job-mis.item'),FALSE, array('width' => '70'));
    $this -> addColumn('job_com', lan('job-com.item'),FALSE, array('width' => '70'));
    $this -> addColumn('job_tra', lan('job-tra.item'),FALSE, array('width' => '70'));
    $this -> getPrefs();

    $this -> mAli = CCor_Res::getByKey('alias', 'fie');
    $this -> mPlain = new CHtm_Fie_Plain();

    $this -> getIterator();
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);
    $this -> setGroup($this -> mOrd);
    $this -> mGrpDef = (isset($this -> mAli[$this -> mOrd])) ? $this -> mAli[$this -> mOrd] : NULL;

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

  protected function getIterator() {
    $this -> mIte = new CCor_TblIte('al_job_sub_'.intval(MID));
    $this -> mIte -> addCnd('pro_id='.$this -> mJobId);
  }

  protected function preLoad() {
    $lArr = array();

    $this -> mIte = $this -> mIte -> getArray('id');
    foreach ($this -> mIte as $lRow) {
      if (!empty($lRow['jobid_art'])) {
        $lArr[$lRow['jobid_art']] = TRUE;
      }
      if (!empty($lRow['jobid_rep'])) {
        $lArr[$lRow['jobid_rep']] = TRUE;
      }
      if (!empty($lRow['jobid_sec'])) {
        $lArr[$lRow['jobid_sec']] = TRUE;
      }
      if (!empty($lRow['jobid_adm'])) {
        $lArr[$lRow['jobid_adm']] = TRUE;
      }
      if (!empty($lRow['jobid_mis'])) {
        $lArr[$lRow['jobid_mis']] = TRUE;
      }
      if (!empty($lRow['jobid_com'])) {
        $lArr[$lRow['jobid_com']] = TRUE;
      }
      if (!empty($lRow['jobid_tra'])) {
        $lArr[$lRow['jobid_tra']] = TRUE;
      }
    }
    $this -> mSub = array();
    if (empty($lArr)) return;
    
    $lJid = '';
    foreach ($lArr as $lKey => $lDum) {
      $lJid.= '"'.$lKey.'",';
    }
    $lJid = strip($lJid);
    
    $lWriter = CCor_Cfg::get('job.writer.default', 'alink');
    if ('portal' == $lWriter) {
      $lIte = new CCor_TblIte('all', $this->mWithoutLimit);
      $lIte -> addField('jobid');
      $lIte -> addField('webstatus');
      $lIte -> addCnd('jobid IN ('.$lJid.')');
    } else {
      $lIte = new CApi_Alink_Query_Getjoblist();
      $lIte -> addField('webstatus', 'webstatus');
      $lIte -> addCondition('jobid', 'in', $lJid);
    }

    foreach ($lIte as $lRow) {
      $this -> mSub[$lRow['jobid']] = $lRow;
    }
  }

  protected function addColumns() {
    $lSql = 'SELECT DISTINCT(wiz_id) FROM al_job_sub_'.intval(MID).' WHERE pro_id='.$this -> mJobId;
    $lQry = new CCor_Qry($lSql);

    $lArr = array();
    foreach ($lQry as $lRow) {
      $lArr[] = $lRow['wiz_id'];
    }

    if (empty($lArr)) return;
    $lFie = array();
    $lSql = 'SELECT * FROM al_wiz_items WHERE wiz_id IN ('.implode(',', $lArr).')';
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
        $lVal = $lNew;
        if ($this -> mGrpDef) {
          $lVal = $this -> mPlain -> getPlain($this -> mGrpDef, $lVal);
        }
        $lRet.= htm($lVal).NB;
        $lRet.= '</td>';
        $lRet.= _TR;
        $this -> mOldGrp = $lNew;
        $this -> mCls = 'td1';
      }
    }
    return $lRet;
  }

  protected function getTdJob_Art() {
    $lId = $this -> getVal('jobid_art');
    $lSid = $this -> getInt('id');
    if (isset($this -> mSub[$lId])) {
      $lRow = $this -> mSub[$lId];
      $lSta = $lRow['webstatus'] / 10;
      $lRet = '<a href="index.php?act=job-art.edt&amp;jobid='.$lId.'" class="nav">';
	  $lPath = CApp_Crpimage::getSrcPath('art', 'img/crp/'.$lSta.'b.gif');
      $lRet.= img($lPath);
      $lRet.= '</a>';
    } else {
      $lRet = NB;
    }
    return $this -> td($lRet);
  }

  protected function getTdJob_Rep() {
    $lId = $this -> getVal('jobid_rep');
    $lSid = $this -> getInt('id');
    if (isset($this -> mSub[$lId])) {
      $lRow = $this -> mSub[$lId];
      $lSta = $lRow['webstatus'] / 10;
      $lRet = '<a href="index.php?act=job-rep.edt&amp;jobid='.$lId.'" class="nav">';
	  $lPath = CApp_Crpimage::getSrcPath('rep', 'img/crp/'.$lSta.'b.gif');
      $lRet.= img($lPath);
      $lRet.= '</a>';
    } else {
      $lRet = NB;
    }
    return $this -> td($lRet);
  }

  protected function getTdJob_Sec() {
    $lId = $this -> getVal('jobid_sec');
    $lSid = $this -> getInt('id');
    if (isset($this -> mSub[$lId])) {
      $lRow = $this -> mSub[$lId];
      $lSta = $lRow['webstatus'] / 10;
      $lRet = '<a href="index.php?act=job-sec.edt&amp;jobid='.$lId.'" class="nav">';
	  $lPath = CApp_Crpimage::getSrcPath('sec', 'img/crp/'.$lSta.'b.gif');
      $lRet.= img($lPath);
      $lRet.= '</a>';
    } else {
      $lRet = NB;
    }
    return $this -> td($lRet);
  }

  protected function getTdJob_Adm() {
    $lId = $this -> getVal('jobid_adm');
    $lSid = $this -> getInt('id');
    if (isset($this -> mSub[$lId])) {
      $lRow = $this -> mSub[$lId];
      $lSta = $lRow['webstatus'] / 10;
      $lRet = '<a href="index.php?act=job-adm.edt&amp;jobid='.$lId.'" class="nav">';
	  $lPath = CApp_Crpimage::getSrcPath('adm', 'img/crp/'.$lSta.'b.gif');
      $lRet.= img($lPath);
      $lRet.= '</a>';
    } else {
      $lRet = NB;
    }
    return $this -> td($lRet);
  }

  protected function getTdJob_Mis() {
    $lId = $this -> getVal('jobid_mis');
    $lSid = $this -> getInt('id');
    if (isset($this -> mSub[$lId])) {
      $lRow = $this -> mSub[$lId];
      $lSta = $lRow['webstatus'] / 10;
      $lRet = '<a href="index.php?act=job-mis.edt&amp;jobid='.$lId.'" class="nav">';
	  $lPath = CApp_Crpimage::getSrcPath('mis', 'img/crp/'.$lSta.'b.gif');
      $lRet.= img($lPath);
      $lRet.= '</a>';
    } else {
      $lRet = NB;
    }
    return $this -> td($lRet);
  }

  protected function getTdJob_Com() {
    $lId = $this -> getVal('jobid_com');
    $lSid = $this -> getInt('id');
    if (isset($this -> mSub[$lId])) {
      $lRow = $this -> mSub[$lId];
      $lSta = $lRow['webstatus'] / 10;
      $lRet = '<a href="index.php?act=job-com.edt&amp;jobid='.$lId.'" class="nav">';
	  $lPath = CApp_Crpimage::getSrcPath('com', 'img/crp/'.$lSta.'b.gif');
      $lRet.= img($lPath);
      $lRet.= '</a>';
    } else {
      $lRet = NB;
    }
    return $this -> td($lRet);
  }

  protected function getTdJob_Tra() {
    $lId = $this -> getVal('jobid_tra');
    $lSid = $this -> getInt('id');
    if (isset($this -> mSub[$lId])) {
      $lRow = $this -> mSub[$lId];
      $lSta = $lRow['webstatus'] / 10;
      $lRet = '<a href="index.php?act=job-tra.edt&amp;jobid='.$lId.'" class="nav">';
	  $lPath = CApp_Crpimage::getSrcPath('tra', 'img/crp/'.$lSta.'b.gif');
      $lRet.= img($lPath);
      $lRet.= '</a>';
    } else {
      $lRet = NB;
    }
    return $this -> td($lRet);
  }

}