<?php
class CInc_App_Event_Action_Email_Groupasrole extends CApp_Event_Action {

  public function execute() {
    $lAli = $this->mParams['sid'];
    $this->mParams['sid'] = $this->mContext['job'][$lAli];
    $lSender = new CApp_Sender('gru', $this -> mParams, $this -> mContext['job'], $this -> mContext['msg']);
    return $lSender -> execute();
  }

  public static function getParamDefs($aRow) {
    $lArr = array();
    $lFieArr = Array();
    $lFieArr = CCor_Res::get('fie');
    $lGruArr = array(); // Group Select Jobfields

    // Get Group Select Jobfields
    foreach ($lFieArr as $lRow) {
      if ($lRow['typ'] == 'gselect') {
        $lGruArr[$lRow['alias']] = $lRow['name_'.LAN];
      }
    }
    $lFie = fie('sid', 'Group as Role', 'select', $lGruArr);
    $lArr[] = $lFie;
    $lTpl = CCor_Res::extract('id', 'name', 'tpl');
    $lFie = fie('tpl', lan('lib.tpl'), 'select', $lTpl);
    $lArr[] = $lFie;
    $lTmp = array('y' => lan('lib.yes'), 'n' => lan('lib.no'), 'f' => lan('lib.forced'));
    $lFie = fie('def', lan('lib.default'), 'select', $lTmp);
    $lArr[] = $lFie;
    $lFie = fie('members', lan('lib.select.members'), 'boolean');
    $lArr[] = $lFie;
    $lTmp = array('all' => lan('crp.usr.confirm.all'), 'one' => lan('crp.usr.confirm.one'));
    $lFie = fie('confirm', lan('crp.apl.used').' '.lan('crp.usr.confirm'), 'select', $lTmp);
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
      $lArr = CCor_Res::extract('alias', 'name_'.LAN, 'fie');
      $lRet.= (isset($lArr[$lSid])) ? $lArr[$lSid] : '['.lan('lib.unknown').' '.$lSid.']';
    } else {
      $lRet.= '[empty group]';
    }
    $lRet.= ', ';
    if (isset($aParams['tpl'])) {
      $lTpl = $aParams['tpl'];
      $lArr = CCor_Res::extract('id', 'name', 'tpl');
      $lRet.= (isset($lArr[$lTpl])) ? $lArr[$lTpl] : lan('lib.unknown').' '.$lTpl;
    } else {
      $lRet.= 'empty';
    }
    $lRet.= ', checked: ';
    $lDef = $aParams['def'];
    $lTmp = array('y' => lan('lib.yes'), 'n' => lan('lib.no'), 'f' => lan('lib.forced'));
    $lRet.= (isset($lTmp[$lDef])) ? $lTmp[$lDef] : lan('lib.yes');

    if (isset($aParams['confirm'])) {
      $lRet.= ', '.lan('crp.apl.used').' '.lan('crp.usr.confirm').': ';
      $lDef = $aParams['confirm'];
      $lTmp = array('all' => lan('crp.usr.confirm.all'), 'one' => lan('crp.usr.confirm.one'));
      $lRet.= (isset($lTmp[$lDef])) ? $lTmp[$lDef] : lan('lib.yes');
    }
    
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