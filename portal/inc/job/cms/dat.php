<?php
/**
 * Jobs: Data
 *
 *  ABSTRACT! Description
 *
 * @package    JOB
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 9446 $
 * @date $Date: 2015-07-02 11:14:53 +0100 (Thu, 02 Jul 2015) $
 * @author $Author: jwetherill $
 */
abstract class CInc_Job_Cms_Dat extends CCor_Dat {

  protected $mSrc = '';
  protected $mJobId = '';
  protected $mProductJobId = '';
  public $mClientKey = '';

  public function __construct($aSrc = '') {
    if (!empty($aSrc)) {
      $this -> mSrc = $aSrc;
    }

	$this -> mOrder = array();
  }

  /**
   * Load job or product information
   * @return array $lRet - content related to job
   */
  public function load($aId, $aJob = array()) {
    $lRet = array();
    $this -> mJobId = $aId;
    $lUsr = CCor_Usr::getInstance();
    
    if(!empty($this -> mJobId) && !empty($this -> mSrc) && empty($aJob)) {
      $lDat = 'CJob_'.$this->mSrc.'_Dat';
      $this -> mJob = new $lDat();
      $this -> mJob -> load($this -> mJobId);
    } else {
      $this -> mJob = $aJob;
    }

    $this -> mPhraseFields = CCor_Cfg::get('job-cms.fields');
    $lKey = $this -> mPhraseFields['client_key'];
	$this -> mClientKey = (empty($this -> mJob[$lKey])) ? 'NULL' : $this -> mJob[$lKey]; //find product jobid try get from there

	$lProductJobs = CCms_Job::getClientKeyJobs('product', $this -> mClientKey);
	$this -> mProductJobId = (empty($lProductJobs)) ? $this -> mJobId : current(array_keys($lProductJobs));
	
    $this -> mIte = new CCor_TblIte('al_cms_ref_job'); //only store master language in job ref
    $this -> mIte -> addCnd('`jobid`='.esc($aId));
    $this -> mIte -> setOrder('id');
    $this -> mIte -> set2Order('type');
    $lRes = $this -> mIte -> getArray();
  
    foreach($lRes as $lIdx => $lCont){
      $lType = $lCont['type'];
      $lLayout = $lCont['layout'];

      $lRet = $this -> loadTranslations($lCont, $lType, $lRet, $lLayout);
  
      $lTemplate = $lUsr -> getPref('phrase.'.$lCont['jobid'].'.template', '');
      if(empty($lTemplate) AND !empty($lCont['template_id'])) {
        $lUsr -> setPref('phrase.'.$lCont['jobid'].'.template', $lCont['template_id']); //set template if not set
      }
    }
    
    $lParams = array('type' => 'product');
    $lExists = $this -> multiArraySearch($lRet, $lParams);
    if(empty($lExists)) {
      $lRet = $this -> loadProduct($lRet);
    }
    $this -> sortContent($lRet);
  
    return $lRet;
  }
  
