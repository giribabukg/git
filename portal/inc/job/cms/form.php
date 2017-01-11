<?php
/**
 * Jobs: Components - Formular
 *
 *  Description
 *
 * @package    JOB
 * @subpackage    Com
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 6390 $
 * @date $Date: 2014-11-07 14:30:02 +0000 (Fri, 07 Nov 2014) $
 * @author $Author: jwetherill $
 */
 class CInc_Job_Cms_Form extends CHtm_Form {

  protected $mSrc;
  protected $mJobId;
  protected $mPhraseTyp;
  protected $mProductUsed;

  public function __construct($aMod = 'job-cms', $aJobId = 0, $aSrc, $aJob = NULL, $aData = array()) {
    $this -> mJobId = $aJobId;
    $this -> mSrc = $aSrc;
    $this -> mMod = $aMod;
    $this -> mUsr = CCor_Usr::getInstance();
    $this -> mJob = $aJob;
    
	$lType = CCor_Res::extract('code', 'eve_phrase', 'crpmaster');
	$this -> mPhraseTyp = $lType[$this -> mSrc];
	$this -> mProductUsed = in_array('product', $lType);
    
	$lTyp = ($this -> mPhraseTyp == 'job') ? '' : '.product';
    $this -> mCanInsert = $this -> mUsr -> canInsert($this -> mMod.$lTyp); //job-cms[insert] or job-cms.product[insert]
    $this -> mCanEdit = $this -> mUsr -> canEdit($this -> mMod.$lTyp); //job-cms[edit] or job-cms.product[edit]
    $this -> mCanBuild = $this -> mUsr -> canRead($this -> mMod.'.build'); //job-cms.build[read]

    $lType = ($this -> mPhraseTyp == 'product') ? 'product' : 'job';
    $this -> mCanTranslate = $this -> mUsr -> canEdit('job-cms.'.$lType.'.translation'); //job-cms.job.translation[edit] or job-cms.product.translation[edit]
    
    $this -> mPhraseFields = CCor_Cfg::get('job-cms.fields');
    $lKey = $this -> mPhraseFields['client_key'];
    $this -> mClientKey = $this -> mJob[$lKey];
    
    $this -> setParam('jobid', $this -> mJobId);
    $this -> setParam('src', $this -> mSrc);
    
    //load job cms info
    $this -> mCmsDat = new CJob_Cms_Dat($this -> mSrc);
    if(empty($aData)) {
      $this -> mData = $this -> mCmsDat -> load($this -> mJobId, $this -> mJob); //load job content
    } else {
      $this -> mData = $aData;
    }
    
    //if no data saved to job use snew function otherwise use sedt
    $lFunc = (sizeof($this -> mData) > 0) ? "sedt" : "snew";
    
    parent::__construct($this -> mMod.'.'.$lFunc, 'Content');
  }
  
  protected function getHeader() {
    $lTpl = new CCor_Tpl();
    $lPhraseCss =  $lTpl -> getProjectFilename('css'.DS.'phrase.css');
    
    $lRet = '<link rel="stylesheet" type="text/css" href="'.$lPhraseCss.'" />'.LF;
    $lRet.= '<link rel="stylesheet" href="htm/default/css/bootstrap.min.css" />'.LF;
    $lRet.= '<script src="js/bootstrap.min.js"></script>'.LF;
    $lRet.= '<style>body{ margin: 5px !important; }.h2 { font-size: 10pt !important; margin: 0 !important; }.modal-body .row.template{ margin: 0; text-align: center; }</style>'.LF;
    
    return $lRet;
  }
  
  protected function getCont() {
    $lRet = $this -> getHeader();
    $lRet.= '<div class="cap">'.lan('job-'.$this -> mPhraseTyp.'-cms.menu').'</div>'.LF;
    $lRet.= $this -> getComment('start');
    $lRet.= $this -> getFormTag();
    
    $lForm = $this -> getForm();
  
    if ($this -> mButtons == TRUE) {
      $lRet.= $this -> getButtons();
    }
    
    $lRet.= $this -> getHiddenFields();
    $lRet.= $lForm;
    $lRet.= $this -> getEndTag();

    $lRet.= '</form>'.LF;
    $lRet.= $this -> getJs();
    
    return $lRet;
  }
  
  /**
   * Construct the job content form
   * @return string $lRet - html for form
   */  
  protected function getForm() {
    $lUpdate = ($this -> mPhraseTyp == 'product') ? 'yes' : 'no';
    $this -> setParam('ref_update', $lUpdate);

    $lRet = '<div id="job_form" class="frm">'.LF;
	$lRet.= $this -> getFieldForm();
    $lRet.= '</div>' . LF;
    
    /*if($this -> mCanBuild) {
      $lRet.= '<div id="save_dialog" title="Publish PDF" style="display:none;">'.LF;
	  $lRet.= '<input id="templateId" type="hidden" value="" />'.LF;
      $lRet.= '<iframe src="" onload="javascript:GetEditor()" id="chiliEditor" class="dn" style="width:100%;height:100%;"></iframe>'.LF;
      $lRet.= '</div>'.LF;
    }*/
    
    $lDlg = new CJob_Cms_Content_Dialog($this -> mJobId, $this -> mSrc, $this -> mJob);
    $lRet.= $lDlg -> getContent();
    
    $lRet.= $lDlg -> getModals();
    
    return $lRet;
  }

  protected function getFieldForm() {
    $lRet = '';

    $lProductExists = $this -> mCmsDat -> multiArraySearch($this -> mData, array('type' => 'product'));
    if(empty($lProductExists) && $this -> mProductUsed){
      $lAreaId = ($this -> mPhraseTyp == 'product') ? 'content' : 'product';
      $lRet.= '<div id="job_'.$lAreaId.'">'.LF;
      $lRet.= '<div class="tg1 h2 fl w100p">'.lan('job-cms.product').': '.$this -> mClientKey.'</div>' .LF;
      if($this -> mPhraseTyp == 'job') {
        $lRet.= '<div class="fl p8 b">'.lan('job-cms.noproduct').$this -> mClientKey.'</div>'.LF;
      }
      $lRet.= '</div>' . LF;
    }
    
    $lRet.= $this -> getFormSections();

    $lContentExists = $this -> mCmsDat -> multiArraySearch($this -> mData, array('type' => 'content'));
    if(empty($lContentExists) && $this -> mPhraseTyp == 'job'){
      $lRet.= '<div id="job_content">'.LF;
      $lRet.= '<div class="tg1 h2 fl w100p">'.lan('job-cms.content').'</div>' .LF;
      $lRet.= '</div>' . LF;
    }
    
    return $lRet;
  }
  
  public function getFormSections() {
  	$lRet = '';
    $lCategories = CCor_Res::get('categories');
  	
  	if (!empty($this -> mData)) {
  		$lData = $this -> setupContent();
  		foreach($lData as $lArea => $lContent) {
  			$lAreaId = ($this -> mPhraseTyp == 'product') ? 'content' : $lArea;
  			$lRet.= '<div id="job_'.$lAreaId.'">'.LF;
  			$lArea = ($lArea == 'product') ? lan('lib.'.$lArea).": ".$this -> mClientKey : lan('lib.'.$lArea); //show client key within header if product area
  			$lRet.= '<div class="tg1 h2 fl w100p">'.ucfirst($lArea).'</div>' .LF;
  	
  			$lFac = new CJob_Cms_Sections($lContent, 0, $this -> mJob, $this -> mSrc);
  			$lFormHtml = $lFac -> getHtml();
  			foreach($lFormHtml as $lCategory => $lArr){
  				$lRet.= '<div id="'.$lCategory.'" class="fl section">' . LF;
  				foreach($lArr as $lIdx => $lItem){
  					if($lIdx == 0) {
  						$lRet.= '<div class="th1 fl sectionHeader">'.$lCategories[$lCategory].'</div>' .LF;
  					}
  	
  					$lRet.= $lItem . LF;
  				}
  				$lRet.= '</div>' . LF;
  			}
  	
  			$lRet.= '</div>' . LF;
  		}
  	}
  	
  	return $lRet;
  }
  
  protected function setupContent() {
    $lRet = array();

    foreach($this -> mData as $lIdx => $lContent) {
      $lType = $lContent['type'];
      $lCategory = $lContent['category'];
      $lLayout = $lContent['layout'];
      $lGroup = $lContent['group'];
      $lLang = $lContent['language'];

      if(!isset($lRet[$lType][$lCategory][$lLayout][$lGroup][$lLang])){
        $lRet[$lType][$lCategory][$lLayout][$lGroup][$lLang] = array();
      }
      array_push($lRet[$lType][$lCategory][$lLayout][$lGroup][$lLang], $lContent);
    }
    krsort($lRet);
    
    return $lRet;
  }
  
  /**
   * Get all options for user
   * @see CInc_Htm_Form::getButtons()
   */
  protected function getButtons($aBtnAtt = array(), $aBtnTyp = 'button') {
    $lRet = '<div class="sub cmsHeader p4">'.LF;

    //check is critical path status flag 'add content' is activated
    $lShow = FALSE;
    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    $lCrpId = $lCrp[$this -> mSrc];
    $lCrpArr = CCor_Res::get('crp', $lCrpId);
    $lSta = $this -> mJob['webstatus'];
    foreach ($lCrpArr as $lRow) {
      if ($lRow['status'] == $lSta) {
        if (bitset($lRow['flags'], staflaAddContent)) {
          $lShow = TRUE;
        }
      }
    }
  
    if ($this -> mCanInsert && ($lShow || $this -> mUsr -> isMemberOf(1))) {
      $lRet.= btn(lan('job-cms.add'), '', 'img/d.gif', $aBtnTyp, array('id' => 'contentButton', 'data-toggle' => "modal", 'data-target' => "#add_master", 'class' => "btn btn-default")).NB;   
    }
    if ($this -> mCanEdit OR $this -> mCanInsert) {
      $lAction = '';
      if ($this -> mPhraseTyp == 'product' && CCor_Cfg::get('phrase.job.update', 'auto') == 'manual' && sizeof($this -> mData) > 0) {
        $lAction = 'return Flow.cmsProduct.update();';
      }
      $lRet.= btn(lan('lib.ok'), $lAction, 'img/ico/16/ok.gif', 'submit', array('id' => 'save', 'class' => 'dn')).NB;
    }
    if ($this -> mCanInsert) {
      $lMen = new CHtm_Menu(lan('lib.opt'), "options fl p4");
      $lMen -> addTh2(lan('lib.opt'));
      $lMen -> addJsItem("go('index.php?act=job-cms.contexp&src=".$this -> mSrc."&jobid=".$this -> mJobId."&typ=xml')", lan('job-cms.exportxml'), 'ico/16/fie.gif');
      $lMen -> addJsItem("go('index.php?act=job-cms.contexp&src=".$this -> mSrc."&jobid=".$this -> mJobId."&typ=excel')", lan('job-cms.exportexcel'), 'ico/16/excel.gif');
      if($this -> mCanBuild && $this -> mPhraseTyp == 'job') {
        $lMen -> addJsItem('Flow.chili.buildArtwork()', lan('job-cms.build'), 'ico/16/pdf.png');
      }
      $lRet.= $lMen -> getContent();
    }
    $lRet.= $this -> getLanguageOptions();
    $lRet.= '</div>'.LF;
    return $lRet;
  }
  
  protected function getLanguageOptions() {
    $lLangKey = $this -> mPhraseFields['languages'];
    $lJobLangs = array_map('trim', explode(",", $this -> mJob[$lLangKey]));
    $lLanguages = CCor_Res::get('htb', 'dln');
    
    $lJobLangs = array_values($lJobLangs);
    $lLangKeys = array_keys($lLanguages);
    $lIntersect = array_intersect($lJobLangs, $lLangKeys);
    foreach($lLanguages as $lKey => $lVal) {
      if(!in_array($lKey, $lIntersect)) {
        unset($lLanguages[$lKey]);
      }
    }
    
    $lRet = ($this -> mCanEdit OR $this -> mCanInsert) ? '<div class="fr">' : '<div>';
    if(!empty($lLanguages)) {
      $lDiv = getNum('do'); // outer div
      $lDivId = getNum('di'); // inner div
      $lLnkId = getNum('l'); // link

      $lRet.= '<div id="'.$lDiv.'" class="options fl p4">'.LF;
      $lRet.= '  <a id="'.$lLnkId.'" class="b nav" href="javascript:Flow.Std.popMen(\''.$lDivId.'\',\''.$lLnkId.'\')">'.lan('job-cms.translations').'</a>'.LF;
      $lRet.= '  <div id="'.$lDivId.'" class="smDiv mw200" style="display:none">'.LF;
      $lRet.= '  <div class="cap">' . lan('lib.phrase.translation.lang.max') . '</div>'.LF;
      
      foreach($lLanguages as $lKey => $lVal) {
        $lRet.= '<input type="checkbox" id="trans_'.strtolower($lKey).'" value="'.strtolower($lKey).'" onclick="javascript:gIgn=1; Flow.cmsForm.showTranslation(this.id);">&nbsp;'.$lVal.BR;
      }
      
      $lRet.= '  </div>'.LF;
      $lRet.= '</div>'.LF;
    }
    $lRet.= '</div>';
    
    return $lRet;
  }
  
  protected function getJs() {
    $lRet = '';
	$lRet.= '<script type="text/javascript">'.LF;
	
    if($this -> mPhraseTyp == 'job') {
	    $lUpdate = CCms_Job::hasProductChanged($this -> mJobId, $this -> mClientKey);
	
	    if($lUpdate && $this -> mCanEdit) {
	      $lRet.= 'jQuery(function(){ ';
	      $lRet.= '  Flow.cmsProduct.hasChanges("'.$this -> mClientKey.'");';
	      $lRet.= '});';
	    }
    }
    
    $lRet.= 'var lContentData = ';
    $lRet.= (!empty($this -> mData)) ? Zend_Json::encode($this -> mData) : '[]';
    $lRet.= ';' . LF;
    
    $lLayoutOptions = array();
    $lLayouts = CCor_Res::get('htb', 'phl');
    $lRet.= 'var lLayoutOptions = {';
    foreach($lLayouts as $lKey => $lValue) {
      $lLayoutOptions[] = $lKey." : '".$lValue."'";
    }
    $lRet.= implode(", ", $lLayoutOptions);
    $lRet.= '};'.LF;
    
    $lJobData = array();
    $lPhraseFields = array_values($this -> mPhraseFields);
    $lFie = CCor_Res::getByKey('alias', 'fie', array("mand" => MID));
    foreach ($lFie as $lKey => $lDef) {
      $lFla = intval($lDef['flags']);
      if (bitset($lFla, ffMetadata) || in_array($lKey, $lPhraseFields)) {
        $lFieldVal = $this -> mJob[$lKey];
        $lJobData[$lKey] = (empty($lFieldVal)) ? '' : $lFieldVal;
      }
    }
    $lRet.= 'var lJobData = '.Zend_Json::encode($lJobData) . ';'.LF;
    $lRet.= '</script>'.LF;
    
    return $lRet;
  }

}