<?php
class CInc_Webservice_Health_Cnt extends CCor_Cnt {
  
  /*
  function unserialize_xml($input, $recurse = false) {
    $data = ((!$recurse) && is_string($input))? simplexml_load_string($input): $input;
    if ($data instanceof SimpleXMLElement) $data = (array) $data;
    if (is_array($data)) foreach ($data as &$item) {
      $item = $this->unserialize_xml($item, true);
    }
    return $data;
  }
  */
  
  protected function actTest() {
    $lJson = file_get_contents("php://input");
    #CCor_Msg::add(MID.': '.$lJson, mtUser, mlError);
    $lRoot = Zend_Json::decode($lJson);
    
    $lRet = array();
    foreach ($lRoot as $lId => $lRow) {
      $lMethod = $lRow['method'];
      $lParams = $lRow['params'];
      
      $lFunc = 'test'.$lMethod;
      if ($this->hasMethod($lFunc)) {
        try {
          $lRes = $this->$lFunc($lParams);
        } catch (Exception $ex) {
          $lRes = array('error' => $ex->getMessage());
        }
      } else {
        $lRes = array('skip' => 'Unknown test '.$lMethod);
      }
      $lRet[$lId] = $lRes;
    }
    echo Zend_Json::encode($lRet);
  }
  
  protected function testDalimuploaddir() {
    $lCfg = CCor_Cfg::getInstance();
    $lDir = $lCfg->get('dalim.basedir');
    if (file_exists($lDir.'remote.txt')) {
      $lRet = array('ok' => 'Dalim upload directory accessible for '.MANDATOR_NAME);
    } else {
      $lRet = array('error' => 'Dalim upload directory not accessible for '.MANDATOR_NAME);
    }
    return $lRet;
  }
  
  protected function testDmsuploaddir() {
    $lCfg = CCor_Cfg::getInstance();
    $lDir = $lCfg->get('dms.upload.folder');
    if (file_exists($lDir.'remote.txt')) {
      $lRet = array('ok' => 'DMS upload directory accessible for '.MANDATOR_NAME);
    } else {
      $lRet = array('error' => 'DMS upload directory not accessible for '.MANDATOR_NAME);
    }
    return $lRet;
  }
  
  protected function testDmsquery() {
    $lQry = new CApi_Dms_Query();
    $lRes = $lQry->getFileList('dummy', 'dummy', '12345');
    if (is_array($lRes)) {
      $lRet = array('ok' => 'DMS API is accessible for '.MANDATOR_NAME);
    } else {
      $lRet = array('error' => 'DMS API is not accessible for '.MANDATOR_NAME);
    }
  }
  
  protected function testAlink() {
    $lQry = new CApi_Alink_Query('getInfo');
    $lQry->addParam('sid', MAND);
    $lRes = $lQry->query();
    $lMsg = $lRes->getVal('errmsg');
    if ('OK' == $lMsg) {
      $lRet = array('ok' => 'Alink responds for '.MANDATOR_NAME);
    } else {
      $lRet = array('error' => 'Alink error for '.MANDATOR_NAME.': '.$lMsg);
    }
    return $lRet;
  }

}