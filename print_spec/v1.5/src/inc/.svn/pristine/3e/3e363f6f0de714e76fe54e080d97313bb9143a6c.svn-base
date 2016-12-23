<?php
define('umAbort',     0);
define('umOverwrite', 1);
define('umAddIndex',  2);

class CInc_Cor_Upload extends CCor_Obj {

  public function isValidUpload($aKey) {
    $lFil = $_FILES[$aKey];
    $lErr = $lFil['error'];
    $lRet = (UPLOAD_ERR_OK == $lErr);
    if (!is_uploaded_file($lFil['tmp_name'])) {
      $lRet = FALSE;
    }
    return $lRet;
  }

  public function makeDir($aPath, $aMode = 0777) {
    $this -> dbg('mkdir '.$aPath);
    $lSub = substr($aPath, 0, strrpos($aPath, DS));
    if ('' != $lSub) {
      $this -> makeDir($lSub, $aMode);
    }
    if (!file_exists($aPath)) {
      mkdir($aPath, $aMode);
    }
  }

  public function doUpload($aTmp, $aDir, $aDest, $aMode = umAbort) {
    if (!is_dir($aDir)) {
      $this -> makeDir($aDir, 0777);
    }
    if (!is_dir($aDir)) {
      echo 'Upload directory '.$aDir.' error, please contact an administrator';
      exit;
      $this -> msg('Upload directory error, please contact an administrator', mtUser, mlError);
      return FALSE;
    }
    if (substr($aDir, -1) != DS) {
      $aDir.= DS;
    }
    if (file_exists($aDir.$aDest)) {
      switch($aMode) {
        case umOverwrite:
          break;
        case umAbort:
          $this -> msg('File '.$aDest.' already exists', mtUser, mlError);
          return FALSE;
          break;
        case umAddIndex:
          $lExt = strrchr($aDest, '.');
          if ($lExt === FALSE) {
            $lExt = '';
            $lNam = $aDest;
          } else {
            $lNam = substr($aDest, 0, -(strlen($lExt)));
          }
          $lCnt = 1;
          $lNew = $lNam;
          while (file_exists($aDir.$lNew.$lExt)) {
            $lNew = $lNam.'_'.$lCnt;
            $lCnt++;
          }
          $aDest = $lNew.$lExt;
          break;
      }
    }
    $lRet = copy($aTmp, $aDir.$aDest);
    if (!$lRet) {
      $this -> msg('Upload file error, please contact an administrator', mtUser, mlError);
    } else {
      unlink($aTmp);
      $lRet = $aDest;
    }
    return $lRet;
  }

  public function doUploadVersion($aTmp, $aDir, $aDest, $aDelimiter = '_') {
    if (!is_dir($aDir)) {
      $this->dbg('Making Dir '.$aDir);
      $this -> makeDir($aDir, 0777);
    }
    if (!is_dir($aDir)) {
      $this -> msg('Upload directory error, please contact an administrator', mtUser, mlError);
      return false;
    }
    if (substr($aDir, -1) != DS) {
      $aDir.= DS;
    }
    $lExt = strrchr($aDest, '.');
    if ($lExt === false) {
      $lExt = '';
      $lNam = $aDest;
    } else {
      $lNam = substr($aDest, 0, -(strlen($lExt)));
    }
    $lCnt = 1;
    $lNew = $lNam.$aDelimiter.$lCnt;
    while (file_exists($aDir.$lNew.$lExt)) {
      $lCnt++;
      $lNew = $lNam.$aDelimiter.$lCnt;
    }
    $lFile = $lNew.$lExt;
    $lRet = copy($aTmp, $aDir.$lFile);
    if (!$lRet) {
      $this -> msg('Upload file error, please contact an administrator', mtUser, mlError);
      exit;
    } else {
      $lRet = $lFile;
    }
    return $lRet;
  }

  public function uploadReqTo($aKey, $aDir) {
    $lFil = $_FILES[$aKey];
    if ($lFil['error'] != UPLOAD_ERR_OK) {
      $this -> msg('Upload file error, please contact an administrator', mtUser, mlError);
      return FALSE;
    }
    $lTmp = $lFil['tmp_name'];
    $lNam = $lFil['name'];
    return $this -> doUpload($lTmp, $aDir, $lNam, umAddIndex);
  }

  public function uploadIndex($aKey, $aDir, $aVar = 'val') {
    $this -> dump($_FILES, '_FILES');
    if (!isset($_FILES[$aVar])) {
      return FALSE;
    }
    $lArr = $_FILES[$aVar];
    $this -> dump($lArr, 'lArr');
    $lTmp = $lArr['tmp_name'][$aKey];
    $lNam = $lArr['name'][$aKey];
    return $this -> doUpload($lTmp, $aDir, $lNam, umAddIndex);
  }
}
