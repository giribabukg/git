<?php
/**
 * Webcenter File List Provider
 *
 * @package    Job
 * @subpackage Files
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 12378 $
 * @date $Date: 2016-02-02 12:05:11 +0100 (Tue, 02 Feb 2016) $
 * @author $Author: ahanslik $
 */
class CInc_Job_Fil_Src_Wec extends CJob_Fil_Files {

  public function __construct($aSrc, $aJobId, $aSub = 'wec', $aDiv = '', $aFrom = 'sub', $aAge = 'job', $aDebug = FALSE, $aUploadButton = TRUE) {
    $lSub = 'wec';

    parent::__construct($aSrc, $aJobId, $lSub, $aDiv, $aFrom, $aAge, TRUE, $aUploadButton);

    $this -> mSrc = $aSrc;
    $this -> mJobId = $aJobId;
    $this -> mSub = $aSub;
    $this -> mDiv = $aDiv;

    $lUsr = CCor_Usr::getInstance();

    $this -> mDebug = $aDebug; // wird bei Aufruf ueber URL mit sub=wec per $this->mWecUpload=TRUE gesetzt

    // Title und Upload im Header
    $this -> mTitle = lan('job-fil.olp');
    $this -> mUpload = FALSE;
    if ($this -> mUsr -> canEdit('wec.view')) {
      $this -> mCompare = TRUE;
    } else {
      $this -> mCompare = FALSE;
    }

    $this -> addCtr();
    if ($this->mCompare) {
      $this -> addColumn('sel', '', FALSE, array('width' => '16', 'valign' => 'middle', 'align' => 'center', 'id' => 'sel'));
    }
    $this -> addColumn('mor', '', FALSE, array('width' => '16', 'id' => 'mor'));

    $this -> addColumn('name', lan('lib.file.name'), FALSE, array('width' => '90%', 'id' => 'name'));
    if (!empty($this -> mCategory)) {
      $this -> addColumn('category', lan('lib.file.category'), FALSE, array('id' => 'category'));
    }
    $this -> addColumn('version', lan('lib.version'), FALSE, array('id' => 'version'));
    $this -> addColumn('size',  lan('lib.file.size'), FALSE, array('id' => 'size'));
    $this -> addColumn('date',  lan('lib.file.time.modification'), FALSE, array('id' => 'date'));

    // Get WebcenterId
    $lObj = new CApp_Wec($this -> mSrc,$this -> mJobId);
    $this -> mWecPid = $lObj -> getWebcenterId();
    $this -> dbg('Webcenter Projekt Id:'.$this -> mWecPid);

    $this -> mClient = new CApi_Wec_Client();
    $this -> mClient -> loadConfig();
    if (CCor_Usr::getAuthId() == 9) {
      $this -> mShowVersions = true;
    }

    $this -> mIte = $this -> getIterator();
  }

  public function getIterator() {
    $lUsr = CCor_Usr::getInstance();

    $lRet = array();
    if (empty($this -> mWecPid)) return $lRet;

    $lWec = $this -> mClient;

    $lWecFileListCurrentUser = CCor_Cfg::get('wec.filelist.currentuser', FALSE); // -1: no limit in the past
    //get Filelist with current User Or with 'wec.admin' from config.
    if ($lWecFileListCurrentUser){
      $lWecUsr = $this -> mUsr -> getInfo('wec_usr');
      $lWecPwd = $this -> mUsr -> getInfo('wec_pwd');
      $lWec -> setUser($lWecUsr, $lWecPwd);
    }

    $lQry = new CApi_Wec_Query_Doclist($lWec, $this -> mDebug);
    $lRet = $lQry -> getList($this -> mWecPid);
    return $lRet;
  }

  protected function getRows() {
    $lRet = '';
    $this -> mCtr = $this -> mFirst + 1;
    foreach ($this -> mIte as $this -> mRow) {
      // if (!$this -> mRow['viewer']) continue;
      $lRet.= $this -> beforeRow();
      $lRet.= $this -> getRow();
      $lRet.= $this -> afterRow();
    }
    return $lRet;
  }

