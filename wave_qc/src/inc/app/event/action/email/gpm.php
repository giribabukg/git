<?php
/**
 * App: Event - Action - Email - Gpm (XMLs)
 *
 * @package    APP
 * @subpackage Event
 * @subsubpackage Action - Email
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 6 $
 * @date $Date: 2012-02-21 16:50:56 +0800 (Tue, 21 Feb 2012) $
 * @author $Author: gemmans $
 */
class CInc_App_Event_Action_Email_Gpm extends CApp_Event_Action {

  public function execute() {
    $lCfg = CCor_Cfg::getInstance();
    $this -> mParams['frm'] = $lCfg -> getVal('gpm.returnmail.from');
    $this -> mParams['to'] = $lCfg -> getVal('gpm.returnmail.to');
    $lSender = new CApp_Sender('gpm', $this -> mParams, $this -> mContext['job'], $this -> mContext['msg']);
    return $lSender -> execute();
  }

  public static function getParamDefs($aRow) {
    $lArr = array();
    $lTpl = CCor_Res::extract('id', 'name', 'tpl');
    $lFie = fie('tpl', lan('lib.tpl'), 'select', $lTpl);
    $lArr[] = $lFie;
    $lTmp = array('y' => 'yes', 'n' => 'no', 'f' => 'forced');
    $lFie = fie('def', 'Checked by default', 'select', $lTmp);
    $lArr[] = $lFie;
  #  $lFie = fie('att', 'as Attachment to eMail', 'select', $lTmp);
  #  $lArr[] = $lFie;
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
    $lRet.= ', checked: ';
    $lDef = $aParams['def'];
    $lTmp = array('y' => 'yes', 'n' => 'no', 'f' => 'forced');
    $lRet.= (isset($lTmp[$lDef])) ? $lTmp[$lDef] : 'yes';
  #  $lRet.= ', attached: ';
  #  $lDef = $aParams['att'];//Attachment to eMail
  #  $lRet.= (isset($lTmp[$lDef])) ? $lTmp[$lDef] : 'yes';
    return $lRet;
  }
}