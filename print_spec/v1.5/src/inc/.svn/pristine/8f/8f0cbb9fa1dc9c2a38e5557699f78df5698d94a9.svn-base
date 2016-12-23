<?php
class CInc_Api_Alink_Query_Getjobdetails extends CApi_Alink_Query {

  protected $mFie;
  protected $mCnd;
  protected $mSrc;
  protected $mArchives;
  protected $mMand;
  protected $mCndMaster;
  private $mCndOnly1 = array();

  public function __construct($aJobId, $aSrc = '', $aArchives = FALSE) {
    parent::__construct('getJobDetails');
    $this -> addParam('jobid', $aJobId);
    $this -> mFie = array();
    $this -> mCnd = array();
    $this -> mMand = MAND;

    $this -> mSrc = $aSrc;
    $this -> mJobId = $aJobId;

    $this -> mArchives = $aArchives;
    $this ->dbg('CApi_Alink_Query_Getjobdetails: '.$aJobId.' '.$aSrc);

    $this -> mDefs = CCor_Res::extract('alias', 'native', 'fie');
    $this -> mCndMaster = CCor_Res::extract('id', 'natived', 'cndmaster');

    $this -> addUserConditions();
    if (false != CCor_Cfg::get('extcnd')) {
      $this -> addGroupConditions();
    }
    $this->mWriter = CCor_Cfg::get('job.writer.default', 'alink');
    if ('mop' == $this->mWriter) {
      $this->addMopCondition();
    }
  }
    
  protected function addMopCondition() {
    $lCid = CCor_Cfg::get('mop.clientid', MID);
    $this -> addCondition('Job#clientid','=', $lCid);
    $this -> addCondition('Job#webstatus','>', 0);
    
    if($this -> mArchives == FALSE){ //set condition to only find active jobs if not reusing archive job
      $this -> addCondition('Job#webstatus','<', 200);
    }
  }

  public function setMand($aMand) {
    $this -> mMand = $aMand;
  }

  protected function checkBnrCondition() {
    $lUseFilter = CCor_Cfg::get('alink.bnr.filter', true);

    // Get Customer Nummer
    $lCustNo = Array();
    $lCustNo = CCor_Cfg::get($this -> mMand.'.alink.knr', array());
    if (empty($lCustNo)){
      $this->msg(lan('lib.no.custnr'), mtDebug, mlWarn);
    }
    $lCustNoArr = array_map("esc", $lCustNo);//jedes Element wird ".mysql_escaped."
    // to get format Bnr in ("CustomerNr_1","CustomerNr_2"...)
    $lCustNoStr = implode(',', $lCustNoArr);
    if (empty($lCustNoStr)) $lUseFilter = false;

    $lWriter = CCor_Cfg::get('job.writer.default');
    if ('alink' != $lWriter) $lUseFilter = false;

    if ($lUseFilter) {
      $this -> addBnrCondition($lCustNoStr);
    }
  }

  protected function addBnrCondition($aCustNrList) {
    $this -> addCondition('BNr','IN', $aCustNrList);
  }

  public function addUserConditions() {
    if (false != CCor_Cfg::get('extcnd')) {
      $lUid = CCor_Usr::getAuthId();
      $lUsrCond = CCor_Res::get('cnd','uid');
      if (!empty($lUsrCond) AND isset($lUsrCond[$lUid]) AND !empty($lUsrCond[$lUid])) {
        $lCond = $lUsrCond[$lUid];
        if (!empty($lCond['cond']) && $lCond['cnd_id'] == 0) {
          $lArr = explode(';', $lCond['cond']);
          foreach ($lArr as $lVal) {
            list($lField, $lOp, $lValue) = explode(' ', $lVal, 3);
            $this -> addCondition($lField, $lOp, $lValue);
            $this -> addField($lField, $this -> mDefs[$lField]);
  }
        } elseif ($lCond['cnd_id'] > 0) {
          $lCndMaster = $this -> mCndMaster;
          if (!empty($lCndMaster) AND isset($lCndMaster[ $lCond['cnd_id'] ]) AND !empty($lCndMaster[ $lCond['cnd_id'] ])) {
            $lNatived = $lCndMaster[ $lCond['cnd_id'] ];
            $this -> addCndEx($lNatived);
          }

          $lQry = new CCor_Qry('SELECT field FROM al_cnd_items WHERE cnd_id='.$lCond['cnd_id']);
          foreach ($lQry as $lRow) {
            $this -> addField($lRow['field'], $this -> mDefs[$lRow['field']]);
          }
        }
      }//end_if (!empty($lUsrCond) AND isset($lUsrCond[$lUid]) AND !empty($lUsrCond[$lUid]))
    } else {
    $lUid = CCor_Usr::getAuthId();
    $lSql = 'SELECT * FROM al_cnd WHERE usr_id='.$lUid.' AND mand='.MID;
    $lQry = new CCor_Qry($lSql);

    foreach ($lQry as $lRow) {
      if ($lRow['cond'] !== ''){
        $lArr = explode(';', $lRow['cond']);
        foreach ($lArr as $lVal) {
        list($lField,$lOp,$lValue) = explode(' ', $lVal,3);
        $this -> addCondition($lField,$lOp,$lValue);
            $this -> addField($lField, $this -> mDefs[$lField]);
        }
      }
    }
  }
  }

