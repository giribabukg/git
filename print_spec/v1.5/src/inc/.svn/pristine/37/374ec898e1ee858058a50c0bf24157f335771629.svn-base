<?php
class CInc_Svc_Xinsert extends CSvc_Base {

  protected function doExecute() {

    $lIgnore = array('id', 'x_src', 'x_jobid', 'x_status', 'x_import_date','x_assign_date', 'x_xml');

    $lTbl = 'al_xchange_projects_'.MID;
    $lSql = 'SELECT * FROM '.$lTbl.' WHERE 1 ';
    $lSql.= 'AND x_status="new" ORDER BY id';

    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lMod = new CJob_Pro_Mod();
      foreach ($lRow as $lKey => $lVal) {
        if (in_array($lKey, $lIgnore)) continue;
        $lMod->forceVal($lKey, $lVal);
      }
      $lRes = $lMod->insert();
      if ($lRes) {
        $lJid = $lMod->getInsertId();
        $lSql = 'UPDATE '.$lTbl.' SET x_status="assigned",x_jobid='.esc($lJid).',x_assign_date=NOW() ';
        $lSql.= 'WHERE id='.esc($lRow['id']);
        CCor_Qry::exec($lSql);
      }
    }

    $lTbl = 'al_xchange_jobs_'.MID;
    $lSql = 'SELECT * FROM '.$lTbl.' WHERE 1 ';
    $lSql.= 'AND x_status="new" ORDER BY id';

    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lSrc = 'art';
      if (!empty($lRow['x_src'])) {
        $lSrc = $lRow['x_src'];
      }
      $lFac = new CJob_Fac($lSrc);
      $lMod = $lFac->getMod();
      foreach ($lRow as $lKey => $lVal) {
        if (in_array($lKey, $lIgnore)) continue;
        $lMod->forceVal($lKey, $lVal);
      }
      $lRes = $lMod->insert();
      if ($lRes) {
        $lJid = $lMod->getInsertId();
        $lSql = 'UPDATE '.$lTbl.' SET x_status="assigned",x_jobid='.esc($lJid).',x_src='.esc($lSrc).',x_assign_date=NOW() ';
        $lSql.= 'WHERE id='.esc($lRow['id']);
        CCor_Qry::exec($lSql);
      }
    }
    return true;
  }

}