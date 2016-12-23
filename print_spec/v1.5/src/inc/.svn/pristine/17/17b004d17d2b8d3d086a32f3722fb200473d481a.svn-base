<?php
class CInc_Api_Alink_Query_Getjobfields extends CApi_Alink_Query {

  public function __construct() {
    parent::__construct('getJobFields');
    $this -> addParam('sid', MAND);
  }

  public function getList() {
    $lRet = array();
    if ($this -> query()) {
      foreach($this -> mResponse -> mDoc -> field as $lRow) {
        $lKey = (string)$lRow -> alias;
        $lVal = (string)$lRow -> native;

        $lRet[] = array('alias' => $lKey, 'native' => $lVal);
      }
    }
    return $lRet;
  }

}