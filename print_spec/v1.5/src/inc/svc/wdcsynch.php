<?php
/**
 * Service to trigger the Wave Data Center job data synchronisation
 * Only used when delayed synchronisation is used 
 * (Config option mop.synch.delayed = true)
 * 
 * @author g.emmans@5flow.eu
 *
 */
class CInc_Svc_Wdcsynch extends CSvc_Base {
  
  protected function doExecute() {
    $lSql = 'SELECT mig FROM al_wdc_synch';
    $lQry = new CCor_Qry($lSql);
    $lMig = array();
    foreach ($lQry as $lRow) {
      $lMig[] = $lRow['mig'];
    }
    if (empty($lMig)) {
      return true;
    }
    $lSql = 'TRUNCATE al_wdc_synch';
    CCor_Qry::exec($lSql);
    
    $lMopLibPath = CCor_Cfg::get('mop.library');
    require_once $lMopLibPath.'MOP/Replication.php';
    MOP_Replication::triggerSync($lMig);
    return true;
  }

}