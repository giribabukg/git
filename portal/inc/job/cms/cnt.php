<?php
class CInc_Job_Cms_Cnt extends CCor_Cnt {

  public $mSrc;
  public $mJobId;
  
  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mJobId = $this -> getReq('jobid', '');
    $this -> mSrc = $this -> getReq('src', '');
    
    $lType = CCor_Res::extract('code', 'eve_phrase', 'crpmaster');
    $this -> mPhraseTyp = $lType[$this -> mSrc];
    $lTyp = ($this -> mPhraseTyp == 'job') ? '' : '.product';

    $this -> mTitle = lan('job-'.$this -> mPhraseTyp.'-cms.menu');
    $this -> mUsr = CCor_Usr::getInstance();
    
    if (!$this -> mUsr -> canRead($aMod.$lTyp)) {
      $this -> denyAccess();
    }
  }

  protected function actStd() {
    $lRet = '';
    
    $lDat = 'CJob_'.$this->mSrc.'_Dat';
    $lJob = new $lDat();
    $lJob -> load($this -> mJobId);

    $lHeader = 'CJob_'.$this->mSrc.'_Header';
    $lVie = new $lHeader($lJob);
    $lRet.= $lVie -> getContent();

    $lTabs = 'CJob_'.$this->mSrc.'_Tabs';
    $lVie = new $lTabs($this -> mJobId, 'cms');
    $lRet.= $lVie -> getContent();

    $lVie = new CJob_Cms_Form('job-cms', $this -> mJobId, $this -> mSrc, $lJob);
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
  }
  
  protected function actSnew() {
    $lDat = 'CJob_'.$this->mSrc.'_Dat';
    $lJob = new $lDat();
    $lJob -> load($this -> mJobId);
    
    $lMod = new CJob_Cms_Mod($this -> mSrc, $this -> mJobId, $lJob);
    $lMod -> getPost($this -> mReq, FALSE);

    if ($lMod -> insert()) {
      $this -> redirect('index.php?act=job-cms&jobid='.$this -> mJobId.'&src='.$this -> mSrc);
    }
    $this -> redirect('index.php?act=job-'.$this -> mSrc.'.edt&jobid='.$this -> mJobId.'&page=job');
  }

  protected function actSedt() {
    $lDat = 'CJob_'.$this->mSrc.'_Dat';
    $lJob = new $lDat();
    $lJob -> load($this -> mJobId);
    
    $lOld = $this->getReq('old');
    $lMod = new CJob_Cms_Mod($this -> mSrc, $this -> mJobId, $lJob);
    $lMod -> getPost($this -> mReq, !empty($lOld));

    if ($lMod -> update()) {
      $this -> redirect('index.php?act=job-cms&jobid='.$this -> mJobId.'&src='.$this -> mSrc);
    }
    $this -> redirect('index.php?act=job-'.$this -> mSrc.'.edt&jobid='.$this -> mJobId.'&page=job');
  }
  
  protected function actContexp() {
    $lUsr = CCor_Usr::getInstance();
    $lTyp = $this -> getReq('typ', 'xml');

    $lDat = 'CJob_'.$this->mSrc.'_Dat';
    $lJob = new $lDat();
    $lJob -> load($this -> mJobId);
    
    $lPhraseFields = CCor_Cfg::get('job-cms.fields');
    $lLangKey = $lPhraseFields['languages'];
    $lLangs = $lJob[$lLangKey];
    
    // Filename
    $lMandArray = CCor_Res::extract('code', 'name_'.LAN, 'mand');
    $lMandName = str_replace(' ', '_', $lMandArray[MAND]);
    $lFileName = lan('job-cms.menu') . '_' . $this -> mJobId . '_' . date('Ymd_H-i-s');
    $lFileName = ($lTyp == 'xml') ? $lFileName . '.xml' : $lFileName . '.xls';

    // File
    $lContentType = ($lTyp == 'xml') ? 'text/xml' : 'application/vnd-ms-excel';
    header('Content-type: '.$lContentType.'; charset=utf-8');
    header('Content-Disposition: attachment; filename="'.$lFileName.'"');
    header("Pragma: no-cache");
    header("Expires: 0");
    flush();

    // Content
    $lJobCms = new CJob_Cms_Dat($this -> mSrc);
    $lDat = $lJobCms -> load($this -> mJobId, $lJob);
    
    echo $lJobCms -> export($lDat, $lLangs, $lTyp);
  }
  
  public function actContentsections() {
    $lContent = json_decode($this -> getReq('content'), true);
    $lCombos = json_decode($this -> getReq('combos'), true);
    $lJob = json_decode($this -> getReq('job'), true);
    $lActive = $this -> getInt('active');
    
    $lForm = new CJob_Cms_Content_Sections($lContent, $lCombos, $lJob, $lActive, $this -> mSrc);
    $lSections = $lForm -> getHtml();
    
    echo Zend_Json::encode( $lSections );
  }
  
  public function actGetjobform() {
    $lContent = json_decode( $this -> getVal('content'), true );
    $lJob = json_decode( $this -> getVal('job'), true ); //content
    
    $lVie = new CJob_Cms_Form('job-cms', $this -> mJobId, $this -> mSrc, $lJob, $lContent);
    echo Zend_Json::encode( $lVie -> getFormSections() );
  }

  protected function actDictionary(){
    $lContent = addslashes(trim($this->getReq('val')));
    $lCategory = $this -> getReq('category');
    $lLang = $this -> getReq('lang');
    $lChosen = $this -> getVal('chosen', array());
  
    echo Zend_Json::encode( $this -> getSearchResults($lContent, $lCategory, $lLang, array(), $lChosen) );
  }
  
  /**
   * Used for dictionary autocomplete to gather translations for content selected
   */
  protected function actTranslations() {
    $lId = $this -> getVal('id'); //content id
    $lJob = json_decode( $this -> getVal('job'), true ); //content
    
    $lCmsDat = new CJob_Cms_Dat($this -> mSrc);
    $lCmsDat -> load($this -> mJobId, $lJob);
    
    $lRet = CCms_Mod::getContent($lId);
    $lData = $lCmsDat -> loadTranslations($lRet, 'content', array());
    $lCmsDat -> sortContent($lData);

    echo Zend_Json::encode($lData);
  }
  
  protected function actSearch() {
    $lContent = $this -> getVal('content', '');
    $lCategory = $this -> getVal('id');
    $lLang = $this -> getVal('language');
    $lMeta = $this -> getVal('meta', array());
    $lChosen = $this -> getVal('chosen', array());
    
    echo Zend_Json::encode( $this -> getSearchResults($lContent, $lCategory, $lLang, $lMeta, $lChosen) );
  }
  
  protected function getSearchResults($aContent, $aCat = '', $aLang, $aMeta = array(), $aChosen = array()) {
    $lResults = (!empty($aContent)) ? CCms_Sanitiser::sanitise($aContent, $aCat, $aLang) : CCms_Mod::getAll($aCat, $aLang);
    $lApproved = CCor_Cfg::get('phrase.search.content', '');
    
    if(!empty($aChosen) || !empty($aCat) || !empty($lApproved)) {
      foreach($lResults as $lIdx => $lArr) {
        if(!empty($aCat) && ($aCat !== $lArr['categories'])) {
          unset($lResults[$lIdx]);
        }
  
        if(!empty($aChosen) && (in_array($lArr['content'], $aChosen) && $lArr['content'] !== $aContent)) {
          unset($lResults[$lIdx]);
        }
        
        if(!empty($lApproved) && ($lApproved !== $lArr['status'])) {
          unset($lResults[$lIdx]);
        }
      }
    }
    
    if(!empty($aMeta)) {
      foreach($lResults as $lIdx => $lArr) {
        $lMeta = $lArr['metadata'];
        foreach($aMeta as $lId => $lVal){
          if(array_key_exists($lId, $lMeta)) {
            if(!in_array($lVal, $lMeta[$lId]))
              unset($lResults[$lIdx]);
          } else {
            unset($lResults[$lIdx]);
          }
        }
      }
    }
    
    $lResults = array_values($lResults);
    
    return $lResults;
  }
  
  /**
   * Save template with combos
   */
  protected function actSettemplate(){
    $lCombos = json_decode( $this -> getVal('data'), true );
    $lName = $this -> getVal('name');
    $lMethod = $this -> getVal('method');
    
    $lPhraseTypes = CCor_Res::extract('code', 'eve_phrase', 'crpmaster');
    $lJobTyp = $lPhraseTypes[$this -> mSrc];

    echo Zend_Json::encode( CCms_Mod::setTemplate($lName, $lCombos, $lMethod, $lJobTyp) );
  }
  
  /**
   * Get template combos by name
   */
  protected function actGettemplate() {
    $lName = $this -> getVal('name');
    
    $lPhraseTypes = CCor_Res::extract('code', 'eve_phrase', 'crpmaster');
    $lJobTyp = $lPhraseTypes[$this -> mSrc];
    
    echo Zend_Json::encode( CCms_Mod::getTemplate($lName, $this -> mJobId, $lJobTyp) );
  }
  
  /**
   * Updates job with latest product information
   */
  public function actUpdatejob() {
    $lJob = json_decode( $this -> getVal('job'), true );
    
	$lMod = new CJob_Cms_Mod($this -> mSrc, $this -> mJobId, $lJob);
	$lRes = $lMod -> updateJobProduct(0, $this -> mJobId);
	
	echo Zend_Json::encode($lRes);
  }
  
  /**
   * Gets the Chili Editor URL based on the template ID
   */
  public function actGeteditorurl() {
    $lTemplate = $this -> getVal('template');
    $lChili = new CApi_Chili_Client($this -> mJobId);
    $lRet = $lChili -> showEditor($lTemplate);
    
    echo Zend_Json::encode($lRet);
  }
  
  /**
   * Get all content within the system for the variables in the chili template
   */
  public function actGetvariables() {
    $lPhraseFields = CCor_Cfg::get('job-cms.fields');
	$lLanguages = CCor_Res::get('htb', 'dln');
    $lJob = json_decode( $this -> getVal('job'), true );
    
    $lLangKey = $lPhraseFields['languages'];
    $lJobLangs = array_filter( array_map('trim', explode(",", $lJob[$lLangKey])) );
	
	$lJobContent = array();
	$lData = new CJob_Cms_Dat($this -> mSrc);
    $lJobData = $lData -> load($this -> mJobId, $lJob); //load job content
	foreach($lJobData as $lIdx => $lArr) {
	  $lCategory = $lArr['category'];
	  $lGroup = $lArr['parent_id'];
	  $lLang = $lArr['language'];
	  
	  $lJobContent[$lCategory][$lGroup][$lLang] = $lArr;	
	  $lJobContent[$lCategory] = array_values($lJobContent[$lCategory]);
	}

	$lTempKey = $lPhraseFields['template_name'];
    $lChili = new CApi_Chili_Client($this -> mJobId);
    $lVariables = $lChili -> getTemplateVariables($lJob[$lTempKey]);
    $lBrief = $lChili -> getBriefFields();
	foreach($lVariables as $lSet => $lTags) {
		foreach($lTags as $lTag => $lTagData){
			foreach($lTagData as $lIdx => $lTdata) {
				$lContent = '';
				$lItem = $lTdata;
				$lId = $lItem['tag_id'];
				
				$lName = $lItem['display_name'];
				$lName = explode('_', $lName);
				$lOrder = 0;
				$lLang = 'MA';
				
				if(sizeof($lName) > 1){
					$lOrder = intval($lName[1]) -1;
					$lLang = intval( substr($lName[2], 1, 2) ) -1;
					$lLang = ($lLang > -1) ? $lJobLangs[$lLang] : 'MA';
				}
				$lIndex = strpos($lName[0], 'Lang0');
				if($lIndex !== FALSE && $lIndex == 0){
					$lOrder = intval( substr($lName[0], 4, 2) ) -1;
					$lLang = ($lOrder > -1) ? $lJobLangs[$lOrder] : '';
					$lContent = (array_key_exists($lLang, $lLanguages)) ? $lLanguages[$lLang] : '';
				} else {
					$lContent = $this -> getVariablecontent($lJobContent, $lBrief, $lTag, $lOrder, $lLang, $lJob);
				}
				
				$lRet[$lId] = $lContent;
			}
		}
	}
    
    echo Zend_Json::encode($lRet);
  }
  
  /**
   * Get chili to create the modified PDF
   */
  public function actGeneratepdf(){
    $lTemplate = $this -> getVal("template");
	
    $lChili = new CApi_Chili_Client($this -> mJobId);
    $lRet = $lChili -> generatePdf($lTemplate);
    
    echo Zend_Json::encode($lRet);
  }
  
  /**
   * Return Variable value based on if it appears in Phrase or the briefing form
   * @param array $aJobContent - Job Content
   * @param array $aBrief - Briefing form alias fields
   * @param string $aTag - variable tag to look for
   * @param integer $aOrder - order of returned value
   * @param string $aLang - language to search for
   * @return string $lRet - variable value
   */
  protected function getVariablecontent($aJobContent, $aBrief, $aTag, $aOrder, $aLang, $aJob) {
	$lRet = '';

	if(strpos($aTag, 'Nutri') > -1) {
	  $lType = str_replace("Nutri", "", $aTag);
	  $aTag = 'Nutrition';
	  if( isset( $aJobContent[$aTag][$aLang][$lType]['content'] ) ) {
	    $lRet = $aJobContent[$aTag][$aLang][$lType]['content'];
	  }
	} else {
    	if( isset( $aJobContent[$aTag][$aOrder][$aLang]['content'] ) ) {
    		$lRet = $aJobContent[$aTag][$aOrder][$aLang]['content'];
    	} else {
    		if(array_key_exists($aTag, $aBrief)){
    			$aTag = $aBrief[$aTag];
    			$lRet = $aJob[$aTag];
    		}
    	}
	}
	
	return $lRet;
  }
}