<?php
/**
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 4987 $
 * @date $Date: 2014-07-03 18:49:55 +0800 (Thu, 03 Jul 2014) $
 * @author $Author: gemmans $
 */
class CInc_Job_Utl_Shadow extends CCor_Obj {

  public static function getFields() {
    $lRet = array();
    $lFie = CCor_Res::getByKey('alias', 'fie');
    foreach ($lFie as $lAli => $lDef) {
      $lAva = intval($lDef['flags']);
      if (bitset($lAva, ffReport)) {
        $lRet[$lAli] = $lDef;
      }
    }
    return $lRet;
  }

  public static function reflectUpdate($aSrc, $aJobId, $aArr) {
    if (empty($aArr) or empty($aJobId)) {
      return;
    }
    if (!in_array($aSrc, array('art', 'rep', 'mis', 'sec', 'adm', 'com', 'tra'))) {
      return;
    }
    $lDefs = self::getFields();
    $lUpd = array();
    $lSql = 'UPDATE al_job_shadow_'.intval(MID).' SET ';
    foreach ($aArr as $lKey => $lVal) {
      if (!isset($lDefs[$lKey])) {
        continue;
      }
      $lUpd[] = $lKey.'='.esc($lVal);
    }
    if (empty($lUpd)) {
      return;
    }
    $lSql.= implode(',', $lUpd);
    $lSql.= ' WHERE ';
    $lSql.= 'jobid='.esc($aJobId);
    CCor_Qry::exec($lSql);
  }

  public static function reflectInsert($aSrc, $aJobId, $aJobVal, $aAddVal) {
    $lDefs = self::getFields();
    $lUpd = array();
    $lSql = 'INSERT INTO al_job_shadow_'.intval(MID).' SET ';
    $lUpd['src'] = $aSrc;
    $lUpd['jobid'] = $aJobId;
    foreach ($aJobVal as $lKey => $lVal) {
      if (!isset($lDefs[$lKey])) {
        continue;
      }
      $lUpd[$lKey] = $lVal;
    }
    foreach ($aAddVal as $lKey => $lVal) {
      $lUpd[$lKey] = $lVal;
    }
    
    $lArr = array();
    foreach ($lUpd as $lKey => $lVal) {
      if (!empty($lVal)) {
        $lArr[] = $lKey.'='.esc($lVal);
      }
    }
    $lSql.= implode(',', $lArr);
    CCor_Qry::exec($lSql);

    $lTyp['art'] = 0;
    $lTyp['rep'] = 1;
    $lTyp['sec'] = 2;
    $lTyp['mis'] = 3;
    $lTyp['adm'] = 4;

    $lTid = (isset($lTyp[$aSrc])) ? $lTyp[$aSrc] : 0;
  }
  
  public static function reflectInsertReport($aSrc, $aJobId, $aJobVal) {
  	if (CCor_Cfg::get('extended.reporting')) {
  		$lSql = 'SELECT id FROM al_crp_master WHERE code="'.$aSrc.'" AND mand='.MID;
  		$lCrpId = CCor_Qry::getInt($lSql);
  		$lSql = 'SELECT report_map FROM al_crp_status WHERE crp_id='.$lCrpId.' AND mand='.MID.' AND status=10';
  		$lFirstStatus = CCor_Qry::getStr($lSql);
  		$lNow = date('Y-m-d H:i:s');
  		$lDefs = self::getFields();
  		$lUpd = array();
  		$lSql = 'INSERT INTO al_job_shadow_'.intval(MID).'_report SET ';
  		if (!empty($lFirstStatus)) {
  			$lSql.= 'row_id="1", fti_cr_'.$lFirstStatus.'="'.$lNow.'", ';
  		}
  		else $lSql.= 'row_id="1", ';
  		$lUpd['src'] = $aSrc;
  		$lUpd['jobid'] = $aJobId;
  		foreach ($aJobVal as $lKey => $lVal) {
  			if (!isset($lDefs[$lKey])) {
  				continue;
  			}
  			$lUpd[$lKey] = $lVal;
  		}
  		$lArr = array();
  		foreach ($lUpd as $lKey => $lVal) {
  			if (!empty($lVal)) {
  				$lArr[] = $lKey.'='.esc($lVal);
  			}
  		}
  		$lSql.= implode(',', $lArr);
  		CCor_Qry::exec($lSql);
  	}
  } 

  public static function reflectData($aJob) {
    $lDefs = self::getFields();
    $lUpd = array();
    foreach ($lDefs as $lAli => $lDef) {
      if (isset($aJob[$lAli])) {
        $lUpd[$lAli] = $aJob[$lAli];
      }
    }
    $lJid = $aJob['jobid'];
    if (empty($lUpd)) return;
    if (empty($lJid)) return;

    $lSql = 'SELECT COUNT(*) FROM al_job_shadow_'.intval(MID).' WHERE jobid='.esc($lJid);
    $lCnt = CCor_Qry::getStr($lSql);
    if (0 == $lCnt) {
      $lSql = 'INSERT INTO al_job_shadow_'.intval(MID).' SET jobid='.esc($lJid).',';
      foreach ($lUpd as $lAli => $lVal) {
        if (!empty($lVal)) {
          $lSql.= $lAli.'='.esc($lVal).',';
        }
      }
      $lSql = strip($lSql);
    } else {
      $lSql = 'UPDATE al_job_shadow_'.intval(MID).' SET ';
      foreach ($lUpd as $lAli => $lVal) {
        $lSql.= $lAli.'='.esc($lVal).',';
      }
      $lSql = strip($lSql);
      $lSql.= ' WHERE jobid='.esc($aJob['jobid']);
    }

    CCor_Qry::exec($lSql);
  }
  
