<?php
/**
 * Jobs: Upload Checker
 *
 *  Description
 *
 * @package    JOB
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 6 $
 * @date $Date: 2012-02-21 09:50:56 +0100 (Di, 21 Feb 2012) $
 * @author $Author: gemmans $
 */
class CInc_Job_Uploadchecker extends CInc_Job_Form {

  public function __construct($aSrc, $aAct, $aJobId, $aJob) {
    $this -> mSrc = $aSrc;
    $this -> mAct = $aAct;
    $this -> mJobId = $aJobId;
    $this -> mJob = $aJob;
  }


  /**
   * will return per defualt 'false' ==> means don't hide any upload field
   * return an array with the the alias name as a key and true as a value to deactivate the upload
   * @return false | array
   */
  protected function disableUpload() {
    return false;
  }
  
  /* Example of usage.
  protected function disableUpload() {
    $lArray = array();
    $lArray['file_upload'] = false; // Means upload will still possible
    $lArray['file_upload_print_proof'] = true; // Mean upload is deactivated
    return $lArray;
  }*/
}