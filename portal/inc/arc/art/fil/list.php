<?php
class CInc_Arc_Art_Fil_List extends CJob_Fil_List {

  public function __construct($aJobId, $aJob, $aSub = '', $aAge = 'arc') {
    parent::__construct('art', $aJobId, $aSub, $aAge);

    $lPid = $aJob['jobid_pro'];
    if (!empty($lPid)) {
      $this -> addRelated('pro', $lPid, 'Project');
    }

  }

}
