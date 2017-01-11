<?php
/**
 * Title
 *
 * Description
 *
 * @package    package
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Job_Cms_Content_Sections extends CJob_Cms_Sections {
  
  public function __construct($aData = array(), $aCombos = array(), $aJob = array(), $aActive = 1, $aSrc) {
    $this -> mSrc = $aSrc;
    $this -> mActive = $aActive;
    $this -> mJob = $aJob;

    $lType = CCor_Res::extract('code', 'eve_phrase', 'crpmaster');
    $lPhraseTyp = $lType[$this -> mSrc];
    
    $this -> mTyp = ($lPhraseTyp == 'job') ? 'content' : 'product';
    $this -> mCombos = $aCombos;
    $this -> mData = $this -> setupContent($aData);
    $this -> mTask = 'getBody';
    $this -> mLangs = array('master' => "MA");

    $this -> mSize = 1000;
    $this -> mSection = array();
    $this -> mFac = new CHtm_Fie_Fac();
    $this -> mCmsDat = new CJob_Cms_Dat($this -> mSrc);
    $this -> mLangOptions = CCor_Res::get('htb', 'dln');
    
    $this -> mUsr = CCor_Usr::getInstance();
    $lTyp = ($lPhraseTyp == 'job') ? '' : '.product';
    $this -> mCanInsert = $this -> mUsr -> canInsert('job-cms'.$lTyp); //job-cms[insert] or job-cms.product[insert]
    $this -> mCanDelete = $this -> mUsr -> canDelete('job-cms'.$lTyp); //job-cms[delete] or job-cms.product[delete]

    $this -> setHtml();
  }
  
  protected function setHtml() {
    $this -> mCount = $this -> mSize + 1;
    
    $lSection = 1;
    foreach($this -> mCombos as $lKey => $lCombo) {
      $lCategory = $lCombo['category'];
      $lLayout = $lCombo['layout'];
      $lAmount = intval( $lCombo['amount'] );

      $lDef = $this -> getFieldDef($lLayout);
      for($lIdx=1; $lIdx <= $lAmount; $lIdx++){
        $lData = (array_key_exists($lCategory, $this -> mData)) ? $this -> mData[$lCategory][$lLayout][$lIdx] : array();
        if(empty($lData)) {
          foreach($this -> mLangs as $lLangIdx => $lLang) {
            $lContent[$lLang] = array();
            $lArr = array(
                'content_id' => 0, 'parent_id' => 0, 'content' => '', 'category' => $lCategory, 'language' => $lLang,
                'version' => 1, 'group' => $lIdx, 'position' => '', 'metadata' => '', 'packtypes' => '', 'ntn' => '', 'type' => $this -> mTyp, 'layout' => $lLayout, 'status' => 'draft'
            );
            $lStatus = 'draft';
            
            array_push($lContent[$lLang], $lArr);
          }
        } else {
          $lContent = $lData;
          
          $lLang = reset($this -> mLangs);
          $lStatus = $lContent[$lLang][0]['status'];
        }

        $lFnc = (stristr($lLayout, 'nutri')) ? 'getLayoutNutri' : 'getLayout'.ucfirst($lLayout);
        $lLayouts = ($this -> hasMethod($lFnc)) ? $this -> $lFnc($lCategory, $this -> mTask, $lContent, $lDef) : $this -> getLayoutGeneric($lCategory, $this -> mTask, $lContent, $lDef);

        $lClass = ($lSection !== $this -> mActive) ? ' dn' : '';
        $lDataCat = (strpos($lCategory, lan('lib.summary')) > -1) ? $lCategory.' &nbsp;' : $lCategory.' '.$lIdx;
        $lDataState = (strpos($lCategory, lan('lib.summary')) > -1) ? '' : $lStatus;
        $lRet = '<div data-layout="'.$lLayout.'" data-category="'.$lDataCat.'" data-status="'.$lDataState.'" class="content_section'.$lClass.'" id="section'.$lSection.'">';
        $lRet.= $lLayouts;
        $lRet.= '</div>';
        
        $this -> mSections[] =  $lRet;
        $lSection++;
      }
    }
    
    $this -> mSections = implode("", $this -> mSections);
  }
  
  protected function getLayoutGeneric($aCat, $aTask, $aArr, $aDef) {
    $lCont = $aArr;
    $lRet = $this -> getStartTag();
    
    $lRet.='<div class="content w100p">';
    $lRet.= '<div class="card"><div class="card-header status_class"><b>'.lan("lib.status").'</b> <span class="status_title"></span></div>';
    foreach($this -> mLangs as $lType => $lLang) {
      $lRet.= '<div class="'.$lType.' p8 w100p card-block">';
      $lCat = $aCat . "_" . $this -> mCount . "_" . strtolower($lLang);
      $this -> mFac -> mIds[$lCat] = $lCat;
      $aDef['alias'] = $lCat;

      if($lCont[$lLang][0]['layout'] != 'rich') {
        $lCont[$lLang][0]['content'] =  strip_tags( $lCont[$lLang][0]['content'] );
      }
      $lRet.= $this -> $aTask($lCat, $lCont[$lLang][0], $aDef, $lType.'_'.strtolower($lLang)); //get translation language(s) from $lCont
      $lRet.= '</div>';
    }
    $lRet.= '</div>';
    $lRet.= $this -> getRelatedArea($lCont['MA'][0]);
    $lRet.= '</div>';

    $lRet.= $this -> getEndTag();
    $this -> mCount++;
    
    return $lRet;
  }
  
  protected function getLayoutMulti($aCat, $aTask, $aArr, $aDef) {
    $lCont = $aArr;
    $lRet = $this -> getStartTag();
    
    $lSize = sizeof($lCont['MA']);
    for($lI=0; $lI < $lSize; $lI++) {
      $lRet.='<div class="content w100p">';
      foreach($this -> mLangs as $lType => $lLang) {
        $lRet.= '<div class="'.$lType.' p8 w100p">';
        $lCat = $aCat. "_" . $this -> mCount . "_" . strtolower($lLang);
        $this -> mFac -> mIds[$lCat] = $lCat;
        $aDef['alias'] = $lCat;

        $lCont[$lLang][$lI]['content'] =  strip_tags( $lCont[$lLang][$lI]['content'] );
        $lBody = $this -> $aTask($lCat, $lCont[$lLang][$lI], $aDef, $lType.'_'.strtolower($lLang));
          
        $lRet.= ($lCont[$lLang][$lI]['layout'] == 'dict') ? $this -> getMetadataField($lBody, $lCont[$lLang][$lI], $lCat) : $lBody;
        $lRet.= '</div>';
      }
      $lRet.= '</div>';
      $this -> mCount++;
    }
    $lRet.= $this -> getRelatedArea($lCont['MA'][0]);
    $lRet.= $this -> getEndTag();
  
    return $lRet;
  }

  protected function getLayoutNutri($aCat, $aTask, $aArr, $aDef) {
    $lDef = $aDef;
    
    $lHtm = 'nutri';
    if (array_key_exists('layout', $lDef) AND !empty($lDef['layout'])) {
      $lHtm = $lDef['layout'];
      unset($lDef['layout']);
    }
  
    $lRet = $this -> getStartTag();
    foreach($this -> mLangs as $lType => $lLang) {
      $lClassTyp = $lType.'_'.strtolower($lLang);
      $lClassTyp = (strpos($lClassTyp, 'translation') > -1) ? $lClassTyp." dn" : $lClassTyp; //initially hide translation content
      $lRet.= '<section class="'.$lClassTyp.' content'.$lType.' p8 w100p">';

      $lTpl = new CCor_Tpl();
      $lTpl -> openProjectFile('job/cms/'.$lHtm.'.htm');

      $lFval  = $lTpl -> findPatterns('val.');
      foreach($lFval as $lColumn) {
        $lCat = $lDef['alias'] = $lColumn . "_" . $this -> mCount . "_" . strtolower($lLang);
        $this -> mFac -> mIds[$lCat] = $lCat;

        $lExists = $this -> mCmsDat -> multiArraySearch($aArr[$lLang], array('metadata' => $lColumn));
        if(empty($lExists)){
          $lGroup = $aArr['MA'][0]['group'];
          $lContType = $aArr['MA'][0]['type'];
          $lArr = array(
              'content_id' => 0, 'parent_id' => 0, 'content' => '', 'category' => 'Nutrition', 'language' => $lLang,
              'version' => 1, 'group' => $lGroup, 'position' => '', 'metadata' => $lColumn, 'type' => $lContType, 'layout' => $lHtm, 'status' => "draft"
          );
        } else $lArr = $aArr[$lLang][$lExists[0]];
        $lInput = $this -> $aTask($lCat, $lArr, $lDef, $lColumn.'_'.strtolower($lLang));
        $lTpl -> setPat("val.".$lColumn, $lInput);
        
        $this -> mCount++;
      }
  
      $lFlan  = $lTpl -> findPatterns('lan.');
      foreach($lFlan as $lLan) {
        $lTpl -> setPat("lan.".$lLan, lan("phrase.".$lLan));
      }

      $lRet.= $lTpl -> getContent();
      $lRet.= "</section>";
    }
    $lRet.= $this -> getRelatedArea($aArr['MA'][0]);
    $lRet.= $this -> getEndTag();
  
    return $lRet;
  }

  protected function getBody($aCat, $aCont, $aDef, $aClass) {
    list($lCat, $lCont, $lDef, $lClass) = array($aCat, $aCont, $aDef, $aClass);
    $lAttr = unserialize($lDef["attr"]);
    $lAttr['class'] = $lAttr['class']." content_search";
    if(empty($lCont['content'])){
      $lAttr['class'] = $lAttr['class']." no_content";
    }
    
    if($this -> mTyp == 'product') {
      $lOnBlur = (array_key_exists('onblur', $lAttr)) ? $lAttr["onblur"] : '';
      $lAttr['onblur'] = $lOnBlur . " Flow.cmsForm.checkContent('".$lCat."');";
    }
    if(in_array($lCont['layout'], array('multi', 'dict')) !== false) {
      $lAttr['onblur'] = " Flow.cmsContent.duplicateItem('".$lCat."');";
    }
    $lDef['attr'] = serialize($lAttr);
    
    $lRet = '<div class="w100p">';
    $lRet.= '  <input type="hidden" name="meta['.$lCat.'_type]" value="'.$lCont['type'].'" />';
    $lRet.= '  <input type="hidden" name="meta['.$lCat.'_layout]" value="'.$lCont['layout'].'" />';
    $lRet.= '  <input type="hidden" name="meta['.$lCat.'_group]" value="'.$lCont['group'].'" />';
    $lRet.= '  <input type="hidden" name="meta['.$lCat.'_ntn]" value="'.$lCont['ntn'].'" />';
    $lRet.= '  <input type="hidden" name="meta['.$lCat.'_packtypes]" value="'.$lCont['packtypes'].'" />';
    $lRet.= '  <input type="hidden" name="meta['.$lCat.'_status]" value="'.$lCont['status'].'" />';
    $lRet.= '  <input type="hidden" name="content['.$lCat.'_cid]" value="'.$lCont['content_id'].'" />';
    $lRet.= '  <input type="hidden" name="content['.$lCat.'_pid]" value="'.$lCont['parent_id'].'" />';
    $lRet.= '  <input type="hidden" name="content['.$lCat.'_ver]" value="'.$lCont['version'].'" />';
    $lRet.= $this -> mFac -> getInput($lDef, $lCont['content']);
    $lRet.= '</div>';
    
    return $lRet;
  }

}