<?php
class CInc_Arc_His_List extends CJob_His_List {

  protected $mCrpId;
  protected $mCrp = Array();

  public function __construct($aMod, $aJobId, $aStage = 'arc') {
    parent::__construct($aMod, $aJobId, $aStage);
  }
}