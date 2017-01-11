<?php
/**
 * Jobs: Data Modification
 *
 *  ABSTRACT! Description (bleibt CJob_Mod)
 *
 * @package    JOB
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 8721 $
 * @date $Date: 2015-05-11 11:40:08 +0100 (Mon, 11 May 2015) $
 * @author $Author: jwetherill $
 */
abstract class CInc_Job_Cms_Mod extends CCor_Mod_Base {

  public function __construct($aSrc, $aJobId = 0, $aJob = array()) {
    $this -> mSrc = $aSrc;
    $this -> mJobId = $aJobId;
    $this -> mJob = $aJob;
    
    $this -> mMetadata = $this -> getMetadata();
	$this -> mPhraseTypes = CCor_Res::extract('code', 'eve_phrase', 'crpmaster');
	$this -> mJobTyp = $this -> mPhraseTypes[$this -> mSrc];
	
	$lPhraseFields = CCor_Cfg::get('job-cms.fields');
	$lKey = $lPhraseFields['client_key'];
	$this -> mClientKey = (empty($this -> mJob[$lKey])) ? '' : $this -> mJob[$lKey]; //find product jobid try get from there
  }

  public function getPost(ICor_Req $aReq, $aOld = TRUE) {
    $this -> mVal = $this -> mUpd = $this -> mMeta = array();

    $this -> mProdUpd = $aReq -> getVal('ref_update'); //product reference update boolean
    $this -> mReqVal = $aReq -> getVal('val'); //values from form (content)
    $this -> mReqOld = $aReq -> getVal('old'); //old values from form (content)
    
    $this -> mReqCont = $aReq -> getVal('content'); // other information for the content (cid, pid, ver)
    $this -> mReqMeta = $aReq -> getVal('meta'); //metadata associated with content (type, layout, position, metadata)
    
    foreach ($this -> mReqMeta as $lKey => $lVal) {
      if(strpos($lKey, "_") !== FALSE){
        list($lCategory, $lIdx, $lLang, $lTyp) = explode("_", $lKey);
        
        if($lTyp == 'type')
          $this -> mMeta[$lCategory][$lIdx][$lTyp] = $lVal;
        else if($lTyp == 'meta')
          $this -> mMeta[$lCategory][$lIdx][$lTyp][$lLang] = $lVal;  
        else
          $this -> mMeta[$lCategory][$lIdx][$lTyp][] = $lVal; 
      }
    }

    foreach ($this -> mReqVal as $lKey => $lNew) {
      if(empty($lNew)) continue;

      list($lCategory, $lIdx, $lLang) = explode("_", $lKey);
      $lMetadata = $this -> mMetadata;
      $lPosition = (array_key_exists('position', $this -> mMeta[$lCategory][$lIdx])) ? $this -> mMeta[$lCategory][$lIdx]['position'] : '';
      $lType = (array_key_exists('type', $this -> mMeta[$lCategory][$lIdx])) ? $this -> mMeta[$lCategory][$lIdx]['type'] : '';
      $lLayout = (array_key_exists('layout', $this -> mMeta[$lCategory][$lIdx])) ? $this -> mMeta[$lCategory][$lIdx]['layout'][0] : 'memo';
      $lStatus = (array_key_exists('status', $this -> mMeta[$lCategory][$lIdx])) ? $this -> mMeta[$lCategory][$lIdx]['status'][0] : 'draft';
      $lContGroup = (array_key_exists('group', $this -> mMeta[$lCategory][$lIdx])) ? $this -> mMeta[$lCategory][$lIdx]['group'][0] : '1';
      $lContNtn = (array_key_exists('ntn', $this -> mMeta[$lCategory][$lIdx])) ? $this -> mMeta[$lCategory][$lIdx]['ntn'][0] : '';
      $lContPackTypes = (array_key_exists('packtypes', $this -> mMeta[$lCategory][$lIdx])) ? $this -> mMeta[$lCategory][$lIdx]['packtypes'][0] : '';
      if(array_key_exists('meta', $this -> mMeta[$lCategory][$lIdx])){
        if(!empty($this -> mMeta[$lCategory][$lIdx]['meta'][$lLang])){
          $lMetadata = array( $this -> mMeta[$lCategory][$lIdx]['meta'][$lLang]);
          $lMetadata = array_merge($lMetadata, $this -> mMetadata);
        }
      }
      
      $lAllowedTags = '<strong><p><span><em><u><i><b>';
      $lFormat = ($lLayout == 'rich') ? strip_tags(ereg_replace("[[:cntrl:]]", " ", $lNew), $lAllowedTags) : NULL;
      $lNew = trim(strip_tags(ereg_replace("[[:cntrl:]]", " ", $lNew)));
      $lArr = array(
        'content_id' => $this -> mReqCont[$lKey."_cid"],
        'parent_id' => $this -> mReqCont[$lKey."_pid"],
      	'content' => $lNew,
        'format' => $lFormat,
        'status' => $lStatus,
        'language' => strtoupper($lLang),
        'version' => $this -> mReqCont[$lKey."_ver"],
      	'position' => $lPosition,
        'group' => $lContGroup,
      	'type' => $lType,
        'metadata' => $lMetadata,
        'ntn' => $lContNtn,
        'packtypes' => $lContPackTypes,
        'layout' => $lLayout
      );
      
      $lGroup = $lCategory."_".$lIdx;
      if ($aOld){
        $lOld = (array_key_exists($lKey, $this -> mReqOld)) ? $this -> mReqOld[$lKey] : "";
        if( (string)$lNew !== (string)$lOld && $lOld !== "") {
          $this -> mUpd[$lCategory][$lGroup][] = $lArr;
        }
      }
      
      $this -> mVal[$lCategory][$lGroup][] = $lArr;
    }
    
    $this -> checkProtocol();
    self::deleteJobRef($this -> mJobId);
  }

