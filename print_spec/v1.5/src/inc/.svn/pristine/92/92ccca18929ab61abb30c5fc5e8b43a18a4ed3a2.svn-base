<?php
class CInc_Utl_Dms_Cnt extends CCor_Cnt {

  protected function actOpen() {
     //https://dms.5flow.net/dmsapiopenfile.aspx?fileversionid=205&username=Emmans&openmode=4
    $lDocVersionId = $this->getReq('docverid');
    $lLock = $this->getReq('lock');
    $lFilename = $this->getReq('fn');
    $lUsr = CCor_Usr::getInstance();
    $lUsrName = $lUsr->getVal('fullname');
    $lMode = ($lLock) ? 5 : 4;

    $lQry = new CApi_Dms_Query();
    $lRet = $lQry->openFile($lDocVersionId, $lUsrName, $lMode);
    header('Content-type: application/octet-stream');
    header('Content-disposition: attachment; filename="'.$lFilename.'"');

    echo $lRet;
    exit;
  }

  protected function actOpenpdf() {
    $lDocVersionId = $this->getReq('docverid');
    $lFilename = $this->getReq('fn');
    
    $lDir = $lDir = '/media/dmspdf/';
    $lPdf = $lDocVersionId.'_'.$lFilename.'.pdf';
  
    header('Content-type: application/octet-stream');
    header('Content-disposition: attachment; filename="'.$lFilename.'.pdf"');
    readfile($lDir.$lPdf);
    exit;
  }

  protected function actEdit() {
    //https://dms.5flow.net/dmsapiopenfile.aspx?fileversionid=205&username=Emmans&openmode=2
    $lDocVersionId = $this->getReq('docverid');
    $lFilename = $this->getReq('fn');
    $lUsr = CCor_Usr::getInstance();
    $lUsrName = $lUsr->getVal('fullname');
    
    $lUrl = CCor_Cfg::get('dms.base.url', 'https://dms.5flow.net/');
    $lUrl.= 'dmsapiopenfile.aspx?fileversionid='.$lDocVersionId;
    $lUrl.= '&username='.$lUsrName.'&openmode=2';
    
    $this->redirect($lUrl);
  }




}
