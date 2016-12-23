<?php
class CInc_App_Event_Action_Email_User extends CApp_Event_Action {

  public function execute() {
    $lSender = new CApp_Sender('usr', $this -> mParams, $this -> mContext['job'], $this -> mContext['msg']);
    return $lSender -> execute();
  }

  public static function getParamDefs($aType) {
    $lArr = array();
    $lFie = fie('sid', lan('lib.user'), 'uselect');
    $lArr[] = $lFie;
    
    $lTpl = CCor_Res::extract('id', 'name', 'tpl');
    $lFie = fie('tpl', 'Email Template', 'select', $lTpl);
    $lArr[] = $lFie;
    
    $lFie = fie('task', 'Task', 'tselect', array('dom' => 'apl_task'));
    $lArr[] = $lFie;
    
    $lTmp = array('y' => lan('lib.yes'), 'n' => lan('lib.no'), 'f' => lan('lib.forced'));
    $lFie = fie('def', lan('lib.default'), 'select', $lTmp);
    $lArr[] = $lFie;
    
    $lTmp = array('y' => lan('lib.yes'), 'n' => lan('lib.no'));
    $lFie = fie('inv', lan('eve.act.invite.active'), 'select', $lTmp);
    $lArr[] = $lFie;
    return $lArr;
  }

  public static function paramToString($aParams) {
    $lRet = '';
    if (isset($aParams['sid'])) {
      $lSid = $aParams['sid'];
      $lArr = CCor_Res::extract('id', 'first_lastname', 'usr');
      $lRet.= (isset($lArr[$lSid])) ? $lArr[$lSid] : '[unknown user '.$lSid.']';
    } else {
      $lRet.= '[empty user]';
    }
    $lRet.= ', ';
    if (isset($aParams['tpl'])) {
      $lTpl = $aParams['tpl'];
      $lArr = CCor_Res::extract('id', 'name', 'tpl');
      $lRet.= (isset($lArr[$lTpl])) ? $lArr[$lTpl] : 'unknown '.$lTpl;
    } else {
      $lRet.= 'empty';
    }
    if (!empty($aParams['task'])) {
      $lRet.= ', Task: '.$aParams['task'];
    }
    $lRet.= ', checked: ';
    $lDef = $aParams['def'];
    $lTmp = array('y' => 'yes', 'n' => 'no', 'f' => 'forced');
    $lRet.= (isset($lTmp[$lDef])) ? $lTmp[$lDef] : 'yes';
    $lRet.= ', '.lan('eve.act.invite.active').': ';
    if (isset($aParams['inv'])) {
      $lDef = $aParams['inv'];
    } else {
      $lDef = 'y';
    }
    $lTmp = array('y' => 'yes', 'n' => 'no');
    $lRet.= (isset($lTmp[$lDef])) ? $lTmp[$lDef] : 'yes';
    return $lRet;
  }


}