  protected function doInsert() {
    $lRet = array();
    foreach ($this -> mVal as $lTag => $lArr) {
      $lAdd = array( "category" => $lTag );
      foreach($lArr as $lGroupName => $lGroup) {
        $lParentId = ($lGroup[0]['parent_id'] == 0) ? self::getMaxParent(0, $lRet) : $lGroup[0]['parent_id'];
        foreach($lGroup as $lIdx => $lCont) {
          $lCont['parent_id'] = $lParentId;
          $lRet[] = array_merge($lCont, $lAdd);
        }
      }
    }
    
    $lRes = $this -> processData($lRet);
      
    return $lRes;
  }
  
  //TODO: try to rework to include $this -> mOld, $this -> mVal, $this -> mUpd
  public function hasChanged() {
    return TRUE;
  }
  
  protected function doUpdate() {
    $lRes = $this -> doInsert();

    if($this -> mJobTyp == 'product' && !empty($lValues) && $this -> mProdUpd == 'yes') { //product update passed down to jobs
      $this -> updateJobProduct($this -> mJobId);
    }
    
    return $lRes;
  }

  protected function doDelete($aId) {
    return '';
  }
  
  /**
   * Process the content
   * @param array $aVals
   * @return boolean - Succession boolean
   */
  protected function processData($aVals) {
    $lValues = $aVals;
    $lCmsDat = new CCms_Mod();
	
	if($this -> mJobTyp == 'product' && !empty($lValues) && !empty($this -> mClientKey)){
	   self::resetProductRef($this -> mClientKey); //remove client_key from content before refreshing the code reference
	}

    foreach($lValues as $lIndex => $lContent) {
      $lLayout = $lContent['layout'];
      $lContentId = (strpos($lLayout, 'nutri') > -1) ? $this -> processNutrition($lContent) : $this -> processContent($lContent);
      if(strpos($lLayout, 'nutri') > -1){
        $lContent['metadata'] = array($lContent['language']);
      }

      //set client_key reference into `al_cms_ref_product`
      if ($this -> mJobTyp == 'product' && !empty($this -> mClientKey)) {
        $lCmsDat -> setClientKey($lContentId, $this -> mClientKey);
      }
      
      //set job references + metadata into `al_cms_ref_job`
      $lCategory = $lContent['category'];
      $lCategoryCheck = CCms_Mod::checkCategory($lContent);
      if($lCategoryCheck) {
        $lPosition = (!empty($lContent['position'])) ? implode(",", $lContent['position']) : '';
        $lType = ($this -> mJobTyp == 'product') ? 'product' : $lContent['type'];
        self::setJobRef($this -> mJobId, $lContentId, $lPosition, $lType, $lContent['group'], $lContent['metadata'], $lContent['layout'], 0, $lContent['status'], $lContent['ntn'], $lContent['packtypes']);
      } else {
        $lMsg = lan("lib.content").': "'.$lContent['content'].'" '.lan("lib.phrase-used").' '.$lCategory;
        $this -> msg($lMsg, mtUser, mlError);
      }
    }
	
    return TRUE;
  }

