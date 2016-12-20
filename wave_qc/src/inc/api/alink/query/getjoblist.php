<?php
class CInc_Api_Alink_Query_Getjoblist extends CApi_Alink_Query implements IteratorAggregate {

  protected $mFie;
  protected $mOrd;
  protected $mTyp;
  protected $mDir;
  protected $mCnd;
  protected $mLimit;
  protected $mSrc;
  protected $mMaxCount = 0;
  protected $mLoaded = FALSE;
  protected $mSid;
  protected $mWithoutLimit = FALSE;// Get Iterator without User Limit(x.lpp),i.e CSV Export.
  protected $mMNr;
  protected $mKNr;
  protected $mCndMaster;
  private $mCndOnly1 = array();

  public function __construct($aSrc = '', $aWithoutLimit = FALSE, $aMNr = '', $aKNr = '') {
    parent::__construct('getJobList');
    $this -> mWithoutLimit = $aWithoutLimit;
    $this -> mMNr = $aMNr;
    $this -> mKNr = $aKNr;

    $this -> mFie = array();
    $this -> mCnd = array();

    $this -> mSrc = $aSrc;

    $this -> mDefs = CCor_Res::extract('alias', 'native', 'fie');
    $this -> mTyps = CCor_Res::extract('alias', 'typ', 'fie');
    $this -> mCndMaster = CCor_Res::extract('id', 'natived', 'cndmaster');

    // Get Customer Nummer
    $lCustNo = CCor_Cfg::get(MAND.'.alink.knr', array());
    if (empty($lCustNo)) {
      $this -> msg(lan('lib.no.custnr'), mtDebug, mlWarn);
    }

    if (empty($lCustNo) && !empty($this -> mMNr) && !empty($this -> mKNr)) {
      $lCustNo = $aKNr;
    }

    $this->mWriter = CCor_Cfg::get('job.writer.default', 'alink');
    if ('alink' == $this->mWriter) {
      $lUseFilter = CCor_Cfg::get('alink.bnr.filter', true);

      $lCustNoArr = array_map("esc", $lCustNo); //jedes Element wird ".mysql_escaped."
      // to get format Bnr in ("CustomerNr_1","CustomerNr_2"...)
      $lCustNoStr = implode(',', $lCustNoArr);
      if (empty($lCustNoStr)) {
        $lUseFilter = false;
      }
      if ($lUseFilter) {
        $this -> addBnrCondition($lCustNoStr);
      }
    } elseif ('mop' == $this->mWriter) {
      $lCid = CCor_Cfg::get('mop.clientid', MID);
      $this -> addCondition('Job#clientid','=', $lCid);
      $this->mTrans = array(
          'jobid' => 'Job#id',
          'webstatus' => 'Job#webstatus',
          'status' => 'Job#status'
      );
      $this -> addCondition('Job#webstatus','>', 0);
      $this -> addCondition('Job#webstatus','<', 200);
    }

    $this -> addUserConditions();
    if (false != CCor_Cfg::get('extcnd')) {
      $this -> addGroupConditions();
    }
  }

  protected function addBnrCondition($aCustNrList) {
    $this -> addCondition('BNr','IN', $aCustNrList);
  }

