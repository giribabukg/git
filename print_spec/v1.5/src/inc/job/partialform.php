<?php
class CInc_Job_Partialform extends CCor_Ren {
  
  /**
   * Array of src => key to get job form block templates
   * @var array
   */
  protected $mParts;
  
  /**
   * The Job data (either CCor_Dat or array(alias => value)
   */
  protected $mJob;
  
  /**
   * Whether to show all fields in readonly state
   * @var boolean
   */
  protected $mDisabled;
  
  public function __construct($aJob) {
    $this->mParts = array();
    $this->mJob = $aJob;
    $this->mSrc = $this->mJob['src'];
    $this->mJid = (isset($this->mJob['jobid'])) ? $this->mJob['jobid'] : $this->mJob['id'];
    
    $this->mDisabled = true;
    $this->mFac = new CHtm_Fie_Fac($this->mSrc, $this->mJid);
    $this->mLang = LAN;
  }
  
  public function setDisabled($aFlag = true) {
    $this->mDisabled = true;
  }
  
  public function setLang($aLanguage) {
    $this->mLang = $aLanguage;
  }
  
  public function addPart($aSrc, $aKey) {
    $this->mParts[] = array($aSrc, $aKey);
  }
  
  protected function getCont() {
    $lDoc = '';
    foreach ($this->mParts as $lRow) {
      $lSrc = $lRow[0];
      $lKey = $lRow[1];
      $lDoc.= $this->getPart($lSrc, $lKey);
    }
    
    $lTpl = new CCor_Tpl();
    $lTpl->setDoc($lDoc);
    $lTpl->setLang($this->mLang);
    
    $lFie = CCor_Res::extract('alias', 'name_'.$this->mLang, 'fie');
    $lBez = $lTpl->findPatterns('bez.');
    if (!empty($lBez)) {
      foreach ($lBez as $lAlias) {
        if (isset($lFie[$lAlias])) {
          $lTpl->setPat('bez.'.$lAlias, $lFie[$lAlias]);
        }
      }
    }
    
    return $lTpl->getContent();
  }
  
  protected function getPart($aSrc, $aKey) {
    $lPart = new CJob_Part($aSrc, $aKey, $this -> mFac, $this -> mJob);
    if ($this->mDisabled) {
      $lPart->setDisabled(true);
    }
    return $lPart->getContent();
  }

}