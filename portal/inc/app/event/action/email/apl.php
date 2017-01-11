<?php
class CInc_App_Event_Action_Email_Apl extends CApp_Event_Action {

  public function execute() {
    if (isset($this -> mContext['msg']['add']['apl_usr'])) {
      $lAplUsr = $this -> mContext['msg']['add']['apl_usr'];
      $this -> mParams['sid'] = $lAplUsr;
    }
    $lSender = new CApp_Sender('apl', $this -> mParams, $this -> mContext['job'], $this -> mContext['msg']);
    return $lSender -> execute();
  }

  public static function getParamDefs($aRow) {
    $lArr = array();
    $lTpl = CCor_Res::extract('id', 'name', 'tpl');
    $lFie = fie('tpl', lan('lib.tpl'), 'select', $lTpl);
    $lArr[] = $lFie;
    #$lFie = fie('dur', lan('lib.duration'), 'int');// hier stehen alle Felder, die Kommasepaiert i. param gespeichert werden
    #$lArr[] = $lFie;
    return $lArr;
  }

  public static function paramToString($aParams) {
    $lRet = '';
    if (isset($aParams['tpl'])) {
      $lTpl = $aParams['tpl'];
      $lArr = CCor_Res::extract('id', 'name', 'tpl');
      $lRet.= (isset($lArr[$lTpl])) ? $lArr[$lTpl] : lan('lib.unknown').' '.$lTpl;
    } else {
      $lRet.= 'empty';
    }
    return $lRet;
  }
}