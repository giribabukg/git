<?php
class CInc_Arc_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan($aMod.'.menu');
    $this -> mUsr = CCor_Usr::getInstance();
    $lMod = 'job'.substr($aMod,3);
    // Ask for archive read right
    if (!$this -> mUsr -> canRead('arc')) {
      $this -> denyAccess();
    }
    if (!$this -> mUsr -> canRead($lMod)) {
      $this -> denyAccess();
    }
    $this -> mAva = fsPro;
  }

  protected function actStd() {
    $lVie = new CJob_List('job');
    $this -> render($lVie);

    $lVie = $this -> getFac() -> getList();
    $this -> render($lVie);
  }


  protected function actFpr() {
    $lJobId = $this -> mReq -> jobid;
    $lVie = new CHtm_Fpr($this -> mMod.'.sfpr');
    $lDef = CCor_Res::get('fie');

    $lArr = array();
    foreach ($lDef as $lFie) {
      $lFla = intval($lFie['flags']);
      $lAva = intval($lFie['avail']);
      if (bitSet($lAva, $this -> mAva)) {
        if (bitSet($lFla, ffList)) {
          // If Jobfield has Read Flag active, ask for User READ-RIGHT (combination of 'fie_' and Alias).
          // If User has no READ-RIGHT, Jobfield not shown in the list.
          $lFieRight = 'fie_'.$lFie['alias'];
          if (bitset($lFla,ffRead) && !$this -> mUsr -> canRead($lFieRight)){
            continue;
          }
          $lArr[$lFie['id']] = $lFie['name_'.LAN];
        }
      }
    }
    $lVie -> setSrc($lArr);
    $lVie -> setParam('jobid', $lJobId);
    $lUsr = CCor_Usr::getInstance();
    $lVie -> setSel($lUsr -> getPref($this -> mPrf.'.cols'));
    $this -> render($lVie);
  }

  protected function actSfpr() {
    $lDst = $this -> mReq -> getVal('dst');
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mPrf.'.cols', implode(',', $lDst));
    $this -> redirect();
  }

  protected function actSpr() {
    $lVie = new CHtm_Fpr($this -> mMod.'.sspr');
    $lVie -> setTitle(lan('lib.opt.spr'));
    $lDef = CCor_Res::get('fie');

    $lArr = array();
    foreach ($lDef as $lFie) {
      $lFla = intval($lFie['flags']);
      $lAva = intval($lFie['avail']);
      if (bitSet($lAva, $this -> mAva)) {
        if (bitSet($lFla, ffSearch)) {
          // If Jobfield has Read Flag active, ask for User READ-RIGHT (combination of 'fie_' and Alias).
          // If User has no READ-RIGHT, Jobfield not shown in the list.
          $lFieRight = 'fie_'.$lFie['alias'];
          if (bitset($lFla,ffRead) && !$this -> mUsr -> canRead($lFieRight)){
            continue;
          }
          $lArr[$lFie['id']] = $lFie['name_'.LAN];
        }
      }
    }

    $lVie -> setSrc($lArr);
    $lUsr = CCor_Usr::getInstance();
    $lVie -> setSel($lUsr -> getPref($this -> mPrf.'.sfie'));
    $this -> render($lVie);
  }

  protected function actSspr() {
    $lDst = $this -> mReq -> getVal('dst');
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mPrf.'.sfie', implode(',', $lDst));
    $this -> redirect();
  }

  protected function actTogfil() {
    $lUsr = CCor_Usr::getInstance();
    $lOld = $lUsr -> getPref($this -> mMod.'.hidefil');
    $lNew = ($lOld == 1) ? 0 : 1;
    $lUsr -> setPref($this -> mMod.'.hidefil', $lNew);
    $this -> redirect();
  }

  protected function actTogser() {
    $lUsr = CCor_Usr::getInstance();
    $lOld = $lUsr -> getPref($this -> mMod.'.hideser');
    $lNew = ($lOld == 1) ? 0 : 1;
    $lUsr -> setPref($this -> mMod.'.hideser', $lNew);
    $this -> redirect();
  }

  protected function actSelview() {
    $lId = $this -> getInt('id');

    $lQry = new CCor_Qry('SELECT * FROM al_usr_view WHERE id='.$lId.' AND mand='.MID);
    if ($lRow = $lQry -> getDat()) {
      $lUsr = CCor_Usr::getInstance();
      $lUsr -> setPref($this -> mMod.'.cols', $lRow['cols']);
      $lUsr -> setPref($this -> mMod.'.lpp', $lRow['lpp']);
      $lUsr -> setPref($this -> mMod.'.ord', $lRow['ord']);
      $lUsr -> setPref($this -> mMod.'.sfie', $lRow['sfie']);
      $lUsr -> setPref($this -> mMod.'.page', 0);
    }
    $this -> redirect();
  }

  protected function actSelsearch() {
    $lId = $this -> getInt('id');

    $lQry = new CCor_Qry('SELECT ser FROM al_usr_search WHERE id='.$lId.' AND mand='.MID);
    if ($lRow = $lQry -> getDat()) {
      $lUsr = CCor_Usr::getInstance();
      $lUsr -> setPref($this -> mMod.'.ser', unserialize($lRow['ser']));
      $lUsr -> setPref($this -> mMod.'.page', 0);
    }
    $this -> redirect();
  }

  protected function actAllview() {
    $lUsr = CCor_Usr::getInstance();

    $lPrf = array();
    $lPrf['cols'] = $lUsr -> getPref($this -> mMod.'.cols');
    $lPrf['lpp']  = $lUsr -> getPref($this -> mMod.'.lpp');
    $lPrf['ord']  = $lUsr -> getPref($this -> mMod.'.ord');
    $lPrf['sfie'] = $lUsr -> getPref($this -> mMod.'.sfie');
    
    foreach ($lPrf as $lKey => $lVal) {
      if (empty($lVal)) unset($lPrf[$lKey]);
    }

    if (!empty($lPrf)) {
      $lQry = new CCor_Qry();
      foreach ($lPrf as $lKey => $lVal) {
        $lModKey = $this -> mMod.'.'.$lKey;
        $lSql = 'INSERT INTO al_sys_pref SET mand='.MID.',code='.esc($lModKey).',val='.esc($lVal).' ';
        $lSql.= 'ON DUPLICATE KEY UPDATE val='.esc($lVal).';';
        $lQry -> query($lSql);
      }
    }
    $this -> redirect();
  }

  protected function actCsvexp() {
    $lUsr = CCor_Usr::getInstance(); // needed for user id and preferences
    $lAge = $this -> getReq('age'); // needed to differ between job and arc
    $lSrc = $this -> getReq('src'); // needed for job type

    if (CCor_Cfg::get('csv-exp.bymail', true)) {
      $lFil = base64_encode(serialize($lUsr -> getPref($lAge.'-'.$lSrc.'.fil')));
      $lSer = base64_encode(serialize($lUsr -> getPref($lAge.'-'.$lSrc.'.ser')));

      $lQueue = new CApp_Queue('createcsv');
      $lQueue -> setParam('uid', $lUsr -> getId());
      $lQueue -> setParam('mid', MID);
      $lQueue -> setParam('age', $lAge);
      $lQueue -> setParam('src', $lSrc);
      $lQueue -> setParam('fil', $lFil);
      $lQueue -> setParam('ser', $lSer);
      $lQueue -> insert();

      $this -> redirect();
    } else {
      // Columns
      $lCols = $lUsr -> getPref($lAge.'-'.$lSrc.'.cols');
      if (empty($lCols)) {
        CCor_Msg::add('No columns to show specified', mtUser, mlError);
        $this -> redirect();
      }

      // Filename
      $lMandArray = CCor_Res::extract('code', 'name_'.LAN, 'mand');
      $lMandName = str_replace(' ', '_', $lMandArray[MAND]);

      $lFileName = lan($lAge.'-'.$lSrc.'.menu');
      $lFileName.= '_';
      $lFileName.= $lMandName;
      $lFileName.= '_';
      $lFileName.= date('Ymd_H-i-s');
      $lFileName.= '.csv';

      // File
      header('Content-type: text/csv');
      header('Content-Disposition: attachment; filename="'.$lFileName.'"');
      flush();

      // Content
      $lClass_List = 'C'.ucfirst($lAge).'_'.ucfirst($lSrc).'_List';
      $lWithoutLimit = true;
      $lJobList = new $lClass_List($lWithoutLimit);

      $lIdField = $lJobList -> mIdField; // it's either jobid, jobnr or id
      $lJobList -> mIte = $lJobList -> mIte -> getArray($lIdField);

      $lJobList -> loadFlags();
      $lRet = $lJobList -> getCsvContent();
    }
  }

  protected function actXlsexp() {
    $lUsr = CCor_Usr::getInstance(); // needed for user id and preferences
    $lAge = $this -> getReq('age'); // needed to differ between job and arc
    $lSrc = $this -> getReq('src'); // needed for job type

    if (CCor_Cfg::get('csv-exp.bymail', true)) {
      $lFil = base64_encode(serialize($lUsr -> getPref($lAge.'-'.$lSrc.'.fil')));
      $lSer = base64_encode(serialize($lUsr -> getPref($lAge.'-'.$lSrc.'.ser')));

      $lQueue = new CApp_Queue('createxls');
      $lQueue -> setParam('uid', $this -> mUsr -> getId());
      $lQueue -> setParam('mid', MID);
      $lQueue -> setParam('age', $lAge);
      $lQueue -> setParam('src', $lSrc);
      $lQueue -> setParam('fil', $lFil);
      $lQueue -> setParam('ser', $lSer);
      $lQueue -> insert();

      $this -> redirect();
    } else {
      // Filename
      $lMandArray = CCor_Res::extract('code', 'name_'.LAN, 'mand');
      $lMandName = str_replace(' ', '_', $lMandArray[MAND]);

      $lFileName = lan($lAge.'-'.$lSrc.'.menu');
      $lFileName.= '_';
      $lFileName.= $lMandName;
      $lFileName.= '_';
      $lFileName.= date('Ymd_H-i-s');
      $lFileName.= '.xls';

      // Content
      $lClass_List = 'C'.ucfirst($lAge).'_'.ucfirst($lSrc).'_List';
      $lWithoutLimit = true;
      $lJobList = new $lClass_List($lWithoutLimit);

      $lIdField = $lJobList -> mIdField; // it's either jobid, jobnr or id
      $lJobList -> mIte = $lJobList -> mIte -> getArray($lIdField);

      $lJobList -> loadFlags();
      $lXls = $lJobList -> getExcel();
      $lXls -> downloadAs($lFileName);
    }
  }

  protected function actRepexp() {
  	$lUsr = CCor_Usr::getInstance(); // needed fuer user id and preferences
  	$lAge = $this -> getReq('age'); // needed to differ between job and arc
  	$lSrc = $this -> getReq('src'); // needed for job type

  	if (CCor_Cfg::get('rep-exp.bymail', true)) {
  		$lFil = base64_encode(serialize($lUsr -> getPref($lAge.'-'.$lSrc.'.fil')));
  		$lSer = base64_encode(serialize($lUsr -> getPref($lAge.'-'.$lSrc.'.ser')));

  		$lQueue = new CApp_Queue('createrep');
  		$lQueue -> setParam('uid', $lUsr -> getId());
  		$lQueue -> setParam('mid', MID);
  		$lQueue -> setParam('age', $lAge);
  		$lQueue -> setParam('src', $lSrc);
  		$lQueue -> setParam('fil', $lFil);
  		$lQueue -> setParam('ser', $lSer);
  		$lQueue -> insert();

  		$this -> redirect();
  	} else {
  		// Filename
  		$lMandArray = CCor_Res::extract('code', 'name_'.LAN, 'mand');
  		$lMandName = str_replace(' ', '_', $lMandArray[MAND]);

  		$lFileName = lan($lAge.'-'.$lSrc.'.menu');
  		$lFileName.= '_';
  		$lFileName.= $lMandName;
  		$lFileName.= '_';
  		$lFileName.= date('Ymd_H-i-s');
  		$lFileName.= '.csv';

  		// File
  		header('Content-type: text/csv');
  		header('Content-Disposition: attachment; filename="'.$lFileName.'"');
  		flush();

  		// Content
  		$lClass_List = 'C'.ucfirst($lAge).'_'.ucfirst($lSrc).'_List';
  		$lWithoutLimit = true;
  		$lJobList = new $lClass_List($lWithoutLimit);

  		$lIdField = $lJobList -> mIdField; // it's either jobid, jobnr or id
  		$lJobList -> mIte = $lJobList -> mIte -> getArray($lIdField);

  		#$lJobList -> loadFlags();
  		$lRet = $lJobList -> getRepContent();
  	}
  }
  
  protected function actReuse() {
    $lJobId = $this -> getReq('jobid');
    $lSrc = $this -> getReq('src');
    $lType = $this -> getReq('type'); // 0 = as is; 1 = keep all WebCenter history; 2 = don't keep WebCenter history; 3 = keep latest WebCenter history

    $lNow = date('Y-m-d H:i:s');
    $lInsertJob = TRUE;

    // create table for complete backups in case there is none yet
    CCor_Qry::exec('CREATE TABLE IF NOT EXISTS `al_job_reuse_'.MID.'` (`jobid` CHAR(13) NOT NULL, `key` CHAR(255) NOT NULL, `value` MEDIUMTEXT NOT NULL, `grabbed` DATETIME NOT NULL);');

    // check whether the current job is already backuped
    $lPDBQry = new CCor_Qry('SELECT * FROM `al_job_reuse_'.MID.'` WHERE `jobid`='.$lJobId.' AND `grabbed`="'.$lNow.'";');
    if (!$lRow = $lPDBQry -> getDat()) {
      if (CCor_Cfg::get('job.writer.default') == 'portal') {
        $lInsertJob = FALSE;
        $lQry = new CCor_Qry();
        $lPdbAliases = $lQry -> getTableColumns('al_job_'.$lSrc.'_'.MID);
        unset($lPdbAliases[0]);
        $lJob = new CArc_Dat($lSrc);
        $lJob -> load($lJobId);
        $lJobDetails = (array) $lJob -> getIterator();
        $lAktiveCols = array_flip($lPdbAliases);
        $lColsIntersact = array_intersect_key($lJobDetails, $lAktiveCols);
        
        $lSqlInsert = 'INSERT INTO al_job_'.$lSrc.'_'.MID.' SET ';
        foreach ($lColsIntersact as $lKey => $lVal) {
          $lSqlParts[] = $lKey.'='.esc($lVal);
          $lRows[] = '`'.$lKey.'`';
          $lVals[] = esc($lVal);
        }
        $lSqlInsert.= implode(',', $lSqlParts);
        $lInsertJob = CCor_Qry::exec($lSqlInsert);
      } else {
        $lQry = new CApi_Alink_Query_Getjobdetails($lJobId, $lSrc, TRUE);
        $lFie = CCor_Res::get('fie');
        foreach ($lFie as $lDef) {
          if (!empty($lDef['native'])) {
            $lQry -> addDef($lDef);
          }
        }
        $lRes = $lQry -> query();
        if (!$lRes) {
          $this -> redirect('index.php?act=arc-'.$lSrc.'.edt&jobid='.$lJobId);
        }
        $lJobDetails = $lQry -> getDat();
      }
      
      $this -> backupJob($lJobDetails, $lJobId, $lNow);
    }

    $lHis = new CJob_His($lSrc, $lJobId);
    $lHis -> add(htReuse, lan('job.reuse.subject'), lan('job.reuse.message'), NULL);

    $lFac = new CJob_Fac($lSrc, $lJobId);
    $lJobMod = $lFac->getMod($lJobId);
    $lArray = CCor_Cfg::get('arc.reuse');
    $lWebstatus = $lArray[$lSrc];
    $lJobMod->forceVal('webstatus', $lWebstatus);
    if ($lJobMod -> update() && $lInsertJob) {
      // delete from archive table
      CCor_Qry::exec('DELETE FROM `al_job_arc_'.MID.'` WHERE `jobid`='.$lJobId.';');
      CJob_Step::incJobToProCrpStatus($lSrc, $lJobId, $lWebstatus);
      $this -> redirect('index.php?act=job-'.$lSrc.'.edt&jobid='.$lJobId);
    } else {
      $this -> redirect();
    }
  }
  
  protected function backupJob($aJob, $aJobId, $aNow) {
    foreach ($aJob as $lKey => $lValue) {
      if (!empty($lValue)) {
        CCor_Qry::exec('INSERT INTO `al_job_reuse_'.MID.'` (`jobid`, `key`, `value`, `grabbed`) VALUES ("'.$aJobId.'","'.$lKey.'","'.addslashes($lValue).'","'.$aNow.'");');
      }
    }
  }
}
