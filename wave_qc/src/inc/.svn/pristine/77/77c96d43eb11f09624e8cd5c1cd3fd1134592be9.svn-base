<?php
class CInc_Job_Fil_Src_Dms extends CJob_Fil_Files {

  public function __construct($aSrc, $aJobId, $aSub = 'doc', $aDiv = '', $aFrom = 'sub', $aAge = 'job', $aDebug = FALSE, $aUploadButton = TRUE) {
    $lSub = 'dms';


    parent::__construct($aSrc, $aJobId, $lSub, $aDiv, $aFrom, $aAge, FALSE, $aUploadButton);

    // Title und Upload im Header
    // weil Dokumente und Projekt die gleiche Sub 'doc' benutzt, muss fuer Titel nach Src nachgefragt werden.
    $lDelAllowed = false;

    $this -> mTitle = lan('job-fil.'.$this -> mSub);
    if ('arc' != $this -> mAge) {
      $lDelAllowed = true;
    }

    $this -> mCompare = FALSE;

    $this -> addColumn('mor');
    $this -> addColumn('name', lan('lib.file.name'), TRUE, array('width' => '90%', 'id' => 'name'));
    $this -> addColumn('version', lan('lib.file.version'), TRUE, array('id' => 'version'));
    $this -> addColumn('size', lan('lib.file.size'), TRUE, array('id' => 'size'));
    $this -> addColumn('user', lan('lib.file.user'), TRUE, array('id' => 'user'));
    $this -> addColumn('date', lan('lib.file.date'), TRUE, array('id' => 'date'));
    if ($lDelAllowed AND $this -> mUsr -> canDelete('job-doc')) {
      $this -> addDel();
    }
    $lUsr = CCor_Usr::getInstance();
    $this -> mIte = $this -> getIterator();
    $this -> mGroupClass = uniqid('c_');
    if (!bitset($this -> mFlags, jfOnhold)) {
      $this -> mUpload = $lUsr -> canInsert('job-dms');
    }
    $this -> mIsFirst = true;
  }

  protected function getUploadButton() {
    $lRet = '';
    return '';

    $lParams = array(
      'act' => 'job-fil.upload',
      'src' => $this -> mSrc,
      'jid' => $this -> mJobId,
      'sub' => $this -> mSub,
      'div' => $this -> mDiv,
      'age' => 'job',
      'loading_screen' => TRUE
    );
    $lParamsJSONEnc = json_encode($lParams);

    $lRet.='<td align="right">';
    $lJs = 'Flow.Std.ajxUpd('.$lParamsJSONEnc.');';
    $lRet.= btn('Upload and unlock', $lJs, 'img/ico/16/new-hi.gif');
    $lRet.= '</td>';
    return $lRet;
  }

  public function getIterator() {
    $lQry = new CApi_Dms_Query();
    #$lStub = new CApi_Dms_Stub(); $lQry->setClient($lStub);
    $lRes = $lQry->getFileList(MANDATOR_ENVIRONMENT, $this->mSrc, $this->mJobId, 1);
    #var_dump($lRes);

    $lRet = array();
    if (empty($lRes)) return $lRet;

    foreach ($lRes as $lRow) {
      $lRow['name'] = $lRow['filename'];
      $lRow['user'] = $lRow['author'];
      $lRet[] = $lRow;
    }

    #var_dump($lRet);
    return $lRet;
  }

  protected function getTdName(){
    $lRet = '';
    $lNam = $this -> getVal('name');
    $lDisplay = $this -> getVal('display');

    $lRet = '<a class="nav" onclick="Flow.togTrOne(\''.$this -> mMoreId.'\',\''.$this -> mGroupClass.'\')">';
    $lRet.= htm($lNam);
    $lRet.= '</a>';
    return $this -> td($lRet);
  }

  protected function getTdMor() {
    $this -> mMoreId = uniqid('tr_');
    $lRet = '<a class="nav" onclick="Flow.togTrOne(\''.$this -> mMoreId.'\',\''.$this -> mGroupClass.'\')">';
    $lRet.= '...</a>';
    return $this -> tdc($lRet);
  }

  protected function fmtDate($aTimestamp) {
    $date = new CCor_Datetime($aTimestamp);
    return $date ->getFmt(lan('lib.datetime.short'));
  }

  protected function afterRow() {
    $lStyle = $this->mIsFirst ? 'table-row' : 'none';
    $this -> mIsFirst = false;

    $lRet = '<tr style="display:'.$lStyle.'" id="'.$this->mMoreId.'" class="'.$this -> mGroupClass.'">';

    $lRet.= '<td class="td1 tg">&nbsp;</td>';
    $lRet.= '<td class="p0" colspan="5">';

    $lRet.= '<table cellpadding="2" cellspacing="0" border="0" class="w100p">';
    $lRows = $this->mRow['versions'];
    //var_dump($lRows);
    $this -> mSubGroupClass = uniqid('c_');
    krsort($lRows);
    $lIsFirst = true;
    foreach ($lRows as $lRow) {
      $lRet.= $this->getVersionRow($lRow, $lIsFirst);
      $lIsFirst = false;
    }
    $lRet.= '</table>';

    $lRet.= '</td>';
    $lRet.= '</tr>';
    return $lRet;
  }