  protected function getTdName(){
    $lRet = '';
    $lWecDocId = $this -> getVal('wec_doc_id');
    $lWecVerId = $this -> getVal('wec_ver_id');
    $lVie = $this -> getVal('viewer');
    $lNam = $this -> getVal('name');
    $lLink = $this -> getVal('link');

    $this -> mTheFileLink = '';
    if (!empty($lLink)) {
      $lLnk = $lLink;
      $lLnk.= $this -> mLnkSrcJobId;
    } else {
      $lLnk.= $this -> mLinkDefault.urlencode($lNam);
    }
    $lLnk = htm($lLnk);

    $lWecView = CCor_Cfg::get('wec.view', '');
    if (strstr($lWecView, 'win')) {
      $this -> mTheFileLink = '<a href="javascript:void(0);" onclick="window.open(\''.$lLnk.'\',\'\',\'dependent=no,hotkeys=yes,location=yes,menubar=yes,resizable=yes,scrollbars=yes,status=yes,toolbar=yes\');">';
    } elseif ($lWecView == 'tab') {
      $this -> mTheFileLink = '<a href="'.$lLnk.'" target="_blank">';
    } else $this -> mTheFileLink = '<a href="'.$lLnk.'">';

    $lRet.= $this -> mTheFileLink;
    $lRet.= htm($lNam);
    $lRet.= '</a>';

    if (!$lVie) {
      $lRet = htm($lNam)."&nbsp;<b><span style=\"color: #FF0000\">".lan('wec.rendering')."</span><b>";
    }

    return $this -> td($lRet, $lWecDocId);
  }

  protected function getMorJScript($aParam = array()) {
    $lTog = "Flow.Std.togWec('".$this -> mMoreId."',";
    $lTog.= "'".htm($aParam['wec_doc_id'])."',";
    $lTog.= "'".htm($this -> mJobId)."',";
    $lTog.= "'".htm($aParam['wec_ver_id'])."'";
    $lTog.= ")";
    $lRet = ' href="javascript:'.$lTog.'"';
    return $lRet;
  }

  protected function getTdMor() {
    $lParam = array();
    $lParam['wec_doc_id'] = $this -> getVal('wec_doc_id');
    $lParam['wec_ver_id'] = $this -> getVal('wec_ver_id');
    $lParam['name']       = $this -> getVal('name');
    $lParam['folder']     = $this -> getVal('folder');
    $lParam['author']     = $this -> getVal('author');
    $lParam['viewer']     = $this -> getVal('viewer');

    $this -> mAfterRow = true;
    if (empty($lParam['wec_doc_id'])) {
      $this -> mAfterRow = false;
      return $this -> td();
    }

    $this -> mMoreId = getnum('tr');
    $this -> mName   = $lParam['name'];
    $this -> mFolder = $lParam['folder'];
    $this -> mAuthor = $lParam['author'];
    $this -> mDocId  = $lParam['wec_doc_id'];
    $this -> mVerId  = $lParam['wec_ver_id'];
    $this -> mViewer = $lParam['viewer'];

    $lRet = '<a class="nav"'.$this -> getMorJScript($lParam).'>';
    $lRet.= '...</a>';
    return $this -> tdc($lRet);
  }

  # START #23645 "Webcenter compare function between different jobs
  # @deprecated
  protected function getTdSrc() {
    $lSrc = $this -> getVal('src');
    $lImg = (THEME === 'default' ? 'job-'.$lSrc : CApp_Crpimage::getColourForSrc($lSrc));
    $lRet = img('img/ico/16/'.$lImg.'.gif');
    return $this -> tdClass($this -> a($lRet), 'w16 ac');
  }

  protected function getTdJobId() {
    $lJobId = $this -> getVal('jobid');
    $lRet = ltrim($lJobId, '0');
    return $this -> td($lRet);
  }

  protected function getTdSel() {
    $lLink = $this -> getVal('link');
    $lPid = substr($lLink, strpos($lLink, '&pid=') + 5, 16);

    $lName = $this -> getVal('name');
    $lPidName = $lPid.'/'.$lName;
    $lRet = '<input class="wec-comp" type="checkbox" value="'.htm($lPidName).'" />';
    return $this -> tdc($lRet);
  }
  # STOP #23645 "Webcenter compare function between different jobs"

