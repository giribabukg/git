<?php
/**
 * JArchiv-Jobs: Formular
 *
 *  Description
 *
 * @package    ARC
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 9985 $
 * @date $Date: 2015-08-07 17:30:07 +0800 (Fri, 07 Aug 2015) $
 * @author $Author: jwetherill $
 */
class CInc_Arc_Form extends CCor_Tpl {

  protected $mAct;    // Controller action to set if ok is pressed
  protected $mCancel; // URL for cancel button. Leave empty if no cancel button should be available
  protected $mUsr;
  protected $mJobId;
  protected $mFie = array();
  protected $mPar = array();
  protected $mVal = array();
  protected $mPag = array();
  protected $mTemplates  = array();
  protected $mFac;
  protected $mShowCopyPanel = TRUE;
  protected $mShowReusePanel = TRUE;
  
  public function __construct($aSrc, $aAct, $aPage = 'job', $aJobId) {
    $this -> mSrc = $aSrc;
    $this -> mAct = $aAct;
    $this -> mTab = (empty($aPage)) ? 'job' : $aPage;
    $this -> setParam('act', $aAct);
    $this -> mJobId = $aJobId;
    $this -> mJob = array();
    $this -> mCanEdit = FALSE;
    $this -> mUsr = CCor_Usr::getInstance();

    $this -> getFac();
    $this -> mPnl = new CJob_Btnpanel();
    $this -> addPanel('act', 'Actions');

    $this -> openProjectFile('job/main.htm');
    $this -> mFie = CCor_Res::getByKey('alias', 'fie');
    $this -> mSrcArr = CCor_Cfg::get('all-jobs'); //array('art', 'rep', 'sec', 'mis', 'adm', 'com', 'tra');
    
    // Show Copy Panel
    $this -> mCopyJob = $this -> mUsr-> canCopyJob($this -> mSrcArr);
    if ($this -> mShowCopyPanel) {
      if (!empty($this -> mCopyJob)) {
        $this -> addCopyPanel();
      }
    }
    //End Show Copy Panel

    // Show Re-use Panel
    $this -> mReuseJob = $this -> mUsr-> canRead('arc-reuse');
    if ($this -> mShowReusePanel) {
      if (!empty($this -> mReuseJob)) {
        $this -> addReusePanel();
      }
    }
    //End Show Re-use Panel
    
    // Templates f�r die Jobtypen festlegen:
    $lCfg = CCor_Cfg::getInstance();
    $this -> lDefaultTabs = $lCfg -> get('job.mask.tabs');
    
    // Angabe der Jobmasken in mand/mand_Nr/mand/inc/job/formtpl.php - OHNE Beschr�nkung, OHNE Funktionen!
    $FormTpl = new CJob_Formtpl();
    $this -> mTemplates = $FormTpl -> mArchivTemplates;
    
	// Webcenter ProjektId nur f�r Jobs "art, rep" und mit dem Recht "job-wec-id" verkn�pft.
	// Falls es in der Job-Maske keine Reiter "Details (det)" gibt, soll es unter "Identifikation (job)" angezeigt werden.
    $lUsr = CCor_Usr::getInstance();
    if( in_array($aSrc, array('rep','art')) AND $lUsr -> canEdit('job-wec-id') ){
      if (in_array('det',$this -> lDefaultTabs)){
        $this -> mTemplates['rep']['det']['wec'] = 'rep';
      } else {
        $this -> mTemplates['rep']['job']['wec'] = 'rep';
      }
    } else {
      unset( $this -> mTemplates['rep']['job']['wec'] );
    }
  }

  protected function getFac() {
    if (isset($this -> mFac)) {
      return;
    }
    if (!empty($this -> mJobId)) {
      $this -> mFac = new CHtm_Fie_Fac($this -> mSrc, $this -> mJobId);
    } else {
      $this -> mFac = new CHtm_Fie_Fac();
    }
  }

  protected function addPage($aKey) {
    $lPag['key'] = $aKey;
    $lPag['par'] = array();
    $this -> mPag[$aKey] = $lPag;
  }

