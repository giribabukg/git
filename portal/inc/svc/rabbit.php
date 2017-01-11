<?php
class CInc_Svc_Rabbit extends CSvc_Base {

  protected $mMessage;

  protected function doExecute() {
    $lClient = new CApi_Rabbit_Client();
    $lClient->loadFromConfig();
    $lQueueName = CCor_Cfg::get('rabbit.site', 'wave'.CCor_Cfg::get('wave.global.id'));
    $lCallback = array($this, 'onMessage');
    $this->addLog('Reading from '.$lQueueName);
    $lClient->readFromQ($lQueueName, $lCallback);
    return true;
  }

  /**
   * @param string $aMsg
   */
  public function onMessage($aMsg) {
    $lBody = $aMsg->body;
    $lArr = \Zend_Json::decode($lBody);
    $lCmd = $lArr['command'];
    $lParams = (isset($lArr['params'])) ? $lArr['params'] : null;
    $lFunc = 'onCmd'.strtr($lCmd, array('.' => ''));
    if ($this->hasMethod($lFunc)) {
      $this->addLog('Run Rabbit command '.$lCmd, mlInfo);
      $this->$lFunc($lParams);
    } else {
      $this->addLog('Error: Command '.$lCmd.' not found', mlError);
    }
  }

  /**
   * @param array $aParams
   */
  protected function onCmdValidateRulesUpdate($aParams) {
    CFie_Validate_Mod::updateAllGlobalRows($aParams);
  }

  /**
   * @param array $aParams
   */
  protected function onCmdFieldmapUpdate($aParams) {
    CFie_Map_Mod::importMap($aParams);
  }

}
