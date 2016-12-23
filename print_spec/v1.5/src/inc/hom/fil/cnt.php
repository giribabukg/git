<?php
class CInc_Hom_Fil_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('job-pro.fls');
    $this -> mMmKey = 'hom-wel';
  }

  protected function getSrc() {
    return MID;
  }

  protected function actStd() {
    $lMen = new CHom_Menu('fil');

    $lRet = '';
    $lArr = CCor_Res::extract('id', 'name_'.LAN, 'mand');
    $lNam = (isset($lArr[MID])) ? $lArr[MID] : lan('lib.unknown');
    $lBox = new CHom_Fil_Filebox($lNam, $this->getSrc());
    $lRet.= $lBox -> getContent();

    $this -> render(CHtm_Wrap::wrap($lMen, $lRet));
  }

  protected function actSnew() {
    $lSrc = $this -> getSrc();
    $lFin = new CApp_Finder('app');
    $lFin->setMid($lSrc);
    $lDir = $lFin -> getPath($lSrc);

    $lFil = $_FILES['file'];
	$lUpl = new CCor_Upload();
    $lRes = $lUpl -> doUpload($lFil['tmp_name'], $lDir, $lFil['name'], umAddIndex);
    
    $this -> redirect();
  }

  protected function actDel() {
    $lSrc = $this -> getSrc();
    $lNam = $this -> getReq('fn');

    $lFin = new CApp_Finder('app');
    $lFin -> setMid($lSrc);

    $lDir = $lFin -> getPath($lSrc);
    unlink($lDir.urldecode($lNam));

    $this -> redirect();
  }

}
