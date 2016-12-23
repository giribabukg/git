<?php
class CInc_Arc_Fil_Src_Rtp extends CCor_Obj {

  public function __construct($aSrc, $aJobId) {
    $this -> mSrc = $aSrc;
    $this -> mJobId = $aJobId;

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


  //rray ( 0 => array ( 'fileid' => '59', 'filename' => 'HP 188526#C8150-81007_1.pdf', 'filetype' => 'pdf', 'destfile' => 'HP 188526#C8150-81007_1_V_1.pdf', 'destpath' => 'SuT', 'version' => '1', 'filesize' => '1296960', 'filedate' => '19.07.2005 13:49:26', 'serveraddress' => '62.157.152.20', 'approval' => '0', 'ftpdownload' => 'ftp://RealTimeUser:proof@192.168.0.12/SuT/HP%20188526%23C8150%2D81007%5F1%5FV%5F1%2Epdf', ), 1 => array ( 'fileid' => '63', 'filename' => 'HP 188526#C8150-81007_1.pdf', 'filetype' => 'pdf', 'destfile' => 'HP 188526#C8150-81007_1_V_2.pdf', 'destpath' => 'SuT', 'version' => '2', 'filesize' => '1296960', 'filedate' => '19.07.2005 13:49:26', 'serveraddress' => '62.157.152.20', 'approval' => '0', 'ftpdownload' => 'ftp://RealTimeUser:proof@192.168.0.12/SuT/HP%20188526%23C8150%2D81007%5F1%5FV%5F2%2Epdf', ), 2 => array ( 'fileid' => '60', 'filename' => 'HP 188526#C8150-81007_2.pdf', 'filetype' => 'pdf', 'destfile' => 'HP 188526#C8150-81007_2_V_1.pdf', 'destpath' => 'SuT', 'version' => '1', 'filesize' => '1288663', 'filedate' => '21.07.2005 13:26:44', 'serveraddress' => '62.157.152.20', 'approval' => '0', 'ftpdownload' => 'ftp://RealTimeUser:proof@192.168.0.12/SuT/HP%20188526%23C8150%2D81007%5F2%5FV%5F1%2Epdf', ), 3 => array ( 'fileid' => '64', 'filename' => 'HP 188526#C8150-81007_2.pdf', 'filetype' => 'pdf', 'destfile' => 'HP 188526#C8150-81007_2_V_2.pdf', 'destpath' => 'SuT', 'version' => '2', 'filesize' => '1288663', 'filedate' => '21.07.2005 13:26:44', 'serveraddress' => '62.157.152.20', 'approval' => '0', 'ftpdownload' => 'ftp://RealTimeUser:proof@192.168.0.12/SuT/HP%20188526%23C8150%2D81007%5F2%5FV%5F2%2Epdf', ), )

}