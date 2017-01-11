<?php
class CInc_Svc_Phrase extends CSvc_Base {
  
  public $mDoc;

  protected function doExecute() {
    $lRet = $this -> doImport();
    
    return $lRet;
  }
  
  protected function doImport() {
  	$lDir = $this->getPar('hotfolder') . DS . 'in';
  	if (empty($lDir)) {
  		$this->msg('Hotfolder not defined', mtApi, mlError);
  		return false;
  	}
  	if (!file_exists($lDir)) {
  		$this->msg('Specified hotfolder '.$lDir.' does not exist', mtApi, mlError);
  		return false;
  	}
  	$lIte = new DirectoryIterator($lDir);
  	foreach ($lIte as $lFile) {
  		if (!$lIte -> isFile()) continue;
  		$lName = $lIte -> getPathname();
  		$lRet = $this->handleFile($lName);
  	}
  	return true;
  }

  protected function handleFile($aFile) {
  	$lXml = file_get_contents($aFile);
  	$lRes = $this->parse($lXml);
	
    if (!$lRes) {
      $this->msg('Phrase: Error parsing '.$aFile, mtApi, mlError);
      $this->moveFile($aFile, 'error');
      return false;
    }
    $this->msg('Phrase: Import of '.$aFile.' successful', mtApi, mlInfo);
    $this->moveFile($aFile, 'parsed');
    return true;
  }
  
  protected function parse($aXml) {
    $this -> mDoc = simplexml_load_string($aXml);
    
	if(is_object($this->mDoc)) {
      $lName = $this -> mDoc -> getName();
    
      if($lName == 'copycontent'){
    	  $lRet = $this->doParseContent();
    	  return (!empty($lRet)) ? $this->doJobUpdate($lRet) : false;
      }
	}
    
    return false;
  }
  
  protected function doJobUpdate($aRet) {
    $lLangClosed = array();
    $lJobId = $this -> mDoc -> job -> jobnr;
    
    foreach($aRet as $lIdx => $lContent) {
      $lCont = $lContent['content'];
      $lOld = $lContent['old'];
      $lNew = $lContent['new'];
      
      if($lOld != $lNew && $lOld > 0) { //replace content in job table
        CJob_Cms_Mod::updateJobRef($lNew, $lOld, $lJobId);
      } elseif($lOld == 0) { //insert into job table
        //master details
        $lMasterContent = CJob_Cms_Mod::getMasterData($lCont['parent_id']);
        if($lMasterContent != FALSE) {
          //get entry in job table for master content
          $lQry = CJob_Cms_Mod::getJobRef($lMasterContent, $lJobId);
          foreach($lQry as $lDat) {
            CJob_Cms_Mod::setJobRef($lJobId, $lNew, $lDat['position'], $lDat['type'], $lDat['group'], array(), $lDat['layout'], $lDat['template_id']);
          }
        }
      }
      
      $lLanguage = $lCont['language'];
      if(!in_array($lLanguage, $lLangClosed)) {
        $this -> closeAddTranslationTask($lLanguage);
        array_push($lLangClosed, $lLanguage);
      }
    }
    
    return true;
  }
  
  protected function closeAddTranslationTask($aLang) {
    $lJobId = $this -> mDoc -> job -> jobnr;
    $lSrc = $this -> mDoc -> job -> src;
  
    $lApl = new CApp_Apl_Loop($lSrc, $lJobId, 'apl-phtra');
    $lLid = $lApl -> getLastOpenLoop();
  
    $lSql = 'SELECT * FROM al_job_apl_states WHERE mand='.intval(MID).' AND loop_id='.esc($lLid);
    $lSql.= ' AND del="N" AND prefix='.esc($aLang).' AND pos=1 ORDER BY position ASC';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lId = $lRow['id'];
      $lUserId = $lRow['user_id'];
      $lState = intval($lRow['status']);
  
      if($lState == 0) {
        $lApl -> setState($lUserId, CApp_Apl_Loop::APL_STATE_APPROVED, '', $lLid, FALSE, array($lId));
      }
    }
  }
  
  /**
   * Processes content in xml file into al_cms tables
   * @param unknown $aXml
   * @return boolean - Successfully executed or not
   */
  protected function doParseContent() {
    $lRet = array();
    $lCopy = $this -> mDoc -> copy;
    
    foreach($lCopy -> copyelement as $lGroup) {
      $lParentId = $lGroup->attributes()->id;
      $lCategory = $lGroup->attributes()->copyelementType;
      
      foreach($lGroup -> value as $lContent) {
        $lContentId = $lContent->attributes()->id;
        $lLanguage = $lContent->attributes()->language;
        $lContent = (string) $lContent;
        
        if($lLanguage != "MA" && !empty($lContent)) {
          $lCont = array(
            'content_id' => intval($lContentId),
            'parent_id' => intval($lParentId),
          	'content' => $lContent, 
            'language' => strtoupper($lLanguage),
            'version' => 1,
            'category' => (string)$lCategory
          );
          
          //process content
          $lNewContentId = $this -> processContent($lCont);
          array_push($lRet, array('old' => intval($lContentId), 'new' => $lNewContentId, 'content' => $lCont));
        }
      }
    }
    
    return $lRet;
  }
  
  protected function processContent($aContent) {
    $lCmsDat = new CCms_Mod();
    $lInsertContent = TRUE;
  
    $lContentId = $aContent['content_id'];
    $lParentId = $aContent['parent_id'];
    $lLanguage = $aContent['language'];
    $lCategory = $aContent['category'];
    $lContentFmt = htmlspecialchars_decode($aContent['content']);
    $lContentStr = trim(strip_tags(ereg_replace("[[:cntrl:]]", " ", $lContentFmt)));
  
    $lCheck = CCms_Mod::contentExist($lContentStr, $lLanguage); //search for similar phrase with language
    if($lCheck > 0){
      if($lCheck != $lContentId) {
        $lContentId = $lCheck;
        $aContent['version'] = intval($aContent['version']) + 1;
      } else {
        $lInsertContent = FALSE;
      }
    } else {
      $lContentId = CCms_Mod::getMax('content_id'); //new content
    }
  
    if($lInsertContent) { //perform if new content
      //get contents tokens and sanitise string
      $lSanStr = CCms_Sanitiser::sanitise($lContentStr, $lCategory, $lLanguage, TRUE);
  
      //insert content into `al_cms_content`
      $lFormat = ($lContentFmt == $lContentStr) ? NULL : $lContentFmt; //if string is formatted then add to database
      $lData = array($lContentId, $lParentId, $lContentStr, $lSanStr, $lFormat, 'draft');
      CCms_Mod::setContent($lData);
  
      //insert language/version into `al_cms_ref_lang`
      $lData = array($lParentId, $lContentId, $aContent['version'], $lLanguage);
      $lCmsDat -> setLangVer($lData);
    }
  
    //set category into `al_cms_ref_category`
    $lCmsDat -> setCategory($lContentId, $lCategory); //check if exists
  
    return $lContentId;
  }

  protected function moveFile($aFilename, $aFolderKey) {
  	$lDir = $this->getPar('hotfolder') . DS . $aFolderKey;
    $lBase = basename($aFilename);
    $lNewName = $lDir.DS.$lBase;
    rename($aFilename, $lNewName);
  }
}