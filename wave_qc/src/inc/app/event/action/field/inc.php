<?php
class CInc_App_Event_Action_Field_Inc extends CApp_Event_Action {

  public function execute() {
    $lJob = $this->mContext['job'];
    $lJid = $lJob->getId();
    $lSrc = $lJob->getSrc();

    $lField = $this->mParams['field'];
    $lDelta = isset($this->mParams['delta']) ? intval($this->mParams['delta']) : 1;
    $lOldValue = intval($lJob[$lField]);
    $lNewValue = $lOldValue + $lDelta;

    $lFac = new CJob_Fac($lSrc, $lJid, $lJob);
    $lMod = $lFac->getMod($lJid);

    return $lMod->forceUpdate(array($lField => $lNewValue));
  }

  public static function getParamDefs($aRow) {
    $lRet = array();
    $lResDef = array('res' => 'fie', 'key' => 'alias', 'val' => 'name_'.LAN);
    $lFie = fie('field', 'Job Field', 'resselect', $lResDef);
    $lRet[] = $lFie;
    $lFie = fie('delta', 'Increase by');
    $lRet[] = $lFie;
    return $lRet;
  }

  public static function paramToString($aParams) {
    $lRet = '';
    if (isset($aParams['field'])) {
      $lFid = $aParams['field'];
      $lFie = CCor_Res::extract('alias', 'name_'.LAN, 'fie');
      $lRet.= (isset($lFie[$lFid])) ? $lFie[$lFid] : '['.lan('lib.unknown').']';
    } else {
      $lRet.= '['.lan('lib.unknown').']';
    }
    if (isset($aParams['delta'])) {
      $lDelta = $aParams['delta'];
      $lRet.= ' by '.$lDelta;
    } else {
      $lRet.= ' by 1';
    }
    return $lRet;
  }

}