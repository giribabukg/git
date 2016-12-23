<?php
class CApp_Event_Action_Js_Btn extends CApp_Event_Action {

  public function execute() {
    $this -> dbg('Execute Javasript to StepButton');
    return TRUE;
  }

  public static function getParamDefs($aType) {
    $lArr = array();
    
    $lJs = CCor_Res::get('htb', 'js');
    $lFie = fie('js', 'JavaScript', 'select', $lJs);
    $lArr[] = $lFie;
    $lFie = fie('value', 'Value');
    $lArr[] = $lFie;
    return $lArr;
  }

  public static function paramToString($aParams) {
    $lRet = '';
    
    if (isset($aParams['js'])) {
      $lJs = $aParams['js'];
      $lArr = CCor_Res::get('htb', 'js');
      $lRet.= (isset($lArr[$lJs])) ? $lArr[$lJs] : 'unknown '.$lJs;
    } else {
      $lRet.= 'empty';
    }
  if (!empty($aParams['value'])) {
      $lRet.= ' ("'.$aParams['value'].'")';
    } else {
      $lRet.= ' ("")';
    }
    return $lRet;
  }

}