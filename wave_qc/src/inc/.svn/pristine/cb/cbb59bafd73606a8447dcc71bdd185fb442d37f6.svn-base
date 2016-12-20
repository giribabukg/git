<?php
class CInc_Api_Alink_Query_Getpdflist extends CApi_Alink_Query implements IteratorAggregate {

  public function __construct($aJobId, $aMask = '', $aSubPath = '') {
    parent::__construct('getFileList');
    $this -> addParam('jobid', $aJobId);
    $this -> addParam('sid', MAND);
    if (!empty($aMask)) {
      $this -> addParam('mask', $aMask);
      $this -> dbg('CInc_Api_Alink_Query_Getpdflist uses "mask": '.$aMask);
    }
    if (!empty($aSubPath)) {
      $this -> addParam('subpath', $aSubPath);
      $this -> dbg('CInc_Api_Alink_Query_Getpdflist uses "subpath": '.$aSubPath);
    }
    $this -> mLoaded = FALSE;
  }

  public function getIterator() {
    if (!$this -> mLoaded) {
      $this -> query();
      $this -> mLoaded = TRUE;
    }
    if ($this -> mResponse) {
      $lRows = $this -> mResponse -> getVal('item');
    }

    $lRet = array();

    if (!empty($lRows)) {
      foreach ($lRows as $lRow) {
        $lTmp = array();
        foreach ($lRow as $lKey => $lVal) {
          $lTmp[(string)$lKey] = $lVal;
        }
        $lRet[] = $lTmp;
      }
    }
    return new ArrayIterator($lRet);
  }

}