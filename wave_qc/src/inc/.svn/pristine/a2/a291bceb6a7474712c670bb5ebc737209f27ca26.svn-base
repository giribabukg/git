<?php
class CInc_Api_Alink_Anyclient extends CApi_Alink_Client {
  
  public function __construct($aConfigKey) {
    $this -> mConfigKey = $aConfigKey;
    if (substr($this->mConfigKey,-1) != '.') {
      $this->mConfigKey.= '.';
    }
    $this -> checkConfig();
  
  }
  
  /**
   * Set the Values for the Alink Instance
   */
  private function setSingleConfig(){
    $lCfg = CCor_Cfg::getInstance();
    $this -> setConfig(
        $lCfg -> getVal($this->mConfigKey.'host'),
        $lCfg -> getVal($this->mConfigKey.'port'),
        $lCfg -> getVal($this->mConfigKey.'user'),
        $lCfg -> getVal($this->mConfigKey.'pass')
    );
  }
  
  /**
   * Fetch the Alinkconfig from Broker and set the Values for the Alink Instance
   */
  private function setMultiConfig(){
    $lCfg = CCor_Cfg::getInstance();
    $lUrl = "http://".$lCfg -> getVal($this->mConfigKey.'broker.host');
    $lUrl.= ':'.$lCfg -> getVal($this->mConfigKey.'broker.port')."/alinkinstance?json=1";
    if ($lResult = file_get_contents($lUrl)){
    		$lBroker = json_decode($lResult);
    		$this -> setConfig(
    		    $lBroker -> host,
    		    $lBroker -> port,
    		    $lCfg -> getVal($this->mConfigKey.'user'),
    		    $lCfg -> getVal($this->mConfigKey.'pass')
    		);
  
    } else $this -> setError('No result from Broker', 500, mlError);
  }
  
  /**
   * Check the 'alink.broker' flag and switch between Standard- and Broker-mode
   */
  protected function checkConfig(){
    $lCfg = CCor_Cfg::getInstance();
    if ($lCfg -> getVal($this->mConfigKey.'broker')){
    		$this -> setMultiConfig();
    } else {
    		$this -> setSingleConfig();
    }
  }

}