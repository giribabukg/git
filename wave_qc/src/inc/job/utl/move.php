<?php
class CInc_Job_Utl_Move extends CCor_Obj {

	public static function moveJob($aJobId, $aCurSrc, $aJob, $aNewSrc, $aWebStatus) {
	  $lJob = $aJob -> getIterator();
	  $lJobDetails = get_object_vars($lJob);
	  $lRes = FALSE;
	  
	  if (CCor_Cfg::get('job.writer.default') == 'portal') {
	    $lRes = self::moveJobType($aJobId, $aCurSrc, $lJobDetails, $aNewSrc, $aWebStatus);
	  }
	  else {
	    // update networker job type & webstatus
	    $lClass = "CJob_".ucfirst($aCurSrc)."_Mod";
	    $lMod = new $lClass($aJobId);
	    $lMod->forceVal('src', $aNewSrc);
	    $lMod->forceVal('webstatus', $aWebStatus);
	    $lRes = $lMod->update();
	    
	  }
	  
	  if ($lRes) {
	    $lQry = new CCor_Qry();
		// update history job type
		$lSql = "UPDATE al_job_his SET src=" . esc($aNewSrc) . " WHERE src_id=" . esc($aJobId);
		$lQry->query($lSql);
		
		// update sub job type
		$lSql = "UPDATE al_job_sub_" . intval(MID) . " SET src=" . esc($aNewSrc) . ", job_" . $aNewSrc . "=" . esc($aJobId) . ", jobid_" . $aCurSrc . "='' WHERE jobid_" . $aCurSrc . "=" . esc($aJobId);
		$lQry->query($lSql);
		
		// update job type for archive
		$lSql = "UPDATE al_job_arc_" . intval(MID) . " SET src=" . esc($aNewSrc) . " WHERE jobid=" . esc($aJobId);
		$lQry->query($lSql);
			
		// update shadow job type
		$lSql = "UPDATE al_job_shadow_" . intval(MID) . " SET src=" . esc($aNewSrc) . " WHERE jobid=" . esc($aJobId);
		$lQry->query($lSql);
			
		// update approval loops job type
		$lSql = "UPDATE al_job_apl_loop SET src=" . esc($aNewSrc) . " WHERE jobid=" . esc($aJobId);
		$lQry->query($lSql);
			
		// update files job type
		$lSql = "UPDATE al_job_files SET src=" . esc($aNewSrc) . " WHERE jobid=" . esc($aJobId);
		$lQry->query($lSql);
			
		// move job files
		$lFileDir = CCor_Cfg::get('file.dir');
		if(is_dir($lFileDir)) {
		  $lCurDir = $lFileDir . "mand_" . intval(MID) . DS . "job" . DS . $aCurSrc . DS . $aJobId . DS . "doc" . DS;
		  $lNewDir = $lFileDir . "mand_" . intval(MID) . DS . "job" . DS . $aNewSrc . DS . $aJobId . DS . "doc" . DS;
		  self::copydir($lCurDir, $lNewDir);
		}
	   }
	   return TRUE;
	}
	
	
	public function copydir($lSrc, $lDest) {
		if(file_exists($lSrc)){
			if (!is_dir($lDest) && !mkdir($lDest, 0777, true)){
				echo "Error creating folder " . $lDest;
				exit;
			}
			
			$lHandle = @opendir($lSrc) or die("Unable to open");
			while($lFile = readdir($lHandle)){
				$lSrcFile = $lSrc.DS.$lFile;
				$lDestFile = $lDest.DS.$lFile;
				if($lFile != "." && $lFile != ".."){
					if(!is_dir($lSrcFile)) { //if a file
						rename($lSrcFile, $lDestFile);
					} else if(is_dir($lSrcFile)) { //if a folder
						$this->copydir($lSrcFile, $lDestFile);
					}
				}
			}
			
			closedir($lHandle);
		}
	}
	
	protected static function moveJobType($aJobId, $aCurSrc, $aJobDetails, $aNewSrc, $aWebStatus) {
	  self::backupJob($aJobDetails, $aJobId);
	  $lQry = new CCor_Qry();
	  $lCurrentTable = 'al_job_'.$aCurSrc.'_'.MID;
	  $lNewTable = 'al_job_'.$aNewSrc.'_'.MID;
	  
	  $lColNamesFrmCurrentTable = $lQry -> getTableColumns($lCurrentTable);
	  unset($lColNamesFrmCurrentTable[0]);
	  $lColNamesFrmNewTable = $lQry -> getTableColumns($lNewTable);
	  unset($lColNamesFrmNewTable[0]);
	  $lColIntersact = array_intersect($lColNamesFrmCurrentTable, $lColNamesFrmNewTable);
	  $lInsertJob = FALSE;
	  $lSql = 'SELECT '.implode(',', $lColIntersact).' FROM '.$lCurrentTable.' WHERE jobid = '.$aJobId;
	  $lQry = new CCor_Qry($lSql);
	  if ($lQry->query($lSql)) {
	    $lSqlInsert = 'INSERT INTO '.$lNewTable.' ';
	    foreach ($lQry as $lValues) {
	      foreach ($lValues as $lAlias => $lValue) {
	        if ($lAlias == 'webstatus') $lValue = $aWebStatus;
	        if ($lAlias == 'src') $lValue = $aNewSrc;
	        $lRows[] = '`'.$lAlias.'`';
	        $lVals[] = esc($lValue);
	      }
	    }
	    $lSqlInsert.=  '('.implode(',', $lRows).') VALUES ('.implode(',', $lVals).');';
	    $lInsertJob = CCor_Qry::exec($lSqlInsert);
	  }
	  
	  if ($lInsertJob) {
	    CCor_Qry::exec('DELETE FROM `'.$lCurrentTable.'` WHERE `jobid`='.$aJobId.';');
	  }
	  return $lInsertJob;
	}
	
	protected function backupJob($aJob, $aJobId) {
	  $lNow = date('Y-m-d H:i:s');
	  foreach ($aJob as $lKey => $lValue) {
	    if (!empty($lValue)) {
	      CCor_Qry::exec('INSERT INTO `al_job_reuse_'.MID.'` (`jobid`, `key`, `value`, `grabbed`) VALUES ("'.$aJobId.'","'.$lKey.'","'.addslashes($lValue).'","'.$lNow.'");');
	    }
	  }
	}
}