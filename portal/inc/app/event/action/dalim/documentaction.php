<?php
class CInc_App_Event_Action_Dalim_DocumentAction extends CApp_Event_Action {

  public function execute() {
    $lJob = $this -> mContext['job'];

    // new job information
    $lSrc = $lJob['src'];
    $lJobID = $lJob['jobid'];

    // old job information
    $lOrigSrc = $lJob['orig_src'];
    $lOrigJobId = $lJob['orig_jobid'];

    $lSQL = 'SELECT * FROM al_job_files WHERE id=(SELECT max(id) FROM al_job_files WHERE src='.esc($lOrigSrc).' AND jobid='.esc($lOrigJobId).' AND sub="dalim");';
    $lOrigFilename = CCor_Qry::getInt($lSQL);

    $lUniqueID = uniqid('dalim_', TRUE);
    $lTemporaryDirectory = "tmp".DS.$lUniqueID;

    $lFinder = new CApp_Finder($lOrigSrc, $lOrigJobId);
    $lName = $lFinder -> getName($lOrigFilename, 'dalim');
    if (is_readable($lName)) {
      $lExtension = strtolower(strrchr($lOrigFilename,'.'));
      if ($lExtension == '.pdf') {
        header('Content-Type: application/pdf');
      } else if ($lExtension == '.dxf'){
        header('Content-Type: application/dxf');
      } else {
        header('Content-Type: application/octet-stream');
      }
      header('Cache-Control: public');
      header('Pragma: public');
      header('Content-Disposition: attachment; filename="'.$lOrigFilename.'"');

      mkdir($lTempDir, 0777, TRUE);
      file_put_contents($lTemporaryDirectory.DS.$lOrigFilename.$lExtension, $lName);
    } else {
      return FALSE;
    }

    $lUpload = new CJob_Fil_Upload($lSrc, $lJobID);
    $lReturn = $lUpload -> uploadToDalim($lTemporaryDirectory.DS.$lOrigFilename.$lExtension, $lJobID.$lExtension);
    if (!$lReturn) {
      return FALSE;
    } else {
      $this -> rrmdir($lTemporaryDirectory);

      return TRUE;
    }
  }

  protected function rrmdir($aDirectory) {
    $lFiles = array_diff(scandir($aDirectory), array('.', '..'));
    foreach ($lFiles as $lFile) {
      (is_dir($aDirectory.DS.$lFile)) ? $this -> rrmdir($aDirectory.DS.$lFile) : unlink($aDirectory.DS.$lFile);
    }
    return rmdir($aDirectory);
  }
}