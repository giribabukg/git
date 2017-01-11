<?php
/**
 * Title
 *
 * Description
 *
 * @package    package
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Job_Cms_Apl_Sections extends CJob_Cms_Content_Sections {
  
  public function __construct($aData = array(), $aCombos = array(), $aLang, $aTask, $aActive = 1, $aTyp = 'job', $aSrc) {
  	$this -> mSrc = $aSrc;
    $this -> mActive = $aActive;
    $this -> mTyp = ($aTyp == 'job') ? 'content' : 'product';
    $this -> mCombos = $aCombos;
    $this -> mData = $this -> setupContent($aData);
    list($this -> mAplTyp, $this -> mTask, $this -> mTrans) = explode(",", $aTask);
    $this -> mTask = 'act' . ucfirst($this -> mTask);
    $this -> mLangs = $aLang;

    $this -> mSize = 0;
    $this -> mSection = array();
    $this -> mFac = new CHtm_Fie_Fac();
    $this -> mCmsDat = new CJob_Cms_Dat($this -> mSrc);
    $this -> mLangOptions = CCor_Res::get('htb', 'dln');
    
    $this -> mUsr = CCor_Usr::getInstance();
    $lTyp = ($aTyp == 'job') ? '' : '.product';
    $this -> mCanInsert = $this -> mUsr -> canInsert('job-cms'.$lTyp); //job-cms[insert] or job-cms.product[insert]
    $this -> mCanDelete = $this -> mUsr -> canDelete('job-cms'.$lTyp); //job-cms[delete] or job-cms.product[delete]

    $this -> setHtml();
  }
  
  protected function getLayoutGeneric($aCat, $aTask, $aArr, $aDef) {
    $lCont = $aArr;
    $lRet = $this -> getStartTag();
    
    $lRet.='<div class="content fl w100p">';
    foreach($this -> mLangs as $lIdx => $lLang) {
      if($lLang !== 'MA' || sizeof($this -> mLangs) == 1) {
        $lRet.= '<div class="fl p8 w100p">';
        $lCat = $aCat . "_" . $this -> mCount . "_" . strtolower($lLang);
        $this -> mFac -> mIds[$lCat] = "content".$lCont[$lLang][0]['content_id'];
        $aDef['alias'] = $lCat;

        if($lCont[$lLang][0]['layout'] != 'rich') {
        	$lCont[$lLang][0]['content'] =  strip_tags( $lCont[$lLang][0]['content'] );
        }

        $lId = (strpos($aTask, 'Translation') > -1) ? $lCont['MA'][0]['content_id'] : $lCont[$lLang][0]['content_id'];
        $lId = (strpos($lCat, lan('lib.summary')) !== 0) ? $lId : 'summaryArea';
        $lRet.= '<div id="'.$lId.'" class="fl w100p">';
        $lRet.= $this -> $aTask($lCat, $lCont[$lLang][0], $lCont['MA'][0]);
        $lRet.= '</div>';
        
        $lRet.= '</div>';
      }
    }
    $lRet.= '</div>';

    $lRet.= $this -> getEndTag();
    $this -> mCount++;
    
    return $lRet;
  }
  
  protected function getLayoutMulti($aCat, $aTask, $aArr, $aDef) {
    $lCont = $aArr;
    
    $lRet = $this -> getStartTag();
    foreach($this -> mLangs as $lIdx => $lLang) {
      $lMaster = $this -> getMultiCont('MA', $lCont, $aCat, 'hidden', $aDef);
      $lLanguage = $this -> getMultiCont($lLang, $lCont, $aCat, 'input', $aDef);

      $lId = (strpos($aTask, 'Translation') > -1) ? $lMaster['content_id'] : $lLanguage['content_id'];
      $lRet.= '<div id="'.$lId.'" class="fl w100p">';
      $lRet.= $this -> $aTask($aCat, $lLanguage, $lMaster);
      $lRet.= '</div>';
    }
    $lRet.= $this -> getEndTag();
    
    return $lRet;
  }
   
  protected function getMultiCont($aLang, $aCont, $aCat, $aType, $aDef) {
    list($lLang, $lCont, $lDef) = array($aLang, $aCont, $aDef);
    $lContent = $lSuggestion = array();
    $lRet = array(
        'content_id' => $lCont[$lLang][0]['content_id'],
        'language' => $lLang,
        'layout' => 'rich',
        'apl_state' => $lCont[$lLang][0]['apl_state']
    );
  
    $lSize = sizeof($aCont['MA']);
    for($lI=0; $lI < $lSize; $lI++) {
      $lCat = $aCat. "_" . $this -> mCount . "_" . strtolower($lLang);
      
      $lCont[$lLang][$lI]['content'] =  strip_tags( $lCont[$lLang][$lI]['content'] );
      
      $this -> mFac -> mIds[$lCat] = "content".$lCont[$lLang][$lI]['content_id'];
      $lDef['alias'] = $lCat;
      $lState = (!empty($lCont[$lLang][$lI]['content'])) ? fsDisabled : fsStandard;
      $lContent[] = $this -> mFac -> getInput($lDef, $lCont[$lLang][$lI]['content'], $lState);


      $this -> mFac -> mIds[$lCat] = "suggestion".$lCont[$lLang][$lI]['content_id'];
      $lDef['alias'] = $lCat;
      $lState = (!empty($lCont[$lLang][$lI]['suggestion'])) ? fsDisabled : fsStandard;
      $lSuggestion[] = $this -> mFac -> getInput($lDef, $lCont[$lLang][$lI]['suggestion'], $lState);
      
      //$lRet.= ($lCont[$lLang][$lI]['layout'] == 'dict') ? $this -> getMetadataField($lBody, $lCont[$lLang][$lI], $lCat) : $lBody;
      $this -> mCount++;
    }
    $lRet['content'] = implode(BR, $lContent);
    $lRet['suggestion'] = implode(BR, $lSuggestion);
  
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
    foreach($this -> mLangs as $lIdx => $lLang) {
      $lRet.= '<section class="content fl p8 w100p">';

      $lMaster = array(
          'content_id' => $aArr['MA'][0]['content_id'],
          'content' => $this -> getTable($lHtm, 'MA', $aArr, $lDef)
      );
      $lLanguage = array(
          'content_id' => $aArr[$lLang][0]['content_id'],
          'content' => $this -> getTable($lHtm, $lLang, $aArr, $lDef),
          'suggestion' => $this -> getTable($lHtm, $lLang, $aArr, $lDef, 'input'),
          'language' => $lLang,
          'layout' => $lHtm,
          'apl_state' => $aArr[$lLang][0]['apl_state']
      );

      $lId = (strpos($aTask, 'Translation') > -1) ? $lMaster['content_id'] : $lLanguage['content_id'];
      $lRet.= '<div id="'.$lId.'" class="fl w100p">';
      $lRet.= $this -> $aTask($aCat, $lLanguage, $lMaster);
      $lRet.= '</div>';
      
      $lRet.= "</section>";
    }
    $lRet.= $this -> getEndTag();
  
    return $lRet;
  }

  protected function getTable($aHtm, $aLang, $aArr, $aDef, $aType='hidden') {
    list($lHtm, $lLang, $lDef) = array($aHtm, $aLang, $aDef);
  
    $lTpl = new CCor_Tpl();
    $lTpl -> openProjectFile('job/cms/'.$lHtm.'.htm');
  
    $lFval  = $lTpl -> findPatterns('val.');
    foreach($lFval as $lColumn) {
      $lCat = $lColumn . "_" . $this -> mCount . "_" . strtolower($lLang);
  
      $lExists = $this -> mCmsDat -> multiArraySearch($aArr[$lLang], array('metadata' => $lColumn));
      if(empty($lExists)){
        $lGroup = $aArr['MA'][0]['group'];
        $lContType = $aArr['MA'][0]['type'];
        $lArr[$lLang][0] = array(
            'content_id' => 0, 'parent_id' => 0, 'content' => '', 'category' => 'Nutrition', 'language' => $lLang,
            'version' => 1, 'group' => $lGroup, 'position' => '', 'metadata' => $lColumn, 'type' => $lContType, 'layout' => $lHtm, 'status' => "draft"
        );
      } else $lArr[$lLang][0] = $aArr[$lLang][$lExists[0]];
      $lArr[$lLang][0]['content'] =  strip_tags( $lArr[$lLang][0]['content'] );
  
      //$lInput = $this -> getContentFields($lCat, $lArr[$lLang][0]);
      $lTyp = ($aType != 'hidden') ? 'suggestion' : 'content';
      $lState = ($aType != 'hidden') ? fsStandard : fsDisabled;
      $this -> mFac -> mIds[$lCat] = $lTyp.$lArr[$lLang][0]['content_id'];
      $lDef['alias'] = $lCat;
      
      $lVal = ($lTyp == 'suggestion' && empty($lArr[$lLang][0][$lTyp])) ? $lArr[$lLang][0]['content'] : $lArr[$lLang][0][$lTyp];
      $lInput = $this -> mFac -> getInput($lDef, $lVal, $lState);
  
      $lTpl -> setPat("val.".$lColumn, $lInput);
  
      $this -> mCount++;
    }
  
    $lFlan  = $lTpl -> findPatterns('lan.');
    foreach($lFlan as $lLan) {
      $lTpl -> setPat("lan.".$lLan, lan("phrase.".$lLan));
    }
  
    return $lTpl -> getContent();
  }

  protected function getContentSec($aCont, $aLang, $aTyp, $aLabel = 'label-info', $aCat='', $lHidden=FALSE) {
    $lLabel = $aLabel;
    if(array_key_exists('apl_state', $aCont) && $aCont['apl_state'] != 2) {
      $lLabel = ($aCont['apl_state'] == 1) ? 'label-danger' : $lLabel;
      $lLabel = ($aCont['apl_state'] == 3) ? 'label-success' : $lLabel;
    }
    
    $lLangTitle = ($aLang == 'MA') ? '' : $this -> mLangOptions[$aLang].' ';
    $lTitle = ($aLang == 'MA') ? 'apl.phrase.content.titel' : 'apl.phrase.translation.titel';
    $lRet = '  <div class="pull-left w100p">';
    $lRet.= '    <span class="label '.$lLabel.' part_title">'.$lLangTitle.lan($lTitle).'</span>';
    if($lHidden) {
      $lRet.= '    <input type="hidden" name="meta['.$aCat.'_type]" value="'.$aCont['type'].'" />';
      $lRet.= '    <input type="hidden" name="meta['.$aCat.'_layout]" value="'.$aCont['layout'].'" />';
      $lRet.= '    <input type="hidden" name="meta['.$aCat.'_group]" value="'.$aCont['group'].'" />';
      $lRet.= '    <input type="hidden" name="content['.$aCat.'_cid]" value="'.$aCont['content_id'].'" />';
      $lRet.= '    <input type="hidden" name="content['.$aCat.'_pid]" value="'.$aCont['parent_id'].'" />';
      $lRet.= '    <input type="hidden" name="content['.$aCat.'_ver]" value="'.$aCont['version'].'" />';
      $lRet.= '    <input type="hidden" name="content['.$aCat.'_lang]" value="'.$aCont['language'].'" />';
      $lRet.= '    <input type="hidden" name="content['.$aCat.'_category]" value="'.$aCont['category'].'" />';
    }
    if(stristr($aCont['layout'], 'nutri')) {
      $lRet.= '   '.$aCont['content'];
    } else {
      $lCat = $aCat . "_content";
      $this -> mFac -> mIds[$lCat] = "content".$aCont['content_id'];
      $lDef = array("mand" => MID, "alias" => $lCat, "typ" => 'memo', "attr" => array('s:5:"class";s:9:"inp w100p";','s:4:"rows";s:1:"4";'), 'flags' => 0);
      if($aCont['layout'] == 'rich') {
        $lDef["typ"] = 'rich';
        $lDef["attr"] = array('s:5:"class";s:8:"inp w50p";','s:11:"data-height";s:3:"100";','s:9:"data-btns";s:21:"bold,italic,underline";');
      }

      $lState = fsStandard;
      //if content is present then
      if(!empty($aCont['content'])) {
        if($aCont['layout'] == 'rich') {
          $lDef['attr'][] = 's:8:"readonly";s:1:"1";';
        } else $lState = fsDisabled;
      }
      $lDef['attr'] = 'a:'.sizeof($lDef['attr']).':{'.implode("", $lDef['attr']).'}';
      
      $lRet.= $this -> mFac -> getInput($lDef, $aCont['content'], $lState);
    }
    $lRet.= '  </div>';
    
    return $lRet;
  }
  
  protected function getSuggestionSec($aCont, $aCat, $aTyp = 'soft') {
    $lLabel = ($aCont['apl_state'] == 2) ? 'label-success' : 'label-info';
    
    $lRet = '  <div class="pull-left w100p">';
    $lRet.= '    <span class="label '.$lLabel.' part_title">'.lan('apl.phrase.suggestion.titel').'</span>';
    if(stristr($aCont['layout'], 'nutri')) {
      $lRet.= '    '.$aCont['suggestion'];
    } else {
      $lTyp = ($aCont['layout'] == 'rich') ? 'rich' : 'memo';
      if($aCont['layout'] == 'rich') {
        $lAttr = array('s:5:"class";s:8:"inp w50p";','s:11:"data-height";s:3:"100";','s:9:"data-btns";s:21:"bold,italic,underline";');
      } else {
        $lAttr = array('s:5:"class";s:9:"inp w100p";','s:4:"rows";s:1:"4";');
      }
      $lAttr[] = 's:7:"onkeyup";s:34:"javascript:Flow.cmsApl.showBtn(2);";';
      
      $lState = fsStandard;
      //if on soft approval and suggestion is present then
      if(!empty($aCont['suggestion']) && $aTyp == 'soft') {
        if($aCont['layout'] == 'rich') {
          $lAttr[] = 's:8:"readonly";s:1:"1";';
        } else $lState = fsDisabled;
      }
    
      $lCat = $aCat . "_suggestion";
      $this -> mFac -> mIds[$lCat] = "suggestion".$aCont['content_id'];
      $lDef = array(
          'mand' => MID,
          'alias' => $lCat,
          'typ' => $lTyp,
          'attr' => 'a:'.sizeof($lAttr).':{'.implode("", $lAttr).'}',
          'flags' => 0
      );
      $lRet.= $this -> mFac -> getInput($lDef, $aCont['suggestion'], $lState);
    }
    $lRet.= '  </div>';
    
    return $lRet;
  }
  
  protected function getCommentSec($aCont, $aCat) {
    $lRet = '  <div class="commentContainer pull-left w100p">';
    $lRet.= '    <span class="label label-info part_title">'.lan('apl.phrase.comment.titel').'</span>';
    $lCat = $aCat . "_comment";
    $this -> mFac -> mIds[$lCat] = "comment".$aCont['content_id'];
    $lDef = array(
      'mand' => MID,
      'alias' => $lCat,
      'typ' => 'memo',
      'attr' => 'a:2:{s:5:"class";s:23:"inp w100p comment_field";s:4:"rows";s:1:"4";}',
      'flags' => 0
    );
    $lRet.= $this -> mFac -> getInput($lDef, $aCont['comment']);
    $lRet.= '  </div>';
    
    return $lRet;
  }
  
  protected function getOverallComment() {
    $lRet = '  <div class="overallcomment pull-left w100p">';
    $lRet.= '    <span class="label label-info part_title">'.lan('apl.phrase.overall.titel').'</span>';
    $lRet.= '    <textarea class="inp w100p comment_field" rows="5" cols="20" id="overallcomment"></textarea>';
    $lRet.= '  </div>';
    
    return $lRet;
  }
  
  protected function getApprovalBtns($aCont, $aJs) {
    $lAplButtons = ($this -> mTrans == 1) ? array(2 => 'conditional', 3 => 'approval') : array(1 => 'amendment', 2 => 'conditional', 3 => 'approval');
    $lRet = '';
    
    if($aCont['status'] !== 'approved') {
      $lRet.= '  <div class="approvalBtnContainer">';
      foreach ($lAplButtons as $lAplKey => $lAplBtn) {
        $lClass = ($lAplKey == 1) ? 'btn-danger' : 'btn-primary';
        $lClass = ($lAplKey == 2 || $lAplKey == 3) ? 'btn-success' : $lClass;
    
        $lDisplay = ($lAplKey == 2 && empty($aCont['suggestion'])) ? ' style="display:none;"' : "";
        $lRet.= '<button type="button" class="approval_buttons btn '.$lClass.' '.$lAplKey.'"'.$lDisplay.' onclick="javascript:'.$aJs.'(this, \'';
        $lRet.= $lAplKey.'\', ';
        $lRet.= $aCont['content_id'].');">';
        
        $lLangTyp = ($this -> mLangs[0] == 'MA') ? 'master' : 'translation';
        $lLangFile = 'apl.phrase.'.$this -> mAplTyp.'.'.$lLangTyp.'.'.$lAplBtn; //soft or hard, master or language
        $lRet.= lan($lLangFile);
        
        $lRet.= '</button>';
      }
      $lRet.= '  </div>';
    }
      
    return $lRet;
  }
  
  protected function actTranslation($aCat, $aCont, $aMaster) {
    $lRet = '';
    $lRet.= $this -> getContentSec($aMaster, 'MA', $this -> mAplTyp);
    $lRet.= $this -> getContentSec($aCont, $aCont['language'], $this -> mAplTyp, 'label-info', $aCat, TRUE);

    $lRet.= '  <div class="commentContainer">&nbsp;</div>';
    $lRet.= '  <div class="approvalBtnContainer">';
    $lRet.= '    <button type="button" class="approval_buttons btn btn-success" onclick="javascript:Flow.cmsAdd.action(\''.$aMaster['content_id'].'\');">';
    $lRet.= lan('lib.ok');
    $lRet.= '    </button>';
    $lRet.= '  </div>';
    
    return $lRet;
  }
  
  protected function actApprove($aCat, $aCont, $aMaster) {
    $lRet = '';
    
    if(strpos($aCat, lan('lib.summary')) !== 0) {
      if($aCont['language'] != 'MA') {
        $lRet.= $this -> getContentSec($aMaster, 'MA', $this -> mAplTyp);
      }
      $lRet.= $this -> getContentSec($aCont, $aCont['language'], $this -> mAplTyp);
      $lRet.= $this -> getSuggestionSec($aCont, $aCat, $this -> mAplTyp); //Suggestion textarea
      $lRet.= $this -> getCommentSec($aCont, $aCat); //Comment textarea
      $lRet.= $this -> getApprovalBtns($aCont, 'Flow.cmsApl.'.$this -> mAplTyp); //Approval buttons
    } else {
      $lRet.= $this -> getOverallComment();
    }
    
    return $lRet;
  }

}