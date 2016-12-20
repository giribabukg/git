<?php
class CInc_Hom_Mass_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('job-pro.mass');
    $this -> mMmKey = 'hom-wel';
  }

  protected function getSrc() {
    return MID;
  }

  protected function actStd() {
    $lMen = new CHom_Menu('mass');

    $lRet = '';
    $lArr = CCor_Res::extract('id', 'name_'.LAN, 'mand');
    $lNam = (isset($lArr[MID])) ? $lArr[MID] : lan('lib.unknown');
    $lBox = new CHom_Mass_Filebox($lNam, $this->getSrc());
    $lRet.= $lBox -> getContent();

    $this -> render(CHtm_Wrap::wrap($lMen, $lRet));
  }

  protected function actSnew() {
    $lSrc = $this -> getSrc();
    $lFin = new CApp_Finder('mass');
    $lFin->setMid($lSrc);
    $lDir = $lFin -> getPath($lSrc);

    $lFil = $_FILES['file'];
	
	$lParts = pathinfo($lFil['name']);
    $lExt = strtolower($lParts['extension']);
    $lExtensions = CCor_Cfg::get('mass.file.extension');
    if(!in_array($lExt, $lExtensions)){
      $this->msg('Uploaded file: ' . $lFil['name'] . ' can not be uploaded due to the file extension not being permitted', mtApi, mlError);
    } else {
      $lUpl = new CCor_Upload();
      $lRes = $lUpl -> doUpload($lFil['tmp_name'], $lDir.DS.'in', $lFil['name'], umAddIndex);
    }
    $this -> redirect();
  }

  protected function actDel() {
    $lSrc = $this -> getSrc();
    $lNam = $this -> getReq('fn');
    $lFin = new CApp_Finder('mass');
    $lFin->setMid($lSrc);
    $lDir = $lFin -> getPath($lSrc);
    unlink($lDir.'parsed'.DS.$lNam);
    $this -> redirect();
  }

}