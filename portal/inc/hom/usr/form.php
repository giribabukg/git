<?php

class CInc_Hom_Usr_Form extends CHtm_Form
{

  public function __construct ($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);
    
    $this -> mCancel = 'hom-wel';
    $this -> addDef(fie('user', lan('usr.name')));
    $this -> addDef(fie('company', lan('lib.company')));
    $this -> addDef(fie('location', lan('lib.location')));
    $this -> addDef(fie('department', lan('usr.department')));
    $this -> addDef(fie('phone', lan('lib.phone')));
    $this -> addDef(fie('pass',lan('hom.usr.curr.pwd'), 'string',null, array('type' => 'password')));
  }
  
  protected function getButtons() {
    $lRet = '<div class="frm" style="padding:16px; text-align:right">'.LF;
    $lRet.= btn(lan('lib.ok'), 'javascript:Flow.checkUsrDet(); return false;', '<i class="ico-w16 ico-w16-ok"></i>', 'submit').NB;
    $lRet.= btn(lan('lib.cancel'), "go('index.php?act=hom-wel')", '<i class="ico-w16 ico-w16-cancel"></i>');
    $lRet.= '</div>'.LF;
    return $lRet;
  }
  
  public function load($aId) {
    $lId = intval($aId);
    $lSql = 'SELECT * FROM al_usr WHERE id=' . $lId;
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry -> getDat()) {
      unset($lRow['pass']);
      $this -> assignVal($lRow);
      $this -> setParam('val[id]', $lId);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }
}