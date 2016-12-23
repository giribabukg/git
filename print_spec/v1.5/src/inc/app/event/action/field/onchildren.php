<?php
class CInc_App_Event_Action_Field_Onchildren extends CApp_Event_Action {

  public function execute() {
    $lJob = $this->mContext['job'];
    $lJid = $lJob->getId();
    $lSrc = $lJob->getSrc();

    $lField = $this->mParams['field'];
    $lValue = $this->mParams['value'];
    $lFrom =  $this->mParams['from_field'];

    if (!empty($lFrom)) {
      $lValue = (isset($lJob[$lFrom])) ? $lJob[$lFrom] : '';
    }
    $lProItemId = CCor_Qry::getInt('SELECT id FROM al_job_sub_'.MID.' WHERE jobid_item = '.esc($lJid));
    if (!$lProItemId) return;
    $lSql = 'SELECT src, jobid_item FROM al_job_sub_'.MID.' WHERE master_id = '.$lProItemId;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      if ($lRow) {
        $lFac = new CJob_Fac($lRow['src'], $lRow['jobid_item']);
        $lMod = $lFac->getMod($lRow['jobid_item']);
        $lMod->forceUpdate(array($lField => $lValue));
      }
    }
    return;
  }

  public static function getParamDefs($aRow) {
    $lRet = array();
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