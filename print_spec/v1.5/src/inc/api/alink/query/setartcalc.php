<?php
class CInc_Api_Alink_Query_Setartcalc extends CApi_Alink_Query {

  /*
   * @param string $aJobId Jobid of Job
   * @param array $aArticles key/value array, $aArr[Artnr] = amount
   */

  public function __construct($aJobId, $aArr) {
    parent::__construct('setArtKalkItemAmount');
    $this -> addParam('sid', MAND);
    $this -> addParam('jobid', $aJobId);
    $lArr = array();
    foreach ($aArr as $lArtNr => $lAmount) {
      $lArr[] = $lArtNr.'='.$lAmount;
    }
    $this -> addParam('items', $lArr);
  }

}