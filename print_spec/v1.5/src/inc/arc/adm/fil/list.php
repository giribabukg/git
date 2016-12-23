<?php
class CInc_Arc_Adm_Fil_List extends CJob_Fil_List {

  public function __construct($aJobId, $aJob, $aSub = '', $aAge = 'arc') {
    parent::__construct('adm', $aJobId, $aSub, $aAge);

    $lPid = $aJob['jobid_pro'];
    if (!empty($lPid)) {
      $this -> addRelated('pro', $lPid, 'Project');
    }

  }

}
