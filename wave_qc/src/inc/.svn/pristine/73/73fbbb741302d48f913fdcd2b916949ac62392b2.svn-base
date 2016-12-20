<?php
class CInc_Api_Xchange_Xml_Signal extends CInc_Api_Xchange_Xml_Base {
  
  protected function doParse($aXml) {
    $this->mDom = simplexml_load_string($aXml);
    if (!$this->mDom) {
      return false;
    }
    
    $this->mUid = CCor_Usr::getAuthId();
    if ($this->hasPath($this->mDom, 'user.email')) {
      $lEmail = (string)$this->mDom->user->email;
      $lUsrArr = CCor_Res::extract('email', 'id', 'usr');
      if (isset($lUsrArr[$lEmail])) {
        $this->mUid = $lUsrArr[$lEmail];
      }
    }
    
    if (!$this->hasPath($this->mDom, 'job.src')) {
      return false;
    }
    $lSrc = (string)$this->mDom->job->src;
    $lJid = (string)$this->mDom->job->jobid;
    
    if ($this->hasPath($this->mDom, 'signals')) {
      $lUpd = array();
      
      $lSig = $this->mDom->signals;
      if ($this->hasPath($lSig, 'add')) {
        $lAdd = $lSig->add;
        foreach ($lAdd as $lField) {
          $lUpd['sig_'.$lField] = 1;
        }
      }
      if ($this->hasPath($lSig, 'consume')) {
        $lRem = $lSig->consume;
        foreach ($lRem as $lField) {
          $lUpd['sig_'.$lField] = '';
        }
      }
      if (!empty($lUpd)) {
        $this->updateJob($lSrc, $lJid, $lUpd);
      }      
    }
    if ($this->hasPath($this->mDom, 'upload')) {
      $lBase = $this->getParam('folder');
      $lUpload = $this->mDom->upload;
      if (isset($lUpload->doc)) {
        $lArr = array();
        $lUpl = $lUpload->doc;
        foreach ($lUpl as $lName) {
          $lName = (string)$lName;
          $lFull = $lBase.$lName;
          if (file_exists($lFull)) {
            $lArr['doc'][$lFull] = $lFull;
          }
        }
      }
      if (isset($lUpload->pdf)) {
        $lArr = array();
        $lUpl = $lUpload->pdf;
        foreach ($lUpl as $lName) {
          $lName = (string)$lName;
          $lFull = $lBase.$lName;
          if (file_exists($lFull)) {
            $lArr['pdf'][$lFull] = $lFull;
          }
        }
      }
      if (!empty($lArr)) {
        $this->uploadFiles($lSrc, $lJid, $lArr);
      }
    }
    return true;
  }
  
  protected function uploadFiles($aSrc, $aJid, $aFiles) {
    $lUpload = new CJob_Fil_Upload($aSrc, $aJid);
    foreach ($aFiles as $lFileType => $lArr) {
      $lParam = array('dest' => $lFileType);
      foreach ($lArr as $lSrcFilename) {
        $this->logDebug('Uploading '.$lSrcFilename.' to '.$aJid);
        $lBaseName = pathinfo($lSrcFilename, PATHINFO_BASENAME);
        $lUpload->uploadFile($lSrcFilename, $lBaseName, $lParam);      
      }
    }
  }
  
  protected function updateJob($aSrc, $aJid, $aUpd) {
    $lFac = new CJob_Fac($aSrc);
    $lMod = $lFac->getMod($aJid);
    $lMod->forceUpdate($aUpd);
    $lBeat = new CJob_Workflow_Heartbeat($aSrc, $aJid);
    $lBeat->setUid($this->mUid);
    $lBeat->heartBeat();
  }
  
  protected function hasPath($aRes, $aPath) {
    $lPath = (is_array($aPath)) ? $aPath : explode('.', $aPath);
    if (!$aRes) return false;
    $lRoot = $aRes;
    foreach ($lPath as $lKey) {
      if (!$lRoot->$lKey)  {
        $this->logError('Result does not have '.$lKey, mlError);
        return false;
      }
      $lRoot = $lRoot->$lKey;
    }
    return true;
  }

}