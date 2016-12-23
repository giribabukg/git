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

  public function __construct($aMod = 'job-cms', $aJobId = 0, $aJob = NULL, $aSrc) {
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
    
    $this -> setParam('jobid', $this -> mJobId);
    $this -> setParam('src', $this -> mSrc);
    
    //load job cms info
    if (!empty($this -> mJobId)) {
      $lData = new CJob_Cms_Mod($this -> mSrc, $this -> mJobId, $this -> mJob);
      $this -> mData = $lData -> loadJobRef(); //load job content
    } else $this -> mData = array('content' => array(), 'product' => array());
    
    //if blank snew otherwise sedt
    $lFunc = (sizeof($this -> mData) > 0) ? "sedt" : "snew";
    
    parent::__construct($this -> mMod.'.'.$lFunc, 'Content');
  }
  
  protected function getCont() {
    $lRet = '<div class="cap">'.ucfirst($this -> mPhraseTyp).' Content</div>'.LF;
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
    $lCategories = CCor_Res::get('htb', 'phc');
    
    $lCode = $this -> mJob['client_key'];
    $lRet = '<div id="job_form" class="frm">'.LF;
    $lUpdate = ($this -> mPhraseTyp == 'product') ? 'yes' : 'no';
	$lRet.= '<input id="referenceUpdate" name="ref_update" type="hidden" value="'.$lUpdate.'" />'.LF;

	if(empty($this -> mData['product']) && $this -> mProductUsed){
	  $lAreaId = ($this -> mPhraseTyp == 'product') ? 'content' : 'product';
	  $lRet.= '<div id="job_'.$lAreaId.'">'.LF;
	  $lRet.= '<div class="tg1 h2 fl w100p">Product: '.$lCode.'</div>' .LF;
	  if($this -> mPhraseTyp == 'job') {
	    $lRet.= '<div class="fl p8 b">No Product information for code '.$lCode.'</div>'.LF;
	  }
	  $lRet.= '</div>' . LF;
	}
	if(empty($this -> mData['content']) && $this -> mPhraseTyp == 'job'){
	  $lRet.= '<div id="job_content">'.LF;
	  $lRet.= '<div class="tg1 h2 fl w100p">Content</div>' .LF;
	  $lRet.= '</div>' . LF;
	}
	
    foreach($this -> mData as $lArea => $lContent) {
      $lAreaId = ($this -> mPhraseTyp == 'product') ? 'content' : $lArea;
      $lRet.= '<div id="job_'.$lAreaId.'">'.LF;
      $lArea = ($lArea == 'product') ? $lArea.": ".$lCode : $lArea; //show client key within header if product area
      $lRet.= '<div class="tg1 h2 fl w100p">'.ucfirst($lArea).'</div>' .LF;
      
      $lFac = new CJob_Cms_Item_Fac($lContent, 0, $this -> mJob, $this -> mSrc);
      $lItems = $lFac -> getItems();
      foreach($lItems as $lCategory => $lArr){
        $lRet.= '<div id="'.$lCategory.'" class="section">' . LF;
        foreach($lArr as $lIdx => $lItem){
          if($lIdx == 0) {
            $lRet.= '<div class="th1 fl w100p">'.$lCategories[$lCategory].'</div>' .LF;
          }
          
          $lRet.= $lItem . LF;
        }
        $lRet.= '</div>' . LF;
      }
      
      $lRet.= '</div>' . LF;
    }
    
    $lRet.= '</div>' . LF;
    
    if($this -> mCanBuild) {
      $lRet.= '<div id="save_dialog" title="Publish PDF" style="display:none;">'.LF;
	  $lRet.= '<input id="templateId" type="hidden" value="" />'.LF;
      $lRet.= '<iframe src="" onload="javascript:GetEditor()" id="chiliEditor" class="dn" style="width:100%;height:100%;"></iframe>'.LF;
      $lRet.= '</div>'.LF;
    }
    
    $lLayoutOptions = array();
    $lLayouts = CCor_Res::get('htb', 'phl');
    $lRet.= '<script type="text/javascript">var lLayoutOptions = {';
    foreach($lLayouts as $lKey => $lValue) {
      $lLayoutOptions[] = $lKey." : '".$lValue."'";
    }
    $lRet.= implode(", ", $lLayoutOptions);
    $lRet.= '};'.LF;
    
    $lJobData = array();
    $lPhraseFields = array('client_key', 'languages','chili_template_name');
    $lFie = CCor_Res::getByKey('alias', 'fie', array("mand" => MID));
    foreach ($lFie as $lKey => $lDef) {
      $lFla = intval($lDef['flags']);
      if (bitset($lFla, ffMetadata) || in_array($lKey, $lPhraseFields)) {
        $lFieldVal = $this -> mJob[$lKey];
        $lJobData[$lKey] = (empty($lFieldVal)) ? '' : $lFieldVal;
      }
    }
    $lRet.= 'var lJobData = '.json_encode($lJobData) . ';</script>'.LF;
    
    return $lRet;
  }
  
  /**
   * Get all options for user
   * @see CInc_Htm_Form::getButtons()
   */
  protected function getButtons($aBtnAtt = array(), $aBtnTyp = 'button') {
    $lRet = '<div class="sub cmsHeader p4">'.LF;
  
    if ($this -> mCanInsert) {
      $lRet.= btn(lan('job-cms.add'), 'Flow.cmsDialog.item();', 'img/ico/16/apl-add.gif', $aBtnTyp, $aBtnAtt).NB;
    }
    if ($this -> mCanEdit OR $this -> mCanInsert) {
      $lAction = '';
      if ($this -> mPhraseTyp == 'product' && CCor_Cfg::get('phrase.job.update', 'auto') == 'manual' && sizeof($this -> mData) > 0) {
        $lAction = 'return Flow.cmsProduct.update();';
      }
      $lRet.= btn(lan('lib.ok'), $lAction, 'img/ico/16/ok.gif', 'submit', array('id' => 'save')).NB;
    }
    if($this -> mCanBuild && $this -> mPhraseTyp == 'job') {
	  $aBtnAtt = array('id' => 'build_artwork', 'class' => 'btn');
      $lRet.= btn(lan('job-cms.build'), 'Flow.chili.buildArtwork()', 'img/ico/16/pdf.png', $aBtnTyp, $aBtnAtt).NB;
    }
    if ($this -> mCanInsert) {
      $lMen = new CHtm_Menu(lan('lib.opt'), "fl p4");
      $lMen -> addTh2(lan('lib.opt'));
      $lMen -> addJsItem("Flow.cmsDialog.search(0);", "Global Search", 'ico/16/col.gif');
      //$lMen -> addJsItem("javascript::void(0);", "Filter", 'ico/16/fie.gif');
      //$lMen -> addJsItem("javascript::void(0);", "Export Data", 'ico/16/excel.gif');
      $lRet.= $lMen -> getContent();
    }
    
    
    //all possible languages in helptable
    $lJobLangs = array_map('trim', explode(",", $this -> mJob['languages']));
    $lLanguages = CCor_Res::get('htb', 'dln');

    $lMandLang = CCor_Cfg::get('masterlanguage');
    $lMandLang = $lLanguages[$lMandLang];

    $lJobLangs = array_values($lJobLangs);
    $lLangKeys = array_keys($lLanguages);
    $lIntersect = array_intersect($lJobLangs, $lLangKeys);
    foreach($lLanguages as $lKey => $lVal) {
      if(!in_array($lKey, $lIntersect)) {
        unset($lLanguages[$lKey]);
      }
    }
    $lLanguages = array_merge(array("" => " "), $lLanguages);
    
    $lRet.= ($this -> mCanEdit OR $this -> mCanInsert) ? '<div class="fr">' : '<div>';
    $lRet.= '<span style="float:left;margin-right:10px;">';
    $lRet.= '<b style="display:inline-block;padding:5px;">Job '.lan('cms-tra').': </b>';
    $lRet.= getSelect('language', $lLanguages, '', array("id" => "translation", "onchange" => "Flow.cmsForm.showTranslation();", "style" => "display:inline-block;height:26px;margin-right:10px;"));
    $lRet.= '</span>';
    $lRet.= '<span style="display:inline-block;padding:5px;margin-right:10px;">';
    $lRet.= '<b>Master Language:</b> '.$lMandLang;
    $lRet.= '</span>';
    $lRet.= '</div>';
    
    $lRet.= '</div>'.LF;
    return $lRet;
  }
  
  protected function getJs() {
    $lCode = $this -> mJob['client_key'];
    
    $lRet = '<script type="text/javascript">'.LF;
    $lRet.= 'document.observe("dom:loaded", function() { ';
    
    if($this -> mCanTranslate) {
      $lRet.= 'Flow.cmsForm.highlightTranslations();';
    }
    
    if($this -> mPhraseTyp == 'job') {
      $lJobCmsMod = new CJob_Cms_Mod($this -> mSrc, $this -> mJobId, $this -> mJob);
      $lUpdate = $lJobCmsMod -> hasProductChanged();
      if($lUpdate && $this -> mCanEdit) {
        $lRet.= 'Flow.cmsProduct.hasChanges("'.$lCode.'");';
      }
  	}
    
    $lRet.= ' });'.LF;
    $lRet.= '</script>';
    
    return $lRet;
  }

}