  protected function afterRow() {
    $lVie = $this -> getVal('viewer');
    $lRet = parent::afterRow();

    if ($this -> mAfterRow) {
      $lRet.= '<tr id="'.$this -> mMoreId.'" style="display:none">';
      $lRet.= '<td class="td1 tg">&nbsp;</td>';
      $lCol =  $this -> mColCnt - 1;
      $lRet.= '<td class="frm p0" colspan="'.$lCol.'">';
      $lRet.= '<div id="'.$this -> mMoreId.'_cnt">';

      $lRet.= '<script>jQuery(document).ready(function() {thumb = new Flow.wec.thumb("'.$this -> mJobId.'", "'.$this -> mDocId.'", "'.$this -> mVerId.'", "'.$this -> mName.'", "'.htmlentities($this -> mTheFileLink).'", "'.$this -> mMoreId.'");});</script>';

      $lRet.= '<table cellpadding="16" cellspacing="0" border="0"><tr>'.LF;
      $lRet.= ' <td class="w200" valign="top">';
      if ($lVie) {
        $lRet.= $this -> mTheFileLink;
      }
      $lRet.= img('img/pag/ajx.gif', array('class' => 'box', 'id' => $this -> mMoreId));
      $lRet.= ' </a>';
      $lRet.= ' </td>';
      $lRet.= ' <td valign="top">';

      $lRet.= '  <table cellpadding="4" cellspacing="0" border="0">';
      $lRet.= '  <tr><td class="b">Name</td><td>'.htm($this -> mName).'</td></tr>';
      $lRet.= '  <tr><td class="b">'.lan('lib.folder').'</td><td>'.htm($this -> mFolder).'</td></tr>';
      $lRet.= '  <tr><td class="b">Author</td><td>'.htm($this -> mAuthor).'</td></tr>';
      $lRet.= '  </table>';

      $lUsr = CCor_Usr::getInstance();

      if ($lUsr -> canRead('job-download-pdf')) {
        $lCanDownloadXfdf = $lUsr -> canRead('job-download-xfdf');
        $lXfdf = ($lCanDownloadXfdf) ? TRUE : FALSE;
        $lRet.= $this -> getWecVersions($this -> mDocId, $lXfdf);
      }

      $lRet.= ' </td>';
      $lRet.= '</tr></table>';

      $lRet.= '</div>';
      $lRet.= '</td>';
      $lRet.= '</tr>'.LF;
    }

    return $lRet;
  }

  /**
   *
   * @param $aDocId: is the Webcenter Document Id
   * @param boolean $aShowXfdfLink: FALSE to hide the xfdf download link
   * @param boolean $aHideOlderVersions: Set true to show all versions
   * @return HTML table with the download links
   */
  public function getWecVersions($aDocId, $aShowXfdfLink=TRUE, $aHideOlderVersions=FALSE) {
    $lQry = new CApi_Wec_Query_History($this->mClient);
    $lRes = $lQry -> getDocVersionIds($aDocId);
    if (empty($lRes)) return '';
    $lResult = ($aHideOlderVersions) ? array(max($lRes)) : $lRes;
    $lResultMax = array(max($lRes));

    $lDat = new CCor_Datetime();
    $lRet = BR.BR.'<table cellpadding="2" cellspacing="0" class="tbl w400"><tr>';
    $lRet.= '<td class="th2 ac">Version</td>';
    $lRet.= '<td class="th2">Date</td>';
    $lRet.= '<td class="th2">&nbsp;</td>';
    if ($aShowXfdfLink) {
      $lRet.= '<td class="th2">&nbsp;</td>';
    }
    $lRet.= '</tr>';
    foreach ($lResult as $lRow) {
      $lRet.= '<tr>';
      $lRet.= '<td class="td1 ac"><span class="app-version">'.$lRow['version'].'</span></td>';
      $lDat->setSql($lRow['date']);
      $lRet.= '<td class="td1">'.$lDat->getString().'</td>';
      $lUrl = 'index.php?act=utl-fil.wecversion&dvi='.$lRow['version_id'];
      $lUrl.= '&amp;src='.$this->mSrc.'&amp;jobid='.$this->mJobId;
      $lRet.= '<td class="td1 ac"><a href="'.$lUrl.'" class="nav">Download</a></td>';
      if($aShowXfdfLink) {
        $lUrl = 'index.php?act=utl-fil.wecxfdf&dvi='.$lRow['version_id'];
        $lUrl.= '&amp;wec_prj_id='.$this->mWecPid;
        $lRet.= '<td class="td1 ac"><a href="'.$lUrl.'" class="nav">Xfdf</a></td>';
      }
      $lRet.= '</tr>';
    }

    $lJobFac = new CJob_Fac($this -> mSrc, $this -> mJobId);
    $lJobDat = $lJobFac -> getDat();

    $lAppCondReg = new CApp_Condition_Registry();
    $lAppCOnd = $lAppCondReg -> loadFromDbByName('approvers');
    $lIsMet = FALSE;
    if ($lAppCOnd) {
      $lAppCOnd -> setContext('data', $lJobDat);
      $lIsMet = $lAppCOnd -> isMet();
    }
    
    if ($lIsMet) {
      $lValues = $lAppCOnd -> getValues();

      $lRet.= '<tr>';
      $lUrl = 'index.php?act=utl-fil.wecapprover&dvi='.$lResultMax[0]['version_id'].'&amp;src='.$this -> mSrc.'&amp;jobid='.$this -> mJobId.'&amp;aplid='.$lValues['id'];
      $lRet.= '<td colspan=4 class="td1 ac"><a href="'.$lUrl.'" class="nav">Download with approver names list</a></td>';
      $lRet.= '</tr>';
    } 

    $lRet.= '</table>';
    return $lRet;
  }


}