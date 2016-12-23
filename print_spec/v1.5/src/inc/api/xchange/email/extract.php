<?php
class CInc_Api_Xchange_Email_Extract extends CApi_Xchange_Email_Base {
  
  protected function doHandle($aMail) {
    $lFolder = $this->getParam('folder', 'tmp/extract/');
    
    $lExt = $this->getParam('extensions');
    if (!empty($lExt)) {
      $lExt = explode(';', $lExt);
    }
    
    $lDest = $this->getParam('rename', false);
    if ($lDest) {
      $lTpl = new CCor_Tpl();
      $lTpl->setDoc($lDest);
      $lTpl->setPat('date', date('Y-m-d'));
      $lFpa = $lTpl->findPatterns();
      
      $lCounter = new CApp_Counter();
      $lCounter->setMid(0);
      if (in_array('mail', $lFpa)) {
        $lCountMail = $lCounter->getNextNumber('xchange.mail');
      }
      if (in_array('counter', $lFpa)) {
        $lCount = true;
      }
    }
    
    $lAtt = $aMail->getAttachments();
    foreach ($lAtt as $lRow) {
      $lName = $lRow['filename'];
      if (!empty($lExt)) {
        if (!in_array($lRow['ext'], $lExt)) {
          $this->logDebug('Skipping extension '.$lRow['ext']);
          continue;
        }
      }
      if ($lDest) {
        $lBase = pathinfo($lName, PATHINFO_FILENAME);
        $lTpl->setPat('time', date('H-i-s'));
        $lTpl->setPat('uniqid', uniqid(' ', true));
        $lTpl->setPat('name', $lBase);
        $lTpl->setPat('ext', $lRow['ext']);
        if ($lCountMail) {
          $lTpl->setPat('mail', $lCountMail);
        }
        if ($lCount) {
          $lTpl->setPat('counter', $lCounter->getNextNumber('xchange.attachment'));
        }
        $lName = $lTpl->getContent();
      }
      $lFn = $lFolder.$lName;
      $this->logDebug($lFn);
      file_put_contents($lFn, $lRow['body']);
      $this->logDebug('Extracting '.$lName);
    }
    return true;
  }

}