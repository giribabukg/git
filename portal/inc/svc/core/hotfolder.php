<?php
class CInc_Svc_Core_Hotfolder extends CSvc_Base {

  protected function doExecute() {
    $this->mMoveFiles = true;
    //$this->mMoveFiles = false;
    $this->mDoInsert = true;
    //$this->mDoInsert = false;
    $this->mRetention = intval($this->getPar('retention', 0));

    try {
      $lRet = $this->doImport();
    } catch (Exception $ex) {
      $this->msg($ex->getMessage(), mtApi, mlError);
      $lRet = false;
    }
    return $lRet;
  }

  protected function logMsg($aMsg, $aLevel = mlInfo) {
    $this->msg($aMsg, mtApi, $aLevel);
    $this->addLog($aMsg);
  }

  protected function normalizeDir($aDir) {
    $lRet = $aDir;
    $lLastChar = substr($lRet, -1);
    if ('\\' != $lLastChar && '/' != $lLastChar) {
      $lRet.= DS;
    }
    return $lRet;
  }

  protected function getDir($aType) {
    $lRet = $this->getPar('dir.'.$aType, $this->mBaseDir.$aType);
    $lRet = $this->normalizeDir($lRet);
    return $lRet;
  }

  protected function doImport() {
    $lBase = $this->getPar('base', CCor_Cfg::get('file.dir').DS.'corehotfolder');
    $this->mBaseDir   = $this->normalizeDir($lBase);
    $this->folderIn   = $this->getDir('in');
    $this->folderOk   = $this->getDir('ok');
    $this->folderEr   = $this->getDir('error');
    $this->folderSkip = $this->getDir('skip');

    if (empty($this->folderIn) || !file_exists($this->folderIn)) {
      $this->logMsg('Hotfolder ('.$this->folderIn.') does not exist', mlError);
      return false;
    }

    $lFsi = new FilesystemIterator($this->folderIn, FilesystemIterator::SKIP_DOTS);
    $lSuffix = ' of '.iterator_count($lFsi);

    $lIte = new DirectoryIterator($this->folderIn);
    $i = 0;
    foreach ($lIte as $lFile) {
      if (!$lIte -> isFile()) continue;
      $this -> progressTick($lFile->getFilename().' ('.$i.$lSuffix.')');
      $lExt = $lIte->getExtension();
      $lValid = $lExt == 'xml';
      if ($lValid) {
        $this->handleFile($lIte);
      } else {
        $this->handleErrorFile($lIte, 'wrong extension');
      }
      $i++;
    }
    return true;
  }

  protected function moveFileTo($aSrcFile, $aDest) {
    if (!$this->mMoveFiles) {
      $this->logMsg('Would move '.$aSrcFile->getPathName().' to '.$aDest);
      return; // used for local debugging to leave files where they are
    }
    if (empty($aDest) || ('/dev/null' == $aDest)) {
      @unlink($aSrcFile->getPathName());
    } else {
      @rename($aSrcFile->getPathName(), $aDest.$aSrcFile->getFileName());
    }
  }


  protected function handleErrorFile($aFile, $aMsg = '') {
    $this->moveFileTo($aFile, $this->folderEr);
    $this->logMsg('CORE: Error in File '.$aFile->getFileName().', '.$aMsg, mlError);
  }

  protected function handleSkipFile($aFile, $aMsg = '') {
    $this->moveFileTo($aFile, $this->folderSkip);
    $this->logMsg('CORE: Info '.$aFile->getFileName().', '.$aMsg, mlWarn);
  }

