<?php
/**
 * Title
 *
 * Description
 *
 * @package    package
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Job_Cms_Item_Fac extends CHtm_Fie_Fac {

  protected $mSections;
  protected $mSize;
  protected $mFac;
  protected $mData;
  protected $mSrc;
  
  public function __construct($aData = array(), $aSize = 0, $aJob = array(), $aSrc) {
    $this -> mSrc = $aSrc;
    $this -> mData = $aData;
    $this -> mSize = $aSize;
    $this -> mSection = array();
    $this -> mFac = new CHtm_Fie_Fac();
    
    $this -> mMaster = "MA";
    $this -> mLanguages = array(
      "master" => array( $this -> mMaster ),
      "translation" => array_map('trim', explode(",", $aJob['languages']))
    );
    
	$lType = CCor_Res::extract('code', 'eve_phrase', 'crpmaster');
	$this -> mPhraseTyp = $lType[$this -> mSrc];
    
    $this -> mUsr = CCor_Usr::getInstance();
    $lTyp = ($this -> mPhraseTyp == 'job') ? '' : '.product';
    $this -> mCanInsert = $this -> mUsr -> canInsert('job-cms'.$lTyp); //job-cms[insert] or job-cms.product[insert]
    $this -> mCanDelete = $this -> mUsr -> canDelete('job-cms'.$lTyp); //job-cms[delete] or job-cms.product[delete]

    $this -> setItems();
  }
  
  public function getItems() {
    return $this -> mSections;
  }
  
  protected function setItems() {
    $this -> mCount = $this -> mSize + 1;
    
    foreach($this -> mData as $lCategory => $lArr){
      foreach($lArr as $lLayout => $lContent) {
        $lFnc = 'getLayout'.ucfirst($lLayout);

        $lDef = array("mand" => MID, "typ" => 'string', "attr" => 'a:1:{s:5:"class";s:8:"inp w50p";}');
        switch(true) {
          case stristr($lLayout, 'memo'):
            $lDef["typ"] = 'memo';
            $lDef["attr"] = 'a:2:{s:5:"class";s:9:"inp w100p";s:4:"rows";s:1:"5";}';
            break;
          case stristr($lLayout, 'vari'): //??
            $lDef["typ"] = 'tselect';
            $lDef['param'] = 'a:1:{s:3:"dom";s:3:"'.substr($lCategory, 0, 3).'";}';
            break;
          case stristr($lLayout, 'nutri'):
            $lDef['layout'] = $lLayout;
            $lDef["attr"] = 'a:1:{s:5:"class";s:9:"inp w100p";}';
            $lFnc = 'getLayoutNutri';
            break;
        }
        
        $lRet = ($this -> hasMethod($lFnc)) ? $this -> $lFnc($lCategory, $lContent, $lDef) : $this -> getLayoutGeneric($lCategory, $lContent, $lDef);
        $this -> mSections[$lCategory] = $lRet;
      }
    }
  }
  
  protected function getLayoutGeneric($aCat, $aArr, $aDef) {
    $lLayout = array();
    
    //$aArr is all languages for the content based on the job split up into each parent_id
    foreach($aArr as $lParentId => $lCont) {
      $lRet = $this -> getStartTag();
      $lCat = $aCat."_". $this -> mCount."_".strtolower($this -> mMaster);
      $lRet.= $this -> getHeader($lCat, $lCont[$this -> mMaster]['position']);

      $lRet.='<div class="content fl w100p">';
      foreach($this -> mLanguages as $lType => $lLangs) {
        $lRet.= '<div class="'.$lType.' fl p8 w400">';
        foreach($lLangs as $lLang){
          $lCat = $aCat . "_" . $this -> mCount . "_" . strtolower($lLang);
          $this -> mFac -> mIds[$lCat] = $lCat;
          $aDef['alias'] = $lCat;

          $lRet.= $this -> getBody($lCat, $lCont[$lLang], $aDef, $lType.'_'.strtolower($lLang)); //get translation language(s) from $lCont
        }
        $lRet.= '</div>';
      }
      $lRet.= '</div>';
      
      $lRet.= $this -> getEndTag();
      $lLayout[] = $lRet;
      $this -> mCount++;
    }
    
    return $lLayout;
  }
  
  protected function getLayoutNutri($aCat, $aArr, $aDef) {
    $lLayout = array();
    $lDef = $aDef;
    
    $lHtm = 'nutri';
    if (array_key_exists('layout', $lDef) AND !empty($lDef['layout'])) {
      $lHtm = $lDef['layout'];
      unset($lDef['layout']);
    }
    
    $lRet = $this -> getStartTag();
    $lCat = $aCat."_". $this -> mCount."_".strtolower($this -> mMaster);
    $lRet.= $this -> getHeader($lCat, array(), FALSE); 

    $lRet.= '<div class="content fl w100p">';
    foreach($this -> mLanguages as $lType => $lLangs) {
        $lRet.= '<div class="'.$lType.' fl p8 w400">';
        foreach($lLangs as $lLang){
          $lClassTyp = $lType.'_'.strtolower($lLang);
          $lClassTyp = (strpos($lClassTyp, 'translation') > -1) ? $lClassTyp." dn" : $lClassTyp; //initially hide translation content
          $lRet.= "<div class='".$lClassTyp." w400'>";
          
          $lTpl = new CCor_Tpl();
          $lTpl -> openProjectFile('job/cms/'.$lHtm.'.htm');
          
          $lFval  = $lTpl -> findPatterns('val.');
          foreach($lFval as $lColumn) {
            $lCat = $lDef['alias'] = $lColumn . "_" . $this -> mCount . "_" . strtolower($lLang);
            $this -> mFac -> mIds[$lCat] = $lCat;
            $lDef['width'] = 'w125';

            $lArr = $aArr[$lLang][$lColumn];
            $lInput = $this -> getBody($lCat, $lArr, $lDef, $lColumn.'_'.strtolower($lLang));
            $lTpl -> setPat("val.".$lColumn, $lInput);
          }
          
          $lFlan  = $lTpl -> findPatterns('lan.');
          foreach($lFlan as $lLan) {
            $lTpl -> setPat("lan.".$lLan, lan("lib.cms.".$lLan));
          }
          
          $lRet.= $lTpl -> getContent();
          $lRet.= "</div>";
        }
        $lRet.= "</div>";
    }
    $lRet.= "</div>";
    $lRet.= $this -> getEndTag();
    
    $lLayout[] = $lRet;
    
    return $lLayout;
  }
  
  protected function getLayoutMulti($aCat, $aArr, $aDef) {
    $lTop = TRUE;
    $lLayout = $lConsolidation = array();
    $lRet = $this -> getStartTag();
    
    foreach($aArr as $lParentId => $lCont) {
      if($lTop) {
        $lCat = $aCat."_". $this -> mCount."_".strtolower($this -> mMaster);
        $lRet.= $this -> getHeader($lCat, $lCont[$this -> mMaster]['position'], false);
        $lTop = false;
      }

      $lRet.='<div class="content fl w100p">';
      $lRet.= "<div class='p8 fl w80 buttons'>" . btn("", "Flow.cmsForm.removeItem(this.id, 'content');", "img/ico/16/cancel.gif", "button", array("id" => "removemulti_".$lCat, "class" => "btn fl")) . '</div>';//add button and </div>
      foreach($this -> mLanguages as $lType => $lLangs) {
        $lRet.= '<div class="'.$lType.' fl p8 w400">';
        foreach($lLangs as $lLang){
          $lCat = $aCat. "_" . $this -> mCount . "_" . strtolower($lLang);
          $this -> mFac -> mIds[$lCat] = $lCat;
          $aDef['alias'] = $lCat;
          $aDef['attr'] = 'a:5:{s:5:"class";s:9:"inp w100p";s:5:"style";s:23:"float:left;height:26px;";s:5:"learn";s:'.strlen($aDef['alias']).':"'.$aDef['alias'].'";s:6:"onblur";s:40:"Flow.cmsLayout.consolidateItem(this.id);";s:9:"onkeydown";s:38:"Flow.cmsLayout.duplicateItem(this.id);";}';
      
          $lRet.= $this -> getBody($lCat, $lCont[$lLang], $aDef, $lType.'_'.strtolower($lLang)); //get translation language(s) from $lCont

          if(empty($lConsolidation[$lType.'_'.strtolower($lLang)])){
            $lCont[$lLang]['content'] = ucfirst($lCont[$lLang]['content']);
          }
          if(!empty($lCont[$lLang]['content'])){
            $lConsolidation[$lType.'_'.strtolower($lLang)][] = $lCont[$lLang]['content'];
          } else {
            $lConsolidation[$lType.'_'.strtolower($lLang)] = array();
          }
        }
        $lRet.= '</div>';
      }
      $lRet.= $lBtns . '</div>';

      $this -> mCount++;
    }

    foreach($lConsolidation as $lType => $lValue) {
      $lClass = $lType." consolidation fl w400 p8 inp";
      $lClass = (strpos($lClass, 'translation') > -1) ? $lClass." dn" : $lClass; //initially hide translation content
      $lStyle = (strpos($lClass, 'translation') > -1) ? "margin: 5px 0 5px 16px;min-height: 50px;" : "margin: 5px 0 5px 56px;min-height: 50px;"; //initially hide translation content
      
      $lRet.= '<div class="'.$lClass.'" style="'.$lStyle.'">'.implode(", ", $lValue).'</div>';
    }
    
    $lRet.= $this -> getEndTag();
    $lLayout[] = $lRet;
  
    return $lLayout;
  }
  
  protected function getLayoutDict($aCat, $aArr, $aDef) {
    $lTop = TRUE;
    $lLayout = array();
    $lRet = $this -> getStartTag();
    
    //$aArr is all languages for the content based on the job split up into each parent_id
    foreach($aArr as $lParentId => $lCont) {
      $lCat = $aCat."_". $this -> mCount."_".strtolower($this -> mMaster);
      if($lTop) {
        $lRet.= $this -> getHeader($lCat, $lCont[$this -> mMaster]['position'], false);
        $lTop = false;
      }
      
      $lRet.='<div class="content fl w100p">';
      $lRet.= "<div class='p8 fl w80 buttons'>" . btn("", "Flow.cmsForm.removeItem(this.id, 'content');", "img/ico/16/cancel.gif", "button", array("id" => "removemulti_".$lCat, "class" => "btn fl")) . '</div>';//add button and </div>;
      foreach($this -> mLanguages as $lType => $lLangs) {
        $lRet.= '<div class="'.$lType.' fl p8 w400">';
        foreach($lLangs as $lLang){
          $lCat = $aCat . "_" . $this -> mCount . "_" . strtolower($lLang);
          $this -> mFac -> mIds[$lCat] = $lCat;
          $aDef['alias'] = $lCat;
          $aDef['attr'] = 'a:4:{s:5:"class";s:8:"inp w50p";s:5:"style";s:23:"float:left;height:26px;";s:6:"onblur";s:40:"Flow.cmsLayout.consolidateItem(this.id);";s:9:"onkeydown";s:80:"Flow.cmsLayout.searchDictionary(this.id); Flow.cmsLayout.duplicateItem(this.id);";}';
          
          $lCont[$lLang]['content'] =  strip_tags( $lCont[$lLang]['content'] );
          
          $lBody = $this -> getBody($lCat, $lCont[$lLang], $aDef, $lType.'_'.strtolower($lLang)); //get translation language(s) from $lCont
          $lBody = str_replace('</div>', '', $lBody);
          $lMetadata = (array_key_exists(0, $lCont[$lLang]['metadata'])) ? $lCont[$lLang]['metadata'][0] : '';
          $lBody.= '<input id="'.$lCat.'111" type="text" class="inp" style="float:left;height:26px;margin-left:5px;" name="meta['.$lCat.'_meta]" value="'.$lMetadata.'" placeholder="" onblur="Flow.cmsLayout.consolidateItem(this.id);" /></div>';//add button and </div>
          
          //script for array filling
          $lBody.= "<script>";
          $lBody.= "Flow.cmsLayout.text['".$lCat."'] = '".$lCont[$lLang]['content']."';";
          $lBody.= "</script>";
          
          $lRet.= $lBody;

          if(empty($lConsolidation[$lType.'_'.strtolower($lLang)])){
            $lCont[$lLang]['content'] = ucfirst($lCont[$lLang]['content']);
          }
          if(!empty($lCont[$lLang]['content'])){
            $lConsolidation[$lType.'_'.strtolower($lLang)][] = (isset($lCont[$lLang]['metadata'][0])) ? $lCont[$lLang]['content'].' ('.$lCont[$lLang]['metadata'][0].'%)' : $lCont[$lLang]['content'];
          } else {
            $lConsolidation[$lType.'_'.strtolower($lLang)] = array();
          }
          
        }
        
        $lRet.= '</div>';
      }

      $lRet.= '</div>';
      $this -> mCount++;
    }
    
    foreach($lConsolidation as $lType => $lValue) {
      $lClass = $lType." consolidation fl w400 p8 inp";
      $lClass = (strpos($lClass, 'translation') > -1) ? $lClass." dn" : $lClass; //initially hide translation content
      $lStyle = (strpos($lClass, 'translation') > -1) ? "margin: 5px 0 5px 16px;min-height: 50px;" : "margin: 5px 0 5px 56px;min-height: 50px;"; //initially hide translation content
      
      $lRet.= '<div class="'.$lClass.'" style="'.$lStyle.'">';
      $lRet.= (!empty($lValue) && strpos($aCat, "Ingredients") > -1) ? '<b>Ingredients: </b>' : '';
      $lRet.= implode(", ", $lValue);
      $lRet.= (!empty($lValue)) ? '.' : '';
      $lRet.= '</div>';
    }
    
    $lRet.= $this -> getEndTag();
  
    $lLayout[] = $lRet;
  
    return $lLayout;
  }
  
  protected function getStartTag(){
    return '<div class="fl w100p item">';
  }
  
  protected function getEndTag() {
    return '</div>';
  }

  protected function getHeader($aCat, $aLoc = '', $aSearch = TRUE) {
    $lCat = $aCat;
    $aLoc = explode(",", $aLoc);
    
    $lRet = '<div class="fl w100p p4 td2">';
    
    $lRet.= '<div class="fl">';
    if($this -> mCanDelete){
      $lRet.= btn("", "Flow.cmsForm.removeItem(this.id, 'item');", "img/ico/16/del.gif", "button", array("id" => "remove_".$lCat));
    }
    if($aSearch && $this -> mCanInsert){
      $lRet.= btn("", "Flow.cmsDialog.search(this.id);", "img/ico/16/search.gif", "button", array("id" => "search_".$lCat));
    }
    $lRet.= '</div>';
    
    if(!empty($aLoc)){
      $lDivId = getNum('di'); // inner div
      $lLnkId = getNum('l'); // link
      $lArr = array('fop' => 'Front of Pack', 'bop' => 'Back of Pack', 'sop' => 'Side of Pack');
      
      $lRet.= '<div id="location_'.$lCat.'" class="fl p4">';
      $lRet.= '  <a class="nav b" id="'.$lLnkId.'" href="javascript:Flow.Std.popMen(\''.$lDivId.'\',\''.$lLnkId.'\')">Location</a>'.LF;
      $lRet.= '  <div id="'.$lDivId.'" class="smDiv" style="display:none">';
      $lRet.= '    <table border="0" cellspacing="0" cellpadding="2" class="tbl mw200">';
      foreach($lArr as $lLoc => $lText) {
        $lName = $lCat.'_position_'.$lLoc;
        $lSel = (in_array($lLoc, $aLoc) !== FALSE) ? 'checked="checked"' : "";

        $lChkBox = '<input type="checkbox" id="'.$lName.'" value="'.$lLoc.'" ';
        $lChkBox.= 'onclick="javascript:gIgn=1;" name="meta['.$lName.']" '.$lSel.'>&nbsp;'.$lText;

        $lRet.= '    <tr>';
        $lRet.= '      <td class="td1 nw">'.$lChkBox.'</td>';
        $lRet.= '    </tr>';
      }
      $lRet.= '    </table>';
      $lRet.= '  </div>';
      $lRet.= '</div>';
    }
    
    $lRet.= '</div>';
    
    return $lRet;
  }

  protected function getBody($aCat, $aCont, $aDef, $aClass) {
    list($lCat, $lCont, $lDef, $lClass) = array($aCat, $aCont, $aDef, $aClass);
    $lClass = (strpos($lClass, 'translation') > -1) ? $lClass." dn" : $lClass; //initially hide translation content
    $lAttr = unserialize($lDef["attr"]);
    if(empty($lCont['content'])){
      $lAttr['class'] = $lAttr['class']." no_content";
    }
    
    $lWidth = 'w400';
    if(array_key_exists('width', $lDef) !== FALSE) {
      $lWidth = $lDef['width'];
      unset($lDef['width']);
    }
    
    $lType = ($lCont['type'] == 'product') ? 'product' : 'job';
    $lCanMaster = $this -> mUsr -> canEdit('job-cms.'.$lType.'.master'); //job-cms.job.master[edit] or job-cms.product.master[edit]
    $lCanTranslate = $this -> mUsr -> canEdit('job-cms.'.$lType.'.translation'); //job-cms.job.translation[edit] or job-cms.product.translation[edit]
    if(($lCont['language'] == 'MA' && !$lCanMaster) || ($lCont['language'] !== 'MA' && !$lCanTranslate)) {
      $lAttr['readonly'] = "readonly";
      $lAttr['class'] = $lAttr['class']." dis";
      $lAttr['disabled'] = "disabled";
    }
    
    if($this -> mPhraseTyp == 'product') {
      $lAttr['onblur'] = $lAttr["onblur"] . " Flow.cmsForm.checkContent('".$lCat."');";
    }
    $lDef['attr'] = serialize($lAttr);
    
    $lRet = '<div class="'.$lClass.' fl '.$lWidth.'">';
    $lRet.= '  <input type="hidden" name="meta['.$lCat.'_type]" value="'.$lCont['type'].'" />';
    $lRet.= '  <input type="hidden" name="meta['.$lCat.'_layout]" value="'.$lCont['layout'].'" />';
    $lRet.= '  <input type="hidden" name="content['.$lCat.'_cid]" value="'.$lCont['content_id'].'" />';
    $lRet.= '  <input type="hidden" name="content['.$lCat.'_pid]" value="'.$lCont['parent_id'].'" />';
    $lRet.= '  <input type="hidden" name="content['.$lCat.'_ver]" value="'.$lCont['version'].'" />';
    $lRet.= $this -> mFac -> getInput($lDef, $lCont['content']);
    $lRet.= '</div>';
    
    return $lRet;
  }

}