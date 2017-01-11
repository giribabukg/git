<?php
/**
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 16940 $
 * @date $Date: 2016-10-24 16:00:22 +0200 (Mon, 24 Oct 2016) $
 * @author $Author: gemmans $
 */
class CInc_Job_Fil_List extends CCor_Ren {

  public function __construct($aSrc, $aJobId, $aSub = '', $aAge = 'job', $aWecUpload = TRUE) {
    $this -> mSrc = $aSrc;
    $this -> mMod = $aSrc.'-fil';
    $this -> mJobId = $aJobId;
    $this -> mSub = $aSub;
    $this -> mAge = $aAge;
    $this -> mWecUpload = $aWecUpload;

    $this -> mFil = new CJob_Fil_Tree();
    $this -> mBom = new CJob_Fil_Tree();

    $lNod = $this -> mFil -> getRoot();
    $lNod -> setExpanded(TRUE);

    $this -> mDefaultSub = CCor_Cfg::getFallback(
        'job-'.$this -> mSrc.'.files.show.default',
        'job.files.show.default',
        'pdf');

    $lUsr = CCor_Usr::getInstance();
    $this -> addFolders($lNod, $this -> mSrc, $this -> mJobId);

    $this -> mShowRelated = CCor_Cfg::get('job.files.show.related', true);

    if ($this -> mShowRelated) {
      $this -> addRelatedJobs();
      $this -> addRecentJobs();
      $this -> addBookmarkJobs();
    }
  }

  protected function addRelatedJobs() {
    $lArr = $this -> getRelatedJobs();
    if (!empty($lArr)) {
      foreach ($lArr as $lRow) {
        $lSrc = $lRow['src'];
        $lJid = $lRow['jobid'];
        if ($lJid == $this -> mJobId) continue;
        $this -> addRelated($lSrc, $lJid, $lRow['caption']);
      }
    }
  }

  protected function addRelated($aSrc, $aJobId, $aCaption = '') {
    if (!isset($this -> mRelated)) {
      $this -> mRelated = $this -> mBom -> add('Related Jobs');
    }
    $lCap = (empty($aCaption)) ? lan('job-'.$aSrc.'.item').' '.jid($aJobId, TRUE) : $aCaption;
    $lNod = $this -> mRelated -> add($lCap);
    $this -> addFolders($lNod, $aSrc, $aJobId);
  }

  protected function addRecentJobs() {
    $lUsr = CCor_Usr::getInstance();
    $lArr = $lUsr -> getRecentJobs(5);
    if (empty($lArr)) return;

    $lNod = $this -> mBom -> add(lan('lib.recent'));
    foreach ($lArr as $lRow) {
      $lSrc = $lRow['src'];
      $lJid = $lRow['jobid'];
      if ($lJid == $this -> mJobId) continue;
      $lJob = $lNod -> add(lan('job-'.$lSrc.'.item').' '.jid($lJid, TRUE), array('tip' => $lRow['keyword']));
      $this -> addFolders($lJob, $lSrc, $lJid);
    }
  }

  protected function addBookmarkJobs() {
    $lUsr = CCor_Usr::getInstance();
    $lArr = $lUsr -> getBookmarks();
    if (empty($lArr)) return;

    $lNod = $this -> mBom -> add(lan('job-bm.menu'));
    foreach ($lArr as $lRow) {
      $lSrc = $lRow['src'];
      $lJid = $lRow['jobid'];
      if ($lJid == $this -> mJobId) continue;
      $lJob = $lNod -> add(lan('job-'.$lSrc.'.item').' '.jid($lJid, TRUE), array('tip' => $lRow['keyword']));
      $this -> addFolders($lJob, $lSrc, $lJid);
    }
  }

  protected function getRelatedJobs() {
    return array();
  }

  protected function addFolders($aNode, $aSrc, $aJid) {
    $lFolder = new CJob_Fil_Folders($aSrc, $aJid);
    $lBase = array('src' => $aSrc, 'jid' => $aJid, 'age' => $this -> mAge);
    foreach ($lFolder as $lKey => $lCaption) {
      $lRow = $lBase;
      $lRow['sub'] = $lKey;
      $aNode -> add($lCaption, $lRow);
    }
    if (empty($this -> mSub)) {
      $this -> mSub = $this -> mDefaultSub;
    }
  }

