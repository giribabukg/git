<?php
class CInc_Svc_Qty extends CSvc_Base {

  protected function doExecute() {
    $lFie = CCor_Res::extract('alias', 'native', 'fie');

    $lWriter = CCor_Cfg::get('job.writer.default', 'alink');
    if ('portal' == $lWriter) {
      $lIte = new CCor_TblIte('all');
      $lIte -> addField('webstatus');
      $lIte -> addField('src');
      $lIte -> addCondition('webstatus', '=', '30');
    } else {
      $lIte = new CApi_Alink_Query_Getjoblist();
      $lIte -> addField('webstatus', $lFie['webstatus']);
      $lIte -> addField('src', $lFie['src']);
      $lIte -> addCondition('webstatus', '=', '30');
    }

    if (!$lIte -> query()) {
      return TRUE;
    }

    $lRes = $lIte -> getArray();

    $lQry = new CCor_Qry();
    $lBas = 'REPLACE INTO al_rep_qty SET ';
    $lBas.= 'datum=NOW(),';
    foreach ($lRes as $lJob) {
      $lAmd = 0;
      $lQry -> query('SELECT amend_count FROM al_job_shadow_'.intval(MID).' WHERE jobid='.esc($aJobId).' AND src='.esc($aSrc));
      if ($lRow = $lQry -> getDat()) {
        $lAmd = intval($lRow['amend_count']);
      }

      $lSql = $lBas.'src='.esc($lJob['src']).',';
      $lSql.= 'jobid='.esc($lJob['jobid']).',';
      $lSql.= 'amend='.esc($lAmd).',';
      $lSql.= 'webstatus='.intval($lJob['webstatus']);
      $lQry -> query($lSql);
    }
    return TRUE;
  }
}