  public function createNewReportRow($aJobId, $aSrc) {
  	if (CCor_Cfg::get('extended.reporting')) {  		
  		$lSql = 'SELECT row_id FROM al_job_shadow_'.intval(MID).'_report WHERE jobid="'.$aJobId.'" AND id=(SELECT MAX(id) FROM al_job_shadow_'.intval(MID).'_report WHERE jobid="'.$aJobId.'")';
  		$lRow_Id = CCor_Qry::getInt($lSql);
  		$lRow_Id = $lRow_Id+1;
  		$lSql = 'INSERT INTO al_job_shadow_'.intval(MID).'_report (src, jobid, row_id) VALUES ("'.$aSrc.'","'.$aJobId.'",'.$lRow_Id.')';
  		CCor_Qry::exec($lSql);
  	}  	
  }
  
  public static function reflectReportData($aJob) {
  	if (!CCor_Cfg::get('extended.reporting')) return;
  	$lJobId = $aJob['jobid'];
  	$lSql = 'SELECT MAX(id) FROM al_job_shadow_'.intval(MID).'_report WHERE jobid="'.$lJobId.'"';
  	$lMaxId = CCor_Qry::getInt($lSql);
  	if ($lMaxId == 0) self::createNewReportRow($lJobId, $aJob['src']); // if somone has deleted the Rows for this job from report table.

  	$lDefs = self::getFields();
  	$lUpd = array();
  	foreach ($lDefs as $lAli => $lDef) {
  		if (isset($aJob[$lAli])) {
  			$lUpd[$lAli] = $aJob[$lAli];
  		}
  	}  	
  	if (empty($lUpd)) return;
  	if (empty($lJobId)) return;
    	
  	$lSql = 'UPDATE al_job_shadow_'.intval(MID).'_report SET ';
  	foreach ($lUpd as $lAli => $lVal) {
  		$lSql.= $lAli.'='.esc($lVal).',';
  	}
  	$lSql = strip($lSql);
  	$lSql.= ' WHERE id='.$lMaxId.' AND jobid='.esc($aJob['jobid']);
  	CCor_Qry::exec($lSql);
  }
  
  
  public function setTimingInReportTable($aStepInfo, $aTime, $aJobId) {
  	if (!CCor_Cfg::get('extended.reporting')) return;
  	$lSql = 'SELECT MAX(id) FROM al_job_shadow_'.intval(MID).'_report WHERE jobid="'.$aJobId.'"';
  	$lMaxId = CCor_Qry::getInt($lSql);
  	if ($lMaxId == 0) return;
  	 
  	$lFromStatus = $aStepInfo['report_from'];
  	$lToStatus = $aStepInfo['report_to'];
  	 
  	$lLti = 'lti_cr_'.$lFromStatus;
  	$lFti = 'fti_cr_'.$lToStatus;
  	
  	if (!empty($lFromStatus)) {
  		$lSql = 'UPDATE al_job_shadow_'.intval(MID).'_report SET '.$lLti.'='.esc($aTime). ' WHERE id='.$lMaxId.' AND jobid='.esc($aJobId);
  		CCor_Qry::exec($lSql);
  	}
  	if (!empty($lToStatus)) {
  		$lQry = new CCor_Qry('SELECT '.$lFti.' FROM al_job_shadow_'.intval(MID).'_report WHERE jobid="'.$aJobId.'" AND id='.$lMaxId);
  		$lRow = $lQry -> getDat();
  		$lTim = $lRow[$lFti];
  		if (empty($lTim) OR $lTim == '0000-00-00 00:00:00') {
  			$lSql = 'UPDATE al_job_shadow_'.intval(MID).'_report SET '.$lFti.'='.esc($aTime). ' WHERE id='.$lMaxId.' AND jobid='.esc($aJobId);
  			CCor_Qry::exec($lSql);
  		}
  	}
  }
  
  public function setAmendRoutCause($aColumn,$aVal,$aJobId){
  	if (!CCor_Cfg::get('extended.reporting')) return;
  	$lSql = 'SELECT MAX(id) FROM al_job_shadow_'.intval(MID).'_report WHERE jobid="'.$aJobId.'"';
  	$lMaxId = CCor_Qry::getInt($lSql);
  	if ($lMaxId == 0) return;
  	$lSql = 'UPDATE al_job_shadow_'.intval(MID).'_report SET ';
  	$lSql.= backtick($aColumn).'='.esc($aVal);
  	$lSql.= ' WHERE `id`='.$lMaxId.' AND jobid="'.$aJobId.'"';
  	CCor_Qry::exec($lSql);
  
  }
  
}