<?php
/**
 * Approval Loop Webcenter Image List
 *
 * Shows an expandable list of Webcenter-Files with links to the Webcenter
 * Viewer.
 *
 * @package    Job
 * @subpackage Approval Loop
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Job_Apl_Page_Images extends CCor_Ren {

  public $mPage; // For normally Jobmaske OR Apl Invitation Page

  public function __construct($aJob, $aPage = 'APL', $aUserAccess = TRUE) {
    $this -> mJob = $aJob;
    $this -> mPage = $aPage;
    if (!empty($this -> mJob)) {
      $this -> mJobId = $aJob['jobid'];
      $this -> mSrc = $aJob['src'];
      $this -> mWecPid = $this -> mJob['wec_prj_id'];
    }
    $lFolders = new CJob_Fil_Folders($this->mSrc, $this->mJobId);
    $this -> mViewer = $lFolders->getViewer();

    $this -> mUserAccess = $aUserAccess;
    $this -> mCanOpenViewer = true;
  }

  public function setCanOpenViewer($aFlag = true) {
    $this->mCanOpenViewer = (bool)$aFlag;
  }

  protected function getFiles() {
    if (empty($this->mViewer)) return;

    $lFunc = 'getFiles'.$this->mViewer;
    if ($this->hasMethod($lFunc)) {
      return $this->$lFunc();
    }
    return null;
  }

  protected function getFilesWec() {
    $lRet = array();
    if (empty($this -> mWecPid)) return $lRet;
    $lWec = new CApi_Wec_Client();
    $lWec -> loadConfig();
    $lQry = new CApi_Wec_Query_Doclist($lWec);
    $lRet = $lQry -> getList($this -> mWecPid);
    return $lRet;
  }

  protected function getFilesDalim() {
    $lRet = new CApi_Dalim_Files($this -> mSrc, $this -> mJobId);
    return $lRet;
  }

  protected function getFilesDms() {
    $lQry = new CApi_Dms_Query();
    #$lStub = new CApi_Dms_Stub(); $lQry->setClient($lStub);
    $lRes = $lQry->getFileList(MANDATOR_ENVIRONMENT, $this->mSrc, $this->mJobId, 0);
    return $lRes;
  }

  protected function getCont() {
    if (empty($this->mViewer)) return;

    $lFunc = 'getCont'.$this->mViewer;
    if ($this->hasMethod($lFunc)) {
      return $this->$lFunc();
    }
  }

  protected function getContDalim() {
    $lRet = '';
    $this->dbg('getContDalim');

    $lIte = $this -> getFiles();
    $lBasenames = $lIte->getFilenames();
    $this -> mDalimCount = $lIte->getFileVersionCount();
    $this -> mCanCompare = ( ($this -> mDalimCount > 1) && ($this->mCanOpenViewer) );

    if ($this->mCanCompare) {
      $lRet.= '<form action="index.php" method="post">';
      $lRet.= '<input type="hidden" name="act" value="utl-dalim.multipleapl" />';
      $lRet.= '<input type="hidden" name="src" value="'.$this->mSrc.'" />';
      $lRet.= '<input type="hidden" name="jid" value="'.$this->mJobId.'" />';
    }
    $lRet.= '<table cellpadding="2" cellspacing="0" class="tbl w300">'.LF;
    if($this->mPage == 'APL'){
      $lRet.= '<tr><td class="cap">'.htm(lan('job-fil.menu')).'</td></tr>';
    } else{
      #$lRet.= '<tr><td class="th1">'.htm(lan('job-fil.menu')).'</td></tr>';
    }

    $lDisplay = 'table-row';
    foreach ($lBasenames as $lName) {
      $lNum = getNum('t');

      $lRet.= '<tr>';
      $lRet.= '<td class="th2">';
      $lRet.= '<a href="javascript:Flow.Std.togTr(\''.$lNum.'\')" class="p4">';
      $lRet.= htm($lName);
      $lRet.= '</a>';
      $lRet.= '</td>';
      $lRet.= '</tr>'.LF;

      $lRet.= '<tr id="'.$lNum.'" style="display:'.$lDisplay.'">';
      $lRet.= '<td class="p0">';
      $lVersions = $lIte->getVersions($lName);
      $lRet.= $this->getVersionImages($lVersions);
      $lRet.= '</td>';

      $lDisplay = 'none';
    }
    $lRet.= '</table>'.LF;
    if ($this -> mCanCompare) {
      $lRet.= btn('Compare selected', 'this.form.submit()','ico/16/copy.gif', 'submit', array('class' => 'btn w300'));
      $lRet.= '</form>';
    }
    return $lRet;
  }

  protected function getVersionImages($aVersions) {
    $lRet = '';
    $lStyle = 'table-row';
    $lRet.= '<table class="w100p">';
    foreach ($aVersions as $lKey => $lRow) {
      $lNum = getNum('tr');

      $lRet.= '<tr>';

      if ($this -> mCanCompare) {
        $lRet.= '<td class="th3 w16 p0">';
        $lRet.= NB.'<input type="checkbox" name="doc[]" value="'.$lRow['name'].'"" />'.NB;
        $lRet.= '</td>';
      }

      $lRet.= '<td class="th3 p0">';
      $lRet.= '<a href="javascript:Flow.Std.togTr(\''.$lNum.'\')">';
      $lRet.= NB.NB.'Version&nbsp;&nbsp;<span class="app-version">'.$lKey.'</span>';
      $lRet.= '</a>';
      $lRet.= '</td>';
      $lRet.= '</tr>';

      $lRet.= '<tr style="display:'.$lStyle.'" id="'.$lNum.'">';
      if ($this->mCanCompare) {
        $lRet.= '<td class="td1 p16 ac" colspan="2">';
      } else {
        $lRet.= '<td class="td1 p16 ac">';
      }

      $lDoc = $this -> mJobId.'/'.$lRow['name'];
      if ($this->mCanOpenViewer) {
        $lChkListNewUrl = $this -> getCheckListUrl();
        $lLnk = 'index.php?act=utl-dalim.openapl&doc='.urlencode($lDoc).'&src='.$this->mSrc.'&jid='.$this->mJobId;
        $lRet.= '<a href="'.htm($lLnk).'" target="_blank" '.$lChkListNewUrl.'>';
#        $lRet.= '<a href="index.php?act=utl-dalim.openapl&amp;doc='.htm(urlencode($lDoc)).'&src='.$this->mSrc.'&jid='.$this->mJobId.'">';
        $lRet.= '<img class="box" src="index.php?act=utl-dalim.thumb&amp;doc='.htm(urlencode($lDoc)).'" />';
        $lRet.= '</a>';
      } else {
        $lRet.= '<img class="box" src="index.php?act=utl-dalim.thumb&amp;doc='.htm(urlencode($lDoc)).'" />';
      }
      $lDateFmt = date(lan('lib.datetime.short'), $lRow['date']);
      $lRet.= BR.'<span class="weak">'.$lDateFmt.'</span>';

      $lRet.= '</td>';
      $lStyle = 'none';
    }
    $lRet.= '</table>';
    return $lRet;
  }

  protected function getContWec() {
    #if (empty($this -> mWecPid)) return '';
    $this->dbg('getContWec');
    $lRet = '';
    if (!empty($this -> mJob)) { // Keine Anzeige der Datei
      $lFil = $this -> getFiles();

      $lRet.= '<table cellpadding="2" cellspacing="0" class="tbl w300">'.LF;
      if($this->mPage == 'APL'){
        $lRet.= '<tr><td class="cap">'.htm(lan('job-fil.menu')).'</td></tr>';
      } else{
        $lRet.= '<tr><td class="th1">'.htm(lan('job-fil.menu')).'</td></tr>';
      }

      $lFirst = true;
      if (!empty($lFil))
      foreach ($lFil as $lRow) {
        if (empty($lRow['link'])) continue;

        $lNum = getNum('t');

        $lRet.= '<tr>';
        $lRet.= '<td class="th2">';
        $lRet.= '<a href="javascript:Flow.Std.togWecSingle(\''.$lNum.'\',\''.$lRow['wec_doc_id'].'\')">';
        $lRet.= htm($lRow['name']);
        $lRet.= '</a>';
        $lRet.= '</td>';
        $lRet.= '</tr>'.LF;

        if ($lFirst) {
          $lDis = 'table-row';
          $lFirst = FALSE;
          $lImg = 'index.php?act=utl-wec.thumb&amp;doc='.$lRow['wec_doc_id'].'&amp;jobid='.$this -> mJobId;
        } else {
          $lDis = 'none';
          $lImg = 'img/ico/big/noimg.gif';
        }
        $lLnk = $lRow['link'];
        $lLnk.= '&src='.$this -> mSrc.'&jid='.$this -> mJobId;

        if ($this -> mPage == 'APL') { // IF Page= APL, redirect to APL Invitation Page
          $lLnk.= '&apl=1';
        } elseif ($this -> mPage == 'arc') { // IF Page= arc, redirect to Archivmaske Tab 'APL'
          $lLnk.= '&arcapl=1';
        } else { // else redirect to Jobmaske Tab 'APL'
          $lLnk.= '&jobapl=1';
        }
        if (!$this -> mUserAccess) {
          $lLnk.= '&nac=1';
        }

        $lRet.= '<tr id="'.$lNum.'" class="togtr" style="display:'.$lDis.'">';
        $lRet.= '<td class="td1 p16 ac">';
        $lRet.= '<div id="'.$lNum.'_cnt">';

        $lChkListNewUrl = $this -> getCheckListUrl();

        # 22758 "When reviewing a pdf can it automatically launch into a new window?"
        $lWecRes = '';
        $lWecView = CCor_Cfg::get('wec.view', '');
        if (strstr($lWecView, 'win')) {
          $lWecRes = '<a href="javascript:void(0);" onclick="window.open(\''.htm($lLnk).'\',\'\',\'dependent=no,hotkeys=yes,location=yes,menubar=yes,resizable=yes,scrollbars=yes,status=yes,toolbar=yes\');">';
        } elseif (strstr($lWecView, 'tab')) {
          $lWecRes = '<a href="'.htm($lLnk).'" target="_blank" '.$lChkListNewUrl.'>';
        } else {
          $lWecRes = '<a href="'.htm($lLnk).' "'.$lChkListNewUrl.'>';
        }
        # 22758 END

        if (!$this->mCanOpenViewer) $lWecRes = '<a name="x">';

        $lRet.= $lWecRes;
        // $lRet.= img($lImg, array('class'=>'box'));
        $lRet.= '<img src="'.$lImg.'" class="box" />';
        $lRet.= '</a>';
        $lRet.= '</div>';
        $lRet.= '<div class="ac p2" style="color:#999">'.htm($lRow['name']).'</div>';
        $lRet.= '<div class="ac p2" style="color:#999">'.$this -> getWecPdfDownloadList($lRow['wec_doc_id']).'</div>';
        $lRet.= '</td>';
        $lRet.= '</tr>'.LF;
      }

      $lRet.= '</table>'.LF;
    #  $lRet.= $this -> getWecPdfDownloadList($lRow['wec_doc_id']);
    }
    return $lRet;
  }

  protected function getWecPdfDownloadList($aDocId) {
    $lRet = '';
    $lUsr = CCor_Usr::getInstance();

    if ($lUsr -> canRead('job-download-pdf')) {
      $lCanDownloadXfdf = $lUsr -> canRead('job-download-xfdf');
      $lWecList = new CInc_Job_Fil_Src_Wec($this -> mSrc, $this -> mJobId);
      $lRet.= $lWecList -> getWecVersions($aDocId, $lCanDownloadXfdf, TRUE);
    }

    return $lRet;
  }

  protected function getContDms() {
    $lRet = '';
    $this->dbg('getContDms');

    $lIte = $this -> getFilesDms();

    $lRet.= '<table cellpadding="2" cellspacing="0" class="tbl w300">'.LF;
    if($this->mPage == 'APL'){
      $lRet.= '<tr><td class="cap">'.htm(lan('job-fil.menu')).'</td></tr>';
    }
    $lDisplay = 'table-row';
    foreach ($lIte as $lRow) {
      $lNum = getNum('t');
      $lName = $lRow['filename'];
      $lDocVerId = $lRow['fileversionid'];
      $lVersionRow = $lRow['versions'][$lDocVerId];

      $lIsLocked = (isset($lVersionRow['locked_by']));
      if ($lIsLocked) {
        $lLockedBy = $lVersionRow['locked_by'];
        $lLockedSince = $lVersionRow['locked_since'];
      }

      $lRet.= '<tr>';
      $lRet.= '<td class="th2">';
      $lRet.= '<a href="javascript:Flow.Std.togTr(\''.$lNum.'\')" class="p4">';
      $lRet.= htm($lName);
      $lRet.= '</a>';
      $lRet.= '</td>';
      $lRet.= '</tr>'.LF;

      $lRet.= '<tr id="'.$lNum.'" style="display:'.$lDisplay.'">';
      $lRet.= '<td class="p16 ac box">';

      $lDate = $lRow['date'];
      $lDate = new CCor_Datetime($lDate);
      $lRet.= '<span class="app-version">Version '.$lRow['version'].'</span>'.NB;
      $lRet.= '<span class="weak">'.$lDate->getFmt('d.m.Y').'</span>'.BR.BR;

      if (($this->mCanOpenViewer) && !$lIsLocked) {
        $lRet.= '<a href="index.php?act=utl-dms.open&amp;docverid='.$lDocVerId.'&amp;lock=1&amp;fn='.urlencode($lName).'">';
        $lRet.= $this->getDmsImage($lName);
        $lRet.= '</a>';
      } else {
        $lRet.= $this->getDmsImage($lName);
      }
      $lRet.= BR;

      if ($lIsLocked) {
        $lRet.= '<span class="app-version">Locked by '.$lLockedBy.'</span>';
      }

      $lRet.= '</td>';

      $lDisplay = 'none';
    }
    $lRet.= '</table>'.LF;
    return $lRet;
  }

  protected function getDmsImage($aFilename) {
    $lExt = pathinfo($aFilename, PATHINFO_EXTENSION);
    $lExt = strtolower(substr($lExt,0,3));
    $lImg = (in_array($lExt, array('doc', 'xls', 'ppt'))) ? $lExt : 'doc';
    $lRet = img('img/ico/big/mime-'.$lImg.'.png');
    return $lRet;
  }

  protected function getCheckListUrl() {
    if (!CCor_Cfg::get('use.checklist')) return;
    $lApl = new CApp_Apl_Loop($this -> mSrc, $this -> mJobId, 'apl');
    $lUser = CCor_Usr::getInstance();
    $lChk = new CApp_Chk_Check($this -> mSrc, $this -> mJobId, $this -> mJob);
    $lUid = $lUser -> getId();
    $lIsUserActiveNow = $lApl -> isUserActiveNow($lUid);
    $doUserHasToCheck = $lChk -> doUserHasCheckList();
    $lChkListNewUrl = '';
    if ($lIsUserActiveNow AND $doUserHasToCheck) {
      $lLastOpenLoopId = $lApl -> getLastOpenLoop();
      $lChkListUrl = 'index.php?act=utl-chk.edt&jobid='.$this -> mJobId.'&src='.$this -> mSrc.'&loopid='.$lLastOpenLoopId;
      $lChkListNewUrl = 'onclick="pop(\''.$lChkListUrl.'\')"';
    }
    return $lChkListNewUrl;
  }

}