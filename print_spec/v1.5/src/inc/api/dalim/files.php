<?php
class CInc_Api_Dalim_Files extends CCor_Obj {

  public function __construct($aSrc, $aJobId) {
    $this -> mSrc = $aSrc;
    $this -> mJid = $aJobId;
    $this -> mVersionDelimiter = '_';
    $this -> mVersionCount = 0;
  }

  public function getDefaultFilename($aName = null) {
    if (!is_null($aName)) return $aName;
    return intval($this->mJid).'.pdf';
  }

  public function getFilenames() {
    if (is_null($this->mFiles)) {
      $this -> readFiles();
    }
    return array_keys($this->mFiles);
  }

  public function getVersions($aBasename) {
    if (is_null($this->mFiles)) {
      $this -> readFiles();
    }
    return isset($this->mFiles[$aBasename]) ? $this->mFiles[$aBasename] : array();
  }

  public function getFileVersionCount() {
    return $this -> mVersionCount;
  }

  public function getMaxVersion($aBasename) {
    $lRow = $this->getMaxVersionRow($aBasename);
    if (!$lRow) return 0;
    return $lRow['version'];
  }

  public function getMaxVersionRow($aBasename) {
    $lBase = $this->getDefaultFilename($aBasename);
    $lRet = false;
    $lArr = $this->getVersions($lBase);
    if (empty($lArr)) return;
    foreach ($lArr as $lRow) {
      $lVersion = $lRow['version'];
      if ($lVersion > $lMax) {
        $lMax = $lVersion;
        $lRet =  $lRow;
      }
    }
    return $lRet;
  }


  protected function readFiles() {
    $lCls = new CApp_Finder($this -> mSrc, $this -> mJid);
    $lDir = $lCls -> getPath('dalim');

    try {
      $lIte = new DirectoryIterator($lDir);
      foreach ($lIte as $lLin) {
        $lItm = array();
        if ($lIte -> isFile()) {
          $lNam = $lIte -> getFilename();
          $lDisplay = $lNam;
          $lVersion = 1;
          $lPos = strrpos($lNam, $this -> mVersionDelimiter);
          if (false !== $lPos) {
            $lDisplay = substr($lNam,0,$lPos).'.'.$lIte->getExtension();
            $lVersion = intval(substr($lNam,$lPos+1));
          }
          $lItm['name']  = $lNam;
          $lItm['display']  = $lDisplay;
          $lItm['size']  = $lIte -> getSize();
          $lItm['date']  = $lIte -> getMTime();
          $lItm['uid']   = 0;
          $lItm['user']  = '';
          $lItm['category']  = '';
          $lItm['version']  = $lVersion;
          $lFiles[$lDisplay][$lVersion] = $lItm;
          $this -> mVersionCount++;
        }
      }
      foreach ($lFiles as $lBaseName => $lRows) {
        krsort($lRows); // reverse version order
        $this->mFiles[$lBaseName] = $lRows;
      }
      ksort($this->mFiles); // ordered by filename
      #$this->dump($this->mFiles, 'FILES');

    } catch (Exception $lExc) {
      $this -> dbg($lExc -> getMessage(), mlWarn);
    }
  }

  public function lockFile($aFilename) {
    $lSql = 'UPDATE al_job_files SET lock_delete = "Y" ';
    $lSql.= 'WHERE src='.esc($this->mSrc).' AND jobid='.esc($this->mJid).' ';
    $lSql.= 'AND filename='.esc($aFilename).' AND sub="dalim"';
    CCor_Qry::exec($lSql);
  }

  public function lockAllFiles() {
      $lSql = 'UPDATE al_job_files SET lock_delete = "Y" ';
      $lSql.= 'WHERE src='.esc($this->mSrc).' AND jobid='.esc($this->mJid).' ';
      $lSql.= 'AND sub="dalim"';
      CCor_Qry::exec($lSql);
  }

  public function unlockAllFiles() {
    $lSql = 'UPDATE al_job_files SET lock_delete = "N" ';
    $lSql.= 'WHERE src='.esc($this->mSrc).' AND jobid='.esc($this->mJid).' ';
    $lSql.= 'AND sub="dalim"';
    CCor_Qry::exec($lSql);
  }

  public function lockLatestVersion($aBasename) {
    $lBase = $this->getDefaultFilename($aBasename);
    $lRow = $this->getMaxVersionRow($lBase);
    if (!$lRow) return;
    $this->lockFile($lRow['name']);
  }

  public function delete($aFilename) {
    $lBase = $this->getDefaultFilename($aFilename);
    $lCls = new CApp_Finder($this -> mSrc, $this -> mJid);
    $lDir = $lCls -> getPath('dalim');
    $lName = $lDir.$lBase;
    echo $lName.BR;
    @unlink($lName);

    $lSql = 'DELETE FROM al_job_files ';
    $lSql.= 'WHERE src='.esc($this->mSrc).' AND jobid='.esc($this->mJid).' ';
    $lSql.= 'AND filename='.esc($lBase).' AND sub="pdf"';
    CCor_Qry::exec($lSql);

    $this->unregister($aFilename);
  }

  public function unregister($aFilename) {
    $lBase = $this->getDefaultFilename($aFilename);
    $lJnr = intval($this->mJid);
    $lRelativeName = $lJnr.DS.$lBase;
    $lUtils = new CApi_Dalim_Utils();
    $lUtils->unregisterDocument($lRelativeName);
  }

  public function register($aFilename) {
    $lBase = $this->getDefaultFilename($aFilename);
    $lJnr = intval($this->mJid);
    $lRelativeName = $lJnr.DS.$lBase;
    $lUtils = new CApi_Dalim_Utils();
    $lUtils->registerDocument($lRelativeName);
  }

  public function removeAllFiles() {
    $this->readFiles();
    foreach ($this->mFiles as $lFiles) {
      foreach ($lFiles as $lRow) {
        $this->delete($lRow['name']);
      }
    }
  }

}