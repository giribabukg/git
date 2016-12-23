<?php
class CInc_Job_Utl_Sub extends CCor_Obj {

  public static function reflectUpdate($aJobId, $aArr) {
    if (empty($aArr) or empty($aJobId)) {
      return;
    }
    $lDefs = array();
    $lFie = CCor_Res::getByKey('alias', 'fie');
    foreach ($lFie as $lAli => $lDef) {
      $lAva = $lDef['avail'];
      if (bitset($lAva, fsSub)) {
        $lDefs[$lAli] = $lDef;
      }
    }
    $lUpd = array();
    $lSql = 'UPDATE al_job_sub_'.intval(MID).' SET ';
    foreach ($aArr as $lKey => $lVal) {
      if (!isset($lDefs[$lKey])) {
        continue;
      }
      $lUpd[] = $lKey.'='.esc($lVal);
    }
    $lJid = esc($aJobId);
    $lSql.= implode(',', $lUpd);
    $lSql.= ' WHERE ';
    $lSql.= 'jobid_art='.$lJid.' OR ';
    $lSql.= 'jobid_rep='.$lJid.' OR ';
    $lSql.= 'jobid_mis='.$lJid.' OR ';
    $lSql.= 'jobid_sec='.$lJid.' OR ';
    $lSql.= 'jobid_com='.$lJid.' OR ';
    $lSql.= 'jobid_tra='.$lJid.';';
    CCor_Qry::exec($lSql);
  }

}