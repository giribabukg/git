<?php
class CInc_App_Event_Action_Xchange_Hotfolder extends CApp_Event_Action {

  public function execute() {
    $lDoc = $this->getContent();
    if (!$lDoc) {
      return false;
    }
    return $this->save($lDoc);
  }
  
  protected function save($aDoc) {
    try {
      $lFileName = $this->getFullFileName();
      $lRet = file_put_contents($lFileName, $aDoc);
      return $lRet;
    } catch (Exception $ex) {
      $this->msg($ex->getMessage(), mtApi, mlError);
      return false;
    }
    return true;
  }
  
  protected function getFullFileName() {
    $lFolder = $this->mParams['folder'];
    $lFileName = $this->getFileName();
    $lRet = $lFolder.DS.$lFileName;
    return $lRet;
  }
  
  protected function getFileName() {
    $lTpl = new CCor_Tpl();
    $lTpl->setDoc($this->mParams['filename']);
    $lJob = $this->mContext['job'];
    foreach ($lJob as $lKey => $lVal) {
      $lTpl->setPat('val.'.$lKey, $lVal);
    }
    $lTpl->setPat('datetime', date('Y-m-d H-i-s'));
    $lTpl->setPat('timestamp', time());
    return $lTpl->getContent();
  }
  
  protected function getContent() {
    $lClass = $this->mParams['generator'];
    if (!class_exists($lClass)) {
      $this->msg('Empty generator in event action xchange send', mtDebug, mlError);
      return false;
    }
    $lJob = $this->mContext['job'];
    
    $lGenerator = new $lClass($lJob, $this->mParams);
    return $lGenerator ->getContent();
  }

  public static function getParamDefs($aRow) {
    $lArr = array();
    $lArr[] = fie('generator', lan('xchange.xsend.generator'));
    $lArr[] = fie('params',    lan('xchange.xsend.param'));
    $lArr[] = fie('folder',    lan('lib.folder'));
    $lArr[] = fie('filename',  lan('lib.file.name'));
    
    $lEnc = explode(',', 'htmlentities,htmlspecialchars');
    $lArr[] = fie('encode',  'Encoding', 'valselect', array('lis' => $lEnc));
    
    $lTpl = CCor_Res::extract('id', 'name', 'tpl');
    $lTpl = array('' => deHtm(NB)) + $lTpl;
    $lFie = fie('tpl', 'Template', 'select', $lTpl);
    $lArr[] = $lFie;
    return $lArr;
  }

  public static function paramToString($aParams) {
    $lRet = array();
    if (!empty($aParams['folder'])) {
      $lRet[] = lan('lib.folder').': '.$aParams['folder'];
    }
    if (!empty($aParams['filename'])) {
      $lRet[] = lan('lib.file.name').': '.$aParams['filename'];
    }
    return implode(', ', $lRet);
  }
}