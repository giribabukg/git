<?php
class CInc_Utl_Pixelboxx_Cnt extends CCor_Cnt {

  protected function actThumb() {
    $lDoi = $this->getReq('doi');
    $lQry = new CApi_Pixelboxx_Query_Getobject();
    $lRes = $lQry->getThumb($lDoi);
    if ($lRes) {
      header('Content-type: image/jpeg');
      echo $lRes;
    } else {
      echo "ouch";
    }
    exit;
  }
  
  protected function actDownload() {
    $lDoi = $this->getReq('doi');
    $lQry = new CApi_Pixelboxx_Query_Getobject();
    $lRes = $lQry->download($lDoi);
    
    if (!$lRes) exit;
    
    $lNam = $lRes['filename'];
    $lExt = strtolower(strrchr($lNam,'.'));
    
    header('Content-Type: application/octet-stream');
    header('Cache-Control: public');
    header('Pragma: public');
    header('Content-Disposition: attachment; filename="'.$lNam.'" filename*="=?UTF-8?B?'.base64_encode($lNam).'?="');
    
    echo $lRes['file_data'];
  }
  
  protected function actSso() {
    $lUrl = 'http://dam21customer24.demo.pixelboxx.com/servlet/login';
  
    $lRet = '';
    $lRet.= '<html>'.LF;
    #$lRet.= '<script>document.onload=function(){document.getElementById("frm").submit();};</script>'.LF;
    $lRet.= '<body onload="document.forms[0].submit()">';
    $lRet.= '<form action="'.$lUrl.'" method="post" id="frm">';
  
    $lUsr = CCor_Usr::getInstance();
    $lArr = array('lng' => 'de', 'script' => 't', 'button' => 'collection', 'back'=>'', 'sonv' => 'start');
    $lArr['user'] = $lUsr->getInfo('pixelboxx_user');
    $lArr['password'] = $lUsr->getInfo('pixelboxx_pass');
  
    foreach ($lArr as $lKey => $lVal) {
      $lRet.= '<input type="hidden" name="'.$lKey.'" value="'.htm($lVal).'" />'.LF;
    }
    $lRet.= '</form>'.LF;
    $lRet.= '</body></html>'.LF;
    echo $lRet;
    exit;
  }
  
}