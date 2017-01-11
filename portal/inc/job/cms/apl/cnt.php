<?php
/**
 * CMS APL Ajx: Controller
 *
 * Description
 *
 * @package    AJX
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 13762 $
 * @date $Date: 2016-05-04 13:30:09 +0100 (Wed, 04 May 2016) $
 * @author $Author: jwetherill $
 */
class CInc_Job_Cms_Apl_Cnt extends CAjx_Cnt {
	
  public function __construct(ICor_Req $aReq, $aMod, $aAct){
	parent::__construct($aReq, $aMod, $aAct);
  }
  
  protected function actSections() {
  	$lContent = json_decode( $this -> getReq('content'), true);
  	$lCombos = json_decode( $this -> getReq('combos'), true);
  	$lLanguage = json_decode( $this -> getVal('language'), true);
  	$lTask = $this -> getVal('task');
    $lTrans = (strpos($lTask, 'content') > -1) ? true : false;
  	$lActive = $this -> getInt('active');
  	$lSrc = $this -> getReq('src', '');
  
  	$lType = CCor_Res::extract('code', 'eve_phrase', 'crpmaster');
  	$lPhraseTyp = $lType[$lSrc];
    switch(true) {
      case stristr($lTask, 'approve'):
        $lTask = 'soft,approve,'.$lTrans;
        break;
      case stristr($lTask, 'check'):
        $lTask = 'hard,approve,'.$lTrans;
        break;
      case stristr($lTask, 'add'):
        $lTask = 'add,translation,'.$lTrans;
        break;
    }
  
  	$lForm = new CJob_Cms_Apl_Sections($lContent, $lCombos, $lLanguage, $lTask, $lActive, $lPhraseTyp, $lSrc);
  	$lSections = $lForm -> getHtml();
  
  	echo Zend_Json::encode( $lSections );
  }
  
  protected function actGetdata() {
    $lJobId = $this -> getReq('jobid', '');
    $lSrc = $this -> getReq('src', '');
    $lSubloopId = $this -> getVal('subloop');
    $lStateId = $this -> getVal('stateid');
    $lCategories = json_decode( $this -> getVal('categories'), true );
    $lLanguage = $this -> getVal('language');
    $lTask = $this -> getVal('task');
    $lAdd = (strpos($lTask, 'add') > -1);
    $lTrans = (strpos($lTask, 'content') > -1);
    
    $lDat = 'CJob_'.$lSrc.'_Dat';
    $lJob = new $lDat();
    $lJob -> load($lJobId);
    
    $lCmsDat = new CJob_Cms_Dat($lSrc);
    $this -> mData = $lCmsDat -> load($lJobId, $lJob); //load job content
    $lLanguage = ($lLanguage == ' ') ? array("MA") : array($lLanguage);
    
    //filter out unwanted content
    $lCombos = array();
	$lData = $this -> mData;
    foreach($lData as $lIdx => $lContent) {
    	if(
    	  !in_array($lContent['category'], $lCategories) || //content category isn't what is needed to be shown
    	  !in_array($lContent['language'], $lLanguage) || //content language isn't what is needed to be shown
    	  (!$lAdd && empty($lContent['content'])) || //Hard/Soft approval and content is empty
    	  ($lTrans && strpos($lContent['ntn'], $lContent['language']) !== FALSE) //Translation task (add,soft,hard) and the contents language appears in the no translation text
        ) {
    		unset($lData[$lIdx]);
    	} else {
    		//get all existing comments and suggestions for content for user
    		$lInfo = CJob_Cms_Mod::getNotes($lContent['content_id'], $lJobId, $lSubloopId, $lStateId, $lTask);
    		list($lState, $lSuggestion, $lComment) = $lInfo;
    		
    		$lContent['apl_state'] = $lState;
    		$lContent['suggestion'] = $lSuggestion;
    		$lContent['comment'] = $lComment;
    		
    		//add one to combo array
    		if(in_array($lContent['language'], $lLanguage)) {
    	      $lParams = array('category' => $lContent['category'], 'layout' => $lContent['layout']);
    	      $lExists = $lCmsDat -> multiArraySearch($lCombos, $lParams);
    	      if(!empty($lExists)) {
    	        if($lContent['group'] > $lCombos[$lExists[0]]['amount']) {
                  $lCombos[$lExists[0]]['amount'] += 1;
  			      $lContent['group'] = $lCombos[$lExists[0]]['amount'];
    	        }
    	      } else {
    	        $lCombos[] = array('category' => $lContent['category'], 'layout' => $lContent['layout'], 'amount' => 1);
  			    $lContent['group'] = 1;
    	      }
          }
          $lData[$lIdx] = $lContent;
    	}
    }
	
    $lData = array_values($lData);
	foreach($lData as $lIdx => $lContent) { //get master copy for translations
		if($lContent['language'] != 'MA') {
			$lParams = array('parent_id' => $lContent['parent_id'], 'language' => 'MA', 'category' => $lContent['category']);
    	    $lExists = $lCmsDat -> multiArraySearch($this -> mData, $lParams);
    	    if(!empty($lExists)) {
			$this -> mData[ $lExists[0] ]['group'] = $lContent['group'];
              array_unshift($lData, $this -> mData[ $lExists[0] ]);
    	    }
		}
	}
    $lData = array_values($lData);
    
    $lRet = array(
      'combos' => $lCombos,
      'data' => $lData,
      'language' => $lLanguage
    );
    
    echo Zend_Json::encode( $lRet );
  }
  