  /**
   * Load product information
   * @return array $lRet - product related to client_key on job form
   */
  protected function loadProduct($aRet = array()){
    $lRet = $aRet;

    if(!empty($this -> mClientKey)) {
      $lRes = CCms_Job::getProductRefs($this -> mProductJobId); //get product references

      foreach($lRes as $lIdx => $lCont){
        $lType = $lCont['type'];
        $lLayout = $lCont['layout'];
  
        $lRet = $this -> loadTranslations($lCont, $lType, $lRet, $lLayout);
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
  public function loadTranslations($aCont, $aType, $aRet = array(), $aLayout = 'memo') {
    $lRet = $aRet;
    $lLayout = (!empty($aLayout)) ? $aLayout : 'memo';
    $lLangKey = $this -> mPhraseFields['languages'];
    $lLangs = array_filter( array_map('trim', explode(",", $this -> mJob[$lLangKey])) );
    array_push($lLangs, "MA");
  
    if(strpos($lLayout, 'nutri') > -1) {
      $lCategory = 'Nutrition';
  
      if($aCont['content_id'] > 0) {
        $lNutri = CCms_Mod::getNutri($aCont['content_id']);
        
        $lJobId = ($aType == 'product') ? $this -> mProductJobId : $this -> mJobId;
        $lRefFields = CCms_Job::getJobReferenceFields($aCont['content_id'], $aType, $this -> mClientKey, $lJobId);
        list($lPosition, $lMetadata, $lGroup, $lStatus) = explode(',', $lRefFields);

        $lParams = array('metadata' => $lNutri['category'], 'group' => $aCont['group'], 'type' => $aType, 'category' => $lCategory, 'layout' => $lLayout, 'language' => $aCont['metadata']);
        $lExists = $this -> multiArraySearch($lRet, $lParams);
        if(empty($lExists)) {
          $lRet[] = array(
              'content_id' => $lNutri['content_id'], 'parent_id' => $lNutri['parent_id'], 'content' => $lNutri['content'],
              'category' => $lCategory, 'language' => $aCont['metadata'], 'version' => 1, 'position' => $lPosition, 'group' => $aCont['group'],
              'metadata' => $lNutri['category'], 'ntn' => $aCont['ntn'], 'packtypes' => $aCont['packtypes'], 'type' => $aType, 'layout' => $lLayout, 'status' => $lStatus
          );
        } else {
          $lRet[$lExists[0]] = array(
              'content_id' => $lNutri['content_id'], 'parent_id' => $lNutri['parent_id'], 'content' => $lNutri['content'],
              'category' => $lCategory, 'language' => $aCont['metadata'], 'version' => 1, 'position' => $lPosition, 'group' => $aCont['group'],
              'metadata' => $lNutri['category'], 'ntn' => $aCont['ntn'], 'packtypes' => $aCont['packtypes'], 'type' => $aType, 'layout' => $lLayout, 'status' => $lStatus
          );
        }
      }
  
      $lTpl = new CCor_Tpl();
      $lTpl -> openProjectFile('job/cms/'.$lLayout.'.htm');
      $lRows  = $lTpl -> findPatterns('val.');
      foreach($lLangs as $lLang) {
        foreach($lRows as $lRow) {
          $lParams = array('type' => $aType, 'category' => $lCategory, 'layout' => $lLayout, 'language' => $lLang, 'metadata' => $lRow);
          $lExists = $this -> multiArraySearch($lRet, $lParams);
          if(empty($lExists)) {
            $lRet[] = array(
                'content_id' => 0, 'parent_id' => 0, 'content' => '', 'category' => $lCategory, 'language' => $lLang,
                'version' => 1, 'group' => $aCont['group'], 'position' => '', 'metadata' => $lRow, 'packtypes' => '', 'ntn' => '', 'type' => $aType, 'layout' => $lLayout, 'status' => "draft"
            );
          }
        }
      }
    } else {
      if($aCont['content_id'] > 0) {
        $lMasterExists = $this -> multiArraySearch($lRet, array('content_id' => $aCont['content_id']));
        if (!empty($lMasterExists)) return $lRet;
        
        $lCmsDat = new CCms_Mod();
        $lMaster = CCms_Mod::getContent($aCont['content_id']);
        $lParentId = $lMaster['parent_id'];
        $lCategory = $lMaster['categories'];

        $lParams = array('parent_id' => $lParentId, 'type' => $aType, 'category' => $lCategory, 'layout' => $lLayout);
        $lExists = $this -> multiArraySearch($lRet, $lParams);
        if(empty($lExists)) {
          $lSql = 'SELECT c.`content_id`, l.`language` FROM `al_cms_content` c INNER JOIN `al_cms_ref_lang` l ON c.`content_id`=l.`content_id` ';
          $lSql.= 'WHERE c.`parent_id`='.esc($lParentId).' ';
          $lSql.= 'AND c.`mand`='.intval(MID).' ORDER BY c.`content_id` ASC';
          $lQry = new CCor_Qry($lSql);
          foreach ($lQry as $lRow) {
            if($lRow['content_id'] != $lMaster['content_id'] && $lRow['language'] == $lMaster['language']) {
              continue;
            }
            $lContent = ($lRow['content_id'] == $lMaster['content_id']) ? $lMaster : CCms_Mod::getContent($lRow['content_id']);
            $lCategory = $lContent['categories'];
            $lLanguage = $lContent['language'];
            $lContent['content'] = ($aCont['layout'] == 'rich' && !empty($lContent['format'])) ? $lContent['format'] : $lContent['content'];
  
            if(in_array($lLanguage, $lLangs)){
              $lJobId = ($aType == 'product') ? $this -> mProductJobId : $this -> mJobId;
              $lRefFields = CCms_Job::getJobReferenceFields($lRow['content_id'], $aType, $this -> mClientKey, $lJobId);
              list($lPosition, $lMetadata, $lNoTranslations, $lPackTypes, $lGroup, $lStatus) = explode(',', $lRefFields);
              $lStatus = (CCor_Cfg::get('phrase.jobcontent.status', FALSE)) ? $lStatus : $lContent['status'];
              
              if(empty($lGroup) || empty($lNoTranslations) || empty($lPackTypes)){
                $lParams['language'] = 'MA';
                $lExists = $this -> multiArraySearch($lRet, $lParams);
                if(empty($lGroup)){
                  $lGroup = (!empty($lExists)) ? $lRet[$lExists[0]]['group'] : 1;
                }
                if(empty($lNoTranslations)){
                  $lNoTranslations = (!empty($lExists)) ? $lRet[$lExists[0]]['ntn'] : '';
                }
                if(empty($lPackTypes)){
                  $lPackTypes = (!empty($lExists)) ? $lRet[$lExists[0]]['packtypes'] : '';
                }
              }
  
              $lRet[] = array(
                  'content_id' => $lRow['content_id'], 'parent_id' => $lParentId, 'content' => $lContent['content'],
                  'category' => $lCategory, 'language' => $lLanguage, 'version' => $lContent['version'], 'group' => $lGroup,
                  'position' => $lPosition, 'metadata' => array($lMetadata), 'ntn' => $lNoTranslations, 'packtypes' => $lPackTypes,
                  'type' => $aType, 'layout' => $lLayout, 'status' => $lStatus
              );
            }
          }
        }
  
        //fill out any missing languages (no translations for them)
        foreach($lLangs as $lLang) {
          $lParams['language'] = $lLang;

          $lExists = $this -> multiArraySearch($lRet, $lParams);
          if(empty($lExists)) {
            $lGroup = 1;
            $lNoTranslations = $lPackTypes = '';
            $lParams['language'] = 'MA';
            $lExists = $this -> multiArraySearch($lRet, $lParams);
            if(!empty($lExists)){
              $lGroup = $lRet[$lExists[0]]['group'];
              $lNoTranslations = $lRet[$lExists[0]]['ntn'];
              $lPackTypes = $lRet[$lExists[0]]['packtypes'];
            }
            
            $lRet[] = array(
                'content_id' => 0, 'parent_id' => $lParentId, 'content' => '', 'category' => $lCategory, 'language' => $lLang,
                'version' => 1, 'group' => $lGroup, 'position' => '', 'metadata' => '', 'ntn' => $lNoTranslations, 'packtypes' => $lPackTypes,
                'type' => $aType, 'layout' => $lLayout, 'status' => 'draft'
            );
          }
        }
      } else {
        $lCategory = $aCont['categories'];
        $this -> mOrder = CCms_Job::getOrder($lCategory, $this -> mOrder);
        $lOrder = $this -> mOrder[$lCategory];
  
        //fill out all languages for job
        $lParams = array('parent_id' => $lOrder, 'type' => $aType, 'category' => $lCategory, 'layout' => $lLayout);
        foreach($lLangs as $lLang) {
          $lParams['language'] = $lLang;

          $lExists = $this -> multiArraySearch($lRet, $lParams);
          if(empty($lExists)) {
            $lRet[] = array(
              'content_id' => 0, 'parent_id' => $aCont['parent_id'], 'content' => '', 'category' => $lCategory, 'language' => $lLang,
              'version' => 1, 'group' => 1, 'position' => '', 'metadata' => '', 'ntn' => '', 'packtypes' => '', 'type' => $aType, 'layout' => $lLayout, 'status' => 'draft'
            );
          }
        }
      }
    }
  
    return $lRet;
  }
  
  public function export($aData = array(), $aLangs, $aTyp = 'xml') {
    $lLangs = array_filter( array_map('trim', explode(",", $aLangs)) );
    $aTyp = 'get'.ucfirst($aTyp);
    
    $lContent = array();
    foreach($aData as $lIdx => $lCont) {
      $lParent = $lCont['parent_id'];
      $lLang = $lCont['language'];
      $lCategory = $lCont['category'];
      
      if($lCategory == "Nutrition") continue;
      if(strpos($lCont['ntn'], $lLang) !== FALSE) continue;
      
      $lContent[$lCategory][$lParent][$lLang] = $lCont;

      foreach($lLangs as $lIdx => $lJobLang) {
        if(!array_key_exists($lJobLang, $lContent[$lCategory][$lParent])) {
          $lContent[$lCategory][$lParent][$lJobLang] = array("content_id" => 0, "content" => '');
        }
      }
    }
    
    $lRet = $this -> $aTyp($lContent);
    
    return $lRet;
  }
  
  protected function getXml($aContent) {
    $lRet = "<?xml version='1.0' encoding='UTF-8'?>".LF;
    $lRet.= "<copycontent>".LF;
    
    //build up job details
    $lRet.= " <job>".LF;
    $lPhraseFields = array_merge( array('jobnr','src'), array_values($this -> mPhraseFields) );
    $lFie = CCor_Res::getByKey('alias', 'fie', array("mand" => MID));
    foreach ($lFie as $lKey => $lDef) {
      $lFla = intval($lDef['flags']);
      if (bitset($lFla, ffMetadata) || in_array($lKey, $lPhraseFields)) {
        $lFieldVal = (empty($this -> mJob[$lKey])) ? '' : $this -> mJob[$lKey];
        $lRet.= "   <".$lKey.">".htmlspecialchars($lFieldVal, ENT_QUOTES | ENT_IGNORE, 'UTF-8')."</".$lKey.">".LF;
      }
    }
    $lRet.= " </job>".LF;
    
    //build up phrase content
    $lRet.= " <copy>".LF;
    foreach($aContent as $lCategory => $lArr) {
      foreach($lArr as $lParentId => $lArr2) {
        $lRet.= '   <copyelement id="'.$lParentId.'" copyelementType="'.$lCategory.'">'.LF;
        foreach($lArr2 as $lLang => $lArr3) {
          $lRet.= '     <value id="'.$lArr3['content_id'].'" language="'.$lLang.'">'.htmlspecialchars($lArr3['content'], ENT_QUOTES | ENT_IGNORE, 'UTF-8').'</value>'.LF;
        }
        $lRet.= "   </copyelement>".LF;
      }
    }
    $lRet.= " </copy>".LF;
    $lRet.= "</copycontent>".LF;
    
    return $lRet;
  }
  
  protected function getExcel($aContent) {
    $lXls = new CApi_Xls_Writer();
    $lCategories = CCor_Res::get('categories');
    $lLanguages = CCor_Res::get('htb', 'dln');
    
    $lXls -> addField('category', lan("lib.category"));
    foreach($aContent as $lCategory => $lArr) {
      foreach($lArr as $lParentId => $lArr2) {
        foreach($lArr2 as $lLang => $lArr3) {
          $lXls -> addField($lLang, $lLanguages[ $lLang ]);
        }
      }
    }
    $lXls -> writeCaptions();
    $lXls -> switchStyle();
    
    foreach($aContent as $lCategory => $lArr) {
      foreach($lArr as $lParentId => $lArr2) {
        $lXls -> writeAsString( $lCategories[ $lCategory ] );
        
        foreach($lArr2 as $lLang => $lArr3) {
          $lContent = trim(strip_tags(ereg_replace("[[:cntrl:]]", " ", $lArr3['content']))); //remove tags from content
          $lXls -> write( utf8_decode($lContent) );
        }
        
        $lXls -> newLine();
        $this -> mCtr++;
        $lXls -> switchStyle();
      }
    }
    
    return $lXls -> getContent();
  }
  
  public function multiArraySearch($aArr, $aParams) {
    $lRes = array();

    foreach ($aArr as $lKey => $lVal) {
      foreach ($aParams as $lParamKey => $lParamVal) {
        if (!isset($lVal[$lParamKey]) || $lVal[$lParamKey] != $lParamVal){
          continue 2;
        }
      }

      $lRes[] = $lKey;
    }

    return $lRes;
  }
  
  public function sortContent($aData) {
    $lTypes = $lCats = $lLayouts = $lGroups = $lLangs = array();
    foreach($aData as $lIdx => $lArr) {
      $lTypes[$lIdx] = $lArr['type'];
      $lCats[$lIdx] = $lArr['category'];
      $lLayouts[$lIdx] = $lArr['layout'];
      $lGroups[$lIdx] = $lArr['group'];
      $lLangs[$lIdx] = $lArr['language'];
    }
    
    array_multisort($lTypes, SORT_ASC, $lCats, SORT_ASC, $lLayouts, SORT_ASC, $lGroups, SORT_ASC, $lLangs, SORT_ASC, $aData);
  }
}