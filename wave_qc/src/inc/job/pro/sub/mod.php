<?php
/**
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 102 $
 * @date $Date: 2012-05-31 17:17:19 +0800 (Thu, 31 May 2012) $
 * @author $Author: gemmans $
 */
class CInc_Job_Pro_Sub_Mod extends CCor_Mod_Table {
  /*
   * Changed Jobfields
   * to use update sub jobs.
   */
  public $mChangedFields = Array();
  /*
   * To Set changes in History
   */
  public $mArrHistoryUpd = Array();
  
  public function __construct() {
    parent::__construct('al_job_sub_'.intval(MID));
    $this -> mInsertId = NULL;

    $lFie = CCor_Res::get('fie');
    foreach ($lFie as $lDef) {
      $this -> addField($lDef);
    }
    #$this -> addField(fie('id'));
  }

  protected function doUpdate() {
    $lSql = 'UPDATE '.$this -> mTbl.' SET ';
    $lArrTemp = Array(); // Changed Jobfields
    foreach ($this -> mOld as $lKey => $lVal) {
      if ($this -> fieldHasChanged($lKey)) {
        $lNew = $this -> getVal($lKey);
        $lArrTemp[$lKey] = $lNew;
        $lItm['old'] = $this -> getOld($lKey);
        $lItm['new'] = $this -> getVal($lKey);
        $this -> setHistoryUpdate ($lKey,$lItm);
        $lSql.= $lKey.'="'.addslashes($lNew).'",';
      }
    }
    $lSql = strip($lSql, 1);
    $lSql.= ' WHERE id='.esc($this -> getOld('id')).' LIMIT 1';
    $this -> mChangedFields = $lArrTemp;
    return CCor_Qry::exec($lSql);
  }

  protected function doInsert() {

    // Get existing columns from table al_job_sub_X
    // non-defined Columns come not in SQL.
    $lTabelColumns = new CCor_Qry('SHOW COLUMNS FROM '.$this -> mTbl);
    foreach ($lTabelColumns as $lRow) {
      $lExistingColumns[] = $lRow -> Field;
    }


    $lSql = 'INSERT INTO '.$this -> mTbl.' SET ';
    foreach ($this -> mVal as $lKey => $lVal) {
      if ($lKey == 'id') continue;
      if ('' == $lVal) continue;
      if (in_array($lKey, array('del','webstatus','mas_state','art_state','rep_state','sec_state','mis_state','adm_state'))) continue;
      if (!in_array($lKey,$lExistingColumns)){
         // non-defined Columns come not in SQL.
        $this ->dbg('Column '.$lKey.' not exist in '.$this -> mTbl,mlWarn);
        continue;
      }

      $lSql.= $lKey.'="'.addslashes($lVal).'",';
    }
    $lSql = strip($lSql, 1);
    if ($this -> mTest) {
      $this -> dbg($lSql);
      return TRUE;
    } else {
      $lQry = new CCor_Qry();
      $lRet = $lQry -> query($lSql);
      if ($lRet) {
        $this -> mInsertId = $lQry -> getInsertId();
      } else {
        $this -> mInsertId = NULL;
      }
      return $lRet;
    }
  }

  /*
   * Assign Job to existing ProjectItem
   * @param $aPrjItmId string|int ID of existing ProjectItem
   * @param $aJobId string|int JobId
   * @param $aSrc string JobType
   */
  public function insertInPrjItem($aJobId, $aPrjItmId = '', $aSrc) {
    $lJobId = $aJobId;
    $lPrjItmId = $aPrjItmId;
    $lSrc = $aSrc;
    $lColoumnName = 'jobid_'.$lSrc; // jobid_art,jobid_rep....

    $lTabelColumns = new CCor_Qry('SHOW COLUMNS FROM '.$this -> mTbl);
    foreach ($lTabelColumns as $lRow) {
      $lExistingColumns[] = $lRow -> Field;
    }
    $lSql = 'UPDATE al_job_sub_'.MID.' SET ';
    foreach ($this -> mVal as $lKey => $lVal) {
      if ($lKey == 'id') continue;
      if ('' == $lVal) continue;
      if (in_array($lKey, array('del','webstatus','mas_state','art_state','rep_state','sec_state','mis_state','adm_state'))) continue;
      if (!in_array($lKey,$lExistingColumns)){
        // non-defined Columns come not in SQL.
        $this ->dbg('Column '.$lKey.' not exist in '.$this -> mTbl,mlWarn);
        continue;
      }
      $lSql.= $lKey.'="'.addslashes($lVal).'",';
    }

    $lSql = strip($lSql, 1);
    $lSql.= ' WHERE id ='.esc($lPrjItmId);
    CCor_Qry::exec($lSql);
    $this ->dbg('Job Assigment to ProjektItemId #'.$lPrjItmId);

    /*
     * If Reference Projekt-Item is Master or Variant,
     * then Update in Job
    */
    $lUpd = Array();
    $lSql = 'Select is_master,master_id from al_job_sub_'.MID;
    $lSql.= ' WHERE id ='.$lPrjItmId;
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry -> getAssoc()) {
      if($lRow['is_master'] == 'X'){
        $lUpd = Array('is_master' => 'X');
      }elseif($lRow['master_id'] != ''){
        $lUpd = array('master_id' => $lRow['master_id']);
      }
    }
    if (!empty($lUpd)){
      $lFac = new CJob_Fac($lSrc);
      $lMod = $lFac -> getMod($lJobId);
      $lMod -> forceUpdate($lUpd);
    }
  }

  public function unassignItems($aMasterId) {
    $lId = intval($aMasterId);
    $lSql = 'UPDATE al_job_sub_'.MID.' SET master_id="" WHERE master_id='.$lId;
    CCor_Qry::exec($lSql);
    //@TODO : remove master_id from networker jobs
  }

  /*
   * Get History Update Array
   * @see CInc_Job_His_List function getMoreUpd();
   */
  public function getHistoryUpdate(){
    return $this -> mArrHistoryUpd;
  }
  
  /*
   * Set History Update Array
   * @param string $aKey changed jobfield
   * @param array $aUpd old and new value
   */
  public function setHistoryUpdate($aKey, $aUpd = Array()){
    $this -> mArrHistoryUpd[$aKey] = $aUpd;
  }
}