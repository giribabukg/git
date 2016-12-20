<?php
interface IApp_Event_Action {
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
  public function execute();

  /**
   * Return string representation of Parameters, e.g. for event lists
   *
   * @param string $aParam
   * @return string The string representation of the parameters
   */
  public static function paramToString($aParam);

  /**
   * Return clear text parameter details, e.g. send mail to whom
   *
   * @param string $aParam
   * @return string The string representation of the parameter details
   */
  public static function getParamDetails($aParam);

  /**
   * Get Field definitions for Parameter Form and Mod
   *
   * @return Array An array of Field definitions as returned by fie()
   */

  public static function getParamDefs($aType);

}

class CInc_App_Event_Action extends CCor_Obj implements IApp_Event_Action {

  public function __construct($aContext, $aParams) {
    $this -> mContext = $aContext;
    $this -> mParams  = $aParams;
  }

  public function execute() {
    return TRUE;
  }

  public static function paramToString($aParams) {
    $lRet = '';
    CCor_Msg::add(var_export($aParams, TRUE));
    foreach ($aParams as $lKey => $lVal) {
      $lRet.= $lKey.' '.$lVal.', ';
    }
    return strip($lRet, 2);
  }

  public static function getParamDetails($aParams) {
    return '';
  }

  public static function getParamDefs($aType) {
    return array();
  }

  protected function & getJob() {
    return $this -> mContext['job'];
  }

  protected function setJobValue($aKey, $aValue) {
    $lJob = $this -> getJob();
    $lJob -> setVal($aKey, $aValue);
  }

  protected function getJobValue($aKey, $aValue) {
    $lJob = $this -> getJob();
    $lJob -> getVal($aKey);
  }


}