  public function addGroupConditions() {
    $lUid = CCor_Usr::getAuthId();
    $lMemberOf = CCor_Res::get('mem','uid');
    if (!empty($lMemberOf) AND isset($lMemberOf[$lUid]) AND !empty($lMemberOf[$lUid])) {
      $lGroups = $lMemberOf[$lUid];
      foreach ($lGroups as $lGid) {
        $lGruCond = CCor_Res::get('cnd','gid');
        if (!empty($lGruCond) AND isset($lGruCond[$lGid]) AND !empty($lGruCond[$lGid])) {
          $lCond = $lGruCond[$lGid];
          if (!empty($lCond['cond']) && $lCond['cnd_id'] == 0) {
            $lArr = explode(';', $lCond['cond']);
            foreach ($lArr as $lVal) {
              list($lField, $lOp, $lValue) = explode(' ', $lVal, 3);
              $this -> addCondition($lField, $lOp, $lValue);
            }
          } elseif ($lCond['cnd_id'] > 0) {
            $lCndMaster = $this -> mCndMaster;
            if (!empty($lCndMaster) AND isset($lCndMaster[ $lCond['cnd_id'] ]) AND !empty($lCndMaster[ $lCond['cnd_id'] ])) {
              $lNatived = $lCndMaster[ $lCond['cnd_id'] ];
              $this -> addCndEx($lNatived);
            }

            $lQry = new CCor_Qry('SELECT field FROM al_cnd_items WHERE cnd_id='.$lCond['cnd_id']);
            foreach ($lQry as $lRow) {
              $this -> addField($lRow['field'], $this -> mDefs[$lRow['field']]);
            }
          }
        }//end_if (!empty($lGruCond) AND isset($lGruCond[$lGid]) AND !empty($lGruCond[$lGid]))
      }//end_foreach ($lGroups as $lGid)
    } //end_if (!empty($lMemberOf) AND isset($lMemberOf[$lUid]) AND !empty($lMemberOf[$lUid]))
  }

  public function addField($aAlias, $aNative) {
    $this -> mFie[$aAlias] = $aNative;
  }

  public function addDef($aDef) {
    $this -> addField($aDef['alias'], $aDef['native']);
  }

  public function addCnd($aCnd) {
    $this -> mCnd[] = $aCnd;
  }

  public function addCndEx($aCnd) {
    if (!isset($this -> mCndOnly1[$aCnd])) {
      $this -> mCndOnly1[$aCnd] = $aCnd;

      $lTmp = array();
      $lTmp['term'] = $aCnd;
      $this -> mCnd[] = $lTmp;
    }
  }

  public function addCondition($aField, $aOp, $aValue) {
    $lTmp = array();
    $lFie = (isset($this -> mDefs[$aField])) ? $this -> mDefs[$aField] : $aField;
    $lTmp['field'] = $lFie;
    $lTmp['op']    = strtolower($aOp);
    $lTmp['value'] = $aValue;
    $this -> mCnd[] = $lTmp;
  }

  public function GetWebStatus() {
    $lStatus = '';
    $lIdsArr = array();
    $lStaArr = array();
    if (defined('MIGRATION') OR ($this -> mArchives == TRUE)) {
      $lStaArr[] = '0';
      $lStaArr[] = STATUS_ARCHIV;
    } elseif ($this -> mSrc !== '') {
      $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
      $lCrpId = $lCrp[ $this -> mSrc ];
      // nicht jeder CRP hat job2arc!
      $lSql = 'SELECT DISTINCT `to_id` FROM `al_crp_step` WHERE `mand`='.MID.' AND `crp_id`='.$lCrpId.' AND `trans` like "job2arc"';
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        $lIdsArr[] = $lRow['to_id'];
      }
      if (!empty($lIdsArr)) {
        $lIdsArr = array_map("esc", $lIdsArr);
        $lIdsStr = implode(',', $lIdsArr);
        $lIdsStr = ' AND id NOT IN ('.$lIdsStr.')';
      } else {
        $lIdsStr = '';
      }
      $lSql = 'SELECT `status` FROM `al_crp_status` WHERE `mand`='.MID.' AND `crp_id`='.$lCrpId.$lIdsStr;
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        $lStaArr[] = $lRow['status'];
      }
    }
    if (!empty($lStaArr)) {
      $lStatus = implode(',', $lStaArr);
    }

    return $lStatus;
  }

  public function query() {
    $this -> mLoaded = TRUE;
    $this -> mMaxCount = 0;
    if (empty($this -> mFie)) {
      return array();
    }
    $lFie = array();
    foreach($this -> mFie as $lKey => $lVal) {
      $lDef = array();
      $lDef['alias'] = $lKey;
      $lDef['native'] = $lVal;
      $lFie[] = $lDef;
    }
    $this -> addParam('fields', $lFie);
    $this -> addParam('sid', $this -> mMand);

    $this -> checkBnrCondition();

    if (!empty($this -> mCnd)) {
      $this -> addParam('where', $this -> mCnd);
    }

    $lWebStaList = $this -> GetWebStatus();
    if ($lWebStaList !== '') {
      $this -> addParam('webstatuslist', $lWebStaList);
    }

    parent::query();
    $lErr = $this -> mResponse -> getVal('errno');
    if (0 != $lErr) return FALSE;
    
	$lDat = $this -> getDat();
	if(sizeof($lDat) == 1) return FALSE; //check if data is passed back for job (always passes back jobid)
	
    return $this -> mResponse;
  }

  public function getDat() {
    $lRet = new CCor_Dat();
    $lDat = $this -> mResponse -> getVal('fields');

    if (!empty($lDat)) {
      foreach ($lDat[0] as $lKey => $lVal) {
        $lRet[$lKey] = (string)$lVal;
      }
    }
    return $lRet;
  }

}