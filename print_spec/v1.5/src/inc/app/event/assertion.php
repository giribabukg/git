<?php
interface IApp_Event_Assertion {
  /**
   * Constuctor, initialize context
   *
   * @param Array $aContext Context, e.g. Job data, message from status dialog
   * @param Array $aParams  Parameters, e.g. Event book list to send emails to
   */
  public function __construct($aContext, $aParams);

  /**
   * Execute the Action, e.g. send an eMail
   *
   * @return boolean Was the execution successfull?
   */
  public function isValid();

  /**
   * Return string representation of Parameters, e.g. for event lists
   *
   * @param string $aParam
   */
  public static function paramToString($aParam);
}

class CInc_App_Event_Assertion extends CCor_Obj implements IApp_Event_Assertion {

  public function __construct($aContext, $aParams) {
    $this -> mContext = $aContext;
    $this -> mParams  = $aParams;
  }

  public function isValid() {
    return TRUE;
  }

  public static function paramToString($aParams) {
    $lRet = '';
    foreach ($aParams as $lKey => $lVal) {
      $lRet.= $lKey.' '.$lVal.', ';
    }
    return strip($lRet, 2);
  }

}