  protected function addPart($aPage, $aKey, $aRc = NULL) {
    if (isset($aRc)) {
      $lPar['src'] = $aRc;
    }
    $lPar['pag'] = $aPage;
    $lPar['key'] = $aKey;

    $this -> mPag[$aPage]['par'][$aKey] = $lPar;
  }

  public function addDef($aDef) {
    $lAlias = $aDef['alias'];
    $this -> mFie[$lAlias] = $aDef;
  }

  protected function & getDef($aAlias) {
    if (isset($this -> mFie[$aAlias])) {
      return $this -> mFie[$aAlias];
    } else {
      return NULL;
    }
  }

  public function setParam($aKey, $aValue) {
    $this -> mPar[$aKey] = $aValue;
  }

  public function getParam($aKey) {
    if (isset($this -> mPar[$aKey])) {
      return $this -> mPar[$aKey];
    } else {
      return NULL;
    }
  }

  protected function getHiddenFields() {
    if (empty($this -> mPar)) {
      return;
    }
    $lRet = '';
    foreach ($this -> mPar as $lKey => $lVal) {
      $lRet.= '<input type="hidden" name="'.$lKey.'" value="'.htm($lVal).'" />'.LF;
    }
    return $lRet;
  }

  public function getVal($aKey, $aDefault = '') {
    if (isset($this -> mJob[$aKey])) {
      return $this -> mJob[$aKey];
    } else {
      return $aDefault;
    }
  }

  public function setVal($aKey, $aValue) {
    $this -> mVal[$aKey] = $aValue;
  }

  public function assignVal($aArr) {
    if (empty($aArr)) {
      return;
    }
    foreach ($aArr as $lKey => $lVal) {
      $this -> setVal($lKey, $lVal);
    }
  }

  public function addPanel($aKey, $aCaption) {
    $this -> mPnl -> addPanel($aKey, $aCaption, '', 'job_'.$aKey);
  }

  public function addBtn($aKey, $aCaption, $aAction = '', $aImg = '', $aType = 'button', $aAttr = array()) {
    $this -> mPnl -> addBtn($aKey, $aCaption, $aAction, $aImg, $aType, $aAttr);
  }

  protected function getContTabs() {
    if (isset($this -> mTabs)) {
      return $this -> mTabs -> getContent();
    }
    return '';
  }

  protected function getContHeader() {
    if (isset($this -> mHeader)) {
      return $this -> mHeader -> getContent();
    }
    return '';
  }
  
  protected function getBlockFilter($aPar) {
    return FALSE;
  }

  protected function getPartForms($aPar) {
    // SonderFunktion unter CUST 340
    $lFilteredBlocks = $this -> getBlockFilter($aPar);
    $lPar = ($lFilteredBlocks) ? $lFilteredBlocks : $aPar;
    $lRet = '';
    foreach ($lPar as $lKey => $lVal) {
      if(!isset($lVal['src'])) {
        $lPar = new CJob_Part($this -> mSrc, $lKey, $this -> mFac, $this -> mJob);
      } else {
        $lPar = new CJob_Part($lVal['src'], $lKey, $this -> mFac, $this -> mJob);
      }
      if ('not' != $lVal['key']) {
        $lPar -> setDisabled(TRUE);
      }

      if ($this -> mHiddenUpload) {
        $lPar -> setHidden($this -> mHiddenUpload);
      }
      $lRet.= $lPar -> getContent();
    }
    return $lRet;
  }

  protected function getPages() {
    $lRet = '';
    foreach ($this -> mPag as $lKey => $lPag) {
      $lDis = ($this -> mTab == $lKey) ? 'block' : 'none';
      $lRet.= '<div style="display:'.$lDis.'" id="pag'.$lKey.'">'.LF;
      $lRet.= $this -> getPartForms($lPag['par']);
      $lRet.= '</div>';
    }
    return $lRet;
  }

  protected function getContButtons() {
    if (isset($this -> mPnl)) {
      return $this -> mPnl -> getContent();
    }
    return '';
  }

