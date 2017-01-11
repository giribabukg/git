<?php
class CInc_Hom_Fil_Tree extends CHtm_Tree_List {

  protected $mRootDirectory = NULL;

//   protected $mSelectedRelativeDirectory = NULL;
//   protected $mSelectedDirectoryName = NULL;

//   public function __construct($aSelectedRelativeDirectory = NULL, $aSelectedDirectoryName = NULL) {
//     parent::__construct();

//     $this -> mSelectedRelativeDirectory = $aSelectedRelativeDirectory;
//     $this -> mSelectedDirectoryName = $aSelectedDirectoryName;
//   }

  public function addRoot($aRootDirectory, $aRootName) {
    $this -> mRootDirectory = $aRootDirectory;

    $lNode = $this -> mRoot -> add($aRootName, array('dir' => $aRootDirectory));
    $lNode -> setExpanded(TRUE);

    $this -> addFolder($lNode, $aRootDirectory);
  }

  protected function addFolder($aNode, $aFolder) {
    $lIterator = new DirectoryIterator($aFolder);
    foreach ($lIterator as $lItem) {
      if ($lItem -> isDot()) {
        continue;
      }

      if ($lItem -> isFile()) {
        continue;
      }

      $lDirectoryName = $lItem -> getFilename();
      $lDirectory = $aFolder.DS.$lDirectoryName;

      $lRootDirectory = preg_quote($this -> mRootDirectory, DS);
      $lRelativeDirectory = preg_replace('/^'.$lRootDirectory.'/', '', $lDirectory);
      $lRelativeDirectory = preg_replace('/'.$lDirectoryName.'$/', '', $lRelativeDirectory);

      $lNode = $aNode -> add($lDirectoryName, array('dir' => $lDirectory, 'relativedirectory' => $lRelativeDirectory, 'directoryname' => $lDirectoryName));
      $this -> addFolder($lNode, $lDirectory);
    }
  }

  protected function getNodeSpan($aNode) {
    $lTag = new CHtm_Tag('span');
    $lTag -> setAtt('class', 'nav nw');
    $lTag -> setAtt('id', $aNode -> getId().'s');

    $lJS = '';
    $lJS.= 'Flow.Files.clickDirectory(this);';
    if ($aNode -> hasChildren()) {
      $lJS.= 'jQuery(this).closest(\'li\').find(\'ul\').first().toggle();';
    }

    $lRelativeDirectory = $aNode -> getVal('relativedirectory');
    if (!empty($lRelativeDirectory)) {
      $lTag -> setAtt('data-relativedirectory', $lRelativeDirectory);
    }

    $lDirectoryName = $aNode -> getVal('directoryname');
    if (!empty($lDirectoryName)) {
      $lTag -> setAtt('data-directoryname', $lDirectoryName);
    }

    $lTag -> setAtt('onclick', $lJS);

// error_log(print_r($this -> mSelectedRelativeDirectory, TRUE).LF, 3, "D:/AMH/getNodeSpan_04.txt");

//     if ($aNode -> getVal('caption') != '' && $aNode -> getVal('caption') != DS && $this -> mSelectedRelativeDirectory != '') {
//       $lMatch = strpos($this -> mSelectedRelativeDirectory, $aNode -> getVal('caption'));
//       if ($lMatch !== FALSE) {
//         $lll = $aNode -> getParent();
//         $aNode -> setExpanded(TRUE);
//       } else {
//         $aNode -> setExpanded(FALSE);
//       }
//     }

    $lRet = $lTag -> getTag();
    $lRet.= htm($aNode -> getVal('caption'));
    $lRet.= '</span>';
    return $lRet;
  }
}