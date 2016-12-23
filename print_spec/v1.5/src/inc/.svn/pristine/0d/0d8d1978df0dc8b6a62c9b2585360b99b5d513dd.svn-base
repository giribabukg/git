<?php
class CInc_Job_Apl2_Dmsfiles extends CCor_Obj {
  
  protected static $mInstances;
  
  protected function __construct($aSrc, $aJid) {
    $this->mSrc = $aSrc;
    $this->mJid = $aJid;
    
    $this->loadFiles();
  }
  
  public static function getInstance($aSrc, $aJid) {
    $lKey = $aSrc.'_'.$aJid;
    if (!isset(self::$mInstances[$lKey])) {
      self::$mInstances[$lKey] = new self($aSrc, $aJid);
    }
    return self::$mInstances[$lKey];
  }
    
  protected function loadFiles() {
    $lDms = new CApi_Dms_Query();
    $lList = $lDms->getFileList(MANDATOR_ENVIRONMENT, $this->mSrc, $this->mJid, 0);
    $this->mFiles = $lList;
  }
  
  public function getFiles() {
    return $this->mFiles;
  }
  
  public function getLatest($aJobFileId = 1) {
    foreach ($this->mFiles as $lRow) {
      if ($lRow['jobfileid'] == $aJobFileId) {
        return $lRow;
      }
    }
    
  }

}
