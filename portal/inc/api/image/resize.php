<?php
class CInc_Api_Image_Resize extends CCor_Obj {

  const EXT_GD = 'gd';
  const EXT_IMAGICK = 'imagick';

  public function __construct() {
    $this->mExtension = self::EXT_GD;
    if (extension_loaded(self::EXT_IMAGICK)) {
      $this->mExtension = self::EXT_IMAGICK;
    }
  }

  public function loadFromFile($aFilename) {
    if (!file_exists($aFilename)) {
      return false;
    }
    $this->fileName = $aFilename;
    list($this->width, $this->height, $this->imageType) = getimagesize($aFilename);

    if ($this->mExtension == self::EXT_GD) {
      return $this->loadGd($aFilename);
    }
    if ($this->mExtension == self::EXT_IMAGICK) {
      return $this->loadImagick($aFilename);
    }
    return false;
  }


  protected function loadGd($aFilename) {

    switch ($this->imageType)
    {
      case IMG_GIF  : $this->image = imagecreatefromgif($aFilename); break;
      case IMG_JPEG : $this->image = imagecreatefromjpeg($aFilename); break;
      case IMG_PNG  : $this->image = imagecreatefrompng($aFilename); break;
      default : return false;
    }
    return $this->image;
  }

  protected function loadImagick($aFilename) {
    $this->image = new Imagick($aFilename);
    return $this->image;
  }

  public function resize($aWidth, $aHeight) {
    $lRatioX = $this->width  / $aWidth;
    $lRatioY = $this->height / $aHeight;

    $lOffY = 0;
    $lOffX = 0;

    if ($lRatioX > $lRatioY) {
      $lWidth = $aWidth;
      $lHeight = floor($this->height / $lRatioX);
      $lOffY = floor(($aHeight - $lHeight) / 2);
    } else {
      $lHeight = $aHeight;
      $lWidth = floor($this->width / $lRatioY);
      $lOffX = floor(($aWidth - $lWidth) / 2);
    }

    if ($this->mExtension == self::EXT_GD) {
      $this->resizeGd($aWidth, $aHeight, $lWidth, $lHeight, $lOffX, $lOffY);
    }
    if ($this->mExtension == self::EXT_IMAGICK) {
      $this->resizeImagick($aWidth, $aHeight, $lWidth, $lHeight, $lOffX, $lOffY);
    }
  }

  protected function resizeGd($aWidth, $aHeight, $aNewWidth, $aNewHeight, $aOffX, $aOffY) {

    $lDstImg = imagecreatetruecolor($aWidth, $aHeight);
    $lCol = imagecolorallocate($lDstImg, 255, 255, 255);
    imagefill($lDstImg, 0, 0, $lCol);

    imagecopyresampled($lDstImg, $this->image, $aOffX, $aOffY, 0, 0,
        $aNewWidth, $aNewHeight, $this->width, $this->height);

    $this->width = $aWidth;
    $this->height = $aHeight;
    $this->image  = $lDstImg;
  }

  protected function resizeImagick($aWidth, $aHeight, $aNewWidth, $aNewHeight, $aOffX, $aOffY) {
    $this->image->thumbnailImage($aNewWidth, $aNewHeight);
  }

  public function saveAs($aFilename, $aType = IMG_GIF) {
    if ($this->mExtension == self::EXT_GD) {
      return $this->saveGd($aFilename, $aType);
    }
    if ($this->mExtension == self::EXT_IMAGICK) {
      return $this->saveImagick($aFilename);
    }
  }

  protected function saveGd($aFilename, $aType = IMG_GIF) {
    switch ($aType)
    {
      case IMG_GIF  : return imagegif($this->image,  $aFilename);
      case IMG_JPEG : return imagejpeg($this->image, $aFilename);
      case IMG_PNG  : return imagepng($this->image,  $aFilename);
      default : return false;
    }
  }

  protected function saveImagick($aFilename) {
    $this->msg('Saved as Imagick');
    $this->image->writeImage($aFilename);
  }

}