  public function setLang($aLang = NULL) {
    $lLoc = (NULL == $aLang) ? LAN : $aLang;
    parent::setLang();
    $lArr = CCor_Res::get('fie');
    foreach ($lArr as $lFie) {
      $lAlias = $lFie['alias'];
      
      // If Jobfield has Read Flag active, ask for User READ-RIGHT (combination of 'fie_' and Alias).
      // If User has no READ-RIGHT, dont show the Jobfield.
      $lFieRight = 'fie_'.$lAlias;
      if (bitset($lFie['flags'],ffRead) && !$this -> mUsr -> canRead($lFieRight)){
        $this -> setPat('bez.'.$lAlias, '');
        continue;
      }
      $this -> setPat('bez.'.$lAlias, $lFie['name_'.$lLoc]);
    }
  }

  protected function onBeforeContent() {
    $lRet = $this -> getContHeader();
    $lRet.= $this -> getContTabs();
    $this -> setPat('frm.btn',    $this -> getContButtons());
    $this -> setPat('frm.hidden', $this -> getHiddenFields());
    $this -> setPat('frm.parts',  $this -> getPages());
    $this -> setPat('frm.act',    $this -> mAct);
    $this -> setPat('frm.page',   $this -> mTab);
    $this -> precompile();
    $this -> setLang();
  }

  public function getTemplates() {
    if(isset($this -> mTemplates[$this -> mSrc])) {
      return $this -> mTemplates[$this -> mSrc];
    } else {
      return array();
    }
  }
  
  protected function addCopyPanel() {
    $this -> addPanel('cpy', lan('lib.copy'), '', 'job.cpy');
    $this -> addButton('cpy', $this -> getCopyButtonMenu());
  }
  
  public function getCopyButtonMenu($aAttr = array()) {
    $lMen = new CHtm_Menu('Button');
    //$lArr = $this -> mSrcArr;
    foreach ($this -> mCopyJob as $lSta) {
      $lImg = (THEME === 'default' ? 'job-'.$lSta : CApp_Crpimage::getColourForSrc($lSta));
      $lMen -> addItem('index.php?act=job-'.$lSta.'.cpy&amp;jobid='.$this -> mJobId.'&amp;target='.$lSta.'&amp;src='.$this -> mSrc.'&amp;arc=', lan('lib.copy_to').' '.lan('job-'.$lSta.'.menu'), 'ico/16/'.$lImg.'.gif');
    }
    $lLnk = (THEME === 'default' ? "Flow.Std.popMain('".$lMen -> mDivId."')" : "javascript:gIgn=1;Flow.Std.popMen('".$lMen -> mDivId."')");
    $lBtn = btn(lan('lib.copy_to'), $lLnk, 'img/ico/16/copy.gif', 'button', array('class' => 'btn w200','id' => $lMen -> mLnkId));
    $lBtn .= $lMen -> getMenuDiv();
    return $lBtn;
  }
  
  public function addButton($aKey, $aBtn) {
    $this -> mPnl -> addButton($aKey, $aBtn);
  }

  #22851 "re-use job from archive"
  protected function addReusePanel() {
    $this -> addPanel('reuse', lan('lib.reuse'), '', 'job.reuse');
    $this -> addButton('reuse', $this -> getReuseButtonMenu());
  }

  #22851 "re-use job from archive"
  public function getReuseButtonMenu($aAttr = array()) {
    $lMen = new CHtm_Menu('Button');
    $lImg = (THEME === 'default' ? 'job-'.$this->mSrc : CApp_Crpimage::getColourForSrc($this->mSrc));
    
    $lMen -> addItem('index.php?act=arc-'.$this -> mSrc.'.reuse&amp;jobid='.$this -> mJobId.'&amp;src='.$this -> mSrc.'&amp;type=0', lan('lib.reuse.asis'), 'ico/16/'.$lImg.'.gif');
    $lLnk = (THEME === 'default' ? "Flow.Std.popMain('".$lMen -> mDivId."')" : "javascript:gIgn=1;Flow.Std.popMen('".$lMen -> mDivId."')");
    $lBtn = btn(lan('lib.reuse'), $lLnk, 'img/ico/16/clock_refresh.gif', 'button', array('class' => 'btn w200','id' => $lMen -> mLnkId));
    $lBtn .= $lMen -> getMenuDiv();
    return $lBtn;
  }

}