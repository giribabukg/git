<?php
/**
 * Api: Alink - Client
 *
 * SINGLETON
 *
 * @package    API
 * @subpackage Moplink
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 33 $
 * @date $Date: 2012-03-19 23:49:25 +0800 (Mon, 19 Mar 2012) $
 * @author $Author: gemmans $
 */
class CInc_Api_Moplink_Client extends CApi_Tcp {
  
  private static $mInstance;
  
  private final function __construct() {
    $lCfg = CCor_Cfg::getInstance();
    $this -> setConfig(
      $lCfg -> getVal('alink.host'),
      $lCfg -> getVal('alink.port'),
      $lCfg -> getVal('alink.user'),
      $lCfg -> getVal('alink.pass')
    );
  }
  
  public static function getInstance() {
    if (NULL === self::$mInstance) {
      self::$mInstance = new self();
    }
    return self::$mInstance;
  }
  
  private function __clone() {}
  
  /**
   * Reads all input from remote host until EOF is found
   *
   * @access public
   * @return string Reply data from remote host
   */
  public function readAll() {
    if (!$this -> mHandle) {
      $this -> setError('Not connected', 500, mlError);
      return '';
    }
    $lRet = '';
    while (!feof($this -> mHandle)) {
      $lTmp = fgets($this -> mHandle);
      #$this -> dbg('READ : '.$lTmp);
      if ($lTmp === FALSE) {
        $this -> setError('TCP: Data not read', 102, mlError);
        return '';
      }
      $lRet.= $lTmp;
      if (substr($lRet,-13,11) == '</response>') break;
    }
    $this -> doMsg('RECV : '.$lRet, mlInfo);
    return $lRet;
  }
  
  public function query($aXml) {
    
    require_once 'Zend/Loader/Autoloader.php';
    $loader = Zend_Loader_Autoloader::getInstance();
    $lMopLinkHost = CCor_Cfg::get('moplink.host');
    $lClient= new Zend_Http_Client();
    $lClient->setUri($lMopLinkHost);
    $lClient->setConfig(array(
      'maxredirects'=>0,
      'timeout'=>60,
      'useragent'=>'QBF Projektportal',
      
    ));
    $this -> doMsg('SEND : '.$aXml, mlInfo);
    $lClient->setParameterPost(array(
      'data'=> $aXml,
    ));
    $lClient->setMethod(Zend_Http_Client::POST);
    $response= $lClient->request();
    $lRet = $response->getBody();
    $this -> doMsg('MOP response from host: '.$lMopLinkHost, mlInfo);
    $this -> doMsg('MOP Response: '.$lRet, mlInfo);
    return $lRet;
  }
  
}