  protected function actSetapl() {
    $lJobId = $this -> getReq('jobid', '');
    $lSubloopId = $this -> getInt('subloop');
    $lStateId = $this -> getInt('stateid');
    $lData = json_decode( $this -> getReq('content'), true );
    
    //set all entries in al_cms_notes for user/state_id/subloop_id/content
    foreach($lData as $lIdx => $lCont) {
      if($lCont['status'] == 'approved') continue; //don't set approved content
      
      CJob_Cms_Mod::setNotes($lJobId, $lStateId, $lSubloopId, $lCont);
    }

    $lSql = "SELECT `position` FROM `al_job_apl_states` WHERE `id`=".esc($lStateId);
    echo CCor_Qry::getInt($lSql);
  }

  protected function actSetcontent() {
    $lCmsDat = new CCms_Mod();
    $lJobId = $this -> getReq('jobid', '');
    $lSubloopId = $this -> getInt('subloop');
    $lStateId = $this -> getInt('stateid');
    $lData = json_decode( $this -> getReq('content'), true );
    $lTask = $this -> getVal('task');
    $lUsr = CCor_Usr::getInstance();
    $lAllowedTags = '<strong><p><span><em><u><i><b>';
  
    //set all entries in al_cms_notes for user/state_id/subloop_id/content
    foreach($lData as $lIdx => $lCont) {
      if($lCont['status'] == 'approved' && intval($lCont['apl_state']) < 1) continue; //don't set approved content
      
      $lContentId = $lCont['content_id'];
      $lParentId = $lCont['parent_id'];
      $lLanguage = $lCont['language'];
      $lCategory = $lCont['category'];
      $lContentFrm = ($lCont['layout'] == 'rich') ? strip_tags(ereg_replace("[[:cntrl:]]", " ", $lCont['content']), $lAllowedTags) : NULL;
      $lContentStr = trim(strip_tags(ereg_replace("[[:cntrl:]]", " ", $lCont['content'])));
      $lSuggestionStr = trim(strip_tags(ereg_replace("[[:cntrl:]]", " ", $lCont['suggestion'])));;
      
      /*if($lContentStr == $lSuggestionStr && $lCont['apl_state'] == 2 && $lLanguage == 'MA') {
        $lContentId = CCms_Mod::getMax('content_id');
        $lParentId = CCms_Mod::getMax('parent_id');
        $lCont['version'] = 1;
      } else {*/
      if(strpos($lCont['layout'], 'nutri') > -1) {
        $lCheck = CCms_Mod::checkNutri($lContentStr, $lCont['metadata']);
        if($lCheck <= 0){
          $lOldContentId = $lContentId;
          $lData = array($lCont['metadata'], $lContentStr);
          $lContentId = CCms_Mod::setNutri($lData);
          CJob_Cms_Mod::updateJobRef($lContentId, $lOldContentId, $lJobId);
        }
      } else {
        $lCheck = CCms_Mod::contentExist($lContentStr, $lLanguage); //search for similar phrase with language
        if($lCheck > 0){
          if($lCheck != $lContentId) { //content found but not the same content_id
            $lContentId = $lCheck;
            $lCont['version'] = intval($lCont['version']) + 1;
          }
        } else {
          if($lContentId == 0) { //content doesn't exist in DB
            $lContentId = CCms_Mod::getMax('content_id');
            
            //insert translation to job reference table
            $lMasterContent = CJob_Cms_Mod::getMasterData($lParentId);
            if($lMasterContent != FALSE) {
              //get entry in job table for master content
              $lQry = CJob_Cms_Mod::getJobRef($lMasterContent, $lJobId);
              foreach($lQry as $lDat) {
                CJob_Cms_Mod::setJobRef($lJobId, $lContentId, $lDat['position'], $lDat['type'], $lDat['group'], NULL, $lDat['layout'], $lDat['template_id'], $lCont['status']);
              }
            }
          } else { //new string doesn't exist in DB but old version does so up the version
            $lOldContentId = $lContentId;
            $lContent = CCms_Mod::getContent($lContentId);
            if($lContent['content'] != $lContentStr && $lCont['version'] > 1) {
              $lContentId = CCms_Mod::getMax('content_id'); //new content
              $lCont['version'] = intval($lCont['version']) + 1;
              
              CJob_Cms_Mod::updateJobRef($lContentId, $lOldContentId, $lJobId);
            }
          }
        }
        
        //get contents tokens and sanitise string
        $lSanStr = CCms_Sanitiser::sanitise($lContentStr, $lCategory, $lLanguage, TRUE);
      
        //insert content into `al_cms_content`
        $lData = array($lContentId, $lParentId, $lContentStr, $lSanStr, $lContentFrm, $lCont['status']);
        CCms_Mod::setContent($lData);
      
        //insert language/version into `al_cms_ref_lang`
        $lData = array($lParentId, $lContentId, $lCont['version'], $lLanguage);
        $lCmsDat -> setLangVer($lData);
        
        //set category into `al_cms_ref_category`
        $lCmsDat -> setCategory($lContentId, $lCategory); //check if exists
      }
      
      $lCont['content_id'] = $lContentId;
      CJob_Cms_Mod::setNotes($lJobId, $lStateId, $lSubloopId, $lCont);

      CJob_Cms_Mod::setJobStatus($lJobId, $lCont);
    }
  
    $lSql = "SELECT `position` FROM `al_job_apl_states` WHERE `id`=".esc($lStateId);
    echo CCor_Qry::getInt($lSql);
  }
}