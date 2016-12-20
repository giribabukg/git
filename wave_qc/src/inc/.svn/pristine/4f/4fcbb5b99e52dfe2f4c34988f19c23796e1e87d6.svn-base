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
  
  public function __construct($aData = array(), $aSize = 0) {
    $this -> mData = $aData;
    $this -> mSize = $aSize;
    $this -> mSection = array();
    $this -> mFac = new CHtm_Fie_Fac();
    
    $lUsr = CCor_Usr::getInstance();
    $this -> mCanEdit = $lUsr -> canEdit('job-cms');
    $this -> mCanDelete = $lUsr -> canDelete('job-cms');
    
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
        switch($lLayout) {
          case 'memo':
            $lDef["typ"] = 'memo';
            $lDef["attr"] = 'a:2:{s:5:"class";s:9:"inp w100p";s:4:"rows";s:1:"5";}';
            break;
          case 'rich':
            $lDef["typ"] = 'rich';
            $lDef["attr"] = 'a:3:{s:5:"class";s:8:"inp w50p";s:11:"data-height";s:3:"100";s:9:"data-btns";s:21:"bold,italic,underline";}';
            break;
          case 'vari':
            $lDef["typ"] = 'tselect';
            $lDef['param'] = 'a:1:{s:3:"dom";s:3:"'.substr($lCategory, 0, 3).'";}';
            break;
        }
        
        //all in same layout and
        $lRet = ($this -> hasMethod($lFnc)) ? $this -> $lFnc($lCategory, $lContent, $lDef) : $this -> getLayoutGeneric($lCategory, $lContent, $lDef);
        $this -> mSections[$lCategory] = $lRet;
      }
    }
  }
  
  protected function getLayoutGeneric($aCat, $aArr, $aDef) {
    $lLayout = array();
    $lHeader = $this -> getCategory($aCat); 
    $lDef = $aDef;
    
    foreach($aArr as $lIdx => $lCont) {
      $lCat = $aCat. $this -> mCount;
      $this -> mFac -> mIds[$lCat] = $lCat;
      $lDef['alias'] = $lCat;
      
      $lRet = $this -> getStartTag();
      $lRet.= $this -> getHeader($lCat, $lCont['location']); 
      $lRet.= $this -> getBody($lCat, $lCont, $lDef);

      $lCat = $aCat . "translation" . $this -> mCount;
      $this -> mFac -> mIds[$lCat] = $lCat;
      $lDef['alias'] = $lCat;
      $lRet.= $this -> getBody($lCat, $lCont, $lDef, 'translation');
      
      $lRet.= $this -> getEndTag();
      
      $lLayout[] = array("header" => $lHeader, "content" => $lCont, "data" => $lRet);
      $this -> mCount++;
    }
    
    return $lLayout;
  }
  
  protected function getLayoutNutri($aCat, $aArr, $aDef) {
    $lLayout = array();
    $lHeader = $this -> getCategory($aCat);
    $lRows = array( 'energykj','energykcal','fat','saturates','carbs','sugars','protein','salt' );
    $lClass = 'td1 nw';
    $lDef = $aDef;
    
    $lRet = $this -> getStartTag();
    $lRet.= $this -> getHeader($aCat, array(), FALSE); 

    $lRet.= '<div class="content fl w100p">';
    $lRet.= "<table class='w100p'>";
    $lRet.= "<tr><th>&nbsp;</th><th class='bld bar2 nw'>".lan("lib.cms.per100")."</th><th class='bld bar2 nw'>".lan("lib.cms.perserving")."</th></tr>";
    foreach($lRows as $lColumn) {
      $lRet.= "<tr>";
      $lRet.= "<td class='".$lClass." bld'>".lan("lib.cms.".$lColumn)."</td>";
      
      $lCat = $lDef['alias'] = $lColumn . $this -> mCount;
      $this -> mFac -> mIds[$lCat] = $lCat;
      
      $lRet.= "<td class='".$lClass."'>";
      $lCont = $this -> searchContent($aArr, 'category', $lColumn);//search in $aArr for index with category
      $lCont = (empty($lCont)) ? $aArr[0] : $lCont[0];
      $lRet.= $this -> getBody($lCat, $lCont, $lDef);
      $lRet.= "</td>";
      $lRet.= "<td class='".$lClass."'>";
      $lRet.= "<input type='text' value='' class='inp w50p' readonly='readonly' id='serving_".$lCat."'/>";
      $lRet.= "</td>";
      
      $this -> mCount++;
        
      $lRet.= "</tr>";
      $lClass = ($lClass == "td1 nw") ? "td2 nw" : "td1 nw";
    }
    $lRet.= "</table></div>";
    $lRet.= $this -> getEndTag();
    
    $lLayout[] = array("header" => $lHeader, "content" => $aArr, "data" => $lRet);
    
    return $lLayout;
  }

  protected function getLayoutIngred($aCat, $aArr, $aDef) {
    $lTop = TRUE;
    $lLayout = array();
    $lDef = $aDef;
    
    $lConsolidation = array();
    $lRet = $this -> getStartTag();
    foreach($aArr as $lIdx => $lCont) {
      $lCat = $aCat. $this -> mCount;
      $this -> mFac -> mIds[$lCat] = $lCat;
      $lDef['alias'] = $lCat; 
      
      if($lTop){
        $lRet.= $this -> getHeader($lCat, array(), FALSE);
        $lTop = FALSE;
      }
      $lContent =  $lCont['content'];
      
      $lDef['attr'] = 'a:4:{s:5:"class";s:8:"inp w50p";s:5:"style";s:23:"float:left;height:26px;";s:6:"onblur";s:31:"Flow.cmsLayout.consolidateItem(this.id);";s:9:"onkeydown";s:25:"Flow.cmsLayout.choice(this.id);";}';
      $lCont['content'] =  strip_tags($lContent);
      $lBody = $this -> getBody($lCat, $lCont, $lDef);
      $lBody = str_replace('</div>', '', $lBody);
      
      //add percentage field for each ingredient
      $lBody.= '<input id="'.$lCat.'111" type="text" class="inp" style="float:left;height:26px;margin-left:5px;" name="product['.$lCat.']" value="'.$lCont['metadata'][0].'" placeholder="%" onblur="Flow.cmsLayout.consolidateItem(this.id);" />';
      
      $lBody.= btn("", "Flow.cmsLayout.duplicateItem(this.id);", "img/ico/16/plus.gif", "button", array("id" => "add_".$lCat)) . '</div>';//add button and </div>
      if(empty($lConsolidation)){
        $lContent = ucfirst($lContent);
      }
      $lConsolidation[] = (!empty($lCont['metadata'])) ? $lContent.' ('.$lCont['metadata'][0].'%)' : $lContent;
      
      //script for array filling
      $lBody.= "<script>";
      $lBody.= "Flow.cmsLayout.text['".$lCat."'] = '".$lContent."';";
      $lBody.= "</script>";
      
      $lRet.= $lBody;
      $this -> mCount++;
    }
    
    $lRet.= '<div class="consolidation fl w50p p8 inp" style="margin: 5px 0;min-height: 50px;">'.implode(", ", $lConsolidation);
    $lRet.= (!empty($lConsolidation)) ? '.' : '';
    $lRet.= '</div>';
    $lRet.= $this -> getEndTag();
    
    $lLayout[] = array("header" => $aCat, "content" => $aArr, "data" => $lRet);
    
    return $lLayout;
  }

  protected function getLayoutMulti($aCat, $aArr, $aDef) {
    $lTop = TRUE;
    $lLayout = array();
    $lHeader = $this -> getCategory($aCat); 
    $lDef = $aDef;
    
    $lConsolidation = array();
    $lRet = $this -> getStartTag();
    foreach($aArr as $lIdx => $lCont) {
      $lCat = $aCat. $this -> mCount;
      $this -> mFac -> mIds[$lCat] = $lCat;
      $lDef['alias'] = $lCat; 
      
      if($lTop){
        $lRet.= $this -> getHeader($lCat, $aArr[0]['location'], FALSE);
        $lTop = FALSE;
      }
      
      $lDef['attr'] = 'a:4:{s:5:"class";s:9:"inp w100p";s:5:"style";s:23:"float:left;height:26px;";s:5:"learn";s:'.sizeof($lDef['alias']).':"'.$lDef['alias'].'";s:6:"onblur";s:34:"Flow.cmsLayout.consolidateItem(this.id);";}';
      $lBody = $this -> getBody($lCat, $lCont, $lDef, 'master');
      
      $lCat = $aCat . "translation" . $this -> mCount;
      $this -> mFac -> mIds[$lCat] = $lCat;
      $lDef['alias'] = $lCat; 
      $lBody.= $this -> getBody($lCat, $lCont, $lDef, 'translation');
      
      $lBody = substr($lBody, 0, -6);
      $lBody.= btn("", "Flow.cmsLayout.duplicateItem(this.id);", "img/ico/16/plus.gif", "button", array("id" => "add_".$lCat));
      $lBody.= btn("", "Flow.cmsLayout.removeItem(this.id);", "img/ico/16/cancel.gif", "button", array("id" => "remove_".$lCat)) . '</div>';//add button and </div>
      $lConsolidation[] = $lCont['content'];
      
      $lRet.= $lBody;
    }
    
    $lRet.= '<div class="consolidation fl w100p p8 inp dn" style="margin: 5px 0;min-height: 50px;">'.implode(", ", $lConsolidation).'</div>';
    $lRet.= $this -> getEndTag();

    $lLayout[] = array("header" => $lHeader, "content" => $aArr, "data" => $lRet);
    
    return $lLayout;
  }

  protected function getLayoutVari($aCat, $aArr, $aDef) {
    $lTop = TRUE;
    $lLayout = array();
    $lHeader = $this -> getCategory($aCat); 
    $lDef = $aDef;
    
    $lMeta = array();
    $lRet = $this -> getStartTag();
    foreach($aArr as $lIdx => $lCont) {
      $lCat = $aCat. $this -> mCount;
      $this -> mFac -> mIds[$lCat] = $lCat;
      $lDef['alias'] = $lCat; 
      
      if($lTop){
        $lRet.= $this -> getHeader($lCat, array(), FALSE);
        $lTop = FALSE;
      }
      $lContent =  $lCont['content'];
      
      $lDef['attr'] = 'a:3:{s:5:"class";s:8:"inp w50p";s:5:"style";s:23:"float:left;height:32px;";s:8:"onchange";s:48:"Flow.cmsForm.variableContent(this.id,\'variables\');";}';
      
      $lBody = $this -> getBody($lCat, $lCont, $lDef);
      $lBody = str_replace('</div>', '', $lBody);
      
      $lPar = toArr($lDef['param']);
      $lDom = (isset($lPar['dom'])) ? $lPar['dom'] : '';
      $lSql = 'SELECT value_'.LAN.' FROM al_htb_itm WHERE domain="'.$lDom.'" AND value='.esc($lCont['content']);
      $lConsolidation = CCor_Qry::getStr($lSql);
      
      //add variable fields
      if(!empty($lCont['metadata'][0])) {
        $lCont['metadata'] = explode(",", $lCont['metadata'][0]);
      }
      foreach($lCont['metadata'] as $lKey => $lVariable){
        $lKey = $lKey + 1;
        $lBody.= '<input id="variable'.$lCat.$lKey.'" type="text" class="inp" style="float:left;height:26px;margin-left:5px;" value="'.$lVariable.'" placeholder="{variable'.$lKey.'}" onblur="Flow.cmsForm.variableContent(\''.$lCat.'\',\'cons\');" />';
        
        $lConsolidation = str_replace("{variable".$lKey."}", $lVariable, $lConsolidation);
        $lMeta[] = $lVariable;
        
      }
      
      $lBody.= '<input id="'.$lCat.'_meta" type="hidden" name="product['.$lCat.']" value="'.implode(",",$lMeta).'" />';
      $lBody.= '</div>';
      
      $lRet.= $lBody;
      $this -> mCount++;
    }
    
    $lRet.= '<div class="consolidation fl w50p p8 inp" style="margin: 5px 0;min-height: 50px;">'.$lConsolidation.'</div>';
    $lRet.= $this -> getEndTag();
    
    $lLayout[] = array("header" => $lHeader, "content" => $aArr, "data" => $lRet);
    
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
    
    $lRet = '<div class="fl w100p bar2">';
    
    if(!empty($aLoc)){
      $lRet.= '<div id="location_'.$lCat.'" class="fl p8"><b>Location:</b>&nbsp;';
      $lArr = array('fop','bop','sop');
      foreach($lArr as $lLoc) {
        $lName = $lCat.'_'.$lLoc;
        $lSel = (in_array($lLoc, $aLoc) !== FALSE) ? 'checked="checked"' : "";
        $lRet.= '  <input type="checkbox" id="'.$lName.'" name="meta['.$lName.']" '.$lSel.'/><label for="'.$lName.'">'.strtoupper($lLoc).'</label>';
      }
      $lRet.= '</div>';
      $lRet.= $this ->getScriptTag($lCat);
    }
    
    $lRet.= '<div class="fr p8">';
    if($aSearch){
      $lRet.= btn("", "Flow.cmsDialog.search(this.id);", "img/ico/16/search.gif", "button", array("id" => "search_".$lCat));
    }
    if($this -> mCanDelete){
      $lRet.= btn("", "Flow.cmsForm.removeItem(this.id);", "img/ico/16/del.gif", "button", array("id" => "remove_".$lCat));
    }
    $lRet.= '</div>';
    
    $lRet.= '</div>';
    
    return $lRet;
  }

  protected function getBody($aCat, $aCont, $aDef, $aClass) {
    list($lCat, $lCont, $lDef, $lClass) = array($aCat, $aCont, $aDef, $aClass);
    
    $lRet = '<div class="'.$lClass.' fl w50p">';
    $lRet.= '  <input type="hidden" name="meta['.$lCat.'_layout]" value="'.$lCont['layout'].'" />';
    $lRet.= '  <input type="hidden" name="content['.$lCat.'_cid]" value="'.$lCont['content_id'].'" />';
    $lRet.= '  <input type="hidden" name="content['.$lCat.'_pid]" value="'.$lCont['parent_id'].'" />';
    $lRet.= '  <input type="hidden" name="content['.$lCat.'_lang]" value="'.$lCont['language'].'" />';
    $lRet.= '  <input type="hidden" name="content['.$lCat.'_ver]" value="'.$lCont['version'].'" />';
    $lRet.= '  <input type="hidden" name="content['.$lCat.'_type]" value="content" />';
    $lRet.= $this -> mFac -> getInput($lDef, $lCont['content']);
    $lRet.= '</div>';
    
    return $lRet;
  }
  
  protected function getScriptTag($aCat) {
    $lRet = '<script>';
    $lRet.= 'jQuery(function() {';
    $lRet.= 'jQuery("#location_'.$aCat.'").buttonset();jQuery("#location_'.$aCat.'").addClass("fl p8");';
    $lRet.= '});';
    $lRet.= '</script>';
    
    return $lRet;
  }
  
  protected function getCategory($aCategory) {
    $lRet = $aCategory;
      
    $lSql = 'SELECT `value_'.LAN.'` FROM `al_htb_itm` WHERE domain="phc" AND `mand`='.MID.' AND value='.esc($lRet);
    $lRet = CCor_Qry::getStr($lSql);
    
    return $lRet;
  }
  
  protected function searchContent($aArr, $aKey, $aValue) {
    $lRes = array();

    if (is_array($aArr)) {
      if (isset($aArr[$aKey]) && $aArr[$aKey] == $aValue) {
        $lRes[] = $aArr;
      }

      foreach ($aArr as $aSubArr) {
        $lRes = array_merge($lRes, $this -> searchContent($aSubArr, $aKey, $aValue));
      }
    }

    return $lRes;
  }
}