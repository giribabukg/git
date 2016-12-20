<?php
class CInc_Cor_TblIte extends CCor_Qry implements IteratorAggregate {

  protected $mTbl;
  protected $mCnd;
  protected $mCondSql = '';
  protected $mLimit;
  protected $mQry;
  protected $mFie;
  protected $mGroupBy;
  protected $mGroup;
  protected $mOrd;
  protected $mDir;
  protected $m2Ord = '';
  protected $m2Dir = '';
  protected $mWithoutLimit = FALSE;// Get Iterator without User Limit(x.lpp),i.e CSV Export.

  protected $mAddUserCondition = FALSE;
  protected $mAddGroupCondition = FALSE;
  protected $mAddProjectCondition = FALSE;

  public function __construct($aTbl, $aWithoutLimit = FALSE) {
    parent::__construct();

    $this -> mWithoutLimit = $aWithoutLimit;
    $this -> mTbl = $aTbl;
    $this -> mCnd = array();
    $this -> mCondSql = '';
    $this -> mFie = array();
    $this -> mCndMaster = CCor_Res::extract('id', 'aliased', 'cndmaster');

    $this -> mAddUserCondition    = FALSE;
    $this -> mAddGroupCondition   = FALSE;
    $this -> mAddProjectCondition = FALSE;

    if (FALSE !== strpos($aTbl, 'al_job_')) {
      $lTbl = substr($aTbl, 0, 11); // al_job_pro_
      $lToken = explode('_', $lTbl);
      $this -> mTyp = $lToken[2]; // pro

      $lJob = CCor_Cfg::get('menu-aktivejobs');
      array_walk($lJob, 'strip_job');
      $lJob = array_diff($lJob, array('pro'));
      $lJob = array_merge($lJob, array('arc', 'sub'));
      $lJob = array_unique($lJob);

      if (in_array($this -> mTyp, array('pro'))) {
        $this -> mAddProjectCondition = TRUE;
      } elseif (in_array($this -> mTyp, $lJob)) {
      $this -> mAddUserCondition = TRUE;
        $this -> mAddGroupCondition = TRUE;
    }
  }
  if ($aTbl == 'all') {
    $this -> mUsr = CCor_Usr::getInstance();
    $lJobTypes = $this -> getJobtypes();
    $lCountJobTypes = count($lJobTypes);
    $lSrc = ($lCountJobTypes == 0) ? 'rep' : $lJobTypes;
    $this -> mMultiTbls = $lSrc;
    $this -> mAddUserCondition = TRUE;
    $this -> mAddGroupCondition = TRUE;
  }

  }

  public function addUserConditions($aUId = null) {
    $lRestr = '';
    $this -> mDefs = CCor_Res::extract('alias', 'native', 'fie');

    if (is_null($aUId)) {
      $lUid = CCor_Usr::getAuthId();
    } else {
      $lUid = $aUId;
    }

    if (false != CCor_Cfg::get('extcnd')) { // die NEUEN Conditions
      $lUsrCond = CCor_Res::get('cnd','uid');
      if (!empty($lUsrCond) AND isset($lUsrCond[$lUid]) AND !empty($lUsrCond[$lUid])) {
        $lCond = $lUsrCond[$lUid];
        if (!empty($lCond['cond']) && $lCond['cnd_id'] == 0) {
          $lArr = explode(';', $lCond['cond']);
          foreach ($lArr as $lVal) {
            list($lField, $lOp, $lValue) = explode(' ', $lVal, 3);
            $this -> addCondition($lField, $lOp, $lValue);
            #$this -> addField($lField, $this -> mDefs[$lField]);//NICHT b. Abfragen in PortalDB! Sonst Anzeige einer ungefuellten Liste
          }
        } elseif ($lCond['cnd_id'] > 0) {
          $lCndMaster = $this -> mCndMaster;
          if (!empty($lCndMaster) AND isset($lCndMaster[ $lCond['cnd_id'] ]) AND !empty($lCndMaster[ $lCond['cnd_id'] ])) {
            $lNatived = $lCndMaster[ $lCond['cnd_id'] ];
            $this -> addCnd($lNatived);
          }
        }
      }//end_if (!empty($lUsrCond) AND isset($lUsrCond[$lUid]) AND !empty($lUsrCond[$lUid]))
    } else { // die ALTEN Conditions
      $lSql = 'SELECT * FROM al_cnd WHERE usr_id='.$lUid.' AND mand='.MID;
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        if ($lRow['cond'] !== '') {
          $lArr = explode(';', $lRow['cond']);
          foreach ($lArr as $lVal) {
            list($lField, $lOp, $lValue) = explode(' ', $lVal, 3);
            $this -> addCondition($lField, $lOp, $lValue);
            #$this -> addField($lField, $this -> mDefs[$lField]);//NICHT b. Abfragen in PortalDB! Sonst Anzeige einer ungefuellten Liste
          }
        }
      }
    } //end_if/else (false != CCor_Cfg::get('extcnd'))

