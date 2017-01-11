<?php
class CInc_Api_Alink_Query_Insertak extends CApi_Alink_Query {

  protected $mFie;
  protected $mCnd;

  public function __construct($aJobId, $aType = 'A', $aDescription = '', $aComment = '') {
    parent::__construct('insertAk');
    $this -> addParam('sid', MAND);
    $this -> addParam('jobid', $aJobId);
    $this -> addParam('type', $aType);
    if (!empty($aDescription)) {
      $this -> addParam('description', $aDescription);
    }
    if (!empty($aComment)) {
      $this -> addParam('comment',  $aComment);
    }
  }

}