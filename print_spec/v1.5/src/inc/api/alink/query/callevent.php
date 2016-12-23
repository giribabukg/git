<?php
class CInc_Api_Alink_Query_Callevent extends CApi_Alink_Query {

  protected $mFie;
  protected $mCnd;

  public function __construct($aJobId, $aEvent, $aParam = NULL) {
    parent::__construct('callEvent');
    $this -> addParam('sid', MAND);
    $this -> addParam('jobid', $aJobId);
    $this -> addParam('event', $aEvent);
    if (NULL !== $aParam) {
      $this -> addParam('param', $aParam);
    }
  }

}