  /**
   * Process the nutritional value
   * @param array $aVals
   * @return $lContentId - contentid
   */
  protected function processNutrition($aContent) {
    $lContentStr = $aContent['content'];
    $lCategory = $aContent['category'];
    
    $lContentId = CCms_Mod::checkNutri($lContentStr, $lCategory);
    if($lContentId <= 0){
      $lData = array($lCategory, $lContentStr);
      $lContentId = CCms_Mod::setNutri($lData);
    }
    
    return $lContentId;
  }

  /**
   * Process the content values
   * @param array $aVals
   * @return $lContentId - contentid
   */
  protected function processContent($aContent) {
    $lCmsDat = new CCms_Mod();
    $lTyp = ($this -> mJobTyp == 'job') ? 'content' : $this -> mJobTyp;
    
    $lContentId = $aContent['content_id'];
    $lParentId = $aContent['parent_id'];
    $lLanguage = $aContent['language'];
    $lCategory = $aContent['category'];
    $lContentStr = $aContent['content'];
    $lContentFmt = $aContent['format'];
    $lStatus = ($lLanguage == 'MA' && $aContent['status'] == 'draft' && $aContent['type'] == $lTyp) ? CCor_Cfg::get('phrase.master.status', $aContent['status']) : $aContent['status'];

    $lCheck = CCms_Mod::contentExist($lContentStr, $lLanguage); //search for similar phrase with language
    $lInsertContent = ($lCheck > 0) ? FALSE : TRUE; //don't insert content
    if(intval($lContentId) > 0) {
      if($lCheck < 1) { //if new content
        $aContent['version'] += 1;
        $lStatus = 'draft';
      } else {
        $lInsertContent = TRUE;
      }
    }
    $lContentId = ($lCheck > 0) ? $lCheck : CCms_Mod::getMax('content_id');
    
    if($lInsertContent) { //perform if new content
      //get contents tokens and sanitise string
      $lSanStr = CCms_Sanitiser::sanitise($lContentStr, $lCategory, $lLanguage, TRUE);
    
      //insert content into `al_cms_content`
      $lData = array($lContentId, $lParentId, $lContentStr, $lSanStr, $lContentFmt, $lStatus);
      CCms_Mod::setContent($lData);
    
      //insert language/version into `al_cms_ref_lang`
      $lData = array($lParentId, $lContentId, $aContent['version'], $lLanguage);
      $lCmsDat -> setLangVer($lData);
    }
    
    //set category into `al_cms_ref_category`
    $lCmsDat -> setCategory($lContentId, $lCategory); //check if exists
    
    //set metadata into `al_cms_ref_meta`
    $lCmsDat -> setMetadata($lContentId, $aContent['metadata']);
    
    return $lContentId;
  }

  /**
   * Function to insert record in job history for content changes
   */
  protected function checkProtocol() {
    return TRUE;
  }
  
