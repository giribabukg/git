<?php
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class CInc_Api_Rabbit_Client extends CCor_Obj {

  protected $mCfg;
  protected $mConnection;
  protected $mClient;
  /**
   * @var PhpAmqpLib\Channel\AMQPChannel
   */
  protected $mChannel;

  public function __construct() {
    $this->mCfg = array();
    $this->mIsAvailable = CCor_Cfg::get('rabbit.available', false);
  }

  public function loadFromConfig($aPrefix = 'rabbit.') {
    $lPrefix = $aPrefix;
    if (substr($lPrefix, -1) != '.') {
      $lPrefix.= '.';
    }
    $this->set('host', CCor_Cfg::get($lPrefix.'host', 'localhost'));
    $this->set('port', CCor_Cfg::get($lPrefix.'port', '5672'));
    $this->set('user', CCor_Cfg::get($lPrefix.'user', 'guest'));
    $this->set('pass', CCor_Cfg::get($lPrefix.'pass', 'guest'));
    $this->set('site', CCor_Cfg::get($lPrefix.'site', 'wave'.CCor_Cfg::get('wave.global.id')));
  }

  public function set($aKey, $aVal) {
    $this->mCfg[$aKey] = $aVal;
  }

  public function get($aKey, $aDefault = null) {
    return (isset($this->mCfg[$aKey])) ? $this->mCfg[$aKey] : $aDefault;
  }

  public function connect() {
    if (!$this->mIsAvailable) {
      return;
    }
    if (!isset($this->mClient)) {
      $this->mClient = new AMQPStreamConnection($this->get('host'), $this->get('port'), $this->get('user'), $this->get('pass'));
      $this->mChannel = $this->mClient->channel();
    }
  }

  public function sendTopic($aMessage, $aTopic, $aRoutingKey) {
    if (!$this->mIsAvailable) {
      return;
    }
    $this->connect();
    $lMsg = new AMQPMessage($aMessage);
    $this->mChannel->basic_publish($lMsg, $aTopic, $aRoutingKey);
  }

  public function sendToQueue($aQueue, $aMessage) {
  }

  public function setupTopic() {
    $this->connect();
    //$this->mChannel->exchange_declare('wave.masterdata.topic', 'topic', false, true, false);

    // queue name, passive, durable, exclusive, auto_delete, nowait, arguments
    $lSite = $this->get('site');
    $lRet = $this->mChannel->queue_declare($lSite, false, true, false, false);
    $this->mChannel->queue_bind($lSite, 'wave.masterdata.topic', '#');
  }

  public function readFromQ($aQueueName, $aCallback) {
    if (!$this->mIsAvailable) {
      return;
    }
    CSvc_Base::addLog('Read from '.$aQueueName);
    $this->connect();
    $lRet = $this->mChannel->queue_declare($aQueueName, false, true, false, false);
    $this->mChannel->queue_bind($aQueueName, 'wave.masterdata.topic', '#');
    $lNumMsg = $lRet[1];
    if ($lNumMsg > 0) {
      $this->mChannel->basic_consume($aQueueName, '', false, true, false, false, $aCallback);
      while (count($this->mChannel->callbacks)) {
        $this->mChannel->wait();
      }
    }
  }

}
