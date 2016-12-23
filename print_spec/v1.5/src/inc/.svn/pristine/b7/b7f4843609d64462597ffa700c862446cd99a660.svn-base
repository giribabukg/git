<?php
 class CInc_Utl_Fil_Mod extends CCor_Obj {
 	
 	public function __construct($aJob, $aJobId) {
 	  $this -> mJob = $aJob;
 	  $this -> mJobid = $aJobId;
 	}
	
 	/**
 	 * 
 	 * @param unknown $aRow conatin the information about the file need to generate a download link for it
 	 * @param unknown $aExpireDate of the download link
 	 * @return string
 	 */
	protected function generateTokenLink($aRow, $aExpireDate) {
		$lTokenGen = new CInc_App_Pwd();
		$lRandomToken = $lTokenGen -> createNewToken();
		$this -> insretTokenLinkData($aRow, $lRandomToken, $aExpireDate);
	
		$lBase = CCor_Cfg::get('base.url');
		$lLnk = $lBase.'index.php?download='.$lRandomToken;
		return $lLnk;
	}
	
	protected function insretTokenLinkData($aRow, $aToken, $aExpireDate) {
		$lRow = $aRow;
		$lRandomToken = $aToken;
		$lSql = 'INSERT INTO al_job_files_down_links SET';
		$lSql.= ' file_id='.$lRow['id'];
		$lSql.= ', mand='.$lRow['mand'];
		$lSql.= ', jobid='.esc($lRow['jobid']);
		$lSql.= ', src='.esc($lRow['src']);
		$lSql.= ', download_src='.esc($lRow['download_src']);
		$lSql.= ', wec_ver_id='.esc($lRow['wec_ver_id']);
		$lSql.= ', generated_date=NOW()';
		$lSql.= ', expire_date='.esc($aExpireDate);
		$lSql.= ', token='.esc($lRandomToken);
		$lSql.= ', available=1';
		CCor_Qry::exec($lSql);
	}
	
	/**
	 * This methode take the sub folder and generate deeplinks for the inside Pdfs
	 * incase of the sub is WC it will generate a deep link for the latest version
	 * @param unknown $aSub
	 * @return string
	 */
	public function getFolderDeepLinks($aSub) {
		$this->getExpireDate();
		if ($aSub == 'wec') {
		  $lWecFileInfo = $this -> getFilesWec();
		  $lRow = array();
		  $lRow['id'] = 0;
		  $lRow['filename'] = $lWecFileInfo[0]['name'];
		  $lRow['mand'] = MID;
		  $lRow['jobid'] = $this -> mJobid;
		  $lRow['src'] = $this -> mJob['src'];
		  $lRow['download_src'] = 'wec';
		  $lRow['wec_ver_id'] = $lWecFileInfo[0]['wec_ver_id'];
		  $lTokenLink[] = $lRow['filename'].': '.$this -> generateTokenLink($lRow, $this -> mExpireDate);
		}
		else {
		  $lSql = 'SELECT * FROM al_job_files WHERE jobid='.$this -> mJobid.' AND sub='.esc($aSub);
		  $lQry = new CCor_Qry($lSql);
		  $lRet = '';
		  foreach ($lQry as $lRow) {
		    $lTokenLink[] = $lRow['filename'].': '.$this -> generateTokenLink($lRow, $this -> mExpireDate);
		  }
		}
		if (!empty($lTokenLink)) $lRet.= 'Expire on: '.$this -> mExpireDate.LF;
		$lRet.= implode(LF, $lTokenLink);
		return $lRet;
	}
	
	protected function getFilesWec() {
	  $lRet = array();
	  if (empty($this -> mJob['wec_prj_id'])) return $lRet;
	  $lWec = new CApi_Wec_Client();
	  $lWec -> loadConfig();
	  $lQry = new CApi_Wec_Query_Doclist($lWec);
	  $lRet = $lQry -> getList($this -> mJob['wec_prj_id']);
	  return $lRet;
	}
	
	public function setFolderSrc($aFolderSrc) {
		return $this -> mFolderSrc = $aFolderSrc;
	}
	
	/**
	 * e.g.: 2015-11-25 12:27:22
	 * @param unknown $aDatetime
	 * @return unknown
	 */
	public function setExpireDate($aDatetime) {
		return $this -> mExpireDate = $aDatetime;
	}
	
	protected function getExpireDate() {
		$lStartDate = time();
		$lExpireDate = date('Y-m-d H:i:s', strtotime('+'.CCor_Cfg::get('downloadlink.duration').' day', $lStartDate));
		$this -> mExpireDate = (empty($this -> mExpireDate)) ? $lExpireDate : $this -> mExpireDate;
		return $this -> mExpireDate;
	}
		
}