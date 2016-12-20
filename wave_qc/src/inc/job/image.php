<?php
class CInc_Job_Image extends CCor_Ren {

  protected $mJob;
  protected $mMagnify;
  protected $mSrc;
  protected $mJobId;

  public function __construct($aJob = NULL, $aMagnify = FALSE) {
    $this -> mJob = $aJob;
    $this -> mMagnify = $aMagnify;

    if (isset($this -> mJob)) {
      $this -> mSrc = $this -> mJob['src'];
      $this -> mJobId = $this -> mJob['jobid'];
    }
  }

  function getPreview() {
    $lSvcWecInst = CSvc_Wec::getInstance();
    $lStatics = $lSvcWecInst -> getStatics();

    $lDynamics = $lSvcWecInst -> getDynamics($this -> mJobId);

    if (file_exists($lDynamics['image_dir'].$lDynamics['image_file'])) {
      $lRet = '<img src="'.$lDynamics['image_dir'].$lDynamics['image_file'].'" border="0px" width="300px" height="300px" alt="" />';
    } else {
      $lRet = '<img src="'.$lStatics['image_not_found'].'" border="0px" width="300px" height="300px" alt="" />';
    }

    return $lRet;
  }

  function getThumbnail() {
    $lSvcWecInst = CSvc_Wec::getInstance();
    $lStatics = $lSvcWecInst -> getStatics();
    $lDynamics = $lSvcWecInst -> getDynamics($this -> mJobId);

	$lThumbnail = $lDynamics['thumbnail_dir'].$lDynamics['thumbnail_file'];
	$lImage = $lDynamics['image_dir'].$lDynamics['image_file'];

	$lThumbnailNotFound = $lStatics['thumbnail_not_found'];
	$lImageNotFound = $lStatics['image_not_found'];

	if (file_exists($lThumbnail)) {
	  $lThb = $lThumbnail;
	} else {
      $lThb = ltrim($lThumbnailNotFound, '//');
	}

	if (file_exists($lImage)) {
	  $lImg = $lImage;
	} else {
      $lImg = ltrim($lImageNotFound, '//');
	}

    $lRet = $this -> printThumbnail($lThb, $lImg, $this -> mJobId);

    return $lRet;
  }

  protected function getCont() {
    if ($this -> mMagnify) {
      $lRet = $this -> getPreview();
    } else {
      $lRet = $this -> getThumbnail();
    }

    return $lRet;
  }

  private function printThumbnail($aImgPathThb, $aImgPathImg, $aJobId) {
    $lParams = array(
      'src' => $this -> mSrc,
      'jobid' => $aJobId
    );
    $lParamsJSONEnc = json_encode($lParams);
    $lParamsHTMLSpecChar = htmlspecialchars($lParamsJSONEnc);

    $lImgLarge = "<img src='".$aImgPathThb."' border='0px' width='300px' height='300px' alt='' />";

    $lRet = '<a id="a'.$aJobId.'" class="a'.$aJobId.'" data-toggle="tooltip" data-tooltip-head="" data-tooltip-body="'.$lImgLarge.'" href="javascript:void(0);" onclick="Flow.thumbnail.update('.$lParamsHTMLSpecChar.');">';
    $lRet.= '<img src="'.$aImgPathImg.'" id="img'.$aJobId.'" border="0px" width="100px" height="100px" />';
    $lRet.= '</a>';

    return $lRet;
  }
}