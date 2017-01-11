<?php
class CInc_App_Event_Action_Xchange_Mail extends CApp_Event_Action {

  public function execute() {
    $lDoc = $this->getContent();
    return $this->send($lDoc);
  }
  
  protected function send($aDoc) {
    try {
      $this -> mMail = new Zend_Mail('UTF-8');
      
      $lTpl = new CCor_Tpl();
      $lTpl->setDoc($this -> mParams['subject']);
      $lJob = $this->mContext['job'];
      foreach ($lJob as $lKey => $lVal) {
        $lTpl->setPat('val.'.$lKey, $lVal);
      }
      $this -> mMail -> setSubject($lTpl->getContent());
      $this -> mMail -> setFrom(CCor_Cfg::get('smtp.sendAs'));
      $this -> mMail -> setBodyText($aDoc);
      $this -> mMail -> addTo($this -> mParams['to']);
      
      $lHost = CCor_Cfg::get('smtp.host');
      $lUser = CCor_Cfg::get('smtp.user');
      $lPass = CCor_Cfg::get('smtp.pass');
      
      $lArr = array('auth' => 'login', 'username' => $lUser, 'password' => $lPass);
      $lSmtp = new Zend_Mail_Transport_Smtp($lHost, $lArr);
      
      $this -> mMail -> send($smtp);
    } catch (Exception $ex) {
      $this->msg($ex->getMessage(), mtApi, mlError);
      return false;
    }
    return true;
    
  }
  
  protected function getContent() {
    $lClass = $this->mParams['generator'];
    if (!class_exists($lClass)) {
      $this->msg('Empty generator in event action xchange send', mtDebug, mlError);
      return false;
    }
    
    $lJob = $this->mContext['job'];
    $lParam = $this->mParams['params'];
    
    $lGenerator = new $lClass($lJob, $lParam);

    return $lGenerator ->getContent();
  }

  public static function getParamDefs($aRow) {
    $lArr = array();
    $lArr[] = fie('generator', lan('xchange.xsend.generator'));
    $lArr[] = fie('params',    lan('xchange.xsend.param'));
    $lArr[] = fie('to',        lan('lib.mail.to'));
    $lArr[] = fie('subject',   lan('lib.sbj'));
    #$lArr[] = fie('attach',    lan('xchange.mail.attach.method'), 'tselect', array('dom' => 'xmam'));
    return $lArr;
  }

  public static function paramToString($aParams) {
    $lRet = array();
    if (!empty($aParams['subject'])) {
      $lRet[] = lan('lib.sbj').': '.$aParams['subject'];
    }
    if (!empty($aParams['to'])) {
      $lRet[] = lan('lib.mail.to').': '.$aParams['to'];
    }
    return implode(', ', $lRet);
  }
}