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
    $lWeekends =  ($this->mParams['weekends'] == 'Yes') ? FALSE : TRUE;

    if (!empty($lFrom)) {
      $lValue = (isset($lJob[$lFrom])) ? $lJob[$lFrom] : '';
    } else {
      $lDefFie = CCor_Res::get('fie');
      foreach ($lDefFie as $lFie) {
        $lAli = $lFie['alias'];
        //if field is date type then add business days to the job field
        if ($lAli == $lField && $lFie['typ'] == 'date') {
          $lValue = intval($lValue);
          if(is_int($lValue) && $lValue > 0) {
            $lDate = self::addBusinessDays($lJob[$lAli], $lValue, $lWeekends);
            $lValue = date( lan("lib.date.long"), $lDate );
          } else {
            $lValue = ($lValue == 0) ? '' : $lJob[$lAli];
          }
        }
      }
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
    $lBool = CCor_Res::get('htb', 'boo');
    $lFie = fie('weekends', 'Weekends Included?', 'select', $lBool);
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
    if (!empty($aParams['weekends'])) {
      $lRet.= ' weekends included "'.$aParams['weekends'].'"';
    }
    return $lRet;
  }

  protected static function addBusinessDays($aDate, $aDays, $aWeekends = TRUE) {
    $aDate = (empty($aDate)) ? date( lan("lib.date.long") ) : date( lan("lib.date.long"), strtotime($aDate));
    
    $lDate = new DateTime();
    $lDate -> setTimestamp( strtotime($aDate) );

    while ($aDays > 0) {
      $lWeekDay = $lDate -> format('N');

      if ($lWeekDay > 5 && $aWeekends !== FALSE) { //Skip Saturday and Sunday
        $lDate = $lDate -> add(new DateInterval('P1D'));
        continue;
      }

      $aDays--;
      $lDate = $lDate -> add(new DateInterval('P1D'));
    }

    return $lDate -> getTimestamp();
  }

}