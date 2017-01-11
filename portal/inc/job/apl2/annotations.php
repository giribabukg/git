<?php
class CInc_Job_Apl2_Annotations extends CCor_Obj {

  protected static $mInstances;

  protected function __construct($aSrc, $aJid) {
    $this->mSrc = $aSrc;
    $this->mJid = $aJid;

    $this->loadAnnotations();
  }

  public static function getInstance($aSrc, $aJid) {
    $lKey = $aSrc.'_'.$aJid;
    if (!isset(self::$mInstances[$lKey])) {
      self::$mInstances[$lKey] = new self($aSrc, $aJid);
    }
    return self::$mInstances[$lKey];
  }


  protected function loadAnnotations() {
    $lSql = 'SELECT * FROM al_dalim_notes WHERE jobid='.esc($this->mJid);
    $lSql.= 'ORDER BY doc,id';
    $lNum = 1;
    $lOldDoc = '';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      if ($lRow['doc'] != $lOldDoc) {
        $lOldDoc = $lRow['doc'];
        $lNum = 1;
      }
      $lRow['num'] = $lNum;
      $this->mAll[] = $lRow;
      $lNum++;
    }
    //var_dump($this->mAll);
  }

  public function getByUser($aUid, $aLoopId = null, $aDoc = null) {
    //echo "Getting annotations for user $aUid in Loop $aLoopId".BR;
    $lLid = intval($aLoopId);
    $lUid = intval($aUid);

    $lDoc = '';
    if (!empty($aDoc)) {
      $lVol = CCor_Cfg::get('dalim.volume', 'A');
      $lDoc = $lVol.'/'.$aDoc;
    }
    $this->dbg('Getting for '.$lDoc);

    $lRet = array();
    foreach ($this->mAll as $lRow) {
      if (!empty($lLid)) {
        if ($lLid != $lRow['loop_id']) continue;
      }
      if (!empty($lDoc)) {
        if ($lDoc != $lRow['doc']) continue;
      }
      if ($lUid != $lRow['user_id']) continue;
      $lRet[] = $lRow;
    }
    return $lRet;
  }

  protected function loadPhraseAnnotations()
  {
    if (isset($this->mPhraseAnnot)) {
      return;
    }

    $lHtb = CCor_Res::get('categories');

    $lSql = 'SELECT n.*, c.`category` FROM `al_cms_notes` n ';
    $lSql.= 'LEFT JOIN `al_cms_ref_category` c ON (n.`content_id`=c.`content_id`) ';
    $lSql.= 'WHERE n.`jobid`='.esc($this->mJid);

    $lRet = array();
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lUid = $lRow['user_id'];
      $lSid = $lRow['sub_loop_id'];

      $lCat = $lRow['category'];
      $lRow['category_name'] = isset($lHtb[$lCat]) ? $lHtb[$lCat] : $lCat;
      $lRet[$lUid][$lSid][] = $lRow;
    }
    $this->mPhraseAnnot = $lRet;
  }

  protected function getStatusImage($aState) {
    $lRet = img('img/ico/16/flag-0'.$aState.'.gif');
    return $lRet;
  }

  public function getPhraseTableByUser($aUid, $aSid) {
    if (!isset($this->mPhraseAnnot)) {
      $this->loadPhraseAnnotations();
    }
    if (!isset($this->mPhraseAnnot[$aUid][$aSid])) {
      return '';
    }
    $lArr = $this->mPhraseAnnot[$aUid][$aSid];
    $lRet = '';
    $lRet.= '<table class="tbl w600">';
    $lRet.= '<tr><th class="th3 w16">&nbsp;</th>';
    $lRet.= '<th class="th3">Content</th>';
    $lRet.= '<th class="th3">'.htm(lan('lib.msg')).'</th></tr>';
    foreach ($lArr as $lRow) {
      $lRet.= '<tr>';
      $lRet.= '<td class="p4">';
      $lRet.= $this->getStatusImage($lRow['status']);
      $lRet.= '</td>';
      $lRet.= '<td class="nw p4">';
      $lRet.= htm($lRow['category_name']).NB;
      $lRet.= '</td>';
      $lRet.= '<td class="w400 p4">';
      $lComment = $lRow['comment'];
      if (strlen($lComment) > 40) {
        $lRet .= toolTip($lComment);
        $lRet .= htm(substr($lRow['comment'],0,40)).'...';
        $lRet.= '</span>';
      } else {
        $lRet.= htm($lComment).NB;
      }
      $lRet.= '</td>';
      $lRet.= '</tr>';
    }
    $lRet.= '</table>';
    return $lRet;
  }

}
