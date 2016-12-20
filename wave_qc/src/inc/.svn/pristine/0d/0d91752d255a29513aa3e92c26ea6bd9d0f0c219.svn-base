<?php
class CInc_Hom_Pic_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('hom.pic');
    $this -> mMod = 'hom-wel';
    $this -> mMmKey = 'hom-wel';

    $lUsr = CCor_Usr::getInstance();
    $this -> mUid = $lUsr -> getId();
  }

  protected function actStd() {
    $lMen = new CHom_Menu('pic');
    $lFrm = new CHom_Pic_Form($this -> mUid);
    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm));
  }

  protected function actSedt() {
    $lVal = $this -> mReq -> getVal('val');
    $lUid = $lVal['id'];
    $lMnd = $lVal['mand'];
    $lErr = $_FILES['photogif']['error'];

    $lAllowed =  array('gif','png' ,'jpg','GIF','PNG' ,'JPG');
    $lFilename = $_FILES['photogif']['name'];
    $lExt = pathinfo($lFilename, PATHINFO_EXTENSION);
    $lFileUploadErrors = array(
        1 => 'The uploaded file exceeds the upload max filesize',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk.',
        8 => 'A PHP extension stopped the file upload.',
    );

    if (in_array($lErr, (array_keys($lFileUploadErrors)))) {
       $this -> msg($lFileUploadErrors[$lErr], mtUser, mlError);
       $this -> redirect('index.php?act=hom-pic');
    }

    if(!in_array($lExt, $lAllowed) ) {
     $this -> msg('File extension '. "'$lExt'" . ' is not allowed, Images can either be jpg, gif or png', mtUser, mlError);
     $this -> redirect('index.php?act=hom-pic');
    }

    else {
      $lCfg = CCor_Cfg::getInstance();
      /** User can be member of more Clients.
      *  Therefore it is better to save User Pictures in the Customer Order.
      */
      $lPath = CUST_PATH_IMG;
      $lNew = $lPath.'usr/usr-'.$lUid.'.gif';

      $lFile = $_FILES['photogif']['tmp_name'];
      if (file_exists($lFile)) {
        list($lOldWid, $lOldHei, $lOldTyp) = getimagesize($lFile);

        $lNewWid = 65;
        $lNewHei = 100;
        switch ($lOldTyp) {
          case 1: $lSrcImg = imagecreatefromgif($lFile); break;
          case 2: $lSrcImg = imagecreatefromjpeg($lFile); break;
          case 3: $lSrcImg = imagecreatefrompng($lFile); break;
          default: $lSrcImg = imagecreatefromjpeg($lFile); break;
        }

        $lRatioX = $lOldWid / $lNewWid;
        $lRatioY = $lOldHei / $lNewHei;

        $lOffY = 0;
        $lOffX = 0;

        if ($lRatioX > $lRatioY) {
          $lWidth = $lNewWid;
          $lHeight = floor($lOldHei / $lRatioX);
          $lOffY = floor(($lNewHei - $lHeight) / 2);
        } else {
          $lHeight = $lNewHei;
          $lWidth = floor($lOldWid / $lRatioY);
          $lOffX = floor(($lNewWid - $lWidth) / 2);
        }

        $lDstImg = imagecreatetruecolor($lNewWid, $lNewHei);
        $lCol = imagecolorallocate($lDstImg, 255, 255, 255);
        imagefill($lDstImg, 0, 0, $lCol);

        imagecopyresampled($lDstImg, $lSrcImg, $lOffX, $lOffY, 0, 0, $lWidth, $lHeight, $lOldWid, $lOldHei);
        imagegif($lDstImg, $lNew);
      }
      $this -> redirect();
    }

  }

}