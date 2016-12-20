<?php
class CInc_App_Event_Action_Field_Set extends CApp_Event_Action {

  public function execute() {
    $lJob = $this->mContext['job'];
    $lJid = $lJob->getId();
    $lSrc = $lJob->getSrc();

    $lField = $this->mParams['field'];
    $lValue = $this->mParams['value'];
    $lFrom =  $this->mParams['from_field'];
    
    $lValue = ($lValue == 'now()') ? date("Y-m-d H:i:s") : $lValue;

    if (!empty($lFrom)) {
      $lValue = (isset($lJob[$lFrom])) ? $lJob[$lFrom] : '';
    }
    $lFac = new CJob_Fac($lSrc, $lJid, $lJob);
    $lMod = $lFac->getMod($lJid);
    return $lMod->forceUpdate(array($lField => $lValue));
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