<?php
class CInc_Arc_Fil_Src_Doc extends CCor_Obj {

  public function __construct($aSrc, $aJobId) {
    $this -> mSrc = $aSrc;
    $this -> mJobId = $aJobId;
  }

  public function getFileList() {
    $lRet = array();
    $lCls = new CApp_Finder($this -> mSrc, $this -> mJobId);
    $lDir = $lCls -> getPath('doc');
    if (file_exists($lDir)) {
      $lUsr = CCor_Res::extract('id', 'fullname', 'usr');
      $lArr = array();
      $lSql = 'SELECT uid,filename FROM al_job_files WHERE src='.esc($this -> mSrc).' ';
      $lSql.= 'AND jobid='.esc($this -> mJobId);
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        $lArr[$lRow['filename']] = $lRow['uid'];
      }
      try {
        $lIte = new DirectoryIterator($lDir);
        foreach ($lIte as $lLin) {
          $lItm = array();
          if ($lIte -> isFile()) {
            $lNam = $lIte -> getFilename();
            $lItm['name']  = $lNam;
            $lItm['size']  = $lIte -> getSize();
            $lItm['date']  = $lIte -> getMTime();
            $lItm['uid']   = 0;
            $lItm['user']  = '???';
            if (isset($lArr[$lNam])) {
              $lUid = $lArr[$lNam];
              $lItm['uid'] = $lUid;
              if (isset($lUsr[$lUid])) {
                $lItm['user'] = $lUsr[$lUid];
              } else {
                $lItm['user'] = 'user '.$lUid;
              }
            }
            $lRet[] = $lItm;
          }
        }
      } catch (Exception $lExc) {
        $this -> dbg($lExc -> getMessage(), mlWarn);
      }
    }
    return $lRet;
  }


}