  protected function handleFile($aFile) {
    $lFilename = $aFile->getPathname();
    $lXml = file_get_contents($lFilename);

    $this->mDoc = new DOMDocument;
    $this->mDoc->preserveWhiteSpace = false;
    $lRet = $this->mDoc->loadXML($lXml);
    if (!$lRet) {
      $this->handleErrorFile($aFile, 'Invalid XML');
      return false;
    }
    $this->mXpath = new DOMXpath($this->mDoc);
    $lMyCfg = CCor_Cfg::get('core.accept.system', 'ND1CLNT310');
    $lMySystems = explode(',', $lMyCfg);
    $lSystem = $this->getNodeValue('/Root/SalesOrder/SalesOrderItem/@LOG_SYSTEM_OWN');
    if (!in_array($lSystem, $lMySystems)) {
      $this->handleSkipFile($aFile, 'Jobs from '.$lSystem.' not accepted (config core.accept.system is '.$lMyCfg.')');
      return false;
    }

    $lMid = $this->getMid();
    if (!$lMid) {
      $this->handleErrorFile($aFile);
      return false;
    }

    $lDat = array();
    $lDat['mand'] = $lMid;
    $lDat['xml'] = $lXml;
    // need to adress directly, we do not have a mand yet
    $lDat['sales_order_id']   = $this->getNodeValue('/Root/SalesOrder/SalesOrderHead/@DOC_NUMBER');
    $lDat['service_order_id'] = $this->getNodeValue('/Root/ServiceOrder/ServiceOrderHead/@ORDERID');
    $lDat['jobid']            = $this->getNodeValue('/Root/SalesOrder/SalesOrderItem[1]/@REF_1');
    $lDat['core_system']      = $this->getNodeValue('/Root/SalesOrder/SalesOrderItem/@LOG_SYSTEM_OWN');

    $lSql = 'INSERT INTO al_core_xml SET ';
    foreach ($lDat as $lKey => $lVal) {
      $lSql.= '`'.$lKey.'`='.esc($lVal).',';
    }
    $lSql.= 'import_time=NOW()';

    if ($this->mDoInsert) {
      CCor_Qry::exec($lSql);
      $this->logMsg($lSql);
      $this->deleteOldEntries($lDat);
    } else {
      $this->logMsg($lSql);
    }
    $this->logMsg('CORE: '.$aFile->getFileName().' successfully imported', mlInfo);
    // move file to ok
    $this->moveFileTo($aFile, $this->folderOk);
  }

  protected function deleteOldEntries($aDat) {
    if (empty($this->mRetention)) {
      return;
    }
    // MySQL does not allow DELETE ... LIMIT 3,10. Only LIMIT 10, so we're using a nested subquery to fool mysql
    // see http://stackoverflow.com/questions/7142097/mysql-delete-statement-with-limit
    $lSql = 'DELETE FROM al_core_xml WHERE id IN (';
    $lSql.= 'SELECT id FROM (';
    $lSql.= 'SELECT id FROM al_core_xml x WHERE ';
    $lSql.= '`core_system`='.esc($aDat['core_system']);
    $lSql.= ' AND `mand`='.esc($aDat['mand']);
    $lSql.= ' AND `service_order_id`='.esc($aDat['service_order_id']);
    $lSql.= ' AND `action`="update"';
    $lSql.= ' ORDER BY id DESC';
    $lSql.= ' LIMIT '.$this->mRetention.',10)y)'; // 10 should be enough. Y is the required alias for the outer subquery
    CCor_Qry::exec($lSql);
    $this->logMsg($lSql);
  }

  protected function getNodeValue($aXpathQuery) {
    $lRet = null;
    $lNodes = $this->mXpath->query($aXpathQuery);
    if ($lNodes->length == 1) {
      $lRet = $lNodes->item(0)->value;
    }
    return $lRet;
  }

  protected function getMid() {
    $lSoldTo = $this->getNodeValue('/Root/SalesOrder/SalesOrderHead/@SOLD_TO');
    // $lPartnerAg = $this->getNodeValue('/Root/ServiceOrder/Partner/AG[1]/@PARTNER_KEY');
    $lRet = $this->getPar('default.mid', false);
    $lMap = CCor_Cfg::get('core.mid');
    if (isset($lMap[$lSoldTo])) {
      $lRet = $lMap[$lSoldTo];
    }
    // subject to cust specific overrides
    return $lRet;
  }

}
