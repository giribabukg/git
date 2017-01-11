<?php
class CInc_App_Event_Action_Field_Onparent extends CApp_Event_Action {

  public function execute() {
    $lJob = $this->mContext['job'];
    $this -> mJobid = $lJob->getId();
    $lSrc = $lJob->getSrc();

    $lField = $this->mParams['field'];
    $lValue = $this->mParams['value'];
    $lFrom =  $this->mParams['from_field'];
    $lLevels =  $this->mParams['levels'];
    
    if (!isset($lLevels) && !empty($lLevels)) return;

    if (!empty($lFrom)) {
      $lValue = (isset($lJob[$lFrom])) ? $lJob[$lFrom] : '';
    }
    if ($lLevels == 'one') {
      $lMasterId = CCor_Qry::getInt('SELECT master_id FROM al_job_sub_'.MID.' WHERE jobid_item = '.esc($this -> mJobid));
      if (!$lMasterId) return;
      $lSql = 'SELECT src, jobid_item FROM al_job_sub_'.MID.' WHERE id = '.$lMasterId;
      $lQry = new CCor_Qry($lSql);
    }
    
    if ($lLevels == 'all') {
      $lSqlAll = 'SELECT pro_id FROM al_job_sub_'.MID.' WHERE jobid_item = '.$this -> mJobid;
      $aProId = CCor_Qry::getInt($lSqlAll);
      $this -> getIterator($aProId);
      $lFirstParentId = $this -> mProItemsPerJobId[$this -> mJobid]['master_id'];
      if ($lFirstParentId) $lParentJobIds = $this -> getParentsRekursive($lFirstParentId);
      $lQry = $this -> mParenstJobIds;
    }
    
    foreach ($lQry as $lRow) {
      if ($lRow) {
        $lFac = new CJob_Fac($lRow['src'], $lRow['jobid_item']);
        $lMod = $lFac->getMod($lRow['jobid_item']);
        $lMod->forceUpdate(array($lField => $lValue));
      }
    }
    return;
  }
  
  protected function getIterator($aProId) {
    $lSql = 'SELECT * FROM al_job_sub_'.MID.' WHERE pro_id='.$aProId.' AND del="N"';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lProItemsPerJobId[$lRow['jobid_item']] = $lRow;
      $lProItemsPerTableId[$lRow['id']] = $lRow;
    }
    $this -> mProItemsPerJobId = $lProItemsPerJobId;
    $this -> mProItemsPerTableId = $lProItemsPerTableId;
  }
  
  protected function getParentsRekursive($aParentId) {
    $lParentJobId = $this -> mProItemsPerTableId[$aParentId]['jobid_item'];
    $lParentJobSrc = $this -> mProItemsPerTableId[$aParentId]['src'];
    $lFirstParentId = $this -> mProItemsPerJobId[$lParentJobId]['master_id'];
    $this -> mParenstJobIds[] = array('jobid_item' => $lParentJobId, 'src' => $lParentJobSrc);
    if ($lFirstParentId) self::getParentsRekursive($lFirstParentId);
  }
  
  public static function getParamDefs($aRow) {
    $lRet = array();
    $lTmp = array('one' => 'Only the direct parent', 'all' => 'All parents recursively');
    $lFie = fie('levels', 'Parent levels to update', 'select', $lTmp);
    $lRet[] = $lFie;
    
    $lAll = CCor_Res::extract('alias', 'name_'.LAN, 'fie');
    $lResDef = array('res' => 'fie', 'key' => 'alias', 'val' => 'name_'.LAN);
    $lFie = fie('field', 'Job Field', 'resselect', $lResDef);
    $lRet[] = $lFie;
    $lFie = fie('value', 'Value');
    $lRet[] = $lFie;
    $lFie = fie('from_field', 'or from Field', 'resselect', $lResDef);
    $lRet[] = $lFie;
    return $lRet;
  }

  public static function paramToString($aParams) {
    if (isset($aParams['field'])) {
      $lFid = $aParams['field'];
      $lFie = CCor_Res::extract('alias', 'name_'.LAN, 'fie');
      $lRet = (isset($lFie[$lFid])) ? $lFie[$lFid] : '['.lan('lib.unknown').']';
    } else {
      $lRet = '['.lan('lib.unknown').']';
    }
    if (!empty($aParams['from_field'])) {

    } else {
      if (!empty($aParams['value'])) {
        $lRet.= ' to "'.$aParams['value'].'"';
      } else {
        $lRet.= ' to [empty value]';
      }
    }
    return $lRet;
  }

}