  protected function getVersionRow($aRow, $aIsFirst) {
    $lName = $aRow['filename'];
    $lDocVerId = $aRow['fileversionid'];
    $lUrl = 'index.php?act=utl-dms.open&docverid='.$lDocVerId.'&fn='.urlencode($lName);

    $lRet = '';
    $lRet.= '<tr>';

    $lMoreId = uniqid('tr_');

    $lRet.= '<td class="td1 w16 ac">';

    $lLnk = '<a class="nav" onclick="Flow.togTrOne(\''.$lMoreId.'\',\''.$this -> mSubGroupClass.'\')">';
    $lRet.= $lLnk;
    $lRet.= '...</a></td>';
    $lRet.= '<td class="td1 p4">'.$lLnk.'Version <span class="app-version">'.htm($aRow['version']).'</span>';
    $lRet.= ' '.NB.NB.'<span class="weak">'.htm(lan('lib.user')).' '.htm($aRow['author']);
    $lRet.= ' '.NB.NB.'<span class="weak">'.htm(lan('lib.date')).' '.htm($this -> fmtDate($aRow['date']));

    $lTopVersion = $this->mRow['maxversion'];
    if ($lTopVersion == $aRow['version']) {
      if (!empty($aRow['locked_by'])) {
        $lRet.= ' '.NB.NB.'<span class="app-version">Locked</span><span class="weak"> by '.htm($aRow['locked_by']).' since '.htm($this -> fmtDate($aRow['locked_since']));

      }
    }
    $lRet.= '</span></a></td>';

    $lRet.= '</tr>'.LF;

    $lStyle = $aIsFirst ? 'table-row' : 'none';
    $lRet.= '<tr style="display:'.$lStyle.'" id="'.$lMoreId.'" class="'.$this->mSubGroupClass.'"><td class="td1 tg">&nbsp;</td>';
    $lRet.= '<td colspan="4" class="p4 td1">';

    $lRet.= '<div class="w200 p8" style="float:left">';
    $lRet.= '<a href="'.$lUrl.'">';
    $lDoc = $this->mJobId.'/'.$lName;
    $lRet.= $this->getDmsImage($lName);
    $lRet.= '</a>';
    $lRet.= '</div>';

    $lRet.= '<div class="w200 p8" style="float:left">';

    $lRet.= btn('Download', 'go(\''.$lUrl.'\',\'tab\')', 'ico/16/pdf.png', 'button', array('class'=> 'btn w300'));
    $lRet.= BR.BR;

    $lTopVersion = $this->mRow['maxversion'];
    if (empty($aRow['locked_by']) && ($lTopVersion == $aRow['version'])) {
      $lRet.= btn('Download and Lock', 'go(\''.$lUrl.'&lock=1\',\'tab\');setTimeout(\'window.location.href=window.location.href\', 100)', 'ico/16/pdf_annotated.png', 'button', array('class'=> 'btn w300'));
      $lRet.= BR.BR;
    }
    $lUsr = CCor_Usr::getInstance();
    $lDir = '/media/dmspdf/';
    $lPdf = $lDocVerId.'_'.$lName.'.pdf';
    if (file_exists($lDir.$lPdf)) {
      $lPdfUrl = 'index.php?act=utl-dms.openpdf&docverid='.$lDocVerId.'&fn='.urlencode($lName);
      $lRet.= btn('Download as PDF', 'go(\''.$lPdfUrl.'\',\'tab\');setTimeout(\'window.location.href=window.location.href\', 100)', 'ico/16/pdf_annotated.png', 'button', array('class'=> 'btn w300'));
    }

    $lLockedBy = $aRow['locked_by'];
    $lUid = CCor_Usr::getAuthId();
    if ( ($lUid == 541) || (!empty($lLockedBy) && ($lTopVersion == $aRow['version']))) {

    //if (!empty($lLockedBy) && ($lTopVersion == $aRow['version'])) {
      $lName = $lUsr->getVal('fullname');
      if (($lLockedBy == $lName) || ($lUid == 541) ) {
        $lRet.= $this->getUploadUnlockButton();
      }
    }
    /*
    if (empty($aRow['locked_by']) && ($lTopVersion == $aRow['version'])) {
      $lUrl = 'index.php?act=utl-dms.edit&docverid='.$lDocVerId.'&fn='.urlencode($lName);
      $lRet.= btn('Edit Online', 'go(\''.$lUrl.'&edit=1\',\'tab\');setTimeout(\'window.location.href=window.location.href\', 100)', 'ico/16/edit.gif', 'button', array('class'=> 'btn w300'));
      $lRet.= BR.BR;
    }
    */

    $lRet.= '</div>';

    $lRet.= '<div style="clear:both"></div>';

    $lRet.= '</td></tr>'.LF;
    return $lRet;
  }

  protected function getDmsImage($aFilename) {
    $lExt = pathinfo($aFilename, PATHINFO_EXTENSION);
    $lExt = strtolower(substr($lExt,0,3));
    $lImg = (in_array($lExt, array('doc', 'xls', 'ppt'))) ? $lExt : 'doc';
    $lRet = img('img/ico/big/mime-'.$lImg.'.png');
    return $lRet;
  }

  protected function getUploadUnlockButton() {
    $lParams = array(
      'act' => 'job-fil.upload',
      'src' => $this -> mSrc,
      'jid' => $this -> mJobId,
      'sub' => 'dms',
      'div' => $this -> mDiv,
      'age' => 'job',
      'loading_screen' => TRUE
    );
    $lParamsJSONEnc = json_encode($lParams);

    $lJs = 'Flow.Std.ajxUpd('.$lParamsJSONEnc.');';
    $lRet = btn('Upload and Unlock', $lJs, 'img/ico/16/new-hi.gif', 'button', array('class'=> 'btn w300'));
    return $lRet;
  }
}