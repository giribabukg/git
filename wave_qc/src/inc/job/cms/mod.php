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
	$this -> mOrder = array();
	$this -> mPhraseTypes = CCor_Res::extract('code', 'eve_phrase', 'crpmaster');
	$this -> mJobTyp = $this -> mPhraseTypes[$this -> mSrc];
	$this -> mClientKey = (empty($this -> mJob['client_key'])) ? 'NULL' : $this -> mJob['client_key']; //find product jobid try get from there
	$this -> mProductJobs = $this -> getClientKeyJobs();
  }

  public function getJobId() {
    return $this->mJobId;
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
      if(array_key_exists('meta', $this -> mMeta[$lCategory][$lIdx])){
        if(!empty($this -> mMeta[$lCategory][$lIdx]['meta'][$lLang])){
          $lMetadata = array( $this -> mMeta[$lCategory][$lIdx]['meta'][$lLang]);
          $lMetadata = array_merge($lMetadata, $this -> mMetadata);
        }
      }
      
      $lArr = array(
        'content_id' => $this -> mReqCont[$lKey."_cid"],
        'parent_id' => $this -> mReqCont[$lKey."_pid"],
      	'content' => $lNew, 
        'language' => strtoupper($lLang),
        'version' => $this -> mReqCont[$lKey."_ver"],
      	'position' => $lPosition,
      	'type' => $lType,
        'metadata' => $lMetadata,
        'layout' => $lLayout
      );
      
      $lGroup = $lCategory."_".$lIdx;
      if ($aOld){
        $lOld = (array_key_exists($lKey, $this -> mReqOld)) ? $this -> mReqOld[$lKey] : "";
        if( (string)$lNew !== (string)$lOld && $lOld !== "") {
          $this -> mUpd[$lCategory][$lGroup][] = $lArr;
        } else {
          $this -> mVal[$lCategory][$lGroup][] = $lArr;
        }
      } else {
        $this -> mVal[$lCategory][$lGroup][] = $lArr;
      }
    }
    
    $this -> clearJobRef();
  }

  protected function doInsert() {
    $lValues = $this -> getFormValues();
    $lRes = $this -> processData($lValues);
      
    return $lRes;
  }
  
  public function hasChanged() {
    return TRUE;
  }
  
  protected function doUpdate() {
    $this -> doInsert();
    $lValues = $this -> getFormValues('upd');
    $lRes = $this -> processData($lValues);

    if($this -> mJobTyp == 'product' && !empty($lValues) && $this -> mProdUpd == 'yes') { //product update passed down to jobs
      $this -> updateJobWithProductInfo($this -> mJobId);
    }
    
    return $lRes;
  }

  protected function doDelete($aId) {
    return '';
  }
  
  /**
   * Gather content form values for processing
   * @param string $aType - type of values to gather
   * @return array $lRet
   */
  protected function getFormValues($aType = 'val') {
    $lRet = array();
    $lValues = ($aType == 'val') ? $this -> mVal : $this -> mUpd;
    
    foreach ($lValues as $lTag => $lArr) {
      $lAdd = array( "category" => $lTag );
    
      foreach($lArr as $lGroupName => $lGroup) {
        $lParentId = ($lGroup[0]['parent_id'] == 0) ? $this->getMaxParent(0, $lRet) : $lGroup[0]['parent_id'];
    
        foreach($lGroup as $lIdx => $lCont) {
          $lCont['parent_id'] = $lParentId;
    
          $lRet[] = array_merge($lCont, $lAdd);
        }
      }
    }
    
    return $lRet;
  }
  
  /**
   * Search content array when loading job to see if we already have content_id loaded
   * @param array $aArr - loaded content
   * @param string $aKey - key to search for
   * @param string $aVal - value to search for
   * @return array $lRet - content if found in $aArr, otherwise empty array
   */
  protected function searchContent($aArr, $aKey, $aVal) {
    $lRet = array();
  
    if (is_array($aArr)) {
      if (isset($aArr[$aKey]) && $aArr[$aKey] == $aVal) {
        $lRet[] = $aArr;
      }
  
      foreach ($aArr as $aSubArr) {
        $lRet = array_merge($lRet, self::searchContent($aSubArr, $aKey, $aVal));
      }
    }
  
    return $lRet;
  }

  /**
   * Process the content given
   * @param array $aVals
   * @return boolean - Succession boolean
   */
  protected function processData($aVals) {
    $lValues = $aVals;
	$lOrdering = array();
    $lCmsDat = new CCms_Mod();
	
	if($this -> mJobTyp == 'product' && !empty($lValues)){
	   $this -> resetProductRef($this -> mClientKey); //remove client_key from content before refreshing the code reference
	}

    foreach($lValues as $lIndex => $lContent) {
      $lLayout = $lContent['layout'];
      $lContentId = (strpos($lLayout, 'nutri') > -1) ? $this -> processNutrition($lContent) : $this -> processContent($lContent);
      if(strpos($lLayout, 'nutri') > -1){
        $lContent['metadata'] = array();
        $lContent['metadata'][0] = $lContent['language'];
      }

      //set client_key reference into `al_cms_ref_product`
      if ($this -> mJobTyp == 'product') {
        $lCmsDat -> setClientKey($lContentId, $this -> mClientKey);
      }
      
      //set job references + metadata into `al_cms_ref_job`
      $lCategory = $lContent['category'];
      if($lContent['language'] == 'MA') {
        $lOrdering[$lCategory] = (!array_key_exists($lCategory, $lOrdering)) ? 1 : intval($lOrdering[$lCategory]) + 1;
      }
      if(!array_key_exists($lCategory, $lOrdering)) {
        $lOrdering[$lCategory] = 1;
      }
      $lPosition = (!empty($lContent['position'])) ? implode(",", $lContent['position']) : '';
      $lType = ($this -> mJobTyp == 'product') ? 'product' : $lContent['type'];
      $this -> setJobRef($lContentId, $lPosition, $lType, $lContent['metadata'], $lContent['layout'], $lOrdering[$lCategory]);
    }
	
    return TRUE;
  }
  
  protected function processNutrition($aContent) {
    $lCmsDat = new CCms_Mod();
    $lContentStr = $aContent['content'];
    $lCategory = $aContent['category'];
    
    $lContentId = $lCmsDat -> checkNutri($lContentStr, $lCategory);
    if($lContentId <= 0){
      $lData = array($lCategory, $lContentStr);
      $lContentId = $lCmsDat -> setNutri($lData);
    }
    
    return $lContentId;
  }
  
  protected function processContent($aContent) {
    $lCmsDat = new CCms_Mod();
    $lInsertContent = TRUE;
    
    $lContentId = $aContent['content_id'];
    $lParentId = $aContent['parent_id'];
    $lLanguage = $aContent['language'];
    $lContentStr = $aContent['content'];
      
    if(intval($lContentId) == 0 || empty($lContentId)) { //if new content
      $lCheck = $lCmsDat -> check($lContentStr, $lLanguage); //search for similar phrase with language
      $lContentId = ($lCheck > 0) ? $lCheck : CCms_Mod::getMax('content_id');
      $lInsertContent = ($lCheck > 0) ? FALSE : TRUE; //don't insert content
    }
    
    if($lInsertContent) { //perform if new content
      //get contents tokens and sanitise string
      $lSantiser = new CCms_Sanitiser();
      $lSanStr = $lSantiser -> sanitise($lContentStr, $lLanguage, TRUE);
    
      //insert content into `al_cms_content`
      $lData = array($lContentId, $lParentId, $lContentStr, $lSanStr);
      $lCmsDat -> setContent($lData);
    
      //insert language/version into `al_cms_ref_lang`
      $lData = array($lParentId, $lContentId, $aContent['version'], $lLanguage);
      $lCmsDat -> setLangVer($lData);
    }
    
    //set category into `al_cms_ref_category`
    $lCmsDat -> setCategory($lContentId, $aContent['category']); //check if exists
    
    //set metadata into `al_cms_ref_meta`
    $lCmsDat -> setMetadata($lContentId, $aContent['metadata']);
    
    return $lContentId;
  }
    
  /**
   * Get the maximum parent_id from the system
   * @param unknown $aPid - Parent ID
   * @param unknown $aValues - Content
   * @return integer $lParentId - maximum parent id
   */
  protected function getMaxParent($aPid, $aValues){
    $lValues = $aValues;
    $lParentId = ($aPid > 0) ? $aPid : CCms_Mod::getMax('parent_id');
   
    if (is_array($lValues)) {
      foreach ($lValues as $lContent) {
        if (isset($lContent['parent_id']) && $lContent['parent_id'] == $lParentId) {
          $lParentId = $this -> getMaxParent($lParentId+1, $lValues);
        }
      }
    }
    
    return $lParentId;
  }
  
  /**
   * Load job or product information
   * @return array $lRet - content related to job
   */
  public function loadJobRef() {
    $lRet = array();
    $lUsr = CCor_Usr::getInstance();
    
    $this -> mIte = new CCor_TblIte('al_cms_ref_job'); //only store master language in job ref
    $this -> mIte -> addCnd('`jobid`='.esc($this -> getJobId()));
    $this -> mIte -> setOrder('id');
    $this -> mIte -> set2Order('type');
    $lRes = $this -> mIte -> getArray();
	
    foreach($lRes as $lIdx => $lCont){
      $lType = $lCont['type'];
      $lLayout = $lCont['layout'];
      
      $lRet = $this -> getTranslations($lCont, $lType, $lRet, $lLayout);
      
      $lTemplate = $lUsr -> getPref('phrase.'.$lCont['jobid'].'.template', '');
      if(empty($lTemplate) AND !empty($lCont['template_id'])) {
        $lUsr -> setPref('phrase.'.$lCont['jobid'].'.template', $lCont['template_id']); //set template if not set
      }
    }
    
    if(!array_key_exists('product', $lRet)){
      $lRet = $this -> getProductInformation($lRet);
    }
    krsort($lRet);
    
    return $lRet;
  }

  /**
   * Remove all job references before processing content
   */
  protected function clearJobRef() {
    $lSql = 'DELETE FROM `al_cms_ref_job` WHERE `jobid`='.esc($this -> getJobId());

    CCor_Qry::exec($lSql);
  }
  
  /**
   * Set jobs related content references
   * @param number $aCid - content_id
   * @param string $aPos - position (BOP/FOP/SOP)
   * @param string $aTyp - type of content (content/product)
   * @param string $aMeta - metadata used against content for job
   * @param string $aLayout - type of layout used on job for content
   * @param number $aOrder - order the content is within the job
   * @param number $aTemp - template id is set if not zero
   * @param number $aJobId - different jobid to use
   * @return number - inserted id in `al_cms_ref_job`
   */
  protected function setJobRef($aCid, $aPos, $aTyp, $aMeta, $aLayout, $aOrder, $aTemp = 0, $aJobId = 0) {
    $lUsr = CCor_Usr::getInstance();
    $lJobid = ($aJobId == 0) ? $this -> getJobId() : $aJobId;
    $lTemplateId = ($aTemp == 0) ? $lUsr -> getPref('phrase.'.$lJobid.'.template', '') : $aTemp;
    $lTemplateId = (empty($lTemplateId)) ? 'NULL' : $lTemplateId;
    $lMetadata = isset($aMeta[0]) ? esc($aMeta[0]) : 'NULL';
    
    $lSql = 'INSERT INTO `al_cms_ref_job` (`jobid`, `template_id`, `content_id`, `type`, `position`, `metadata`, `layout`, `order`) ';
    $lSql.= 'VALUES ('.esc($lJobid).', '.$lTemplateId.', '.$aCid.', '.esc($aTyp).', '.esc($aPos).', '.$lMetadata.', '.esc($aLayout).', '.$aOrder.')';
    $lQry = new CCor_Qry($lSql);
    
    return $lQry -> getInsertId();
  }

  /**
   * Load product information
   * @return array $lRet - product related to client_key on job form
   */
  protected function getProductInformation($aRet = array()){
    $lRet = $aRet;
    
    if($this -> mClientKey !== 'NULL' && !empty($this -> mClientKey)) {
      $lProdId = current(array_keys($this -> mProductJobs));
      $lRes = $this -> getContentRefs($lProdId); //get product references
      
      foreach($lRes as $lIdx => $lCont){
        $lType = $lCont['type'];
        $lLayout = $lCont['layout'];
      
        $lRet = $this -> getTranslations($lCont, $lType, $lRet, $lLayout);
      }
    }
    
    return $lRet;
  }
  
  /**
   * Get all translations for given job and content
   * @param array $aCont
   * @param unknown $aType
   * @param unknown $aRet
   * @param string $aLayout
   * @return Ambigous <unknown, multitype:number string unknown Ambigous <string, unknown> >
   */
  public function getTranslations($aCont, $aType, $aRet = array(), $aLayout = 'memo') {
    $lRet = $aRet;
    $lLayout = (!empty($aLayout)) ? $aLayout : 'memo';
    $lLangs = array_map('trim', explode(",", $this -> mJob['languages']));
    array_push($lLangs, "MA");
    
    if(strpos($lLayout, 'nutri') > -1) {
      $lCategory = 'Nutrition';
      
      if($aCont['content_id'] > 0) {
        $lNutri = CCms_Mod::getNutri($aCont['content_id']);
      
        $lRet[$aType][$lCategory][$lLayout][$aCont['metadata']][$lNutri['category']] = array(
            'content_id' => $lNutri['content_id'], 'parent_id' => $lNutri['parent_id'], 'content' => $lNutri['content'], 'language' => $aCont['metadata'],
            'version' => 1, 'position' => '', 'metadata' => $lNutri['category'], 'type' => $aType, 'layout' => $lLayout
        );
      }

      $lTpl = new CCor_Tpl();
      $lTpl -> openProjectFile('job/cms/'.$lLayout.'.htm');
      $lRows  = $lTpl -> findPatterns('val.');
      foreach($lLangs as $lLang) {
          foreach($lRows as $lRow) {
            if(!array_key_exists($lRow, $lRet[$aType][$lCategory][$lLayout][$lLang])) {
              $lRet[$aType][$lCategory]['nutri'][$lLang][$lRow] = array(
                  'content_id' => 0, 'parent_id' => 0, 'content' => '', 'language' => $lLang,
                  'version' => 1, 'position' => '', 'metadata' => $lRow, 'type' => $aType, 'layout' => $lLayout
              );
            }
          }
      }
    } else {
      if($aCont['content_id'] > 0) {
        $lArr = $this -> searchContent($lRet, 'content_id', $aCont['content_id']);
        if (!empty($lArr)) return $lRet;
        
        $lCmsDat = new CCms_Mod();
        $lMaster = $lCmsDat -> getContent($aCont['content_id']);
        $lParentId = $lMaster['parent_id'];
        $lCategory = $lMaster['categories'][0];
    
        $lExists = false;
        if(array_key_exists($aType, $lRet)){
          if(array_key_exists($lCategory, $lRet[$aType])) {
            if(array_key_exists($lLayout, $lRet[$aType][$lCategory])) {
              if(array_key_exists($lParentId, $lRet[$aType][$lCategory][$lLayout])){
                $lExists = true;
              }
            }
          }
        }
        
        if($lExists == false) {
          $lSql = 'SELECT `content_id` FROM `al_cms_content` WHERE `parent_id`='.esc($lParentId).' AND `mand`='.MID;
          $lQry = new CCor_Qry($lSql);
          foreach ($lQry as $lRow) {
            $lContent = ($lRow['content_id'] == $lMaster['content_id']) ? $lMaster : $lCmsDat -> getContent($lRow['content_id']);
            $lCategory = $lContent['categories'][0];
            $lLanguage = $lContent['language'];
            
            if(in_array($lLanguage, $lLangs)){
              $lRefFields = $this -> getJobReferenceField($lRow['content_id'], $aType);
              list($lPosition, $lMetadata) = explode(',', $lRefFields);
              
              $lRet[$aType][$lCategory][$lLayout][$lParentId][$lLanguage] = array(
                'content_id' => $lRow['content_id'], 'parent_id' => $lParentId,
                'content' => $lContent['content'], 'language' => $lLanguage, 'version' => $lContent['version'],
                'position' => $lPosition, 'metadata' => array($lMetadata), 'type' => $aType, 'layout' => $lLayout  
              );
            }
          }
        }
        
        //fill out any missing languages (no translations for them)
        foreach($lLangs as $lLang) {
          if(!array_key_exists($lLang, $lRet[$aType][$lCategory][$lLayout][$lParentId])) {
            $lRet[$aType][$lCategory][$lLayout][$lParentId][$lLang] = array(
                'content_id' => 0, 'parent_id' => $lParentId, 'content' => '', 'language' => $lLang,
                'version' => 1, 'position' => '', 'metadata' => '', 'type' => $aType, 'layout' => $lLayout
            );
          }
        }
      } else {
        $lCategory = $aCont['categories'][0];
  	    $lOrder = $this -> getOrder($lCategory);
        
  	    //fill out all languages for job
        foreach($lLangs as $lLang) {
          $lRet[$aType][$lCategory][$lLayout][$lOrder][$lLang] = array(
              'content_id' => 0, 'parent_id' => $aCont['parent_id'], 'content' => '', 'language' => $lLang,
              'version' => 1, 'position' => '', 'metadata' => '', 'type' => $aType, 'layout' => $lLayout
          );
        }
      }
    }
    
    return $lRet;
  }
  
  /**
   * Get the order a piece of content needs to be at for the job base on the category
   * @param string $aCat - catgory the content is related to
   * @return number - order for content in job
   */
  protected function getOrder($aCat) {
	if(array_key_exists($aCat, $this -> mOrder)){
		$this -> mOrder[$aCat] = intval($this -> mOrder[$aCat]) + 1;
	} else {
		$this -> mOrder[$aCat] = 1;
	}
	  
	return intval($this -> mOrder[$aCat]);
  }
  
  /**
   * Get layout from template if used otherwise get related job layout
   * @param number $lId - content id
   * @return string $lLayout - layout used by content id in job
   */
  public function getLayout($aId = 0) {
    $lId = intval($aId);
    $lLayout = 'memo';
    $lCat = CCms_Mod::getCategory($lId);
    $lTemplateId = CCor_Qry::getStr("SELECT `template_id` FROM `al_cms_ref_job` WHERE `jobid`=".esc($this -> getJobId())); //if job uses template
    
    if($lTemplateId == FALSE){
      $lSql = 'SELECT `layout` FROM `al_cms_ref_job` WHERE `content_id`='.$lId.' AND `jobid`='.esc($this -> getJobId()); //check if has reference
      $lLayout = CCor_Qry::getStr($lSql);
      if($lLayout == FALSE) {
        $lSql = 'SELECT `col2` as `layout` FROM `al_pck_items` WHERE domain="pclr" AND `mand`='.MID.' AND `col1`='.esc($lCat); //get from combo picklist
        return CCor_Qry::getStr($lSql);
      } else {
        return $lLayout;
      }
    } else {
      $lSql = 'SELECT `layout` FROM `al_cms_template` WHERE `template_id`='.esc($lTemplateId).' AND `category`='.esc($lCat);
      return CCor_Qry::getStr($lSql);
    }
  }
  
  /**
   * Add a history item to the job based on situation on the job/product content tab
   * @param integer $aType - category number for history entry
   * @param string $aSubject - subject for history item
   * @param string $aMsg - message for history item
   * @param string $aAdd - additional information
   */
  public function addHistory($aType, $aSubject, $aMsg = '', $aAdd = NULL) {
    $lMod = new CJob_His($this -> mSrc, $this -> mJobId);
    $lMod -> add($aType, $aSubject, $aMsg, $aAdd);
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
   * Get Job which relates to the client_key and src arguments
   * @return array - list of jobid related to client_key and src
   */
  protected function getClientKeyJobs($aType = 'product') {
    $lSrc = array_search($aType, $this -> mPhraseTypes); //get src for product job type
    if($this -> mClientKey == 'NULL' || empty($this -> mClientKey) || $lSrc == FALSE) return array();
    
    if (CCor_Cfg::get('job.writer.default') == 'portal') {
      $lIte = new CCor_TblIte('al_job_'.$lSrc.'_'.intval(MID), TRUE);
      $lIte -> addField('jobid');
      $lIte -> addCnd('webstatus >= 10');
      $lIte -> addCnd('client_key = '.esc($aClientKey));
      if($aType == 'product') $lIte -> setLimit('1');
    
      $lRes = $lIte -> getArray('jobid');
    } else {
      $lSql = 'SELECT `jobid` FROM `al_job_shadow_'.MID.'` WHERE `src`='.esc($lSrc).' AND `client_key`='.esc($aClientKey);
      $lRes = CCor_Qry::getStr($lSql);
    }
    
    return $lRes;
  }


  /**
   * Remove client_key from product content
   * @param string $aRef - client key
   */
  protected function resetProductRef($aRef) {
    if($aRef !== 'NULL' && !empty($aRef)){
      $lSql = 'DELETE FROM `al_cms_ref_product` WHERE `client_key`='.esc($aRef);
      CCor_Qry::exec($lSql);
    }
  }
  
  /**
   * Check if product has changed since last saved job
   */
  public function hasProductChanged() {
    $lProductJobs = $this -> getClientKeyJobs('product');
    $lProductLastChange = 0;
    foreach($lProductJobs as $lJobId => $lArr) {
      $lProductLastChange = $this -> getLastJobChange($lJobId);
    }
    $lJobLastChange = $this -> getLastJobChange($this -> getJobId());
    $lChange = $lProductLastChange - $lJobLastChange;
    
    return ($lChange > 0) ? TRUE : FALSE;
  }
  
  /**
   * Gets the last date a job reference has changed
   * @param number $aJobId
   */
  protected function getLastJobchange($aJobId = 0) {
    $lSql = 'SELECT MAX(`lastchange`) as `time` FROM `al_cms_ref_job` WHERE `jobid`='.esc($aJobId).' AND `type`="product"';
    $lRes = CCor_Qry::getStr($lSql);
    $lRes = (!empty($lRes)) ? $lRes : date("Y-m-d H:i:s");
    
    return strtotime($lRes);
  }
  
  /**
   * Get information from al_cms_ref_job table for job and content_id
   * @param number $aCid - content_id
   * @param string $aType - type of content
   * @return string $lRes - imploded array of position and metadata
   */
  protected function getJobReferenceField($aCid = 0, $aType = 'job') {
    $lJobs = ($aType == 'product') ? array_keys($this -> mProductJobs) : array($this -> getJobId());
    $lSql = 'SELECT DISTINCT `position`, `metadata` FROM `al_cms_ref_job` WHERE `content_id`='.esc($aCid).' AND `jobid` IN ("'.implode('","', $lJobs).'")';
    $lRes = CCor_Qry::getArrImp($lSql);
  
    return $lRes;
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
    foreach($this -> mProductJobs as $lJob => $lJobVal) {
      $lSql = 'SELECT * FROM `al_cms_ref_job` WHERE `jobid`='.esc($lJob).' AND `type`="product";';
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        $lMetadata = ($lRow['metadata'] != "NULL") ? esc($lRow['metadata']) : 'NULL';
        $lSql = 'INSERT INTO `al_cms_ref_job` SET `jobid`='.esc($this -> mJobId).', `template_id`='.$lTemplateId.', `content_id`='.$lRow['content_id'];
        $lSql.= ', `type`="product", `position`='.esc($lRow['position']).', `metadata`='.$lMetadata;
        $lSql.= ', `layout`='.esc($lRow['layout']).', `order`='.$lRow['order'].';';
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
  public function updateJobWithProductInfo($aProdId = 0, $aJobId = 0) {
    $lProdId = ($aProdId > 0) ? $aProdId : current(array_keys($this -> mProductJobs));
    $lProduct = $this -> getContentRefs($lProdId); //get product references
    
    //get jobids with client_key
    $this -> mJobs = $this -> getClientKeyJobs('job');
    foreach($this -> mJobs as $lJobId => $lJobVal) { //cycle through jobs
      if($aJobId > 0 && $lJobId != $aJobId) continue;
      
      $lJob = $this -> getContentRefs($lJobId); //get job references and compare
      
      $lJobCont = array_keys($lJob);
      $lProdCont = array_keys($lProduct);
      
      $lAdd = array_diff($lProdCont, $lJobCont); //new product info && $aProcess == 'insert'
      $lRemove = array_diff($lJobCont, $lProdCont); //version update
      $lSame = array_intersect($lJobCont, $lProdCont); //same refresh layout from product
      
      //add reference to job from product update/insert
      foreach($lAdd as $lCid) {
        $lProd = $lProduct[$lCid];
        $lMeta = array($lProd['metadata']);
        $this -> setJobRef($lCid, $lProd['position'], 'product', $lMeta, $lProd['layout'], $lProd['order'], $lJob['template_id'], $lJobId);
      }
      
      foreach($lRemove as $lCid) {
        $lSql = 'DELETE FROM `al_cms_ref_job` WHERE `jobid`='.esc($lJobId).' AND content_id='.esc($lCid);
        CCor_Qry::exec($lSql);
      }
      
      foreach($lSame as $lCid) {
        $lSql = 'DELETE FROM `al_cms_ref_job` WHERE `jobid`='.esc($lJobId).' AND content_id='.esc($lCid);
        CCor_Qry::exec($lSql);
        
        $lProd = $lProduct[$lCid];
        $lMeta = array($lProd['metadata']);
        $this -> setJobRef($lCid, $lProd['position'], 'product', $lMeta, $lProd['layout'], $lProd['order'], $lJob['template_id'], $lJobId);
      }
    }
    
    return TRUE;
  }
  
  /**
   * Gather product content details for given jobid to be used in updateJobWithProductInfo() function
   * @param number $aJobId
   * @return array $lRet
   */
  protected function getContentRefs($aJobId = 0) {
    $lRet = array();
    
    $lIte = new CCor_TblIte('al_cms_ref_job'); //only store master language in job ref
    $lIte -> addCnd('`jobid`='.esc($aJobId));
    $lIte -> addCnd('`type`="product"');
    $lIte -> setOrder('id');
    $lIte -> set2Order('type');
    $lRes = $lIte -> getArray();
    
    foreach($lRes as $lIdx => $lCont){
      $lRet[ $lCont['content_id'] ] = array(
          'template_id' => $lCont['template_id'],
          'content_id' => $lCont['content_id'], 
          'type' => $lCont['type'],
          'position' => $lCont['position'], 
          'metadata' => $lCont['metadata'], 
          'layout' => $lCont['layout'],
          'order' => $lCont['order']
      );
    }
    
    return $lRet;
  }
}