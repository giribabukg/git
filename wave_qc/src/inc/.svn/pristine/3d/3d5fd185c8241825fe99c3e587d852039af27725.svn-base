<?php

class CInc_Hom_Usr_Form extends CHtm_Form
{

  public function __construct ($aAct, $aCaption, $aCancel = NULL)
  {
    parent::__construct($aAct, $aCaption, $aCancel);
    
    $this -> mCancel = 'hom-wel';
    $this -> addDef(fie('user', lan('hom.usr.old')));
    $this -> addDef(fie('new', lan('hom.usr.new')));
    $this -> addDef(fie('confirm', lan('hom.usr.confirm')));
  }

  public function load ($aId)
  {
    $lId = intval($aId);
    $lSql = 'SELECT * FROM al_usr WHERE id=' . $lId;
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry -> getDat()) {
      #$this -> assignVal($lRow);
      $this -> setParam('val[id]', $lId);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }
}