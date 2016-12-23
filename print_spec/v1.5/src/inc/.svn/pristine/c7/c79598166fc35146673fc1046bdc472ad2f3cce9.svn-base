<?php
class CInc_Job_Cms_Cnt extends CCor_Cnt {

  public $mSrc;
  public $mJobId;
  
  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mJobId = $this -> getReq('jobid');
    $this -> mSrc = $this -> getVal('src');
    
    $lType = CCor_Res::extract('code', 'eve_phrase', 'crpmaster');
    $this -> mPhraseTyp = $lType[$this -> mSrc];
    $lTyp = ($this -> mPhraseTyp == 'job') ? '' : '.product';

    $this -> mTitle = lan('job-'.$this -> mPhraseTyp.'-cms.menu');
    $this -> mUsr = CCor_Usr::getInstance();
    
    if (!$this -> mUsr -> canRead($aMod.$lTyp)) {
      $this -> denyAccess();
    }
    $this -> mAva = fsPro;
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

    $lVie = new CJob_Cms_Form('job-cms', $this -> mJobId, $lJob, $this -> mSrc);
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
  }
  
  protected function actSnew() {
    $lDat = 'CJob_'.$this->mSrc.'_Dat';
    $lJob = new $lDat();
    $lJob -> load($this -> mJobId);
    
    $lMod = new CJob_Cms_Mod($this -> mSrc, $this -> mJobId, $this -> mJob);
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
    $lMod = new CJob_Cms_Mod($this -> mSrc, $this -> mJobId, $this -> mJob);
    $lMod -> getPost($this -> mReq, !empty($lOld));

    if ($lMod -> update()) {
      $this -> redirect('index.php?act=job-cms&jobid='.$this -> mJobId.'&src='.$this -> mSrc);
    }
    $this -> redirect('index.php?act=job-'.$this -> mSrc.'.edt&jobid='.$this -> mJobId.'&page=job');
  }
  
  /**
   * Get HTML for items to be added to DOM
   */
  public function actGetitems() {
    $lData = array();
    $lSize = $this -> getVal('size');
    $lCombos = json_decode( $this -> getVal('combo'), true ); //combos
    $lContent = json_decode( $this -> getVal('data'), true ); //content
    $lJob = json_decode( $this -> getVal('job'), true ); //content
    
    $lMod = new CJob_Cms_Mod($this -> mSrc, $this -> mJobId, $lJob);
    if(!empty($lContent)) {
      foreach($lContent as $lType => $lItems) {
        foreach($lItems as $lCategory => $lArr){
          foreach($lArr as $lIdx => $lGroup){
            foreach($lGroup as $lParentId => $lCont){
              $lData = $lMod -> getTranslations($lCont['MA'], 'content', $lData);
            }
          }
        }
      }
    } else {
      foreach($lCombos as $lIdx => $lCombo) {
        $lCat = $lCombo['category'];
        $lLayout = $lCombo['layout'];
        $lAmount = intval($lCombo['amount']);
        $lCont = array('content_id' => 0, 'parent_id' => 0, 'content' => '', 'tokens' => '', 'categories' => array($lCat), 'metadata' => array(), 'version' => 1, 'language' => 'MA');
        
        for($lI=0; $lI < $lAmount; $lI++) {
          $lAllType = CCor_Res::extract('code', 'eve_phrase', 'crpmaster');
          $lTyp = ($lAllType[$this -> mSrc] == 'job') ? 'content' : 'product';
          
          $lData = $lMod -> getTranslations($lCont, $lTyp, $lData, $lLayout);
        }
      }
    }
    
    foreach($lData as $lTyp => $lContent){
      $lItems = new CJob_Cms_Item_Fac($lContent, $lSize, $lJob, $this -> mSrc);
      echo json_encode( $lItems->getItems() );
    }
  }
  
  protected function actGetitemdlg() {
    $lDlg = new CJob_Cms_Item_Dialog($this -> mJobId, $this -> mSrc);
    echo $lDlg->getContent();
  }

  protected function actDictionary(){
    $lContent = addslashes(trim($this->getReq('val')));
    $lLang = $this -> getReq('lang');
  
    $lCategory = $this -> getReq('category');
  
    echo json_encode( $this -> getSearchResults($lContent, $lCategory, $lLang) );
  }
  
  protected function actGettranslations() {
    $lId = $this -> getVal('id'); //content id
    $lJob = json_decode( $this -> getVal('job'), true ); //content
    
    $lMod = new CJob_Cms_Mod($this -> mSrc, $this -> mJobId, $lJob);
    $lCmsMod = new CCms_Mod();
    
    $lRet = $lCmsMod -> getContent($lId);
    $lData = $lMod -> getTranslations($lRet, 'content', array());
    
    $lCont = array();
    foreach($lData['content'] as $lCat => $lArr) {
      foreach($lArr as $lLayout => $lArr2) {
        foreach($lArr2 as $lOrd => $lArr3) {
          foreach($lArr3 as $lLang => $lVal) {
            $lCont[$lLang] = $lVal;
          }
        }
      }
    }

    echo json_encode($lCont);
  }

  protected function actGetsearchdlg(){
    $lCategory = $this -> getVal('id');
    $lContent = $this -> getVal('content');
    $lLang = $this -> getVal('lang');
    $lMeta = $this -> getVal('meta');
    $lJob = json_decode( $this -> getVal('job'), true ); //content
    
    $lSuggestions = $this -> getSearchResults($lContent, $lCategory, $lLang, $lMeta);
    
    $lDlg = new CJob_Cms_Search_Dialog($lCategory, $lContent, $lSuggestions, $this -> mJobId, $this -> mSrc, $lJob);
    echo $lDlg->getContent();
  }
  
  protected function actSearch() {
    $lContent = $this -> getVal('content', '');
    $lLang = $this -> getVal('language');
    $lCategory = $this -> getVal('id');
    $lMeta = $this -> getVal('meta');
    
    echo json_encode( $this -> getSearchResults($lContent, $lCategory, $lLang, $lMeta) );
  }
  
  protected function actGetresult() {
    $lData = $lCombos = $lCats = array();
    $lIds = json_decode( $this -> getVal('ids'), true ); //content id
    $lJob = json_decode( $this -> getVal('job'), true ); //content
    
    $lMod = new CJob_Cms_Mod($this -> mSrc, $this -> mJobId, $lJob);
    $lCmsMod = new CCms_Mod();
    
    foreach($lIds as $lId){
      $lRet = $lCmsMod -> getContent($lId);
      $lLayout = $lMod -> getLayout($lId);
      
      $lCat = $lRet['categories'][0];
      if(array_key_exists($lCat, $lCats)){
        $lCats[$lCat][$lLayout] = intval($lCats[$lCat][$lLayout]) + 1;
      } else {
        $lCats[$lCat][$lLayout] = 1;
      }
      $lData = $lMod -> getTranslations($lRet, 'content', $lData, $lLayout);
    }
    
    foreach($lCats as $lCat => $lArr) {
      foreach($lArr as $lLayout => $lAmount) {
        $lCombos[] = array('category' => $lCat, 'layout' => $lLayout, 'amount' => $lAmount);
      }
    }

    $lRet = array('combos' => $lCombos, 'data' => $lData);
    echo json_encode($lRet);
  }
  
  protected function getSearchResults($aContent, $aCat = '', $aLang, $aMeta = array()) {
    $lCmsMod = new CCms_Mod();
    
    if($aContent !== ''){
      $lSanitiser = new CCms_Sanitiser();
      $lResults = $lSanitiser -> sanitise($aContent, $aLang);
    } else {
      $lResults = $lCmsMod -> getAll($aCat, $aLang);
    }
    
    if(!empty($aCat)) {
      $lCat = explode('_', $aCat);
      $lCat = $lCat[0];
      //search content by categories
      foreach($lResults as $lIdx => $lArr) {
        $lCategories = $lArr['categories'];
        if($lCat !== $lCategories)
          unset($lResults[$lIdx]);
        
      }
    }
    
    if(!empty($aMeta)) {
      //search content by categories
      foreach($lResults as $lIdx => $lArr) {
        $lMeta = $lArr['metadata'];
        foreach($aMeta as $lId => $lVal){
          if(array_key_exists($lId, $lMeta)) {
            if(!in_array($lVal, $lMeta[$lId]))
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
    $lTyp = $this -> getVal('type');
    
    $lPhraseTypes = CCor_Res::extract('code', 'eve_phrase', 'crpmaster');
    $lJobTyp = $lPhraseTypes[$this -> mSrc];

    $lCmsMod = new CCms_Mod();
    echo json_encode( $lCmsMod -> setTemplate($lName, $lCombos, $lTyp, $lJobTyp) );
  }
  
  /**
   * Get template combos by name
   */
  protected function actGettemplate() {
    $lName = $this -> getVal('name');
    
    $lPhraseTypes = CCor_Res::extract('code', 'eve_phrase', 'crpmaster');
    $lJobTyp = $lPhraseTypes[$this -> mSrc];
    
    $lCmsMod = new CCms_Mod();
    echo json_encode( $lCmsMod -> getTemplate($lName, $this -> mJobId, $lJobTyp) );
  }
  
  /**
   * Updates job with latest product information
   */
  public function actUpdatejob() {
    $lCode = $this -> getVal('code');
    $lJob = json_decode( $this -> getVal('job'), true ); //content
    
	$lData = new CJob_Cms_Mod($this -> mSrc, $this -> mJobId, $lJob);
	$lRes = $lData -> updateJobWithProductInfo(0, $this -> mJobId);
	
	echo json_encode($lRes);
  }
  
  public function actGeteditorurl() {
    $lTemplate = $this -> getVal('template'); //content
    $lChili = new CApi_Chili_Client($this -> mJobId);
    $lRet = $lChili -> showEditor($lTemplate);
    
    echo json_encode($lRet);
  }
  
  public function actGetvariables() {
    $lJob = json_decode( $this -> getVal('job'), true ); //content
	$lJobContent = array();
	$lData = new CJob_Cms_Mod($this -> mSrc, $this -> mJobId, $lJob);
    $lJobData = $lData -> loadJobRef(); //load job content
	foreach($lJobData as $lType => $lArr) {
		foreach($lArr as $lCategory => $lArr2) {
			foreach($lArr2 as $lLayout => $lArr3) {
				foreach($lArr3 as $lOrder => $lArr4) {
					foreach($lArr4 as $lLang => $lCont) {
						$lJobContent[$lCategory][$lOrder][$lLang] = $lCont;
					}
				}
			}
			
			$lJobContent[$lCategory] = array_values($lJobContent[$lCategory]);
		}
	}
	
    $lJobLangs = explode(",", $lJob['languages']);
    $lJobLangs = array_map('trim', $lJobLangs);
	$lLanguages = CCor_Res::get('htb', 'dln');

    $lTemplate = $lJob['chili_template_name'];
    $lChili = new CApi_Chili_Client($this -> mJobId);
    $lVariables = $lChili -> getTemplateVariables($lTemplate);
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
					$lContent = $this -> getVariablecontent($lJobContent, $lBrief, $lTag, $lOrder, $lLang);
				}
				
				$lRet[$lId] = $lContent;
			}
		}
	}
    
    echo json_encode($lRet);
  }
  
  public function actGeneratepdf(){
    $lTemplate = $this -> getVal("template");
	
    $lChili = new CApi_Chili_Client($this -> mJobId);
    $lRet = $lChili -> generatePdf($lTemplate);
    
    echo json_encode($lRet);
  }
  
  protected function getVariablecontent($aJobContent, $aBrief, $aTag, $aOrder, $aLang) {
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
    			$lRet = $this -> mJob[$aTag];
    		}
    	}
	}
	
	return $lRet;
  }
}