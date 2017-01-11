<?php
/**
 * Api: Mail - Smtp
 *
 * SINGLETON
 *
 * @package    API
 * @subpackage Mail
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 14 $
 * @date $Date: 2012-02-21 12:54:32 +0100 (Tue, 21 Feb 2012) $
 * @author $Author: gemmans $
 */

define('SMTP_DISABLED', 'Smtp is disabled');
define('CONNECTION_ERROR', 'Connection error');
define('AUTH_FAILED',      'Auth failed');
define('SUCCESSFUL_SEND',  'Successful send');
define('DATA_ERROR',       'Data error');
define('BODY_ERROR',       'Body error');
define('SENDER_ERROR',     'Sender address error');
define('RECIPIENT_ERROR',  'Recipient address error');

class CApi_Mail_Smtp extends CApi_Tcp {

  private static $mInstance = NULL;

  private $mIsFailed = FALSE; // will not retry
  private $mIsAuth   = FALSE; // already authenticated?
  private $mState = mlNone;   // status
  private $mDisabled = FALSE; // disabled sendmail

  public $mTestMode;
  public $mTestMail;

  private function __clone() {}

  /**
   * Enter description here...
   *
   * @return CApi_Mail_Smtp
   */
  public static function getInstance(){
    if (NULL === self::$mInstance) {
      self::$mInstance = new self();
    }
    $lRet = self::$mInstance;
    $lCfg = CCor_Cfg::getInstance();

    $lRet -> setConfig($lCfg -> getVal('smtp.host'),
                       $lCfg -> getVal('smtp.port'),
                       $lCfg -> getVal('smtp.user'),
                       $lCfg -> getVal('smtp.pass'),
                       $lCfg -> getVal('smtp.authhost'));
    $lRet -> mTestMode = $lCfg -> getVal('smtp.test');
    $lRet -> mTestMail = $lCfg -> getVal('smtp.admin');
    if ($lCfg -> getVal('smtp.disabled')) {
      $lRet -> mDisabled = True;
      $ldisabled = 'NO email delivery';
    } else {
      $lRet -> mDisabled = False;
      $ldisabled = 'send emails';
    }
    $lInformation = '>';
    $lInformation.= $lCfg -> getVal('smtp.host');
    $lInformation.= '|'.$lCfg -> getVal('smtp.port');
    $lInformation.= '|'.$lCfg -> getVal('smtp.user');
    $lInformation.= '|'.$lCfg -> getVal('smtp.pass');
    $lInformation.= '|'.($lCfg -> getVal('smtp.test')? 'EmailToSpecialAdress: '.$lCfg -> getVal('smtp.admin') : 'To eMail receiver');
    $lInformation.= '|'.$ldisabled;
    $lInformation.= '<';
    CCor_Msg::add($lInformation, mtApi, mlInfo);
    return $lRet;
  }

  public function __destruct() {
    if ($this -> isConnected()) {
      $this -> disconnect();
    }
  }

  protected function doConnect() {
    $this -> mIsAuth = FALSE;
    if (parent::doConnect()) {
      if ($this -> codeError(220)) {
        $this -> setState(CONNECTION_ERROR);
        $this -> mIsFailed = TRUE;
        $this -> disconnect();
        $this -> dbg('CONNECTION ERROR');
        return FALSE;
      }
      $this -> mIsFailed = FALSE;
      return TRUE;
    } else {
      $this -> setState(CONNECTION_ERROR);
      $this -> mIsFailed = TRUE;
      $this -> disconnect();
      return FALSE;
    }
  }

  protected function codeError($aCode) {
    $lRet = trim($this -> readLines());
    $lCod = intval($lRet);
    if ($lCod != intval($aCode)) {
      $this -> setError($lRet.', '.$aCode.' expected', 100);
      return TRUE;
    }
    return FALSE;
  }

  protected function authenticate() {
    if ($this -> mIsAuth) {
      return TRUE;
    }
    if (empty($this -> mUser)) {
      return $this -> sendHelo();
    } else {
      return $this -> sendEhlo();
    }
  }

  protected function sendHelo() {
    $this -> sendLine('HELO '.$this -> mAuthHost);
    if ($this -> getErrNo() != 0) {
      return FALSE;
    };
    if ($this -> codeError(250)) {
      $this -> setState(AUTH_FAILED);
      $this -> setError($this -> mStateText, 401);
      return FALSE;
    } else {
      return TRUE;
    };
  }

