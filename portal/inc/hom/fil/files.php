<?php
class CInc_Hom_Fil_Files extends CHtm_List {

  public function __construct($aMainDirectory) {
    parent::__construct('hom-fil');

    $this -> setAtt('class', 'w800');

    $this -> mShowHdr = FALSE;
    $this -> mShowSubHdr = FALSE;
    $this -> mShowSerHdr = FALSE;
    $this -> mShowColHdr = TRUE;

    $this -> addCtr();
    $this -> addChk();
    $this -> addColumn('name', lan('lib.file.name'),              TRUE, array('width' => '100%', 'id' => 'name'));
    $this -> addColumn('size', lan('lib.file.size'),              TRUE, array('width' => '16',   'id' => 'size'));
    $this -> addColumn('time', lan('lib.file.time.modification'), TRUE, array('width' => '16',   'id' => 'time'));
    $this -> addColumn('user', lan('lib.file.user'),              TRUE, array('width' => '16',   'id' => 'user'));
    $this -> addColumn('view', lan('lib.file.view'), TRUE, array('width' => '16',   'id' => 'view'));
    $this -> addDel();

    $this -> mIte = $this -> getIterator($aMainDirectory);
  }

  public function getIterator($aFolder) {
    $lRows = array();
    if (file_exists($aFolder)) {
      $lUserList = CCor_Res::extract('id', 'fullname', 'usr');
      $lMandatorList = CCor_Res::extract('id', 'name_'.LAN, 'mand');
      $lCategoryList = CCor_Qry::getArrImp('SELECT id, value_'.LAN.' FROM al_htb_itm WHERE mand IN (0,'.MID.') AND domain="fil"');

      $lResult = array();
      $lSQL = 'SELECT * FROM al_job_files WHERE mand IN (0,'.MID.') AND sub="app";';
      $lQuery = new CCor_Qry($lSQL);
      foreach ($lQuery as $lRow) {
        $lResult[$lRow['pathname']][$lRow['filename']]['id'] = $lRow['id'];
        $lResult[$lRow['pathname']][$lRow['filename']]['user'] = $lUserList[$lRow['uid']];
        $lResult[$lRow['pathname']][$lRow['filename']]['mandator'] = $lMandatorList[$lRow['mand']];
        $lResult[$lRow['pathname']][$lRow['filename']]['category'] = $lCategoryList[$lRow['category']];
        $lResult[$lRow['pathname']][$lRow['filename']]['comment'] = $lRow['txt'];
      }

      try {
        $lIterator = new DirectoryIterator($aFolder);
        foreach ($lIterator as $lRow) {
          $lItem = array();
          if ($lIterator -> isFile()) {
            // PHP specific file information
            $lItem['atime'] = $lIterator -> getATime();
            $lItem['basename'] = $lIterator -> getBasename();
            $lItem['ctime'] = $lIterator -> getCTime();
            $lItem['extension'] = $lIterator -> getExtension();
            $lItem['filename'] = $lIterator -> getFilename();
            $lItem['mtime'] = $lIterator -> getMTime();
            $lItem['path'] = substr($lIterator -> getPath(), -1) == '/' ? $lIterator -> getPath() : $lIterator -> getPath().'/';
            $lItem['permissions'] = $lIterator -> getPerms();
            $lItem['size'] = $lIterator -> getSize();
            $lItem['type'] = $lIterator -> getType();

            // portal specific file information
            $lItem['id'] = $lResult[$lItem['path']][$lItem['filename']]['id'];
            $lItem['user'] = $lResult[$lItem['path']][$lItem['filename']]['user'];
            $lItem['mandator'] = $lResult[$lItem['path']][$lItem['filename']]['mandator'];
            $lItem['category'] = $lResult[$lItem['path']][$lItem['filename']]['category'];
            $lItem['comment'] = $lResult[$lItem['path']][$lItem['filename']]['comment'];

            $lRows[] = $lItem;
          }
        }
      } catch (Exception $lException) {
      }
    }
    return $lRows;
  }

  protected function getTdChk() {
    $lID = $this -> getVal('id');

    $lReturn = '<input type="checkbox" id="file" data-id="'.$lID.'"/>';
    return $this -> tdc($lReturn);
  }

  protected function getTdName() {
    $lReturn = $this -> getVal('filename');
    return $this -> td($lReturn);
  }

  protected function getTdSize() {
    $lReturn = $this -> getVal('size');
    return $this -> td($this -> fmtSize($lReturn));
  }

  protected function getTdTime() {
    $lReturn = $this -> getVal('mtime');
    return $this -> td($this -> fmtTime($lReturn));
  }

  protected function getTdUser() {
    $lReturn = $this -> getVal('user');
    return $this -> td($lReturn);
  }
  
  protected function getTdView() {
    $lFileName = $this -> getVal('filename');
    $lSub = $this -> getVal('category');

    $lFile = 'index.php?act=utl-fil.view&src=app&sub='.$lSub.'&fn='.urlencode($lFileName);
    $lReturn = '<td class="'.$this -> mCls.($this -> mHighlight ? 'r': '').' nw w16 ac">'.LF;
    $lReturn.= '<a class="nav" href="'.$lFile.'" target="_BLANK">';
    $lReturn.= '<i class="ico-w16 ico-w16-search"></i>'.LF;
    $lReturn.= '</a>'.LF;
    $lReturn.= '</td>'.LF;
    return $lReturn;
  }

  protected function getTdDel() {
    $lFileName = $this -> getVal('filename');

    $lReturn = '<td class="'.$this -> mCls.($this -> mHighlight ? 'r': '').' nw w16 ac">'.LF;
    $lReturn.= '<a class="nav" href="javascript:Flow.Files.removeFile(\''.$lFileName.'\')">'.LF;
    $lReturn.= '<i class="ico-w16 ico-w16-del"></i>'.LF;
    $lReturn.= '</a>'.LF;
    $lReturn.= '</td>'.LF;
    return $lReturn;
  }

  protected function fmtSize($aBytes) {
    $lBytes = $aBytes;

    $lReturn = $lBytes.' Bytes';

    $lkB = 1024;
    if ($lBytes > 1024) {
      $lReturn = number_format($lBytes / $lkB, 1).' kB';
    }

    $lMB = 1024 * 1024;
    if ($lBytes > $lMB) {
      $lReturn = number_format($lBytes / $lMB, 1).' MB';
    }

    $lGB = 1024 * 1024 * 1024;
    if ($lBytes > $lGB) {
      $lReturn = number_format($lBytes / $lGB, 1).' GB';
    }

    $lTB = 1024 * 1024 * 1024 * 1024;
    if ($lBytes > $lTB) {
      $lReturn = number_format($lBytes / $lTB, 1).' TB';
    }

    return $lReturn;
  }

  protected function fmtTime($aTimestamp) {
    $lTimestamp = $aTimestamp;

    $lReturn = date(lan('lib.datetime.short'), $lTimestamp);

    return $lReturn;
  }
}