<?php
class CInc_Job_Fac extends CCor_Obj {

  public function __construct($aSrc, $aJid = NULL, $aJob = NULL) {
    $this -> mSrc = $aSrc;
    $this -> mJid = $aJid;
    $this -> mJob = $aJob;
  }

  /**
   * @return CJob_Header
   */
  public function getHeader() {
    $lCls = 'CJob_'.$this -> mSrc.'_Header';
    $lObj = new $lCls($this -> mJob);
    return $lObj;
  }

  /**
   * @param string $aActiveTab
   * @return CHtm_Tabs
   */
  public function getTabs($aActiveTab = NULL) {
    $lCls = 'CJob_'.$this -> mSrc.'_Tabs';
    $lObj = new $lCls($this -> mJid, $aActiveTab);
    return $lObj;
  }

  /**
   * @param string $aAct
   * @param string $aPage
   * @return CJob_Form
   */
  public function getForm($aAct, $aPage = 'job') {
    $lCls = 'CJob_'.$this -> mSrc.'_Form';
    $lObj = new $lCls($aAct, $this -> mJid, $this -> mJob, $aPage);
    return $lObj;
  }

  /**
   * @return CJob_Dat
   */
  public function getDat() {
    $lCls = 'CJob_'.$this -> mSrc.'_Dat';
    $lObj = new $lCls();
    $lObj -> load($this -> mJid);
    $this -> mJob = $lObj;
    return $lObj;
  }

  /**
   * @return CJob_Mod
   */
  public function getMod($aJobId = null) {
    $lCls = 'CJob_'.$this -> mSrc.'_Mod';
    $lObj = new $lCls($aJobId);
    return $lObj;
  }

}