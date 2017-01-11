<?php
class CInc_Job_Utl_Sub extends CCor_Obj {

  public static function reflectUpdate($aJobID, $aJob) {
    if (empty($aJobID) OR empty($aJob)) {
      return;
    }

    $lJobID = esc($aJobID);
    $lJobFields = CCor_Res::getByKey('alias', 'fie');

    $lJobField = array();
    foreach ($lJobFields as $lAlias => $lProperties) {
      $lAva = $lProperties['avail'];
      if (bitset($lAva, fsSub)) {
        $lJobField[$lAlias] = $lProperties;
      }
    }

    $lSQL = 'UPDATE al_job_sub_'.intval(MID).' SET ';

    $lUpdate = array();
    foreach ($aJob as $lKey => $lValue) {
      if (!isset($lJobField[$lKey])) {
        continue;
      }

      if (!in_array($lJobFields[$lKey]['typ'], array('gselect', 'int', 'uselect'))) {
        $lUpdate[] = $lKey.'='.esc($lValue);
      } elseif (in_array($lJobFields[$lKey]['typ'], array('gselect', 'int', 'uselect')) AND !empty($lValue)) {
        $lUpdate[] = $lKey.'='.$lValue;
      } else {
        // When the jobfield type is integer but it has no value this needs to be blank!
      }
    }

    $lSQL.= implode(',', $lUpdate);
    $lSQL.= ' WHERE';
    $lSQL.= ' jobid_art='.$lJobID.' OR';
    $lSQL.= ' jobid_rep='.$lJobID.' OR';
    $lSQL.= ' jobid_mis='.$lJobID.' OR';
    $lSQL.= ' jobid_sec='.$lJobID.' OR';
    $lSQL.= ' jobid_com='.$lJobID.' OR';
    $lSQL.= ' jobid_tra='.$lJobID.';';
    CCor_Qry::exec($lSQL);
  }
}