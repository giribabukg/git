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

}