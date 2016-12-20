<?php
class CInc_Xchange_Log_Cnt extends CCor_Cnt {

	public function __construct(ICor_Req $aReq, $aMod, $aAct){
		parent::__construct($aReq, $aMod, $aAct);
		$this->mTitle = lan('xchange-log.menu');
		$this->mMmKey = 'opt';
		
		// Ask If user has right for this page
		$lpn = 'xchange';
		$lUsr = CCor_Usr::getInstance();
		if(! $lUsr->canRead($lpn)){
			// this->setProtection('*', $lpn, rdNone);
		}
	}

	protected function actStd(){
		$lTab = new CXchange_Tabs('log');
		$lVie = new CXchange_Log_List();
		$lRet = $lTab->getContent();
		$lRet .= $lVie->getContent();
		$this->render($lRet);
	}

	protected function actSer(){
		$this->mReq->expect('val');
		$lReq = $this->getReq('val');
		$lArr = array();
		foreach($lReq as $lKey => $lVal){
			if('' === $lVal)
				continue;
			$lArr[$lKey] = $lVal;
		}
		$lUsr = CCor_Usr::getInstance();
		$lUsr->setPref($this->mMod . '.ser', $lArr);
		$lUsr->setPref($this->mMod . '.page', 0);
		$this->redirect();
	}

	protected function actFil(){
		$this->mReq->expect('val');
		$lVal = $this->mReq->getVal('val');
		$lUsr = CCor_Usr::getInstance();
		$lUsr->setPref($this->mPrf . '.fil', $lVal);
		$lUsr->setPref($this->mPrf . '.page', 0);
		$this->redirect();
	}

	protected function actTruncate(){
		CCor_Qry::exec('TRUNCATE al_xchange_log WHERE mand='.MID);
		$this->actClser();
		$this->redirect();
	}
	
	public function actReprocess(){
		$lLogger = new CApi_Xchange_Logger();
		
		$lCfg = CCor_Cfg::getInstance();
		$lIn = $lCfg->get('xchange.in', '');
		$lParsed = $lCfg->get('xchange.parsed', '');
		$lError = $lCfg->get('xchange.error', '');
		
		$lId = $this->getInt('id');
    	$lSql = 'SELECT * FROM al_xchange_log WHERE id='.$lId;
    	$lQry = new CCor_Qry($lSql);
    	foreach($lQry as $lRow){
    		$lFile = $lRow['filename'];
		
			$lOldFil = $lError . $lFile;
			if(file_exists($lOldFil)){ //If parsed file exists
				$lNewFil = $lIn . $lFile;
		
				if(!copy($lOldFil, $lNewFil)){ //try copying the parsed file to the in folder
					$lLogger->logMsg(256, 4, "Not able to copy file to be reprocessed", $lFile);
				} else {
					$lLogger->logMsg(256, 64, "File successfuly moved to be reprocessed", $lFile);

					$lSql = 'UPDATE al_xchange_log SET rp="Y" WHERE filename='.esc($lFile);
					CCor_Qry::exec($lSql);
				}
			} else {		
				$lOldFil = $lParsed . $lFile;
				if(file_exists($lOldFil)){ //If parsed file exists
					$lNewFil = $lIn . $lFile;
						
					if(!copy($lOldFil, $lNewFil)){ //try copying the parsed file to the in folder
						$lLogger->logMsg(256, 4, "Not able to copy file to be reprocessed", $lFile);
					} else {
						$lLogger->logMsg(256, 64, "File successfuly moved to be reprocessed", $lFile);

						$lSql = 'UPDATE al_xchange_log SET rp="Y" WHERE filename='.esc($lFile);
						CCor_Qry::exec($lSql);
					}
				} else {
					$lLogger->logMsg(256, 4, "Can't locate file ".$lFile.". Please contact Administrators of the system!", $lFile);
				}
			}
    	}

    	$this->redirect();
	}
}