<?php
class CInc_App_Event_Action_Select_Event extends CApp_Event_Action {

  public function execute() {
    $lField = isset($this->mParams['field']) ? $this->mParams['field'] : NULL;
    if (empty($lField)) return TRUE;
    $lJob = $this->mContext['job'];
    if (empty($lJob[$lField])) return TRUE;

    $lEvent = $lJob[$lField];

    //@TODO: what about ign?
    $lEve = new CJob_Event($lEvent, $lJob, $this->mContext['msg']);
    return $lEve -> execute();
  }

  public static function getParamDefs($aRow) {
    $lArr = array();
    $lRes = CCor_Res::extract('code', 'name', 'evetype');
    $lRes = array('' => '') + $lRes;
    $lFie = fie('type', lan('lib.type'), 'select', $lRes);
    $lArr[] = $lFie;
    return $lArr;
  }

  public static function paramToString($aParams) {
    $lRet = '';
    if (isset($aParams['type'])) {
      $lTyp = $aParams['type'];
      $lArr = CCor_Res::extract('code', 'name', 'evetype');
      $lRet.= (isset($lArr[$lTyp])) ? $lArr[$lTyp] : lan('lib.unknown').' '.$lTyp;
    } else {
      $lRet.= 'empty';
    }
    return $lRet;
  }
}