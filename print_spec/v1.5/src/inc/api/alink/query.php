<?php
class CInc_Api_Alink_Query extends CCor_Obj {

  protected $mMethod;
  /** var CApi_Alink_Param $mParam */
  protected $mParam;
  protected $mClient;
  protected $mResponse;

  public function __construct($aMethod, $aClient = NULL) {
    $this -> mMethod = $aMethod;
    $this -> mParam  = new CApi_Alink_Param($aMethod);
    $this -> mClient = $aClient;
  }

  public function addParam($aKey, $aVal = NULL) {
    $this -> mParam -> add($aKey, $aVal);
  }

  public function setClient($aClient) {
    $this -> mClient = $aClient;
  }

  public function query() {
    if (empty($this -> mClient)) {
      $lWriter = CCor_Cfg::get('job.writer.default', 'alink');
      if ('mop' == $lWriter) {
         $this -> mClient = CApi_Moplink_Client::getInstance();
      } else {
         $this -> mClient = CApi_Alink_Client::getInstance();
      }
    }
    $this -> addParam('transmitbuffer', $this -> mClient -> getByteBuffer());
    $lXml = $this -> mParam -> getXml();
    $lRes = $this -> mClient -> query($lXml, $this-> mMethod);
    $this -> mResponse = new CApi_Alink_Response($lRes, $this -> mMethod);
    return $this -> mResponse;
  }

}
