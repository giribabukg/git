<?php
class CInc_Pri_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('printer').' '.lan('lib.specsheet');
  }

  protected function actStd() {
    $lVie = new CPri_List();
    $this -> render($lVie);
  }

  protected function actUpl() {
    $lGid = $this -> getReqInt('id');
    $lVie = new CPri_Form('pri.supl', lan('lib.file.upload'));
    $lVie -> setUploadFormTag();
    $lVie -> setParam('id', $lGid);
    $this -> render($lVie);
  }

  protected function actSupl() {
    $lGid = $this -> getReqInt('id');
    $lFil = $_FILES['file'];
    if ($lFil['error'] != UPLOAD_ERR_OK) {
      $this -> msg('Upload file error, please contact an administrator', mtUser, mlError);
      return FALSE;
    }
    $lUpl = new CCor_Upload();
    $lFin = new CApp_Finder('pri', 0);
    $lDir = $lFin -> getPath();
    $lNam = $lGid.'_'.$lFil['name'];
    $lUpl -> doUpload($lFil['tmp_name'], $lDir, $lNam, umAddIndex);

    $this -> redirect();
  }

  protected function actDel() {
    $lGid = $this -> getReqInt('id');
    $lFin = new CApp_Finder('pri',0);
    $lDir = $lFin -> getPath();
    $lIte = new DirectoryIterator($lDir);
    foreach ($lIte as $lLin) {
      if ($lIte -> isFile()) {
        $lNam = $lIte -> getFilename();
        $lPos = strpos($lNam, '_');
        if (FALSE !== $lPos) {
          $lArr = explode('_', $lNam,2);
          if ($lArr[0] == $lGid) {
            $this -> dbg('Deleting '.$lDir.$lNam, mlWarn);
            @unlink($lDir.$lNam);
          }
        }
      }
    }
    $this -> redirect();
  }

}