  public static function getMasterData($aParentId) {
    $lSql = 'SELECT `content_id` FROM (SELECT `parent_id`, max(`version`) AS maxver, `language` ';
    $lSql.= 'FROM `al_cms_ref_lang` WHERE `language`="MA" GROUP BY `parent_id`,`language`) as a ';
    $lSql.= 'INNER JOIN `al_cms_ref_lang` b ON (a.`parent_id`=b.`parent_id` AND a.`maxver`=b.`version` AND a.`language`=b.`language`) ';
    $lSql.= 'WHERE a.`parent_id`='.esc($aParentId).' AND a.`language`="MA"';
    
    return CCor_Qry::getStr($lSql);
  }
  
  /**
   * Set jobs related content references
   * @param number $aCid - content_id
   * @param string $aPos - position (BOP/FOP/SOP)
   * @param string $aTyp - type of content (content/product)
   * @param string $aMeta - metadata used against content for job
   * @param string $aLayout - type of layout used on job for content
   * @param number $aTemp - template id is set if not zero
   * @param number $aJobId - different jobid to use
   * @return number - inserted id in `al_cms_ref_job`
   */
  public static function setJobRef($aJobId, $aCid, $aPos, $aTyp, $aGroup, $aMeta, $aLayout, $aTemp = 0, $aStatus='draft', $aNoTranslation = NULL, $aPackTypes = NULL) {
    $lUsr = CCor_Usr::getInstance();
    $lTemplateId = ($aTemp == 0) ? $lUsr -> getPref('phrase.'.$aJobId.'.template', '') : $aTemp;
    $lTemplateId = (empty($lTemplateId)) ? 'NULL' : esc($lTemplateId);
    $lMetadata = isset($aMeta[0]) ? esc($aMeta[0]) : 'NULL';
    $lNoTranslation = (empty($aNoTranslation)) ? 'NULL' : esc($aNoTranslation);
    $lPackTypes = (empty($aPackTypes)) ? 'NULL' : esc($aPackTypes);
    
    $lCountSql = 'SELECT count(*) FROM `al_cms_ref_job` WHERE `jobid`='.esc($aJobId).' AND `content_id`='.esc($aCid);
    $lCountSql.= (strpos($aLayout, 'nutri') > -1) ? ' AND `group`='.esc($aGroup) : '';
    $lCount = CCor_Qry::getInt($lCountSql);
    if($lCount < 1) {
      $lSql = 'INSERT INTO `al_cms_ref_job` (`jobid`, `template_id`, `content_id`, `status`, `type`, `group`, `position`, `metadata`, `layout`, `ntn`, `packtypes`) ';
      $lSql.= 'VALUES ('.esc($aJobId).', '.$lTemplateId.', '.esc($aCid).', '.esc($aStatus).', '.esc($aTyp).', '.esc($aGroup).', '.esc($aPos).', '.$lMetadata.', '.esc($aLayout).', '.$lNoTranslation.', '.$lPackTypes.')';
      $lQry = new CCor_Qry($lSql);
      return $lQry -> getInsertId();
    }
    
    return FALSE;
  }
  
  public static function setJobStatus($aJobId, $aCont) {
    $lSql = 'UPDATE `al_cms_ref_job` SET `status`='.esc($aCont['status']).' WHERE `jobid`='.esc($aJobId).' AND `content_id`='.esc($aCont['content_id']);
    CCor_Qry::exec($lSql);
    
  }
  
  public static function getJobRef($aContentId, $aJobId) {
    $lSql = 'SELECT * FROM `al_cms_ref_job` WHERE `content_id`='.esc($aContentId).' AND `jobid`='.esc($aJobId);
    $lRet = new CCor_Qry($lSql);
    
    return $lRet;
  }
  
