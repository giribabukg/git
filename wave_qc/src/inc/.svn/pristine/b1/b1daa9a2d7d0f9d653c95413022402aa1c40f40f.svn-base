<?php
class CInc_Job_Fil_Src_Rtp extends CCor_Obj {

  public function __construct($aSrc, $aJobId, $aOrd = '') {
    $this -> mSrc = $aSrc;
    $this -> mJobId = $aJobId;
    $this -> mOrd = $aOrd;

    $this -> mUrl = 'http://mail2.st-packline.de/php/hebridge/';
  }

  public function getFileList() {
    $lRet = array();
    $lSid = md5(sha1(date(lan('lib.date.long')).$this -> mJobId));
    $lUrl = $this -> mUrl.'bri.php?act=getRtpList&sid='.$lSid.'&jobid='.$this -> mJobId;
    try {
      $lRes = file_get_contents($lUrl);
      $lRet = unserialize($lRes);
      $this -> dump($lRet);
    } catch (Exception $lExc) {
      $this -> dbg($lExc -> getMessage(), mlWarn);
    }
    if (empty($lRet)) {
      $lRet = array();
    } else {
      $lTmp = array();
      foreach ($lRet as $lRow) {
        $lItm = array();
        $lItm['name'] = $lRow['filename'];
        $lItm['size'] = $lRow['filesize'];
        $lDat = preg_split('@[/.:-\s]@', $lRow['filedate']);
        $lItm['date'] = mktime($lDat[3], $lDat[4], $lDat[5],  $lDat[1], $lDat[0], $lDat[2]);
        $lItm['version'] = $lRow['version'];
        $lItm['fileid'] = $lRow['fileid'];
        $lTmp[] = $lItm;
      }
      $lRet = $lTmp;
    }
    return $lRet;
  }

}