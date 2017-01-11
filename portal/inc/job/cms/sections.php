<?php
/**
 * Title
 *
 * Description
 *
 * @package    package
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Job_Cms_Sections extends CHtm_Form {
  
  public function __construct($aData = array(), $aSize = 0, $aJob = array(), $aSrc) {
    $this -> mSrc = $aSrc;
    $this -> mData = $aData;
    $this -> mSize = $aSize;
    $this -> mSection = array();
    $this -> mFac = new CHtm_Fie_Fac();
    $this -> mTask = 'getBody';
    
    $lPhraseFields = CCor_Cfg::get('job-cms.fields');
    $lLangKey = $lPhraseFields['languages'];
    $this -> mLangs = array(
      "master" => array("MA"),
      "translation" => array_filter( array_map('trim', explode(",", $aJob[$lLangKey])) )
    );
    $this -> mLangOptions = CCor_Res::get('htb', 'dln');
    
	$lType = CCor_Res::extract('code', 'eve_phrase', 'crpmaster');
	$this -> mPhraseTyp = $lType[$this -> mSrc];
    
    $this -> mUsr = CCor_Usr::getInstance();
    $lTyp = ($this -> mPhraseTyp == 'job') ? '' : '.product';
    $this -> mCanInsert = $this -> mUsr -> canInsert('job-cms'.$lTyp); //job-cms[insert] or job-cms.product[insert]
    $this -> mCanDelete = $this -> mUsr -> canDelete('job-cms'.$lTyp); //job-cms[delete] or job-cms.product[delete]
    $this -> mCmsDat = new CJob_Cms_Dat($this -> mSrc);
    
    $this -> setHtml();
  }
  
  public function getHtml() {
    return $this -> mSections;
  }
  
  protected function setHtml() {
    $this -> mCount = $this -> mSize + 1;
    
    foreach($this -> mData as $lCategory => $lArr){
      $this -> mCons = array();
      foreach($lArr as $lLayout => $lContents) {
        $lFnc = (stristr($lLayout, 'nutri')) ? 'getLayoutNutri' : 'getLayout'.ucfirst($lLayout);
        $lDef = $this -> getFieldDef($lLayout, FALSE);
        
        foreach($lContents as $lGroup => $lVals) {
          $lRet = ($this -> hasMethod($lFnc)) ? $this -> $lFnc($lCategory, $this -> mTask, $lVals, $lDef) : $this -> getLayoutGeneric($lCategory, $this -> mTask, $lVals, $lDef);
          $this -> mSections[$lCategory][] = $lRet;
        }
      }
    }
  }

  protected function getFieldDef($aLayout = 'memo', $aEditable = TRUE) {
    $lDef = array("mand" => MID, "typ" => 'string', "attr" => 'a:1:{s:5:"class";s:8:"inp w50p";}', 'flags' => 0);
    switch(true) {
      case stristr($aLayout, 'memo'):
        $lDef["typ"] = 'memo';
        $lDef["attr"] = 'a:2:{s:5:"class";s:9:"inp w100p";s:4:"rows";s:1:"5";}';
        break;
      case stristr($aLayout, 'nutri'):
        $lDef['layout'] = $aLayout;
        $lDef["attr"] = 'a:1:{s:5:"class";s:9:"inp w100p";}';
        $lFnc = 'getLayoutNutri';
        break;
      case stristr($aLayout, 'multi'):
        $lDef["attr"] = 'a:1:{s:5:"class";s:13:"inp mt5 w100p";}';
        break;
      case stristr($aLayout, 'rich'):
        $lDef["typ"] = ($aEditable) ? 'rich' : 'memo';
        $lDef["attr"] = ($aEditable) ? 'a:3:{s:5:"class";s:8:"inp w50p";s:11:"data-height";s:3:"350";s:9:"data-btns";s:21:"bold,italic,underline";}' : 'a:2:{s:5:"class";s:9:"inp w100p";s:4:"rows";s:1:"5";}';
        break;
    }
  
    return $lDef;
  }
  
  protected function getLayoutGeneric($aCat, $aTask, $aArr, $aDef) {
    $this -> mCons = array();
    $lCont = $aArr;
    
    $lRet = $this -> getStartTag();
    $lRet.= $this -> getHeader($aCat, $lCont["MA"][0]['position']);

    $lRet.='<div class="content fl w100p dn">';
    foreach($this -> mLangs as $lType => $lLangs) {
      $lRet.= '<div class="'.$lType.' fl p8 w400">';
      foreach($lLangs as $lLang){
        $lCat = $aCat . "_" . $this -> mCount . "_" . strtolower($lLang);
        $this -> mFac -> mIds[$lCat] = $lCat;
        $aDef['alias'] = $lCat;
        
        if(!array_key_exists($lLang, $lCont)) {
          $lContent = array(
              "content_id" => 0,
              "parent_id" => $lCont['MA'][0]['parent_id'],
              "category" => $lCont['MA'][0]['category'],
              "language" => $lLang,
              "version" => 1,
              "group" => $lCont['MA'][0]['group'],
              "type" => $lCont['MA'][0]['type'],
              "layout" => $lCont['MA'][0]['layout'],
              "content" => '',
              "position" => null,
              "metadata" => array(),
              "status" => "draft"
          );
          $lCont[$lLang][0] = $lContent; 
        }

        $this -> setConsolidation($lType, $lLang, $lCont[$lLang][0]);
        if($lCont[$lLang][0]['layout'] != 'rich') {
        	$lCont[$lLang][0]['content'] =  strip_tags( $lCont[$lLang][0]['content'] );
        }
        $lRet.= $this -> $aTask($lCat, $lCont[$lLang][0], $aDef, $lType.'_'.strtolower($lLang)); //get translation language(s) from $lCont
      }
      $lRet.= '</div>';
    }
    $lRet.= '</div>';

    $lRet.= $this -> getConsolidation($aCat);
    $lRet.= $this -> getEndTag();
    $this -> mCount++;
    
    return $lRet;
  }
  
  protected function getLayoutMulti($aCat, $aTask, $aArr, $aDef) {
    $this -> mCons = array();
    $lCont = $aArr;
    $lRet = $this -> getStartTag();
    $lRet.= $this -> getHeader($aCat, $lCont["MA"][0]['position']);
    
    $lSize = sizeof($lCont["MA"]);
    for($lI=0; $lI < $lSize; $lI++) {
      $lRet.='<div class="content fl w100p dn">';
      foreach($this -> mLangs as $lType => $lLangs) {
        $lRet.= '<div class="'.$lType.' fl p8 w400">';
        foreach($lLangs as $lLang){
          $lCat = $aCat. "_" . $this -> mCount . "_" . strtolower($lLang);
          $this -> mFac -> mIds[$lCat] = $lCat;
          $aDef['alias'] = $lCat;
        
          if(!array_key_exists($lLang, $lCont)) {
            $lContent = array(
                "content_id" => 0,
                "parent_id" => $lCont['MA'][$lI]['parent_id'],
                "category" => $lCont['MA'][$lI]['category'],
                "language" => $lLang,
                "version" => 1,
                "group" => $lCont['MA'][$lI]['group'],
                "type" => $lCont['MA'][$lI]['type'],
                "layout" => $lCont['MA'][$lI]['layout'],
                "content" => '',
                "position" => null,
                "metadata" => array(),
                "status" => "draft"
            );
            $lCont[$lLang][$lI] = $lContent; 
          }
  
          $this -> setConsolidation($lType, $lLang, $lCont[$lLang][$lI]);
          $lCont[$lLang][$lI]['content'] =  strip_tags( $lCont[$lLang][$lI]['content'] );
          $lBody = $this -> $aTask($lCat, $lCont[$lLang][$lI], $aDef, $lType.'_'.strtolower($lLang));
          
          $lRet.= ($lCont[$lLang][$lI]['layout'] == 'dict') ? $this -> getMetadataField($lBody, $lCont[$lLang][$lI], $lCat) : $lBody;
        }
        $lRet.= '</div>';
      }
      $lRet.= '</div>';
      $this -> mCount++;
    }

    $lRet.= $this -> getConsolidation($aCat);
    $lRet.= $this -> getEndTag();
  
    return $lRet;
  }
  
  protected function getLayoutDict($aCat, $aTask, $aArr, $aDef) {  
    return $this -> getLayoutMulti($aCat, $aTask, $aArr, $aDef);
  }

  protected function getLayoutNutri($aCat, $aTask, $aArr, $aDef) {
    $lDef = $aDef;
    $lDef['attr'] = 'a:1:{s:5:"class";s:12:"inp w100p dn";}';
    $lStatus = 'draft';
  
    $lHtm = 'nutri';
    if (array_key_exists('layout', $lDef) AND !empty($lDef['layout'])) {
      $lHtm = $lDef['layout'];
      unset($lDef['layout']);
    }
  
    $lRet = $this -> getStartTag();
    $lCat = $aCat."_". $this -> mCount."_ma";
    $lRet.= $this -> getHeader($lCat, '');
  
    foreach($this -> mLangs as $lType => $lLangs) {
      foreach($lLangs as $lLang){
        $this -> mCons = array();
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
          $lTpl -> setPat("val.".$lColumn, $lInput.$lArr['content']);
          $this -> mCount++;
          
          if($lArr['status'] == 'approved') {
            $lStatus = $lArr['status'];
          }
        }
  
        $lFlan  = $lTpl -> findPatterns('lan.');
        foreach($lFlan as $lLan) {
          $lTpl -> setPat("lan.".$lLan, lan("phrase.".$lLan));
        }

        $lCont = array('content' => $lTpl->getContent(), 'metadata' => array(), 'status' => $lStatus);
        $this -> setConsolidation($lType, $lLang, $lCont);
        $lRet.= $this -> getConsolidation($aCat);
      }
    }
    $lRet.= $this -> getEndTag();
  
    return $lRet;
  }
  
  protected function getStartTag(){
    return '<div class="item">';
  }
  
  public function getEndTag() {
    return '</div>';
  }

  protected function getHeader($aCat, $aLoc = '') {
    $lCat = $aCat."_". $this -> mCount."_ma";
    $aLoc = explode(",", $aLoc);
    
    $lRet = '';
    if(!empty($aLoc) && !in_array($lCat, $this -> mFac -> mIds)){
      $lArr = array('fop' => 'Front of Pack', 'bop' => 'Back of Pack', 'sop' => 'Side of Pack');
      
      $lRet.= '  <div style="display:none">';
      foreach($lArr as $lLoc => $lText) {
        $lName = $lCat.'_position_'.$lLoc;
        $lSel = (in_array($lLoc, $aLoc) !== FALSE) ? 'checked="checked"' : "";

        $lRet.= '<input type="checkbox" id="'.$lName.'" value="'.$lLoc.'" name="meta['.$lName.']" '.$lSel.'>'.LF;
      }
      $lRet.= '</div>';
    }
    
    return $lRet;
  }

  protected function getBody($aCat, $aCont, $aDef, $aClass) {
    list($lCat, $lCont, $lDef, $lClass) = array($aCat, $aCont, $aDef, $aClass);
    $lClass = (strpos($lClass, 'translation') > -1) ? $lClass." dn" : $lClass; //initially hide translation content
    $lAttr = unserialize($lDef["attr"]);
    if(empty($lCont['content'])){
      $lAttr['class'] = $lAttr['class']." no_content";
    }
    
    $lType = ($lCont['type'] == 'product') ? 'product' : 'job';
    $lCanMaster = $this -> mUsr -> canEdit('job-cms.'.$lType.'.master'); //job-cms.job.master[edit] or job-cms.product.master[edit]
    $lCanTranslate = $this -> mUsr -> canEdit('job-cms.'.$lType.'.translation'); //job-cms.job.translation[edit] or job-cms.product.translation[edit]
    if(($lCont['language'] == 'MA' && !$lCanMaster) || ($lCont['language'] !== 'MA' && !$lCanTranslate)) {
      $lAttr['class'] = $lAttr['class']." dis";
      $lAttr['disabled'] = "disabled";
    }
    $lAttr['readonly'] = "readonly";
    
    if($this -> mPhraseTyp == 'product') {
      $lBlur = (array_key_exists('onblur', $lAttr)) ? $lAttr['onblur'] : '';
      $lAttr['onblur'] = $lBlur . " Flow.cmsForm.checkContent('".$lCat."');";
    }
    $lDef['attr'] = serialize($lAttr);
    
    $lRet = '<div class="'.$lClass.' fl">';
    $lRet.= '  <input type="hidden" name="meta['.$lCat.'_type]" value="'.$lCont['type'].'" />';
    $lRet.= '  <input type="hidden" name="meta['.$lCat.'_layout]" value="'.$lCont['layout'].'" />';
    $lRet.= '  <input type="hidden" name="meta['.$lCat.'_group]" value="'.$lCont['group'].'" />';
    $lRet.= '  <input type="hidden" name="meta['.$lCat.'_ntn]" value="'.$aCont['ntn'].'" />';
    $lRet.= '  <input type="hidden" name="meta['.$lCat.'_packtypes]" value="'.$aCont['packtypes'].'" />';
    $lRet.= '  <input type="hidden" name="meta['.$lCat.'_status]" value="'.$lCont['status'].'" />';
    $lRet.= '  <input type="hidden" name="content['.$lCat.'_cid]" value="'.$lCont['content_id'].'" />';
    $lRet.= '  <input type="hidden" name="content['.$lCat.'_pid]" value="'.$lCont['parent_id'].'" />';
    $lRet.= '  <input type="hidden" name="content['.$lCat.'_ver]" value="'.$lCont['version'].'" />';
    $lRet.= $this -> mFac -> getInput($lDef, $lCont['content']);
    $lRet.= '</div>';
    
    return $lRet;
  }
  
  protected function setConsolidation($aTyp, $aLang, $aCont) {
    $lContent = $aCont['content'];
    $lMetadata = (isset($aCont['metadata'][0])) ? $aCont['metadata'][0] : '';
    $lKey = $aTyp.'_'.strtolower($aLang);
    
    if(!empty($lContent)){
      if(!empty($lMetadata) && is_numeric($lMetadata)) {
        $this -> mCons[$lKey]['content'][] = $lContent.' ('.$lMetadata.'%)';
      } else {
        $this -> mCons[$lKey]['content'][] = $lContent;
      }
      $this -> mCons[$lKey]['status'] = $aCont['status'];
    } else {
      if(empty($this -> mCons[$lKey])){
        $this -> mCons[$lKey]['content'] = array();
        $this -> mCons[$lKey]['status'] = '';
      }
    }
  }
  
  protected function getConsolidation($aCat) {
    $lRet = '';
    $lLangOpt = array_change_key_case($this -> mLangOptions, CASE_LOWER);
    foreach($this->mCons as $lType => $lValue) {
      list($lLangType, $lLang) = explode("_", $lType);
      $lClass = $lType." consolidation fl performance-facts";
      $lClass = (strpos($lClass, 'translation') > -1) ? $lClass." dn" : $lClass; //initially hide translation content
      $lClass = (empty($lValue['content'])) ? $lClass." no_content" : $lClass;
      
      $lHeader = $lLangOpt[$lLang];
      if(strpos($lType, 'master') !== false && $this -> mCanDelete) {
        $lAttr = array(
          "class" => "removeBtn",
          "onclick" => "Flow.cmsForm.removeContent(this);",
          "style" => "cursor:pointer;float:right;"
        );
    
        $lHeader.= img("img/ico/16/cancel.gif", $lAttr);
      }
    
      $lRet.= '<div class="'.$lClass.'">';
      $lRet.= '<div class="header td2 b p8 content_'.$lValue['status'].'">'.$lHeader.'</div>';
      $lRet.= '<div class="p8">';
      $lRet.= implode(", ", $lValue['content']);
      $lRet.= '</div></div>';
    }
    
    return $lRet;
  }
  
  protected function getRelatedArea($aCont) {
    $lRet = $this -> getAdditionalField($aCont['ntn'], 'languages', 'ntn');
    
    if(CCor_Cfg::get("phrase.job-rel.meta", FALSE)) {
      $lRet.= $this -> getAdditionalField($aCont['packtypes'], 'packtype', 'packtypes', TRUE);
    }
    
    return $lRet;
  }
  
  protected function getMetadataField($aHtm, $aCont, $aCat) {
    $lPlaceholder = (strpos($aCat, 'ingredients') == 0 || strpos($aCat, 'zutaten') == 0) ? '%' : '';
    $lVal = (array_key_exists(0, $aCont['metadata'])) ? $aCont['metadata'][0] : '';
    
    $lRet = str_replace('</div>', '', $aHtm);
    $lRet.= '<input id="'.$aCat.'111" type="text" name="meta['.$aCat.'_meta]" placeholder="'.$lPlaceholder.'" class="inp w50 m08" value="'.$lVal.'" /></div>';//add button and </div>
    $lRet.= "<script>";
    $lRet.= "Flow.cmsContent.text['".$aCat."'] = '".$aCont['content']."';";
    $lRet.= "</script>";
    
    return $lRet;
  }
  
  protected function getAdditionalField($aCont, $aField, $aClass, $aSingle = FALSE) {
    $lFie  = CCor_Res::get('fie');
    $lMaster = CCor_Cfg::get('masterlanguage', 'EN');
    $lPhraseFields = CCor_Cfg::get('job-cms.fields');
    $lJobKey = $lPhraseFields[$aField];
    

    $lSelected = array_filter( array_map('trim', explode(" ", $aCont)) );
    $lJobValues = array_filter( array_map('trim', explode(",", $this -> mJob[$lJobKey])) );
    if($aField == 'languages' && ($lKey = array_search($lMaster, $lJobValues)) !== false) {
      unset($lJobValues[$lKey]);
    }

    $lSize = sizeof($lJobValues);
    $lMinSize = ($aSingle) ? 1 : 0;
    if($lSize > $lMinSize) {
      $lRet = '<div class="card">';
      $lRet.= '<div class="card-header">'.lan("lib.phrase.".$aClass).'</div>';
      $lRet.= '<div class="card-block p8">';
      if($lSize > 1) {
        $lChecked = ($lSelected == $lJobValues) ? ' checked="checked"' : '';
        $lRet.= '<input type="checkbox" value="ALL" onclick="javascript:gIgn=1; Flow.cmsContent.record(this, \''.$aClass.'\');"'.$lChecked.'>&nbsp;<b>'.lan("lib.all").'</b>'.BR.LF;
      }
      foreach($lJobValues as $lValue) {
        $lChecked = (in_array($lValue, $lSelected)) ? ' checked="checked"' : '';
        $lName = ($aFields == 'languages') ? $this -> mLangOptions[ $lValue ] : $lValue;
        $lRet.= '<input type="checkbox" value="'.$lValue.'" name="'.$aClass.strtolower($lValue).'" onclick="javascript:gIgn=1; Flow.cmsContent.record(this, \''.$aClass.'\');"'.$lChecked.'>&nbsp;'.$lName.BR.LF;
      }
      $lRet.= '</div>';
      $lRet.= '</div>';
    }
    
    return $lRet;
  }
  
  protected function setupContent($aData) {
      $lRet = array();

      foreach($aData as $lIdx => $lContent) {
        $lCategory = $lContent['category'];
        $lLayout = $lContent['layout'];
        $lGroup = $lContent['group'];
        $lLang = $lContent['language'];
        
        if($lContent['type'] == $this -> mTyp) {
          if(!isset($lRet[$lCategory][$lLayout][$lGroup][$lLang])){
            $lRet[$lCategory][$lLayout][$lGroup][$lLang] = array();
          }
          array_push($lRet[$lCategory][$lLayout][$lGroup][$lLang], $lContent);
        }
      }
    
      return $lRet;
  }

}