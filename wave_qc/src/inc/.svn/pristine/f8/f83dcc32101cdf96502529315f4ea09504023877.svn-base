<?php
class CInc_App_Finder extends CCor_Obj {

  public function __construct($aSrc, $aJobId = 0, $aJob = NULL, $aMid = MID) {
    $this -> mSrc = $aSrc;
    $this -> mJobId = $aJobId;
    $this -> mMid = $aMid;
    if (NULL == $aJob) {
      $this -> load();
    } else {
      $this -> mJob = & $aJob;
    }
  }

  public function setMid($aMid) {
    $this -> mMid = intval($aMid);
  }

  public function prefixToType($aPrefix) {
    $lRet = 'File';
    return $lRet;
  }

  protected function load() {
    #$this -> dbg('Load not implemented yet');
  }

  public function getBasePath() {
    if($this->mMid > 0) {
      $lFileDir = CCor_Cfg::get('file.dir');
      $lPath = $lFileDir.'mand_'.$this -> mMid.DS;
    } else {
      $lPath = CUST_PATH;
    }
    $lRet = $lPath.'job'.DS;  //z.B. files/mand/mand_3/job/rep/000303628/doc/green.jpg
    return $lRet;
  }

  /**
   * Get the absolute path of a given Source
   * @param string Subpath (e.g. doc or pdf)
   * @return string Absolute path
   */
  public function getPath($aSub = NULL) {
    $lFnc = 'getPath'.ucfirst($this -> mSrc);
    if ($this -> hasMethod($lFnc)) {
      return $this -> $lFnc($aSub);
    }
    $lFnc = 'getSub'.ucfirst($aSub);
    if ($this -> hasMethod($lFnc)) {
      return $this -> $lFnc($aSub);
    }
    $lRet = $this -> getBasePath().$this -> mSrc.DS.$this -> mJobId.DS;
    if (!empty($aSub)) {
      $lRet.= $aSub.DS;
    }
    return $lRet;
  }

  //Aufruf über getPath & $this -> hasMethod
  protected function getPathApp($aSub = NULL) {
    if($this -> mMid > 0) {
      $lFileDir = CCor_Cfg::get('file.dir');
      $lPath = $lFileDir.'mand_'.$this -> mMid.DS;
    } else {
      $lPath = CUST_PATH;
    }
    $lRet = $lPath.'app'.DS;
    return $lRet;
  }
  
  protected function getPathMass($aSub = NULL) {
    if($this -> mMid > 0) {
      $lFileDir = CCor_Cfg::get('file.dir');
      $lPath = $lFileDir.'mand_'.$this -> mMid.DS;
    } else {
      $lPath = CUST_PATH;
    }
    $lRet = $lPath.'files'.DS;
    return $lRet;
  }
  
  protected function getPathUploaded($aSub = NULL) {
    if($this -> mMid > 0) {
      $lFileDir = CCor_Cfg::get('file.dir');
      $lPath = $lFileDir.'mand_'.$this -> mMid.DS;
    } else {
      $lPath = CUST_PATH;
    }
    $lRet = $lPath.'files'.DS;
    return $lRet;
  }

  protected function getSubDalim($aSub = NULL) {
    $lFileDir = CCor_Cfg::get('dalim.basedir');
    return $lFileDir.DS.$this->mJobId.DS;
  }

  public function getName($aName, $aSub = '') {
    $lDir = $this -> getPath($aSub);
    return $lDir.$aName;
  }

  public function getFileArray() {

  }

  public function makeDir($aPath, $aMode = 0777) {
    $lSub = substr($aPath, 0, strrpos($aPath, DS));
    if ('' != $lSub) {
      $this -> makeDir($lSub, $aMode);
    }
    if (!file_exists($aPath)) {
      mkdir($aPath, $aMode);
    }
  }