  protected function sendEhlo() {
    $this -> sendLine('EHLO '.$this -> mAuthHost);
    if ($this -> getErrNo() != 0) {
      return FALSE;
    };
    if ($this -> codeError(250)) {
      $this -> setState(AUTH_FAILED);
      $this -> setError($this -> mStateText, 401);
      return FALSE;
    }
    $this -> sendLine('AUTH LOGIN');
    if ($this -> getErrNo() != 0) {
      return FALSE;
    };
    if ($this -> codeError(334)) {
      $this -> setState(AUTH_FAILED);
      $this -> setError($this -> mStateText, 401);
      return FALSE;
    }
    $this -> sendLine(base64_encode($this -> mUser));
    if ($this -> getErrNo() != 0) {
      return FALSE;
    };
    if ($this -> codeError(334)) {
      $this -> setState(AUTH_FAILED);
      $this -> setError($this -> mStateText, 401);
      return FALSE;
    }
    $this -> sendLine(base64_encode($this -> mPass));
    if ($this -> getErrNo() != 0) {
      return FALSE;
    };
    if ($this -> codeError(235)) {
      $this -> setState(AUTH_FAILED);
      $this -> setError($this -> mStateText, 401);
      return FALSE;
    }
    return TRUE;
  }

  public function sendMail($aFrom, $aTo, $aHeader, $aBody) {
    if ($this -> mDisabled) {
      $this -> setState(SMTP_DISABLED);
      $this -> setError($this -> mStateText, $this -> mState);
      return;
    }
    if ($this -> mIsFailed) {
      return FALSE;
    }
    if (!$this -> isConnected()) {
      if (!$this -> connect()) {
        return FALSE;
      }
    }
    if (!$this -> mIsAuth) {
      $this -> mIsAuth = $this -> authenticate();
    }
    if (!$this -> mIsAuth) {
      return FALSE;
    }
    if ($this -> mTestMode) {
      $aTo = $this -> mTestMail;
    }

    // Sender address
    $this -> sendLine('MAIL FROM: <'.$aFrom.'>');
    if ($this -> getErrNo() != 0) {
      return FALSE;
    };
    if ($this -> codeError(250)) {
      $this -> setState(SENDER_ERROR);
      return FALSE;
    }

    // Recipient address
    $this -> sendLine('RCPT TO: <'.$aTo.'>');
    if ($this -> getErrNo() != 0) {
      return FALSE;
    };
    if ($this -> codeError(250)) {
      $this -> setState(RECIPIENT_ERROR);
      return FALSE;
    }

    // Data senden
    $this -> sendLine('DATA');
    if ($this -> getErrNo() != 0) {
      return FALSE;
    };
    if ($this -> codeError(354)) {
      $this -> setState(DATA_ERROR);
      return FALSE;
    }

    $lEod = $this -> mEol.'.'.$this -> mEol;
    $lRep = $this -> mEol.' .'.$this -> mEol;
    $lBod = trim($aBody);
    $lBod = strtr($lBod, array($lEod => $lRep));
    $this -> send($aHeader);
    $this -> send($lBod.$lEod);

    if ($this -> codeError(250)) {
      $this -> setState(BODY_ERROR);
      return FALSE;
    }
    $this -> setState(SUCCESSFUL_SEND);
    $this -> doMsg('Send from '.$aFrom.' to '.$aTo.' okay', mlInfo);
    return TRUE;
  }

  protected function doDisconnect() {
    $this -> sendLine('QUIT');
    if ($this -> getErrNo() != 0) {
      $this -> codeError(221);
    };
    parent::doDisconnect();
  }

  /**
   * Set the state number of smtp object
   * mlNone   Idle
   * mlInfo   Sent
   * mlError  TCP Error
   * mlFatal  Data Error
   *
   * @access private
   * @return int Status Number of smtp object
   */
  protected function setState($aStatetext) {
    $this -> mStateText = $aStatetext;
    if ($aStatetext === SUCCESSFUL_SEND) {
      $this -> mState = mlInfo;
    } else if ($aStatetext === SMTP_DISABLED) {
      $this -> mState = mlWarn;
    } else if ($aStatetext === CONNECTION_ERROR) {
      $this -> mState = mlError;
    } else if ($aStatetext === AUTH_FAILED) {
      $this -> mState = mlError;
    } else if ($aStatetext === DATA_ERROR) {
      $this -> mState = mlFatal;
    } else if ($aStatetext === BODY_ERROR) {
      $this -> mState = mlFatal;
    } else if ($aStatetext === SENDER_ERROR) {
      $this -> mState = mlFatal;
    } else if ($aStatetext === RECIPIENT_ERROR) {
      $this -> mState = mlFatal;
    } else {
      $this -> mState = mlNone;
    }
    $this -> dbg('SMTP-State: ' . $aStatetext);
    return $this -> mState;
  }

  /**
   * Get the state number of smtp object
   * mlNone   Idle
   * mlInfo   Sent
   * mlError  TCP Error
   * mlFatal  Data Error
   *
   * @access public
   * @return int Status Number of smtp object
   */
  public function getState() {
    return $this -> mState;
  }

}