<?php
class CInc_Job_Image extends CCor_Obj {

  private static $mInstance = NULL;
  private static $mSrc = NULL;
  private static $mJobID = NULL;
  private static $mWidth = NULL;
  private static $mHeight = NULL;

  public function __construct() {
  }

  public function __clone() {
  }

  public function __destruct() {
    self::$mInstance = NULL;
  }

  public static function getInstance() {
    if (NULL === self::$mInstance) {
      self::$mInstance = new self();
    }
    return self::$mInstance;
  }

  public function getBase64EncodedImage($aFilename) {
    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == filemtime($aFilename))) {
      header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($aFilename)).' GMT', TRUE, 304);
      exit;
    } else {
      header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($aFilename)).' GMT', TRUE, 200);

      if (!$lImage = @imagecreatefromjpeg($aFilename)) {
        return NULL;
      }

      $lFileGetContents = file_get_contents($aFilename);
      $lBase64Encode = base64_encode($lFileGetContents);

      return $lBase64Encode;
    }
  }

  public static function setAttributes($aAttributes) {
    self::$mSrc = isset($aAttributes['src']) ? $aAttributes['src'] : NULL;
    self::$mJobID = isset($aAttributes['jobid']) ? $aAttributes['jobid'] : NULL;
    self::$mWidth = isset($aAttributes['width']) ? $aAttributes['width'] : NULL;
    self::$mHeight = isset($aAttributes['height']) ? $aAttributes['height'] : NULL;
  }

  public static function getImage($aFilename) {
    $lDirname = pathinfo($aFilename, PATHINFO_DIRNAME);
    $lFilename = pathinfo($aFilename, PATHINFO_FILENAME);
    $lExtension = pathinfo($aFilename, PATHINFO_EXTENSION);

    if (file_exists($aFilename)) {
      $lBase64EncodedOriginalSizedImage = self::getBase64EncodedImage($aFilename);
      $lOriginalSizedImage = '<img id="img'.self::$mJobID.'" src="data:image/jpeg;base64,'.$lBase64EncodedOriginalSizedImage.'" width="300px" height="300px" alt="">';
    } else {
      $lOriginalSizedImage = img('img/no-img-blank-200.gif', array('width' => 200, 'height' => 200)); // TODO: nameing
    }

    if (file_exists($lDirname.'/'.$lFilename.'_128.'.$lExtension)) {
      $lBase64EncodedSmallSizedImage = self::getBase64EncodedImage($lDirname.'/'.$lFilename.'_128.'.$lExtension); // TODO: generalize
      $lSmallSizedImage = '<img id="img'.self::$mJobID.'" src="data:image/jpeg;base64,'.$lBase64EncodedSmallSizedImage.'" width="'.self::$mWidth.'px" height="'.self::$mHeight.'px" text-align="center" vertical-align="middle" alt="">';
    } else {
      $lSmallSizedImage = img('img/no-img-blank-200.gif', array('width' => self::$mWidth, 'height' => self::$mHeight)); // TODO: nameing
    }

    $lParameters = json_encode(array('src' => self::$mSrc, 'jobid' => self::$mJobID));
    $lReturn = '<a data-toggle="tooltip" data-tooltip-head="" data-tooltip-body="'.htmlentities($lOriginalSizedImage).'" href="javascript:void(0);" onclick="Flow.thumbnail.update('.htmlentities($lParameters).');">';
    $lReturn.= $lSmallSizedImage;
    $lReturn.= '</a>';

    return $lReturn;
  }

  public static function getImageByJobID($aJobID) {
    return NULL;
  }
}