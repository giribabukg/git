<?php
class CInc_Arc_Fil_List extends CCor_Ren {

  public function __construct($aSrc, $aJobId, $aSub = '') {
    #parent::__construct();
    $this -> mSrc = $aSrc;
    $this -> mMod = $aSrc.'-fil';
    $this -> mJobId = $aJobId;
    $this -> mSub = $aSub;

    $this -> mFil = new CArc_Fil_Tree();
    $this -> mBom = new CArc_Fil_Tree();

    $lNod = $this -> mFil -> getRoot();
    $lNod -> setExpanded(TRUE);
    if ('pro' != $this -> mSrc) {
      if (empty($this -> mSub)) {
        $this -> mSub = 'pdf';
      }
      $lSub = $lNod -> add('Latest PDF', array('src' => $this -> mSrc, 'jid' => $this -> mJobId, 'sub' => 'pdf'));
      $lNod -> add('OnlineProof', array('src' => $this -> mSrc, 'jid' => $this -> mJobId, 'sub' => 'rtp'));
      $lDoc = $lNod -> add('Documents', array('src' => $this -> mSrc, 'jid' => $this -> mJobId, 'sub' => 'doc'));
    } else {
      if (empty($this -> mSub)) {
        $this -> mSub = 'doc';
      }
      $lDoc = $lNod -> add('Documents', array('src' => $this -> mSrc, 'jid' => $this -> mJobId, 'sub' => 'doc'));
    }

    $lUsr = CCor_Usr::getInstance();
    $lArr = $lUsr -> getRecentJobs();

    $lNod = $this -> mBom -> add('Recent Jobs');
    foreach ($lArr as $lRow) {
      $lSrc = $lRow['src'];
      $lJid = $lRow['jobid'];
      if ($lJid == $this -> mJobId) continue;
      $lJob = $lNod -> add(lan('job-'.$lSrc.'.menu').' '.jid($lJid, TRUE), array('tip' => $lRow['keyword']));
      if ($lSrc != 'pro') {
        $lJob -> add('Latest PDF', array('src' => $lSrc, 'jid' => $lJid, 'sub' => 'pdf'));
        $lJob -> add('OnlineProof', array('src' => $lSrc, 'jid' => $lJid, 'sub' => 'rtp'));
      }
      $lJob -> add('Documents', array('src' => $lSrc, 'jid' => $lJid, 'sub' => 'doc'));
    }
    $lNod = $this -> mBom -> add('Bookmarks');
    $lArr = $lUsr -> getBookmarks();
    foreach ($lArr as $lRow) {
      $lSrc = $lRow['src'];
      $lJid = $lRow['jobid'];
      if ($lJid == $this -> mJobId) continue;
      $lJob = $lNod -> add(lan('job-'.$lSrc.'.menu').' '.jid($lJid, TRUE), array('tip' => $lRow['keyword']));
      if ($lSrc != 'pro') {
        $lJob -> add('Latest PDF', array('src' => $lSrc, 'jid' => $lJid, 'sub' => 'pdf'));
        $lJob -> add('OnlineProof', array('src' => $lSrc, 'jid' => $lJid, 'sub' => 'rtp'));
      }
      $lJob -> add('Documents', array('src' => $lSrc, 'jid' => $lJid, 'sub' => 'doc'));
    }

  }

  protected function addMainFolder($aCaption, $aSub) {
    $lNod = $this -> mFil -> getRoot();
    $lNod -> add($aCaption, array('src' => $this -> mSrc, 'jid' => $this -> mJobId, 'sub' => $aSub));
  }

  protected function addRelated($aSrc, $aJobId, $aCaption = '') {
    if (!isset($this -> mRelated)) {
      $this -> mRelated = $this -> mBom -> add('Related Jobs');
    }
    $lCap = (empty($aCaption)) ? lan('job-'.$aSrc.'.menu').' '.jid($aJobId, TRUE) : $aCaption;
    $lNod = $this -> mRelated -> add($lCap);
    $lNod -> add('Documents', array('src' => $aSrc, 'jid' => $aJobId, 'sub' => 'doc'));
    if ($aSrc != 'pro') {
      $lNod -> add('PDF', array('src' => $aSrc, 'jid' => $aJobId, 'sub' => 'pdf'));
      $lNod -> add('Proof', array('src' => $aSrc, 'jid' => $aJobId, 'sub' => 'rtp'));
    }

  }

  protected function addJobDir($aSrc, $aJobId, $aCaption) {
    $lFin = new CApp_Finder($aSrc, $aJobId);
    $this -> addDir($lFin -> getPath(), $aCaption);
  }

  protected function getFileList($aDivId) {
    $lLis = new CArc_Fil_Files($this -> mSrc, $this -> mJobId, $this -> mSub, $aDivId);
    return $lLis -> getContent();
  }

  protected function getCont() {
    $lRet = '';
    $lRet.= '<table cellpadding="2" cellspacing="0" class="tbl w100p">'.LF;

    // Job Files

    $lTid = getNum('t');
    $lRet.= '<tr>';
    $lRet.= '<td valign="top" class="p0 w200">';
    $lRet.= '<div class="cap w200 cp" onclick="Flow.Std.togTr(\''.$lTid.'\')">Job Related</div>';
    $lRet.= '</td>';
    $lRet.= '<td valign="top" class="p0">';
    $lRet.= '<div class="cap cp" onclick="Flow.Std.togTr(\''.$lTid.'\')">Files</div>';
    $lRet.= '</td>';
    $lRet.= '</tr>';

    $lRet.= '<tr id="'.$lTid.'" style="display:table-row">';
    $lRet.= '<td valign="top" class="td1 p0 w200">';
    $lRet.= '<div style="height:200px; overflow:auto;">';
    $lRet.= $this -> mFil -> getContent();
    $lRet.= '</div>';
    $lRet.= '</td>';

    $lFid = $this -> mFil -> getContentId();
    $lRet.= '<td valign="top" class="td1 p0">';
    $lRet.= '<div id="'.$lFid.'" style="margin-bottom:16px;">';
    $lRet.= $this -> getFileList($lFid);
    $lRet.= '</div>';
    $lRet.= '</td>';
    $lRet.= '</tr>';


    // Bookmarks

    $lTid = getNum('t');
    $lRet.= '<tr>';
    $lRet.= '<td valign="top" class="p0 w200">';
    $lRet.= '<div class="cap w200 cp" onclick="Flow.Std.togTr(\''.$lTid.'\')">Other Files</div>';
    $lRet.= '</td>';
    $lRet.= '<td valign="top" class="p0">';
    $lRet.= '<div class="cap cp" onclick="Flow.Std.togTr(\''.$lTid.'\')">Files</div>';
    $lRet.= '</td>';
    $lRet.= '</tr>';

    $lRet.= '<tr id="'.$lTid.'" style="display:table-row">';
    $lRet.= '<td valign="top" class="td1 p0 w200">';
    $lRet.= '<div style="height:200px; overflow:auto;">';
    $lRet.= $this -> mBom -> getContent();
    $lRet.= '</div>';
    $lRet.= '</td>';
    $lRet.= '<td valign="top" class="td1 p0">';
    $lRet.= '<div id="'.$this -> mBom -> getContentId().'" style="margin-bottom:16px;"></div>';
    $lRet.= '</td>';
    $lRet.= '</tr>';


    $lRet.= '</table>';
    return $lRet;
  }

}