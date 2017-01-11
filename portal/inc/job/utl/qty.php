<?php
class CInc_Job_Utl_Qty extends CCor_Obj {

  public static function addJob($aSrc, $aJobId, $aWebstatus, $aFirstTime = TRUE) {
    $lAmd = 0;
    $lQry = new CCor_Qry();
    if (!$aFirstTime) {
      $lQry -> query('SELECT amend_count FROM al_job_shadow_'.intval(MID).' WHERE jobid='.esc($aJobId).' AND src='.esc($aSrc));
      if ($lRow = $lQry -> getDat()) {
        $lAmd = intval($lRow['amend_count']);
      }
    }
    $lFirst = ($aFirstTime) ? 'Y' : 'N';
    $lSql = 'INSERT INTO al_rep_qty SET src='.esc($aSrc).', ';
    $lSql.= 'datum=NOW(),';
    $lSql.= 'jobid='.esc($aJobId).',';
    $lSql.= 'webstatus='.esc($aWebstatus).',';
    $lSql.= 'amend='.esc($lAmd).',';
    $lSql.= 'first_time='.esc($lFirst);
    $lQry -> query($lSql);
  }

}