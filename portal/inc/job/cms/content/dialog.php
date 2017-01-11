<?php
/**
 * Title
 *
 * Description
 *
 * @package    package
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Job_Cms_Content_Dialog extends CCor_Tpl {
  
  /**
   * Open HTML item dialog page and replace patterns with content
   * @param number $aJobId
   */
  public function __construct($aJobId = 0, $aSrc, $aJob, $aTyp = 'add_master') {
    $this -> mJobId = $aJobId;
    $this -> mSrc = $aSrc;
    $this -> mJob = $aJob;
    $this -> mTyp = $aTyp;
    $this -> mUsr = CCor_Usr::getInstance();
  }

  /**
   * Get HTML representation of template options with job template auto selected.
   * @param string $aJobId
   * @return string $lRet
   */
  protected function getTemplates() {
    $lReadOnly = (!$this -> mUsr -> canEdit('job-cms.template')) ? ' disabled' : '';
    $lPhraseFields = CCor_Cfg::get('job-cms.fields');
    $lTempName = $lPhraseFields['template_name'];
    if(!empty($this -> mJob[$lTempName])) {
      $lTemp = CCor_Qry::getStr('SELECT DISTINCT `template_id` FROM `al_cms_template` WHERE `name`='.esc($this -> mJob[$lTempName]));
    } else {
      $lTemp = $this -> mUsr -> getPref('phrase.'.$this -> mJobId.'.template', 0);
    }
    
    $lRet = '<select id="templates" name="templates" class="cms_lay w100p" onchange="Flow.cmsTemplate.select();"'.$lReadOnly.'>';
    $lRet.= '<option value="">&nbsp;</option>';
    
    $lPhraseTypes = CCor_Res::extract('code', 'eve_phrase', 'crpmaster');
    $lJobTyp = $lPhraseTypes[$this -> mSrc];
    
    $lQry = new CCor_Qry('SELECT DISTINCT `template_id`, `name` FROM `al_cms_template` WHERE `type`='.esc($lJobTyp).' AND `mand`='.intval(MID).' ORDER BY `name` ASC');
    foreach ($lQry as $lRow) {
      $lId = $lRow['template_id'];
      $lKey = $lRow['name'];
      $lSel = ($lId == $lTemp) ? ' selected="selected"' : '';
      
      $lRet.= '<option value="'.$lKey.'"'.$lSel.'>'.htm($lKey).'</option>'.LF;
    }
    $lRet.= '</select>';
    
    return $lRet;
  }

  /**
   * Gather all layout options into an array
   */
  protected function getLayouts() {
    $lReadOnly = (!$this -> mUsr -> canEdit('job-cms.template')) ? ' disabled' : '';
    $lArr = CCor_Res::get('htb', array('domain' => 'phl'));
    aSort($lArr);
    
    $lLayouts = array_merge(array("" => ""), $lArr);
	$lRet = '<select id="layout1" name="layout" class="cms_lay w100p" onchange="Flow.cmsTemplate.addItem(this);"'.$lReadOnly.'>';
    foreach ($lLayouts as $lKey => $lName) {
      $lRet.= '<option value="'.$lKey.'">'.htm($lName).'</option>'.LF;
    }
    $lRet.= '</select>';
    
    return $lRet;
  }

  /**
   * Get the remove button for first combo row 
   * @return image html
   */
  protected function getRemoveButton() {
    $lRet = '';
    
    if($this -> mUsr -> canDelete('job-cms.template')) { //job-cms[delete] or job-cms.product[delete])
      $lAttr = array(
        "class" => "removeBtn",
      	"onclick" => "Flow.cmsTemplate.removeItem(this);",
      	"style" => "cursor:pointer;"
      );
      $lImg = "img/ico/16/cancel.gif";
    } else {      
      $lAttr = array( "class" => "removeBtn" );
      $lImg = "img/d.gif";
    }
    
    $lRet = img($lImg, $lAttr);
    
    return $lRet;
  }
  
  /**
   * Gather all category options into an array
   */
  protected function getCategoryOptions() {
    $lReadOnly = (!$this -> mUsr -> canEdit('job-cms.template')) ? ' disabled' : '';
    $lArr = CCor_Res::get('categories');
    aSort($lArr);
    
    $lCategories = array_merge(array("" => ""), $lArr);
    $lRet = '<select id="category1" name="cat" class="cms_cat w100p"'.$lReadOnly.'>';
    foreach ($lCategories as $lKey => $lName) {
      $lRet.= '<option value="'.$lKey.'">'.htm($lName).'</option>'.LF;
    }
    $lRet.= '</select>';
    
    return $lRet;
  }
  
  protected function getCategories() {
    $lCategories = array();
    $lSql = 'SELECT * FROM `al_cms_categories` WHERE `mand`='.intval(MID).' AND `active`=1 ORDER BY `value` ASC';
    $lQry = new CCor_Qry($lSql);
    foreach($lQry as $lRow){
      $lTooltip = ($this -> mUsr -> getPref('job.feldtips', 'Y') == 'Y') ? $lRow['tooltip_'.LAN] : '';
      $lCategories[$lRow['value']] = array('name' => $lRow['value_'.LAN], 'layouts' => $lRow['layouts'], 'tooltip' => $lTooltip);
    }
    $lSummary = lan('lib.summary');
    $lCategories[ $lSummary ] = array('name' => $lSummary, 'layouts' => '', 'tooltip' => '');
    
    $lRet = '<script type="text/javascript">var lCategories = ';
    $lRet.= (!empty($lCategories)) ? Zend_Json::encode($lCategories) : '{}';
    $lRet.= ';</script>'.LF;
    
    return $lRet;
  }

  /**
   * Get the amount field html tag
   */
  protected function getAmount() {
    $lReadOnly = (!$this -> mUsr -> canEdit('job-cms.template')) ? ' disabled' : '';
    
    $lRet = '<input id="amount1" name="amount" type="number" class="cms_amt" value="1" min="1"'.$lReadOnly.' />';
    
    return $lRet;
  }

  /**
   * Get HTML representation of metadata options.
   * @return string $lRet
   */
  protected function getMetadataOptions(){
    $lChkBox = array();
    $lRet = '';
    $lFie = CCor_Res::getByKey('alias', 'fie', array("mand" => MID));

    $lMod = new CJob_Cms_Mod($this -> mSrc, $this -> mJobId, $this -> mJob);
    $lJobMeta = $lMod -> getMetadata();

    foreach ($lFie as $lKey => $lDef) {
      $lId = intval($lDef['id']);
      if (array_key_exists($lId, $lJobMeta)) {
        $lName = $lDef['name_'.LAN];
        $lVal = $lJobMeta[$lId];

        $lAuto = CCor_Cfg::get('phrase.meta.check', FALSE);
        $lChecked = ($lAuto) ? ' checked' : '';
        $lChkBox = '<input type="checkbox" id="'.$lId.'" value="'.$lVal.'" ';
        $lChkBox.= 'onclick="javascript:gIgn=1; Flow.cmsSearch.metaCheck(this.id);" name="meta'.$lId.'"'.$lChecked.'>&nbsp;<b>'.$lName.'</b>: '.$lVal;
        $lChkBoxes[] = $lChkBox;
      }
    }

    if(sizeof($lChkBoxes) > 0) {
      $lDiv = getNum('do'); // outer div
      $lDivId = getNum('di'); // inner div
      $lLnkId = getNum('l'); // link

      $lRet.= '<div id="'.$lDiv.'" class="options fr w50">'.LF;
      $lRet.= '  <a id="'.$lLnkId.'" href="javascript:Flow.Std.popMen(\''.$lDivId.'\',\''.$lLnkId.'\')">'.lan('lib.metadata').'</a>'.LF;
      $lRet.= '  <div id="'.$lDivId.'" class="smDiv mw200" style="display:none">';

      for($lI = 0; $lI < count($lChkBoxes); $lI++) {
        $lRet.= $lChkBoxes[$lI]."<br/>";
      }

      $lRet.= '  </div>';
      $lRet.= '</div>'.LF;
    }

    return $lRet;
  }

  public function getCont(){
  	$lBody = $lPagination = $lRet = '';
  	
  	switch($this -> mTyp) {
  		case 'add_master':
	  		$this -> openProjectFile('job/cms/content/'.$this -> mTyp.'_one.htm');
	  		$this -> setPat('templates', $this -> getTemplates());
	  		$this -> setPat('categories', $this -> getCategoryOptions());
	  		$this -> setPat('layout', $this -> getLayouts());
	  		$this -> setPat('amount', $this -> getAmount());
	  		$this -> setPat('removebtn', $this -> getRemoveButton());
	  		$lFlan  = $this -> findPatterns('lan.');
	  		foreach($lFlan as $lLan) {
	  		  $this -> setPat("lan.".$lLan, lan("lib.".$lLan));
	  		}
	  		$lStepOne = parent::getCont();
	  		
	  		$this -> openProjectFile('job/cms/content/'.$this -> mTyp.'_two.htm');
	  		$this -> setPat('metadata', $this -> getMetadataOptions());

	  		$lNoCont = '';
	  		if(CCor_Cfg::get("phrase.nocontent", TRUE)) {
	  		  $lNoCont.= '<span id="noContent" class="part_title">'.LF;
	  		  $lNoCont.= '<button type="button" id="blankContent" class="btn btn-danger" onclick="javascript:Flow.cmsContent.notneeded();">'.lan('lib.nocontent').'</button>'.LF;
	  		  $lNoCont.= '</span>';
	  		}
	  		$this -> setPat('no_content', $lNoCont);
	  		
	  		$lFlan  = $this -> findPatterns('lan.');
	  		foreach($lFlan as $lLan) {
	  		  $this -> setPat("lan.".$lLan, lan("lib.".$lLan));
	  		}
	  		$lStepTwo = parent::getCont();

	  		$lBody.= '       <div class="row template hide" data-step="1" data-title="'.lan('apl.phrase.window.tempsel').'">'.LF;
	  		$lBody.= $lStepOne;
	  		$lBody.= '       </div>'.LF;
	  		$lBody.= '       <div class="row hide" data-step="2" data-title="'.lan('apl.phrase.window.contsel').'">'.LF;
	  		$lBody.= $lStepTwo;
	  		$lBody.= '       </div>'.LF;

  			$lPagination.= '<span class="pull-left hide" data-step="1">'.LF;
  			if($this -> mUsr -> canInsert('job-cms.template')) { //job-cms[insert] or job-cms.product[insert]
  				$lPagination.= '       <button type="button" id="saveNewTemplate" class="btn btn-info" role="button">'.lan('lib.ok').'</button>'.LF;
  			}
  			if($this -> mUsr -> canEdit('job-cms.template')) { //job-cms[edit] or job-cms.product[edit]
  				$lPagination.= '       <button type="button" id="updateTemplate" class="btn btn-info" role="button">'.lan('lib.update').'</button>'.LF;
  			}
  			$lPagination.= '</span>'.LF;
  			$lPagination.= '<span class="pull-left hide" data-step="2">'.LF;
  			$lPagination.= '       <button type="button" class="contprev btn btn-info" onclick="javascript:Flow.cmsContent.show(\'prev\',\'#add_master\');">'.lan('apl.phrase.window.previous').'</button>'.LF;
  			$lPagination.= '       <button type="button" class="contnext btn btn-info" onclick="javascript:Flow.cmsContent.show(\'next\',\'#add_master\');">'.lan('apl.phrase.window.next').'</button>'.LF;
  			$lPagination.= '</span>'.LF;
	  		break;
  		case 'add_translation':
	  		$this -> openProjectFile('job/cms/content/add_translation.htm');
	  		$this -> setPat('lan.title', lan('apl.phrase.translation'));
  			$lBody.= '       <div class="row hide" data-step="1" data-title="'.lan('apl.add.translation').'">'.LF;
  			$lBody.= parent::getCont();
  			$lBody.= '       </div>'.LF;
  			
  			$lPagination.= '<span class="pull-left hide" data-step="1">'.LF;
  			$lPagination.= '       <button type="button" class="contprev btn btn-info" onclick="javascript:Flow.cmsContent.show(\'prev\',\'#add_translation\');">'.lan('apl.phrase.window.previous').'</button>'.LF;
  			$lPagination.= '       <button type="button" class="contnext btn btn-info" onclick="javascript:Flow.cmsContent.show(\'next\',\'#add_translation\');">'.lan('apl.phrase.window.next').'</button>'.LF;
  			$lPagination.= '</span>'.LF;
  			break;
  		case 'approve_review':
	  		$this -> openProjectFile('job/cms/content/approve_review.htm');
	  		$this -> setPat('lan.title', lan('apl.phrase.approval'));
  			$lBody.= '       <div class="row hide" data-step="1" data-title="'.lan('apl.approval').'">'.LF;
  			$lBody.= parent::getCont();
  			$lBody.= '       </div>'.LF;
  			
  			$lPagination.= '<span class="pull-left hide" data-step="1">'.LF;
  			$lPagination.= '       <button type="button" class="contprev btn btn-info" onclick="javascript:Flow.cmsContent.show(\'prev\',\'#approve_review\');">'.lan('apl.phrase.window.previous').'</button>'.LF;
  			$lPagination.= '       <button type="button" class="contnext btn btn-info" onclick="javascript:Flow.cmsContent.show(\'next\',\'#approve_review\');">'.lan('apl.phrase.window.next').'</button>'.LF;
  			$lPagination.= '       <button type="button" class="contall btn btn-info active" onclick="javascript:Flow.cmsApl.showAll();">'.lan('apl.phrase.window.hideall').'</button>'.LF;
  			$lPagination.= '</span>'.LF;
  			break;
  	}
  	
    $lRet.= '<div class="modal fade" id="'.$this -> mTyp.'" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">'.LF;
    $lRet.= ' <div class="modal-dialog modal-lg">'.LF;
    $lRet.= '   <div class="modal-content">'.LF;
    $lRet.= '     <div class="modal-header">'.LF;
    $lRet.= '       <h4 class="js-title-step pull-left"></h4>'.LF;
    $lRet.= '       <button type="button" class="btn btn-secondary pull-right" data-dismiss="modal">X</button>'.LF;
    $lRet.= '     </div>'.LF;
    $lRet.= '     <div class="modal-body">'.LF;
    $lRet.= $lBody;
    $lRet.= '     </div>'.LF;
    $lRet.= '     <div class="modal-footer">'.LF;
    $lRet.= $lPagination;
    $lRet.= '       <button type="button" class="btn btn-warning js-btn-step" data-orientation="previous"></button>'.LF;
    $lRet.= '       <button type="button" class="btn btn-success js-btn-step" data-orientation="next"></button>'.LF;
    $lRet.= '     </div>'.LF;
    $lRet.= '   </div>'.LF;
    $lRet.= ' </div>'.LF;
    $lRet.= '</div>'.LF;

    $lRet.= $this -> getCategories();
    
    return $lRet;
  }
  
  public function getModals(){
    $lRet = '<div class="modal fade" id="productChange" role="dialog" data-backdrop="static" data-keyboard="false">'.LF;
    $lRet.= ' <div class="modal-dialog modal-lg">'.LF;
    $lRet.= '   <div class="modal-content">'.LF;
    $lRet.= '     <div class="modal-header"><h4 class="pull-left">'.lan('phrase.product.change').'</h4></div>'.LF;
    $lRet.= '     <div class="modal-body productChanges"></div>'.LF;
    $lRet.= '     <div class="modal-footer">'.LF;
    $lRet.= '       <button id="change_product" type="button" class="btn btn-success">'.lan('lib.yes').'</button>'.LF;
    $lRet.= '       <button type="button" class="btn btn-danger pull-right" data-dismiss="modal">'.lan('lib.no').'</button>'.LF;
    $lRet.= '     </div>'.LF;
    $lRet.= '   </div>'.LF;
    $lRet.= ' </div>'.LF;
    $lRet.= '</div>'.LF;
    
    $lRet.= '<div class="modal fade" id="productUpdate" role="dialog" data-backdrop="static" data-keyboard="false">'.LF;
    $lRet.= ' <div class="modal-dialog modal-lg">'.LF;
    $lRet.= '   <div class="modal-content">'.LF;
    $lRet.= '     <div class="modal-header"><h4 class="pull-left">'.lan('phrase.product.update').'</h4></div>'.LF;
    $lRet.= '     <div class="modal-body updateProduct"></div>'.LF;
    $lRet.= '     <div class="modal-footer">'.LF;
    $lRet.= '       <button id="update_product_yes" type="button" class="btn btn-success">'.lan('lib.yes').'</button>'.LF;
    $lRet.= '       <button id="update_product_no" type="button" class="btn btn-danger pull-right">'.lan('lib.no').'</button>'.LF;
    $lRet.= '     </div>'.LF;
    $lRet.= '   </div>'.LF;
    $lRet.= ' </div>'.LF;
    $lRet.= '</div>'.LF;
    
    $lRet.= '<div class="modal fade" id="deleteItem" role="dialog" data-backdrop="static" data-keyboard="false">'.LF;
    $lRet.= ' <div class="modal-dialog modal-lg">'.LF;
    $lRet.= '   <div class="modal-content">'.LF;
    $lRet.= '     <div class="modal-header"><h4 class="pull-left">'.lan('phrase.content.removal').'</h4></div>'.LF;
    $lRet.= '     <div class="modal-body contentRemoval"></div>'.LF;
    $lRet.= '     <div class="modal-footer">'.LF;
    $lRet.= '       <button id="delete_item" type="button" class="btn btn-success">'.lan('lib.yes').'</button>'.LF;
    $lRet.= '       <button type="button" class="btn btn-danger pull-right" data-dismiss="modal">'.lan('lib.no').'</button>'.LF;
    $lRet.= '     </div>'.LF;
    $lRet.= '   </div>'.LF;
    $lRet.= ' </div>'.LF;
    $lRet.= '</div>'.LF;
    
    return $lRet;
  }

}