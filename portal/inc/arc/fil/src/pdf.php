<?php
class CInc_Arc_Fil_Src_Pdf extends CCor_Obj {

  public function __construct($aSrc, $aJobId) {
    $this -> mSrc = $aSrc;
    $this -> mJobId = $aJobId;
  }

  public function getFileList() {
    $lUsr = CCor_Usr::getInstance();
    if ($lUsr -> canRead('job-pdf')) {
      $lRet = array();
      $lQry = new CApi_Alink_Query_Getpdflist($this -> mJobId);
      $lTmp = array();
      foreach ($lQry as $lRow) {
        $lItm = array();
        $lItm['name'] = $lRow['filename'];
        $lItm['size'] = $lRow['filesize'];
        $lDat = preg_split('@[/.:-\s]@', $lRow['filedate']);
        $lItm['date'] = mktime($lDat[3], $lDat[4], $lDat[5],  $lDat[1], $lDat[0], $lDat[2]);
        $lRet[] = $lItm;
      }
      return $lRet;
    }
  }

}