  public function addUserConditions($aUId = null) {
    if (false != CCor_Cfg::get('extcnd')) {
      if (is_null($aUId)) {
        $lUId = CCor_Usr::getAuthId();
      } else {
        $lUId = $aUId;
      }
      $lUsrCond = CCor_Res::get('cnd', 'uid');
      if (!empty($lUsrCond) AND isset($lUsrCond[$lUId]) AND !empty($lUsrCond[$lUId])) {
        $lCond = $lUsrCond[$lUId];
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
      } //end_if (!empty($lUsrCond) AND isset($lUsrCond[$lUId]) AND !empty($lUsrCond[$lUId]))
    } else {
      if (is_null($aUId)) {
        $lUId = CCor_Usr::getAuthId();
      } else {
        $lUId = $aUId;
      }
      $lSql = 'SELECT * FROM al_cnd WHERE usr_id='.$lUId.' AND mand='.MID;
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

  public function addGroupConditions($aUId = null) {
    if (is_null($aUId)) {
      $lUId = CCor_Usr::getAuthId();
    } else {
      $lUId = $aUId;
    }
    $lMemberOf = CCor_Res::get('mem','uid');
    if (!empty($lMemberOf) AND isset($lMemberOf[$lUId]) AND !empty($lMemberOf[$lUId])) {
      $lGroups = $lMemberOf[$lUId];
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
    } //end_if (!empty($lMemberOf) AND isset($lMemberOf[$lUId]) AND !empty($lMemberOf[$lUId]))
  }

  protected function translateNative($aNative) {
    if ('mop' != $this->mWriter) {
      return $aNative;
    }
    $lRet = $aNative;
    $lNative = strtolower($aNative);
    if (isset($this->mTrans[$lNative])) {
      $lRet = $this->mTrans[$lNative];
    }
    return $lRet;
  }

  public function addField($aAlias, $aNative) {
    $lNat = $this->translateNative($aNative);
    $this -> mFie[$aAlias] = $lNat;
  }

  public function addDef($aDef) {
    $lNat = $this->translateNative($aDef['native']);
    $this -> addField($aDef['alias'], $lNat);
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
    $lFie = $this->translateNative($lFie);
    $lTmp['field'] = $lFie;
    $lTmp['op']    = strtolower($aOp);
    $lTmp['value'] = $aValue;
    $this -> mCnd[] = $lTmp;
    if (isset($this -> mDefs[$aField])) {
      $this -> addField($aField, $lFie);
    }
  }

  public function setOrder($aField, $aDir = '') {
    if (isset($this -> mDefs[$aField])) {
      $lNat = $this->translateNative($this -> mDefs[$aField]);
      $this -> mOrd = $lNat;
      $this -> mTyp = $this -> mTyps[$aField];
      $this -> mDir = $aDir;
      $this -> addField($aField, $lNat);
    }
  }

  public function setLimit($aFrom, $aLpp = NULL) {
    $this -> mLimit = intval($aFrom);
    if (NULL !== $aLpp) {
      $this -> mLimit.= ','.intval($aLpp);
    }
  }

  public function GetWebStatus() {
    $lstatus = '';

    // Wird nur bei einer Migration definiert, da die Webstatusbeschränkung ausser Kraft gesetzt wird!
    if (defined('MIGRATION')) {
      $lstatus.= '0,'.STATUS_ARCHIV;
    } elseif ($this -> mSrc !== '') {
      $lSql = 'SELECT DISTINCT cs.status FROM al_crp_status cs, al_crp_master cm WHERE cs.mand='.MID.' AND cm.mand='.MID.' AND cm.id=cs.crp_id and cm.code="'.$this -> mSrc.'"  order by cs.status';
      $lQry = new CCor_Qry($lSql);
      $lval = '';
      foreach ($lQry as $lRow) {
        if (STATUS_MonitorTest != $lRow['status']) {
          if ($lstatus == '') {
            $lstatus = $lval;
          } else {
            $lstatus = $lstatus . ',' . $lval;
          }
          // den letzten ignorieren (Archiv)
          $lval = $lRow['status'];
        }
      }
    }
    return $lstatus;
  }

  public function query() {
    $this -> mLoaded = TRUE;
    $this -> mMaxCount = 0;
    $this -> mParam -> clear();
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
    if (empty($this -> mMNr)) {
      $this -> addParam('sid', MAND);
    } else {
      $this -> addParam('sid', $this -> mMNr);
    }

    if (!empty($this -> mCnd)) {
      $this -> addParam('where', $this -> mCnd);
    }

    if (!empty($this -> mOrd)) {
      $lTmp = array();
      if ('mop' == $this->mWriter) {
        if (substr($this->mOrd,0,4) == 'Zus.') {
          $this->mOrd = 'Job#Zus#'.substr($this->mOrd, 4);
        }
      }
      $lTmp['field'] = $this -> mOrd;
      $lTmp['dir']    = (empty($this -> mDir)) ? 'asc' : $this -> mDir;
      if (!empty($this -> mTyp)) {
        $lTmp['type'] = $this -> mTyp;
      }
      $this -> addParam('order', $lTmp);
    }

    if(!$this->mWithoutLimit){
      if (!empty($this -> mLimit)) {
        $this -> addParam('limit', $this -> mLimit);
      }
    }

    $lws = $this -> GetWebStatus();

    if ($lws !== '') {
      $this -> addParam('webstatuslist', $lws);
    }

    parent::query();
    $this -> mMaxCount = (integer)$this -> mResponse -> getVal('maxcount');
    return $this -> mResponse;
  }

  public function getCount() {
    if (!$this -> mLoaded) {
      $this -> query();
    }
    return $this -> mMaxCount;
  }

  public function getIterator() {
    if (!$this -> mLoaded) {
      $this -> query();
    }
    if ($this -> mResponse) {
      $lRows = $this -> mResponse -> getVal('item');
    }

    $lRet = array();

    if (!empty($lRows)) {
      foreach ($lRows as $lRow) {
        $lTmp = array();
        foreach ($lRow as $lKey => $lVal) {
          $lTmp[(string)$lKey] = (string) $lVal;
        }
        $lRet[] = $lTmp;
      }
    }
    return new ArrayIterator($lRet);
  }

  public function getArray($aKeyField = NULL) {
    $lRet = array();
    $lIte = $this -> getIterator();

    if (NULL == $aKeyField) {
      foreach ($lIte as $lRow) {
        $lRet[] = $lRow;
      }
    } else {
      foreach ($lIte as $lRow) {
        $lRet[$lRow[$aKeyField]] = $lRow;
      }
    }
    return $lRet;
  }

}