<?php
/**
 * Api: Alink - Client
 *
 * SINGLETON
 *
 * @package    API
 * @subpackage Alink
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 11856 $
 * @date $Date: 2015-12-21 23:28:26 +0100 (Mon, 21 Dec 2015) $
 * @author $Author: gemmans $
 */
class CInc_Api_Alink_Client extends CApi_Tcp {
  
  private static $mInstance;
 
  /**
   * The Alink Method
   * @var string
   */
  public $mMethod = '';
  
  private function __construct() {
    $lCfg = CCor_Cfg::getInstance();
    $this -> checkConfig();

  }

  /**
   * Set the Values for the Alink Instance
   */
  private function setSingleConfig(){
  	$lCfg = CCor_Cfg::getInstance();
  	$this -> setConfig(
  			$lCfg -> getVal('alink.host'),
  			$lCfg -> getVal('alink.port'),
  			$lCfg -> getVal('alink.user'),
  			$lCfg -> getVal('alink.pass')
  	);
  }

  /**
   * Fetch the Alinkconfig from Broker and set the Values for the Alink Instance
   */
  private function setMultiConfig(){
  	$lCfg = CCor_Cfg::getInstance();
  	$lUrl = "http://".$lCfg -> getVal('alink.broker.host').":".$lCfg -> getVal('alink.broker.port')."/alinkinstance?json=1";
  	if ($lResult = file_get_contents($lUrl)){
  		$lBroker = json_decode($lResult);
  		$this -> setConfig(
  				$lBroker -> host,
  				$lBroker -> port,
  				$lCfg -> getVal('alink.user'),
  				$lCfg -> getVal('alink.pass')
  		);

  	} else $this -> setError('No result from Broker', 500, mlError);
  }

  /**
   * Check the 'alink.broker' flag and switch between Standard- and Broker-mode
   */
  protected function checkConfig(){
  	$lCfg = CCor_Cfg::getInstance();
  	if ($lCfg -> getVal('alink.broker')){
  		$this -> setMultiConfig();
  	} else {
  		$this -> setSingleConfig();
  	}
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
    // No message by Method 'getFile'.
    if ($this -> mMethod != 'getFile'){
      $this -> doMsg('RECV : '.$lRet, mlInfo);
    }
    
    return $lRet;
  }
  
  public function query($aXml, $aMethod = '') {
    $this -> mMethod = $aMethod;
    $this -> connect();
    $this -> send('Content-Length: '.strlen($aXml).$this -> mEol.$this -> mEol);
    $this -> send($aXml);

    $lLogIt = CCor_Cfg::get('msg.log.FileNotFound', TRUE);
    $lRet = $this -> readAll();
    if (!$lLogIt AND !(strpos($lRet, '<errno>602</errno>') === false)) {
      // Do Nothing
    } else {
    $this -> doMsg('Alink Query: '.$aXml, mlInfo);
    $this -> doMsg('Alink Response: '.$lRet, mlInfo);
    }
    $this -> disconnect();
    return $lRet;
  }
  
}