  public function checkProject() {
    $lSql = 'SELECT pro_id FROM al_job_sub_'.intval(MID).' ';
    $lSql.= 'WHERE jobid_'.$this -> mSrc.'='.esc($this -> mJobId).' LIMIT 1';
    $lPid = CCor_Qry::getInt($lSql);
    if (!empty($lPid)) {
      $lNod = $this -> mFil -> getRoot();
      $lNod -> add(lan('job-pro.item'), array('src' => 'pro', 'jid' => $lPid, 'sub' => 'doc', 'age' => $this -> mAge));
    }
  }

  protected function getFileList($aDivId, $aUploadButton) {
    $lCls = 'CJob_Fil_Src_'.ucfirst($this -> mSub);
    $lLis = new $lCls($this -> mSrc, $this -> mJobId, $this -> mSub, $aDivId, '', $this -> mAge, $this -> mWecUpload, $aUploadButton);
    return $lLis -> getContent();
  }

  protected function getCont() {
    $lTid = getNum('t');

    $lRet = '';
    $lRet.= '<table cellpadding="2" cellspacing="0" class="tbl w100p">'.LF;

    // Job Files

    if ($this -> mShowRelated) {
      $lRet.= '<tr>';
      $lRet.= '<td valign="top" class="p0 w200">';
      $lRet.= '<div class="cap w200 cp" onclick="Flow.Std.togTr(\''.$lTid.'\')">&nbsp;</div>';
      $lRet.= '</td>';
      $lRet.= '<td valign="top" class="p0">';
      $lRet.= '<div class="cap cp" onclick="Flow.Std.togTr(\''.$lTid.'\')">'.htm(lan('job-fil.menu')).'</div>';
      $lRet.= '</td>';
      $lRet.= '</tr>';
    }

    $lRet.= '<tr id="'.$lTid.'" style="display:table-row">';
    $lRet.= '<td valign="top" class="td1 p0 w200">';
    $lRet.= '<div style="height:300px; overflow:auto;">';
    $lRet.= $this -> mFil -> getContent();
    $lRet.= '</div>';
    $lRet.= '</td>';

    $lFid = $this -> mFil -> getContentId();
    $lRet.= '<td valign="top" class="td1 p0">';
    $lRet.= '<div id="'.$lFid.'" style="margin-bottom:16px;">';
    $lRet.= $this -> getFileList($lFid, TRUE);
    $lRet.= '</div>';
    $lRet.= '</td>';
    $lRet.= '</tr>';

    if ($this -> mShowRelated) {
      $lTid = getNum('t');

      $lRet.= '<tr>';
      $lRet.= '<td valign="top" class="p0 w200">';
      $lRet.= '<div class="cap w200 cp" onclick="Flow.Std.togTr(\''.$lTid.'\')">&nbsp;</div>';
      $lRet.= '</td>';
      $lRet.= '<td valign="top" class="p0">';
      $lRet.= '<div class="cap cp" onclick="Flow.Std.togTr(\''.$lTid.'\')">'.htm(lan('job-fil.related')).'</div>';
      $lRet.= '</td>';
      $lRet.= '</tr>';

      $lRet.= '<tr id="'.$lTid.'" style="display:none">';
      $lRet.= '<td valign="top" class="td1 p0 w200">';
      $lRet.= '<div style="height:300px; overflow:auto;">';
      $lRet.= $this -> mBom -> getContent();
      $lRet.= '</div>';
      $lRet.= '</td>';

      $lFid = $this -> mBom -> getContentId();
      $lRet.= '<td valign="top" class="td1 p0">';
      $lRet.= '<div id="'.$lFid.'" style="margin-bottom:16px;">';
      $lRet.= $this -> getFileList($lFid, FALSE);
      $lRet.= '</div>';
      $lRet.= '</td>';
      $lRet.= '</tr>';
    }

    $lRet.= '</table>';
    return $lRet;
  }

}