  protected function deleteJobRef($aJobId, $aCid = 0) {
    $lSql = 'DELETE FROM `al_cms_ref_job` WHERE `jobid`='.esc($aJobId);
    $lSql.= ($aCid == 0) ? '' : ' AND `content_id`='.esc($aCid);
    CCor_Qry::exec($lSql);
  }
  
  public static function updateJobRef($aContentId, $aOldContentId, $aJobId) {
    $lSql = 'UPDATE `al_cms_ref_job` SET `content_id`='.esc($aContentId).' WHERE `jobid`='.esc($aJobId).' AND `content_id`='.esc($aOldContentId);
    CCor_Qry::exec($lSql);
  }
  
  public static function resetJobRefTiming($aJobId) {
    $lSql = 'UPDATE `al_cms_ref_job` SET `lastchange`=CURRENT_TIMESTAMP WHERE `jobid`='.esc($aJobId);
    CCor_Qry::exec($lSql);
  }

  /**
   * Get Suggestions & Comments from al_cms_notes
   * @param string $aRef - client key
   */
  public static function getNotes($aCid, $aJobId, $aSubloopId, $aStateId, $aTask) {
  	$lRet = array(0,'','');
    
  	$lSql = 'SELECT DISTINCT `status`, `suggestion`, `comment` FROM `al_cms_notes` ';
  	$lSql.= 'WHERE `content_id`='.esc($aCid).' AND `jobid`='.esc($aJobId).' ORDER BY id DESC';
  	$lQry = new CCor_Qry($lSql);
  	foreach($lQry as $lKey => $lRow) {
  	  $lStatus = $lRow['status'];
  	  $lSuggestion = $lRow['suggestion'];
  	  $lComment = $lRow['comment'];

  	  $lRet = array($lStatus, $lSuggestion, $lComment);
  	  if( $lStatus == 3 || (in_array($lStatus, array(1,2)) && (!empty($lSuggestion) || !empty($lComment))) ) {
  	    break;
  	  }
  	}
  	
  	return $lRet;
  }
  
  public static function setNotes($aJobId, $aStateId, $aSubloopId, $aCont) {
    list($lJobId, $lStateId, $lSubloopId, $lCont) = array($aJobId, $aStateId, $aSubloopId, $aCont);
    $lUsr = CCor_Usr::getInstance();
    
    $lCountSql = 'SELECT count(*) FROM `al_cms_notes` ';
    $lCountSql.= 'WHERE `jobid`='.esc($lJobId).' AND `user_id`='.$lUsr->getId().' ';
    $lCountSql.= 'AND `state_id`='.esc($lStateId).' AND `sub_loop_id`='.esc($lSubloopId).' ';
    $lCountSql.= 'AND `content_id`='.esc($lCont['content_id']);
    $lCount = CCor_Qry::getInt($lCountSql);
    if($lCount < 1) {
      $lSql = 'INSERT INTO `al_cms_notes` (`jobid`, `user_id`, `state_id`, `sub_loop_id`, `content_id`, `status`, `suggestion`, `comment`) VALUES ';
      $lSql.= '('.esc($lJobId).', '.$lUsr->getId().', '.esc($lStateId).', '.esc($lSubloopId).', ';
      $lSql.= esc($lCont['content_id']).', '.esc($lCont['apl_state']).', ';
      $lSql.= esc($lCont['suggestion']).', '.esc($lCont['comment']).');';
    } else {
      $lSql = 'UPDATE `al_cms_notes` SET ';
      $lSql.= '`status`='.esc($lCont['apl_state']).', ';
      $lSql.= '`suggestion`='.esc($lCont['suggestion']).', ';
      $lSql.= '`comment`='.esc($lCont['comment']).' ';
      $lSql.= 'WHERE `jobid`='.esc($lJobId).' AND `user_id`='.$lUsr->getId().' ';
      $lSql.= 'AND `state_id`='.esc($lStateId).' AND `sub_loop_id`='.esc($lSubloopId).' ';
      $lSql.= 'AND `content_id`='.esc($lCont['content_id']);
    }
    CCor_Qry::exec($lSql);
  }

