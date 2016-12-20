<?php
class CInc_Job_Pro_Mod extends CJob_Mod {

  protected $mJobId;

  public function __construct($aJobId = 0) {
    parent::__construct('pro');
    $this -> mJobId = intval($aJobId);
    $this -> mTbl = 'al_job_pro_'.intval(MID);
    $this -> mInsertId = NULL;

    $lFie = CCor_Res::get('fie');
    foreach ($lFie as $lDef) {
      $this -> addField($lDef);
    }
    #$this -> addField(fie('id'));
  }

  protected function doUpdate() {
    foreach ($this -> mOld as $lKey => $lVal) {
      if ($this -> fieldHasChanged($lKey)) {
        $this -> mUpd[$lKey] = $this -> getVal($lKey);
      }
    }
    if (empty($this -> mUpd)) {
      return TRUE;
    }
    return $this -> forceUpdate($this -> mUpd);
  }

  public function forceUpdate($aArr = array()) {
    if (empty($aArr)) return TRUE;
    $lSql = 'UPDATE '.$this -> mTbl.' SET ';
    foreach ($aArr as $lKey => $lVal) {
      $lSql.= $lKey.'='.esc($lVal).',';
    }
    $lSql = strip($lSql, 1);
    $lSql.= ' WHERE id='.$this -> mJobId.' LIMIT 1';
    return CCor_Qry::exec($lSql);
  }

  protected function doInsert() {
    $this -> mVal['webstatus'] = 10;
    $lNow = date('Y-m-d H:i:s');
    $this -> mVal['last_status_change'] = $lNow;
    $this -> mVal['fti_1'] = $lNow;
    $this -> mVal['lti_1'] = $lNow;
    $lSql = 'INSERT INTO '.$this -> mTbl.' SET ';
    foreach ($this -> mVal as $lKey => $lVal) {
      if(!empty($lVal))
        $lSql.= $lKey.'='.esc($lVal).',';
    }
    $lSql = strip($lSql, 1);
    if ($this -> mTest) {
      $this -> dbg($lSql);
      return TRUE;
    } else {
      $lQry = new CCor_Qry();
      $lRet = $lQry -> query($lSql);
      if ($lRet) {
        $this -> mInsertId = $lQry -> getInsertId();
        $lMod = new CJob_His($this -> mSrc, $this -> mInsertId);
        $lMod -> add(htStatus, 'Project created', '', '', '','',10);
      } else {
        $this -> mInsertId = NULL;
      }
      return $lRet;
    }
  }

  protected function doDelete($aId) {
    $lSql = 'DELETE FROM '.$this -> mTbl.' ';
    $lSql.= 'WHERE id='.$this -> mJobId.' LIMIT 1';
    return CCor_Qry::exec($lSql);
  }

  public function getInsertId() {
    return $this -> mInsertId;
  }

  protected function checkProtocol() {
    parent::checkProtocol();
    if ($this -> fieldHasChanged('project_briefing')) {
      $lSub = 'Project Briefing changed';
      $this -> addHistory(htComment, $lSub);
      $lDat = new CJob_Pro_Dat();
      $lDat -> load($this -> mJobId);
      $lMsg = array('subject' => $lSub);
      $lEve = new CJob_Event(1, $lDat, $lMsg);
      $lEve -> execute();
    }
  }

  public static function reportDraft($aPid, $aSrc, $aJid) {
    $lSrc = $aSrc;
    $lTxt = lan('job-'.$lSrc.'.menu').' job created';
    if ('mas' == $lSrc) {
      $lSrc = 'art';
      $lTxt = 'Master Artwork created';
    }

    $lAdd = array('job' => array('src' => $lSrc, 'jid' => $aJid));
    $lHis = new CJob_His('pro', $aPid);
    $lHis -> add(htStatus, $lTxt, '', $lAdd);

    if ('adm' != $aSrc) {
      $lSql = 'UPDATE al_job_pro_'.intval(MID).' SET '.$aSrc.'_state=1 WHERE id='.intval($aPid);
      CCor_Qry::exec($lSql);
    }
  }

  protected function beforePost($aNew = FALSE) {
    $this -> setKeyword();
  }

  protected function setKeyword() {
    $lArr = CCor_Cfg::get('job-pro.keyw');
    $lRet = '';
    if ($this -> hasValues($lArr)) {
      foreach ($lArr as $lAlias) {
        $lVal = trim($this -> getVal($lAlias));
        $lRet = cat($lRet, $lVal, ' ');
      }
      $this -> mUpd['stichw'] = $lRet;
      $this -> setVal('stichw', $lRet);
    }
  }

}