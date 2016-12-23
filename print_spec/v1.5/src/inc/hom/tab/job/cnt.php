<?php
class CInc_Hom_Tab_Job_Cnt extends CCor_Cnt {
  
  /*
   * archive job?
   */
  public $mArc = FALSE;
  
  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mSrc = $this -> getReq('src');
    $this -> mAll = $this -> getReq('all');
    $this -> mArc = $this -> getReq('arc', FALSE);

    $this -> mCode = $this -> getReq('code');
    $this -> mTitle = lan('tab.job.'.$this -> mSrc.'.'.$this -> mCode);

    $this -> mTarget = CCor_Qry::getStr('SELECT target FROM al_tab_slave WHERE mand='.MID.' AND code="'.$this -> mCode.'"');

    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead('tab.job.'.$this -> mSrc.'.'.$this -> mCode)) {
      $this -> setProtection('*', 'tab.job.'.$this -> mSrc.'.'.$this -> mCode, rdNone);
    }
    //archive job; set protection
    if ($this -> mArc){
      $this -> setProtection('*', 'tab.job.'.$this -> mSrc.'.'.$this -> mCode, rdNone);
    }
  }

  protected function actStd() {
    $lJobID = $this -> getReq('jobid');

    if ('all' == $this -> mSrc) {
      $this -> mSrc = $this -> mAll;
    }

    if (!$this -> mArc){
      $lClass = 'CJob_'.ucfirst($this -> mSrc).'_Dat';
      $lJob = new $lClass();
    }else{
      // archive job
      $lClass = 'CArc_Dat';
      $lJob = new $lClass($this -> mSrc);
    }
    $lJob -> load($lJobID);

    $lRet = '';

    $lClass = 'CJob_'.ucfirst($this -> mSrc).'_Header';
    $lVie = new $lClass($lJob);
    $lRet.= $lVie -> getContent();

    if (!$this -> mArc){
      $lClass = 'CJob_'.ucfirst($this -> mSrc).'_Tabs';
    }else{
      $lClass = 'CArc_'.ucfirst($this -> mSrc).'_Tabs';
    }
    
    $lVie = new $lClass($lJobID, $this -> mCode);
    $lRet.= $lVie -> getContent();
    
    if ('formtpl' == $this -> mTarget) {
      if (!$this -> mArc){
        $lVie = new CHom_Tab_Job_Form_Form($lJobID, $this -> mSrc, $this -> mCode);
      }else{
        //archive job
        $lVie = new CHom_Tab_Job_Form_Arcform($lJobID, $this -> mSrc, $this -> mCode);
      }
    } elseif ('iframe' == $this -> mTarget) {
      $lVie = new CHom_Tab_Job_Iframe_Form($this -> mCode);
    }
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
  }

}