  /**
   * Get the maximum parent_id from the system
   * @param unknown $aPid - Parent ID
   * @param unknown $aValues - Content
   * @return integer $lParentId - maximum parent id
   */
  protected static function getMaxParent($aPid, $aValues){
    $lValues = $aValues;
    $lParentId = ($aPid > 0) ? $aPid : CCms_Mod::getMax('parent_id');
     
    if (is_array($lValues)) {
      foreach ($lValues as $lContent) {
        if (isset($lContent['parent_id']) && $lContent['parent_id'] == $lParentId) {
          $lParentId = self::getMaxParent($lParentId+1, $lValues);
        }
      }
    }
  
    return $lParentId;
  }

  /**
   * Get metadata used on job form for job currently on
   * @return array $lRet - array of job field ids with job values
   */
  public function getMetadata() {
    $lRet = array();
      
    $lFie = CCor_Res::getByKey('alias', 'fie', array("mand" => MID));
    foreach ($lFie as $lKey => $lDef) {
      $lFla = intval($lDef['flags']);
      $lId = intval($lDef['id']);
      if (bitset($lFla, ffMetadata)) {
        $lRet[$lId] = $this -> mJob[$lKey];
      }
    }
    
    return $lRet;
  }

  /**
   * Remove client_key from product content
   * @param string $aRef - client key
   */
  protected static function resetProductRef($aRef) {
    if($aRef !== 'NULL' && !empty($aRef)){
      $lSql = 'DELETE FROM `al_cms_ref_product` WHERE `client_key`='.esc($aRef);
      CCor_Qry::exec($lSql);
    }
  }

  /**
   * Refresh product information for job if client_key field is changed
   * @param string $aVal
   */
  public function updateProductRefs($aVal) {
    if($this -> mPhraseTypes[$this -> mSrc] !== 'job') return;
  
    //get new product references
    $lSrc = array_search('product', $this -> mPhraseTypes);
    if($lSrc == FALSE) return;
  
    //get job template id
    $lSql = 'SELECT `template_id` FROM `al_cms_ref_job` WHERE `jobid`='.esc($this -> mJobId);
    $lTemplateId = CCor_Qry::getInt($lSql);
    $lTemplateId = (!empty($lTemplateId)) ? $lTemplateId : 'NULL';
  
    //remove product references from jobid
    $lSql = 'DELETE FROM `al_cms_ref_job` WHERE `jobid`='.esc($this -> mJobId).' AND `type`="product";';
    CCor_Qry::exec($lSql);
  
    //get all product references from product job
    $lProductJobs = CCms_Job::getClientKeyJobs('product', $aVal);
    foreach($lProductJobs as $lJobId => $lJobVal) {
      $lSql = 'SELECT * FROM `al_cms_ref_job` WHERE `jobid`='.esc($lJobId).' AND `type`="product";';
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        $lMetadata = ($lRow['metadata'] != "NULL") ? esc($lRow['metadata']) : 'NULL';
        $lSql = 'REPLACE INTO `al_cms_ref_job` SET `jobid`='.esc($this -> mJobId).', `template_id`='.$lTemplateId.', `content_id`='.$lRow['content_id'];
        $lSql.= ', `group`='.esc($lRow['group']).', `type`="product", `position`='.esc($lRow['position']).', `metadata`='.$lMetadata;
        $lSql.= ', `layout`='.esc($lRow['layout']).';';
        CCor_Qry::exec($lSql);
      }
    }
  
