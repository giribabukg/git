<?php
class CInc_App_Event_Action_Alink_Addtimesheet extends CApp_Event_Action {

  public function execute() {
    $this->dbg('Add Timesheet Record');
    
    $lPnr = $this -> mParams['pnr'];
    $lArtNr = $this -> mParams['artnr'];
    $lTime = $this -> mParams['time'];
    $lAmount = $this -> mParams['amount'];
    $lComment = $this -> mParams['comment'];
    $lDate = date("Y-m-d H:i:s");
    
    $lSet = new CApi_Alink_Query_Setworkitem();
    $lSet -> insert($this -> mContext['job']->jobid, $lPnr, $lArtNr, $lTime, $lAmount, $lComment, $lDate);
    
    return TRUE;
  }

  public static function getParamDefs($aType) {
    $lArr = array();
  
    $lArr[] = fie('pnr', 'Employee Number', 'input');
    $lArr[] = fie('artnr', 'Article Number', 'input');
    $lArr[] = fie('time', 'Time Spent', 'input');
    $lArr[] = fie('amount', 'Amount/Format (Cost)', 'input');
    $lArr[] = fie('comment', 'Comments', 'input');
  
    return $lArr;
  }
  
  public static function paramToString($aParams) {
    $lRet = '';
    $lFields = array(
        'pnr' => 'Employee Number',
        'artnr' => 'Article Number',
        'time' => 'Time Spent',
        'amount' => 'Amount/Format (Cost)',
        'comment' => 'Comments'
    );
    CCor_Msg::add(var_export($aParams, TRUE));
    foreach ($aParams as $lKey => $lVal) {
      $lRet.= $lFields[$lKey].': '.$lVal.', ';
    }
    return strip($lRet, 2);
  }
}