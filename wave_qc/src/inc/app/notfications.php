<?php
class CInc_App_Notfications extends CCor_Obj {

  public function __construct() {
  
  }
  
  public function getJobNotfications($aJobId) {
    $lJobid = $aJobId;
    $lRows = array();
    $lSql = 'SELECT id, jobid, from_name, to_name, response FROM al_sys_mails WHERE `response` = 1 AND jobid='.$lJobid;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lRows[] = array('from_name' => $lRow['from_name'], 'to_name' => $lRow['to_name']);
    }
  
    $lNum = count($lRows);
    $lContent = '';
    foreach ($lRows as $lName => $lRow) {
      $lContent.= $lRow['from_name'].' ==> '.$lRow['to_name'].BR;
    }
  
    $lImg = ($lNum > 0) ? $lImg = img('img/wave8/ico/16/email.gif').' '.$lNum : '';
    $lTitel= "Pending Questions";
    $lCaption = toolTip($lContent, $lTitel).$lImg.'</span>';
    return $lCaption;
  }
}