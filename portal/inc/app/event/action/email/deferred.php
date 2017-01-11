<?php
class CInc_App_Event_Action_Email_Deferred extends CApp_Event_Action {

  public function execute() {

    $this->dbg('-------------- Exec  -------------');
    $this->mJob = $this->mContext['job'];
    $lJid = $this->mJob->getId();
    $lSrc = $this->mJob->getSrc();

    $lSql = 'SELECT id FROM al_job_apl_loop ';
    $lSql.= 'WHERE jobid='.esc($lJid).' ';
    $lSql.= 'AND src='.esc($lSrc).' ';
    $lSql.= 'ORDER BY id desc LIMIT 1;';
    $lLoopId = CCor_Qry::getInt($lSql);
    
    $lRet = true;
    $lSql = 'SELECT * FROM al_job_apl_loop_events WHERE loop_id='.esc($lLoopId).' AND jobid='.esc($lJid);
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      if (!$this -> sendDeferred($lRow['event_id'])) {
        $lRet = false;
      }
    }
    return $lRet;
  }

  protected function sendDeferred($aEventId) {
    $lSql = 'SELECT * FROM al_eve_act ';
    $lSql.= 'WHERE eve_id='.esc($aEventId).' ';
    $lSql.= 'AND pos=100';

    $lRet = true;

    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lAct) {
      if (1 != $lAct['active']) continue;
      if (!empty($lAct['cond_id'])) {
        $lMet = $this->isConditionMet($lAct['cond_id']);
        if (!$lMet) continue;
      }
      if (!$this -> doAction($lAct)) {
        $lRet = false;
      }
    }
    return $lRet;
  }

  protected function isConditionMet($aCondId) {
    if (isset($this->mConditions[$aCondId])) {
      return $this->mConditions[$aCondId];
    }
    $lFac = new CInc_App_Condition_Registry();
    $lObj = $lFac->loadFromDb($aCondId);
    $lObj->setContext('data', $this->mJob);
    $lMet = $lObj->isMet();
    $this->mConditions[$aCondId] = $lMet;
    return $lMet;
  }

  protected function doAction($aAct) {
    $lTyp = $aAct['typ'];
    $lPar = toArr($aAct['param']);
    $lPar['tablekeyid'] = $aAct['id'];

    $this->dbg('-------------- Do '.$lTyp.' -------------');

    $this -> getRegistry();
    $lObj = $this -> mReg -> factory($lTyp, $this -> mContext, $lPar);
    if (!$lObj) {
      $this -> dbg('Failed to create '.$lTyp);
      return FALSE;
    }
    return $lObj -> execute();
  }

  protected function getRegistry() {
    if (!isset($this -> mReg)) {
      $this -> mReg = new CApp_Event_Action_Registry();
    }
  }

}