  // TODO: this function needs a makeover. The whole URL parsing would be more secure when we would use special characters to define parse-able parts like {{ and }} or else
  public function getDynPath($aURL) {
    $lURL = str_replace("\\", "/", $aURL);
    $lURLArr = explode('/', $lURL);

    $lAddJobFieType = array(
        'AName' => 'string',
        'ANr' => 'string',
        'Art' => 'string',
        'BName' => 'string',
        'BNr' => 'string',
        'Eingang' => 'datetime',
        'KName' => 'string',
        'KNr' => 'string',
        'SachBExt' => 'string',
        'SachBInt' => 'string',
        'StichW1' => 'string',
        'StichW2' => 'string',
        'StichW3' => 'string',
        'UnterNr' => 'int'
    );

    $lAddJobFieName = array(
        'ANAME' => 'AName',
        'ANR' => 'ANr',
        'BESTNR' => 'StichW3',
        'BETREFF' => 'StichW1',
        'BNAME' => 'BName',
        'BNR' => 'BNr',
        'FULLJNR' => 'JobNr',
        'JNR' => 'JobNr',
        'KDNJOB' => 'StichW2',
        'KNAME' => 'KName',
        'KNR' => 'KNr',
        'M' => 'Eingang',
        'MM' => 'Eingang',
        'MMM' => 'Eingang',
        'MMMM' => 'Eingang',
        'SBEXT' => 'SachBExt',
        'SBINT' => 'SachBInt',
        'YY' => 'Eingang',
        'YYYY' => 'Eingang'
    );

    $lChanges = array();

    foreach ($lAddJobFieName as $lOuterKey => $lOuterValue) {
      foreach ($lURLArr as $lInnerKey => $lInnerValue) {
        // currently we do a very simple check
        if (strpos($lInnerValue, $lOuterKey) === 0) {
          $lChanges[] = array('urlarr' => $lInnerKey, 'addjobfiename' => $lOuterKey);
        }
      }
    }

    if (count($lChanges) == 0) {
      return $lURL;
    } else {
      // get job fields
      $lWriter = CCor_Cfg::get('job.writer.default', 'alink');
      if ('portal' == $lWriter) {
        $lIte = new CCor_TblIte('all');
        foreach ($lAddJobFieType as $lKey => $lValue) {
          $lIte -> addField('flink_'.strtolower($lKey));
        }
        $lIte -> addField('flink_jobnr');
      } else {
        $lIte = new CApi_Alink_Query_Getjoblist($this -> mSrc);
        foreach ($lAddJobFieType as $lKey => $lValue) {
          $lIte -> addField('flink_'.strtolower($lKey), $lKey);
        }
        $lIte -> addField('flink_jobnr', 'jobnr');
      }

      $lIte -> addCondition('jobid', '=',  $this -> mJobId);
      $lIte -> query();
      $lJob = $lIte -> getArray();
      $lJobArr = $lJob[0];

      // set job fields
      foreach ($lAddJobFieName as $lKey => $lValue) {
        $lAddJobFieName[$lKey] = $lJobArr['flink_'.strtolower($lValue)];
      }

      if (!$lJobArr['flink_jnr']) {
        $lAddJobFieName['FULLJNR'] = $this -> mJobId;
        if (strtolower(substr($this -> mJobId, 0, 1)) == 'a') {
          $lAddJobFieName['JNR'] = $this -> mJobId;
        } else {
          $lAddJobFieName['JNR'] = intval($this -> mJobId);
        }
      }

      // format job fields: job numbers
      if ($lJobArr['flink_unternr'] > 0) {
        $lAddJobFieName['FULLJNR'] = $lAddJobFieName['JNR'].'-'.$lJobArr['flink_art'];
        if ($lJobArr['flink_unternr'] >= 10) {
          $lAddJobFieName['FULLJNR'] = $lAddJobFieName['JNR'].'-'.$lJobArr['flink_art'].$lJobArr['flink_unternr'];
        } else {
          $lAddJobFieName['FULLJNR'] = $lAddJobFieName['JNR'].'-'.$lJobArr['flink_art'].'0'.$lJobArr['flink_unternr'];
        }
      }

      // format job fields: job dates
      $lDate = new DateTime($lJobArr['flink_eingang']);
      $lAddJobFieName['M'] = $lDate -> format("n");
      $lAddJobFieName['MM'] = $lDate -> format("m");
      $lAddJobFieName['MMM'] = $lDate -> format("M");
      $lAddJobFieName['MMMM'] = $lDate -> format("F");
      $lAddJobFieName['YY'] = $lDate -> format("y");
      $lAddJobFieName['YYYY'] = $lDate -> format("Y");

      $lJobNr = intval($this -> mJobId);

      // transfer
      foreach ($lChanges as $lKey => $lValue) {
        if ($lValue['addjobfiename'] != 'JNR') {
          $lURLArr[$lValue['urlarr']] = $lAddJobFieName[$lValue['addjobfiename']];
        } else {
          $lTerm = explode(' ', $lURLArr[$lValue['urlarr']]);
          if (count($lTerm) == 3) {
            $lDividend = strtoupper($lTerm[0]);
            $lQuotient = strtoupper($lTerm[1]);
            $lDivisor = intval($lTerm[2]);


            if ($lQuotient == 'DIV') {
              $lURLArr[$lValue['urlarr']] = (int)($lJobNr / $lDivisor);
            } elseif ($lQuotient == 'MOD') {
              $lURLArr[$lValue['urlarr']] = substr($lJobNr, 0, -$lDivisor);
            }
          } else {
            $lURLArr[$lValue['urlarr']] = $lAddJobFieName[$lValue['addjobfiename']];
          }
        }
      }

      // re-build the url
      $lURL = implode('/', $lURLArr);

      return $lURL;
    }
  }
}