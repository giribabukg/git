<?php
class CInc_Svc_Shadow extends CSvc_Base {

  protected function doExecute() {
    if (0 == MID) return TRUE;

    $lWriter = CCor_Cfg::get('job.writer.default', 'alink');
    if ('portal' == $lWriter) {
      $lIte = new CCor_TblIte('all');
    } else {
      $lIte = new CApi_Alink_Query_Getjoblist();
    }

    $lArr = array();
    $lFie = CCor_Res::getByKey('alias', 'fie');
    foreach ($lFie as $lKey => $lVal) {
      $lFla = $lVal['flags'];
      $lAva = $lVal['avail'];
      if (bitSet($lFla, ffReport) || (bitSet($lFla, ffReport) && bitSet($lAva, fsSub))) {
        $lArr[] = $lKey;
        if ('portal' == $lWriter) {
          $lIte -> addField($lKey);
        } else {
          $lIte -> addField($lKey, $lVal['native']);
        }
      }
    }

    $this->progressTick('load jobs');
    if (!$lIte -> query()) return FALSE;

    $lRes = $lIte -> getArray();
    $lCount = count($lRes);
    $lNum = 1;
    foreach ($lRes as $lRow) {
      $this->updateTable($lRow, 'shadow');
      $this->updateTable($lRow, 'sub');
      $this->progressTick('update '.$lNum.' of '.$lCount);
      $lNum++;
      if (!$this->canContinue()) {
        return TRUE;
    }
    }
    return TRUE;
  }
  
  protected function updateTable($aRow, $aTyp) {
    $lIsUpdate = FALSE;
    $lQry = new CCor_Qry();
    
    $lJid = $aRow['jobid'];
    $lJobIdFie = ($aTyp == 'sub' ? 'jobid_'.$aRow['src'] : 'jobid');
    
    $lSql = 'SELECT id FROM al_job_'.$aTyp.'_'.intval(MID).' WHERE '.$lJobIdFie.'='.esc($lJid);
    $lQry -> query($lSql);
    $lDat = $lQry -> getArray();
    $lNum = $lDat[0];
    if ($lNum) {
      $lSql = 'UPDATE al_job_'.$aTyp.'_'.intval(MID).' SET ';
      $lIsUpdate = TRUE;
    } else {
      $lSql = 'INSERT INTO al_job_'.$aTyp.'_'.intval(MID).' SET ';
      if($aTyp == 'sub') return TRUE;
    }
    foreach ($aRow as $lKey => $lVal) {
      if (!empty($lVal)) {
        if($aTyp == 'sub' AND $lKey == 'jobid'){
          $lKey = 'jobid_'.$aRow['src'];
        }
        if ($lIsUpdate AND $lKey == 'jobid') continue;
        if ($lKey == 'flags') continue;
        if (substr($lKey,0,4) == 'fti_') continue;
        if (substr($lKey,0,4) == 'lti_') continue;
        $lSql.= $lKey.'='.esc($lVal).',';
      }
    }
    $lSql = strip($lSql);
    if ($lNum) {
      $lSql.= ' WHERE id='.$lNum;
    }
    $lQry -> query($lSql);
  }
}