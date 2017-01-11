<?php
class CInc_Hom_Fil_Cnt extends CCor_Cnt {

  protected $mMainDirectory = NULL;

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mTitle = lan('hom-fil');
    $this -> mMmKey = 'hom-wel';

    $lFolder = new CApp_Finder('app');
    $this -> mMainDirectory = rtrim($lFolder -> getPath(MID), DS);
  }

  protected function actStd() {
    $lList = new CHom_Fil_List();
    $lListContent = $lList -> getContent();

    $lMenu = new CHom_Menu('fil');
    $this -> render(CHtm_Wrap::wrap($lMenu, $lListContent));
  }

  protected function actCreateDirectory() {
    $lPath = $this -> getReq('path');
    $lName = $this -> getReq('name');

    $lRelativeDirectory = (!empty($lPath)) ? DS.$lPath : '';
    $lDirectoryName = (!empty($lName)) ? DS.$lName : '';

    $lNewPath = $this -> mMainDirectory.$lRelativeDirectory.$lDirectoryName;

    clearstatcache();
    if (!file_exists($lNewPath)) {
      $lMkDir = mkdir($lNewPath);

      if (!$lMkDir) {
        $lParams = array('result' => $lMkDir, 'comment' => lan('lib.directory.not.created'), 'relativedirectory' => $lRelativeDirectory, 'directoryname' => $lDirectoryName);
      } else {
        $lParams = array('result' => $lMkDir, 'comment' => lan('lib.directory.created'), 'relativedirectory' => $lRelativeDirectory, 'directoryname' => $lDirectoryName);
      }
    } else {
      $lParams = array('result' => FALSE, 'comment' => lan('lib.directory.exists'), 'relativedirectory' => $lRelativeDirectory, 'directoryname' => $lDirectoryName);
    }

    $lReturn = json_encode($lParams);

    echo $lReturn;
    exit;
  }

  protected function actRenameDirectory() {
    $lPath = $this -> getReq('path');
    $lName = $this -> getReq('name');

    $lRelativeDirectory = (!empty($lPath)) ? DS.dirname($lPath) : '';
    $lDirectoryName = (!empty($lName)) ? DS.$lName : '';

    $lNewPath = $this -> mMainDirectory.$lRelativeDirectory.$lDirectoryName;

    clearstatcache();
    if (!file_exists($lNewPath)) {
      $lRename = rename($this -> mMainDirectory.DS.$lPath, $lNewPath);

      if (!$lRename) {
        $lParams = array('result' => $lRename, 'comment' => lan('lib.directory.not.renamed'), 'relativedirectory' => $lRelativeDirectory, 'directoryname' => $lDirectoryName);
      } else {
        $lParams = array('result' => $lRename, 'comment' => lan('lib.directory.renamed'), 'relativedirectory' => $lRelativeDirectory, 'directoryname' => $lDirectoryName);
      }
    } else {
      $lParams = array('result' => FALSE, 'comment' => lan('lib.directory.not.exists'), 'relativedirectory' => $lRelativeDirectory, 'directoryname' => $lDirectoryName);
    }

    $lReturn = json_encode($lParams);

    echo $lReturn;
    exit;
  }

  protected function actRemoveDirectory() {
    $lPath = $this -> getReq('path');

    $lNewPath = $this -> mMainDirectory.$lPath;

    clearstatcache();
    if (file_exists($lNewPath)) {
      $lRename = $this -> rrmdir($lNewPath);

      if (!$lRename) {
        $lParams = array('result' => $lRename, 'comment' => lan('lib.directory.not.removed'));
      } else {
        $lParams = array('result' => $lRename, 'comment' => lan('lib.directory.removed'));
      }
    } else {
      $lParams = array('result' => FALSE, 'comment' => lan('lib.directory.not.exists'));
    }

    $lReturn = json_encode($lParams);

    echo $lReturn;
    exit;
  }

  protected function rrmdir($aDirectory) {
    $lFiles = array_diff(scandir($aDirectory), array('.', '..'));
    foreach ($lFiles as $lFile) {
      (is_dir($aDirectory.DS.$lFile)) ? $this -> rrmdir($aDirectory.DS.$lFile) : unlink($aDirectory.DS.$lFile);
    }
    return rmdir($aDirectory);
  }

  protected function actUploadFile() {
    $lPath = $this -> getReq('path');
    $lName = $this -> getReq('name');
    $lNewPath = $lPath.DS.$lName;

    clearstatcache();
    if (!file_exists($lNewPath)) {
//      $lUpload = rename($lPath, $lNewPath);

      if (!$lUpload) {
        $lParams = array('result' => $lRename, 'comment' => lan('lib.file.not.uploaded'));
      } else {
        $lParams = array('result' => $lRename, 'comment' => lan('lib.file.uploaded'));
      }
    } else {
      $lParams = array('result' => FALSE, 'comment' => lan('lib.file.exists'));
    }

    $lReturn = json_encode($lParams);

    echo $lReturn;
    exit;
  }

  protected function actRenameFile() {
    $lPath = $this -> getReq('path');
    $lName = $this -> getReq('name');
    $lNewPath = $lPath.DS.$lName;

    clearstatcache();
    if (!file_exists($lNewPath)) {
//      $lUpload = rename($lPath, $lNewPath);

      if (!$lUpload) {
        $lParams = array('result' => $lRename, 'comment' => lan('lib.file.not.renamed'));
      } else {
        $lParams = array('result' => $lRename, 'comment' => lan('lib.file.renamed'));
      }
    } else {
      $lParams = array('result' => FALSE, 'comment' => lan('lib.file.not.exists'));
    }

    $lReturn = json_encode($lParams);

    echo $lReturn;
    exit;
  }

  protected function actRemoveFile() {
    $lPath = $this -> getReq('path');
    $lName = $this -> getReq('name');
    $lNewPath = $lPath.DS.$lName;

    $lFolder = new CApp_Finder('app');
    $lBase = rtrim($lFolder -> getPath(MID), DS);

    clearstatcache();
    if (file_exists($lBase.$lNewPath)) {
      unlink($lBase.$lNewPath);

      if (!$lUpload) {
        $lParams = array('result' => $lRename, 'comment' => lan('lib.file.not.removed'));
      } else {
        $lParams = array('result' => $lRename, 'comment' => lan('lib.file.removed'));
      }
    } else {
      $lParams = array('result' => FALSE, 'comment' => lan('lib.file.not.exists'));
    }
  
    $lReturn = json_encode($lParams);
  
    echo $lReturn;
    exit;
  }

  protected function actGetDirectories() {
    $lRelativeDirectory = $this -> getReq('relativedirectory');
    $lDirectoryName = $this -> getReq('directoryname');

    $lFolder = new CApp_Finder('app');
    $lBase = rtrim($lFolder -> getPath(MID), DS);

    $lTree = new CHom_Fil_Tree($lRelativeDirectory, $lDirectoryName);
    $lTree -> addRoot($lBase, lan('hom-fil.root'));
    $lTree -> render();
  }

  protected function actGetFiles() {
    $lRelativeDirectory = $this -> getReq('relativedirectory');
    $lDirectoryName = $this -> getReq('directoryname');

    $lFiles = new CHom_Fil_Files($this -> mMainDirectory.$lRelativeDirectory.$lDirectoryName);
    $lFiles -> render();
  }
}