    return $lRestr; //"Abwaertskompatibilitaet" mit mand_1003
  }//end public function addUserConditions()

  public function addGroupConditions($aUId = null) {
    if (is_null($aUId)) {
      $lUid = CCor_Usr::getAuthId();
    } else {
      $lUid = $aUId;
    }

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
              $this -> addCnd($lNatived);
            }
          }
        }//end_if (!empty($lGruCond) AND isset($lGruCond[$lGid]) AND !empty($lGruCond[$lGid]))
      }//end_foreach ($lGroups as $lGid)
    } //end_if (!empty($lMemberOf) AND isset($lMemberOf[$lUid]) AND !empty($lMemberOf[$lUid]))
  }//end public function addGroupConditions()

  public function addProjectConditions() {
    $lUid = CCor_Usr::getAuthId();
    $lProCndViaUsr = CCor_Qry::getInt("SELECT procnd FROM al_usr WHERE id=".$lUid);
    if (isset($lProCndViaUsr) && $lProCndViaUsr > 0 ) {
      $lResult = CCor_Qry::getStr("SELECT aliased FROM al_cnd_master WHERE id=".$lProCndViaUsr);
      $this -> addCnd($lResult);
    }
    $lMemberOf = CCor_Res::get('mem', 'uid');
    if (!empty($lMemberOf) AND isset($lMemberOf[$lUid]) AND !empty($lMemberOf[$lUid])) {
      $lGroups = $lMemberOf[$lUid];
      foreach ($lGroups as $lGid) {
        $ProCndViaGru = CCor_Qry::getInt("SELECT procnd FROM al_gru WHERE id=".$lGid);
        if (isset($ProCndViaGru) && $ProCndViaGru > 0 ) {
          $lResult = CCor_Qry::getStr("SELECT aliased FROM al_cnd_master WHERE id=".$ProCndViaGru);
          $this -> addCnd($lResult);
        }
      }
    }
  }//end public function addProjectConditions()

  public function addCnd($aCnd) {
    if(!empty($aCnd))
      $this -> mCnd[] = $aCnd;
  }

  public function addCondition($aField, $aOp, $aValue) {
    $lOp = strtoupper(trim($aOp));

    if (in_array($lOp, array('IN', 'NOT IN'))) {
      $this -> addCnd('(`'.$aField.'` '.$lOp.' ('.$aValue.'))');
    } else {
      $this -> addCnd('(`'.$aField.'` '.$lOp.' "'.$aValue.'")');
    }

    unset($this -> mCondSql);
  }

  public function addField($aAlias) {
    $this -> mFie[$aAlias] = $aAlias;
  }

  public function addDef($aDef) {
    $this -> addField($aDef['alias']);
  }

  public function getCondSql() {
    if (empty($this -> mCondSql)) {
    $lRet = 'WHERE 1 ';

      if ($this -> mAddUserCondition) {
        $lRet.= $this -> addUserConditions();  //"Abwaertskompatibilitaet" mit mand_1003
      }
      if ($this -> mAddGroupCondition && false != CCor_Cfg::get('extcnd')) {
        $this -> addGroupConditions();
      }
      if ($this -> mAddProjectCondition && false != CCor_Cfg::get('extcnd')) {
        $this -> addProjectConditions();
      }

    if (!empty($this -> mCnd)) {
      foreach ($this -> mCnd as $lCnd) {
        $lRet.= 'AND '.$lCnd.' ';
      }
    }
      $this -> mCondSql = $lRet;
    }
    return $this -> mCondSql;
  }

  public function getOrderSql() {
    if (empty($this -> mOrd) and empty($this -> mGroup)) {
      return '';
    }
    $lRet = 'ORDER BY ';
    if (!empty($this -> mGroup)) {
      $lRet.= backtick($this -> mGroup).',';
    }
    if (!empty($this -> mOrd)) {
      $lRet.= $this -> mOrd.' '.$this -> mDir.',';
    }
    if (!empty($this -> m2Ord)) {//used in hol/list
      $lRet.= $this -> m2Ord.' '.$this -> m2Dir.',';
    }
    $lRet = substr($lRet, 0, -1).' ';
    return $lRet;
  }

  public function setLimit($aFrom, $aLpp = NULL) {
    $this -> mLimit = intval($aFrom);
    if (NULL !== $aLpp) {
      $this -> mLimit.= ','.intval($aLpp);
    }
  }

  public function setGroup($aField) {
    $this -> mGroup = $aField;
  }

  public function setOrder($aField, $aDir = 'asc') {
    $this -> mOrd = $aField;
    $this -> mDir = $aDir;
  }

  public function set2Order($aField, $aDir = 'asc') {
    $this -> m2Ord = $aField;
    $this -> m2Dir = $aDir;
  }

  public function getCount($aField = NULL) {
    if(isset($this -> mMultiTbls) and !empty($this -> mMultiTbls)) return $this -> getMultiCount($aField);
    if (empty($aField)) {
      $aField = '*';
    }
    $lSql = 'SELECT COUNT('.$aField.') FROM '.$this -> mTbl.' ';
    $lSql.= $this -> getCondSql();
    return CCor_Qry::getStr($lSql);
  }
  
  public function getMultiCount($aField = NULL) {
    if (empty($aField)) {
      $aField = '*';
    }
    
    $lSql = 'SELECT ';
    $lCount = count($this -> mMultiTbls);
    $lIndex = 0;
    foreach ($this -> mMultiTbls as $lVal) {
      $lSql.= '(SELECT COUNT('.$aField.') FROM al_job_'.$lVal.'_'.MID.' ';
      $lSql.= $this -> getCondSql();
      $lSql.= ') ';
      if (++$lIndex !== $lCount) {
        $lSql.= '+ ';
      }
    }
    $lSql.= 'AS count';
    return CCor_Qry::getStr($lSql);
  }

  public function getIterator() {
    if(isset($this -> mMultiTbls) and !empty($this -> mMultiTbls)) return  $this -> getMultiIterator();
    $lSql = 'SELECT ';
    if (empty($this -> mFie)) {
      $lSql.= '* ';
    } else {
      $lSql.= implode(',', $this -> mFie).' ';
    }
    $lSql.= 'FROM '.$this -> mTbl.' ';
    $lSql.= $this -> getCondSql();
    $lSql.= $this -> getGroupBy();
    $lSql.= $this -> getOrderSql();
    if(!$this -> mWithoutLimit){
      if (!empty($this -> mLimit)) {
        $lSql.= ' LIMIT '.$this -> mLimit;
      }
    }

    $this -> dbg('Getting Iterator with '.$lSql, mlInfo);
    $this -> query($lSql);

    return new CCor_QryIte($this -> mHandle, $this -> mDb);
  }
  
  public function getMultiIterator() {
    if (empty($this -> mFie)) {
      $lCloumns = '* ';
    } else {
      $lCloumns = implode(',', $this -> mFie).' ';
    }
    
    $lSql = '';
    $lCount = count($this -> mMultiTbls);
    $lIndex = 0;
    foreach ($this -> mMultiTbls as $lVal) {
      $lSql.= 'SELECT ';
      $lSql.= $lCloumns;
      $lSql.= 'FROM al_job_'.$lVal.'_'.MID.' ';
      $lSql.= $this -> getCondSql();
      $lSql.= ' AND webstatus >= 10 ';
      if (++$lIndex !== $lCount) {
        $lSql.= 'UNION ';
      }
    }
    
    $lSql.= $this -> getGroupBy();
    if (in_array($this -> mOrd, $this -> mFie)) $lSql.= $this -> getOrderSql();
    if(!$this -> mWithoutLimit){
      if (!empty($this -> mLimit)) {
        $lSql.= ' LIMIT '.$this -> mLimit;
      }
    }
  
    $this -> dbg('Getting Iterator with '.$lSql, mlInfo);
    $this -> query($lSql);
  
    return new CCor_QryIte($this -> mHandle, $this -> mDb);
  }

  public function getArray($aKeyField = NULL) {
    $lRet = array();
    $lIte = $this -> getIterator();

    if (NULL == $aKeyField) {
      foreach ($lIte as $lRow) {
        $lRet[] = $lRow -> toArray();
      }
    } else {
      foreach ($lIte as $lRow) {
        if(!empty($lRow[$aKeyField])) {
          $lRet[$lRow[$aKeyField]] = $lRow -> toArray();
        }
      }
    }
    return $lRet;
  }
  public function setGroupBy($aField) {
    $this -> mGroupBy[] = $aField;
  }
  public function getGroupBy() {
    if (empty($this -> mGroupBy)) {
      return '';
    }
    $lRet = 'GROUP BY ';
    if (!empty($this -> mGroupBy)) {
    foreach ($this -> mGroupBy as $lGroup) {
        $lRet.= $lGroup.',';
      }
      $lRet = substr($lRet, 0, -1).' ';
    }
    return $lRet;
  }  

  public function getDetailHandle() {
    $lIte = $this -> getIterator();
  }
  
  protected function getJobtypes() {
    $lMnu_Akt = CCor_Cfg::get('menu-aktivejobs');
    $lJobTypes = array();
    foreach ($lMnu_Akt as $lKey => $lVal) {
      if ($lVal == 'job-all') continue;
      if ($this->mUsr->canRead($lVal)) {
        $lJobTypes[] = substr($lVal, strrpos($lVal, '-') + 1);;
      }
    }
    return $lJobTypes;
  }
}