    //add history record
    $lMod = new CJob_His($this -> mSrc, $this -> mJobId);
    $lMod -> add(htEdit, lan('job.changes'), lan('job.code.change').' to '.$aVal);
  }

  /**
   * Update related jobs with new/updated product content from product job type
   */
  public function updateJobProduct($aProdId = 0, $aJobId = 0) {
    if($aProdId > 0) {
    	$lProdId = $aProdId;
  	} else {
  		$lProductJobs = CCms_Job::getClientKeyJobs('product', $this -> mClientKey);
  		$lProdId = current(array_keys($lProductJobs));
  	}
    $lProduct = CCms_Job::getProductRefs($lProdId); //get product references
    
    //get jobids with client_key
    $this -> mJobs = CCms_Job::getClientKeyJobs('job', $this -> mClientKey);
    foreach($this -> mJobs as $lJobId => $lJobVal) { //cycle through jobs
      if($aJobId > 0 && $lJobId != $aJobId) continue;
      
      $lJob = CCms_Job::getProductRefs($lJobId); //get job references and compare
      
      $lJobCont = array_keys($lJob);
      $lProdCont = array_keys($lProduct);
      
      $lAdd = array_diff($lProdCont, $lJobCont); //new product info && $aProcess == 'insert'
      $lRemove = array_diff($lJobCont, $lProdCont); //version update
      $lSame = array_intersect($lJobCont, $lProdCont); //same refresh layout from product
      
      //add reference to job from product update/insert
      foreach($lAdd as $lCid) {
        $lProd = $lProduct[$lCid];
        $lMeta = array($lProd['metadata']);
        self::setJobRef($lJobId, $lCid, $lProd['position'], 'product', $lProd['group'], $lMeta, $lProd['layout'], $lJob['template_id']);
      }
      
      foreach($lRemove as $lCid) {
        self::deleteJobRef($lJobId, $lCid);
      }
      
      foreach($lSame as $lCid) {
        $lProd = $lProduct[$lCid];
        $lMeta = array($lProd['metadata']);
        self::setJobRef($lJobId, $lCid, $lProd['position'], 'product', $lProd['group'], $lMeta, $lProd['layout'], $lJob['template_id']);
      }
      
      self::resetJobRefTiming($lJobId);
    }
    
    return TRUE;
  }
  
  public static function copyPhraseContent($aOrigJobId, $aOrigSrc, $aJobId, $aSrc) {
    list($lOrigJobId, $lOrigSrc, $lJobId, $lSrc) = array($aOrigJobId, $aOrigSrc, $aJobId, $aSrc);
  
    $lType = CCor_Res::extract('code', 'eve_phrase', 'crpmaster');
    $lOrigPhraseTyp = $lType[$lOrigSrc];
    $lPhraseTyp = $lType[$lSrc];
  
    if($lPhraseTyp == 'job' && $lOrigPhraseTyp == 'job') {
      $lPhraseFields = CCor_Cfg::get('job-cms.fields');
      $lPackTypes = $lPhraseFields['packtype'];
      
      if(CCor_Cfg::get("phrase.job-rel.meta", FALSE)) {
      	$lDat = 'CJob_'.$lSrc.'_Dat';
      	$lJob = new $lDat();
      	$lJob -> load($lJobId);
      }
      
      //copy al_cms_ref_job from old job to new one
      $lQry = new CCor_Qry('SELECT * FROM `al_cms_ref_job` WHERE `jobid`='.esc($lOrigJobId).' AND `type`="content";');
      foreach($lQry as $lRow) {
        if(CCor_Cfg::get("phrase.job-rel.meta", FALSE) !== FALSE && !empty($lRow['packtypes'])) {
          $lJobPackTypes = array_filter( array_map('trim', explode(",", $lJob[$lPackTypes])) );
          $lContPackTypes = array_filter( array_map('trim', explode(" ", $lRow['packtypes'])) );
          
          if(count(array_intersect($lContPackTypes, $lJobPackTypes)) < 1) continue;
        }
        
        $lMetadata = ($lRow['metadata'] != NULL) ? array($lRow['metadata']) : array();
        CJob_Cms_Mod::setJobRef($lJobId, $lRow['content_id'], $lRow['position'], $lRow['type'], $lRow['group'], $lMetadata, $lRow['layout'], $lRow['template_id']);
      }
    }
  }
}