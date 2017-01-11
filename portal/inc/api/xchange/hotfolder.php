<?php
class CInc_Api_Xchange_Hotfolder extends CApi_Xchange_Base {

  const ACTION_OKAY   = 1;
  const ACTION_IGNORE = 2;
  const ACTION_ERROR  = 4;

  public function execute() {
    try {
      $lRet = $this->doImport();
    } catch (Exception $ex) {
      $this->msg($ex->getMessage(), mtApi, mlError);
      $lRet = false;
    }
    return $lRet;
  }
  
  protected function normalizeDir($aDir) {
    $lRet = $aDir;
    $lLastChar = substr($lRet, -1);
    if ('\\' != $lLastChar && '/' != $lLastChar) {
       $lRet.= DS;
    }
    return $lRet;
  }

  protected function doImport() {
    $lHome = $this->getParam('base');
    $lHome = $this->normalizeDir($lHome);
    $lDir = $lHome . $this->getParam('in');
    $this->mSrcDir = $lDir;

    if (empty($lDir)) {
      $this->logError('Hotfolder not defined');
      return false;
    }
    if (!file_exists($lDir)) {
      $this->logError('Specified hotfolder '.$lDir.' does not exist');
      return false;
    }
    $lIte = new DirectoryIterator($lDir);
    foreach ($lIte as $lFile) {
      if (!$lIte -> isFile()) continue;
      $lName = $lIte -> getPathname();
      if (!$this->isValidExtension($lName)) {
        $this->logDebug('Ignoring '.$lName);
        continue;
      }
      $this->logDebug('Handling '.$lName);
      $lRet = $this->handleFile($lName);
    }
    return true;
  }
  
  protected function isValidExtension($aFilename) {
    $lExt = $this->getParam('extensions');
    if (empty($lExt)) return true;
    if (!empty($lExt)) {
      $lExt = explode(';', $lExt);
    }
    $lFileExt = pathinfo($aFilename, PATHINFO_EXTENSION);
    return in_array($lFileExt, $lExt);
  }

  protected function handleFile($aFilename) {
    //$lXml = file_get_contents($aFilename);
    $lXml = $aFilename;
    $lRes = $this->parse($lXml);
    if (!$lRes) {
      $this->logError('XChange: Error parsing '.$aFilename);
      $this->moveFile($aFilename, 'error');
      return false;
    }
    $this->logDebug('XChange: Import of '.$aFilename.' successful');
    $this->moveFile($aFilename, 'parsed');
    return true;
  }

  protected function moveFile($aFilename, $aFolderKey) {
    $lHome = $this->getParam('base');
    $lHome = $this->normalizeDir($lHome);
    $lDir = $lHome . $this->getParam($aFolderKey);
    
    $lBase = basename($aFilename);
    $lNewName = $lDir.DS.$lBase;
    rename($aFilename, $lNewName);
    #copy($aFilename, $lNewName);
  }

  protected function getParser() {
  	$lParser = $this->getParam('parser');
  	if (!class_exists($lParser)) {
  	  $this->logError('Parser '.$lParser.' does not exist');
  	  return false;
  	}
  	$lParam = array('folder' => $this->mSrcDir);
    return new $lParser($lParam);
  }

  protected function parse($aXml) {
    $lRet = array();
    $lParser = $this->getParser();
    if (!$lParser) {
      return false;
    }
    return $lParser->parse($aXml);
  }
  
  protected function logMsg($aTyp, $aLvl, $aMsg = '', $aFile) {
  	$lDat = array();
  	$lDat['filename'] = $aFile;
  	$lDat['mand'] = MID;
  	$lDat['typ'] = $aTyp;
  	$lDat['lvl'] = $aLvl;
  	$lDat['msg'] = $aMsg;
  
  	$lSql = 'INSERT INTO al_xchange_log SET ';
  	foreach ($lDat as $lKey => $lVal) {
  		$lSql.= $lKey.'='.esc($lVal).',';
  	}
  	$lSql = strip($lSql).';';
  	return CCor_Qry::exec($lSql);
  }

}