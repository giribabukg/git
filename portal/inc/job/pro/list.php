<?php
class CInc_Job_Pro_List extends CJob_List {

  protected $mSrc = 'pro';
  protected $mUsr; // Currrent User
  protected $mShowDeleteButton = FALSE;
  protected $mProCanBeDeleted = FALSE;
  protected $mWithoutLimit = FALSE; // Get Iterator without User Limit(x.lpp),e.g. by CSV Export
  protected $mArcStatus; // Archive Status
  protected $mProStatus = array();
  public $mViewJoblist = TRUE;

  public function __construct($aWithoutLimit = FALSE, $aAnyUsrID = NULL) {
    $this -> mUsr = CCor_Usr::getInstance();
    $this -> mWithoutLimit = $aWithoutLimit;
    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    $this -> mCrpId = $lCrp['pro'];
    $this -> mShowCopyButton = FALSE;
    $this -> mArcStatus = $this -> getArcStatus();

    $this -> mViewJoblist = CCor_Cfg::get('view.projekt.joblist', TRUE);

   	/** Project can be deleted :
   	* -- If Configuration Variable 'job-pro.del' is TRUE
   	* -- AND current User has Right 'Delete'
   	* -- AND Project has no Assigment Job.
   	*/
    $this -> mProCanBeDeleted = CCor_Cfg::get('job-pro.del',FALSE);
    if ($this -> mProCanBeDeleted){
      if ($this ->mUsr -> canDelete('job-pro')){
        $this -> mShowDeleteButton = TRUE;
      }
    }
    $this -> mShowColumnMore = TRUE;
    $this -> mShowCsvExportButton = TRUE;

    parent::__construct('job-pro', $this -> mCrpId, '', $aAnyUsrID);

    $this -> mImg = 'img/ico/40/'.LAN.'/job-pro.gif';

    $this -> addFilter('webstatus', 'Status', $this -> mCrpId);
    $this -> getFilterbyAlias(); //default 'per_prj_verantwortlich'

    if ($this -> mUsr -> canInsert('job-pro')) {
      $this -> addBtn(lan('job-pro.new'), 'go("index.php?act=job-pro.new")', '<i class="ico-w16 ico-w16-plus"></i>');
    }

    $this -> addSort('last_status_change');
    $this -> addButton(lan('lib.sort'), $this -> getButtonMenu($this -> mMod));

    $this -> mMaxLines = $this -> mIte -> getCount();
    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());

    $this -> addJs();

  }

  protected function onBeforeContent() {
    $lRet = parent::onBeforeContent();
    $this -> mIte = $this -> mIte -> getArray('jobid');
    $lProIds = array_keys($this -> mIte);

    //22651 Project Critical Path Functionality
    if (empty($lProIds)) {
      $lProIds = array();
    }
    $lProCrp = CJob_Pro_Crp::getInstance($lProIds);

    $this -> mProStatus = $lProCrp -> getProStatus();
    $this -> mProStatusAll = $lProCrp -> getProStatusAll(); //with "afore & after" jobs
    $this -> mJobsAmount = $lProCrp -> getProjectsAmount();
    $this -> mSubAmount = $lProCrp -> getSubAmount();

    #$this -> mStatusClosedMax = $lProCrp -> getStatusClosedMax();
    $this -> mAutoProStatus    = $lProCrp -> getAutoProStatus();
    $this -> mStatusClosed = $lProCrp -> getStatusClosed();
    $this -> mViewJoblist      = $lProCrp -> getViewJoblist();
    $this -> mNoStatusFromStep = $lProCrp -> getNoStatusFromStep();

    #$this -> m1LastStatusNoStep = $lProCrp -> get1StatusNoFromStep();

    $this -> mCrpStatus = CCor_Res::extract('status', 'display', 'crp', $this -> mCrpId);

    #$this -> mViewJoblist = $lProCrp -> getViewJoblist();
    $this -> loadApl();
    $this -> loadFlags();
    return $lRet;
  }

  protected function addGlobalSearchConditions() {
    return ;
  }

  protected function addJs() {
    $lJs = 'function loadSub(aId,aJid){';
    $lJs.= 'Flow.Std.togTr(aId);';
    $lJs.= 'lDiv = aId + "r";';
    $lJs.= 'Flow.Std.ajxUpd({act:"job-pro.sub",jid:aJid,div:lDiv});';
    $lJs.= '}';
    $lPag = CHtm_Page::getInstance();
    $lPag -> addJs($lJs);
  }

  protected function getIterator() {
    $this -> mIte = new CCor_TblIte('al_job_pro_'.intval(MID), $this -> mWithoutLimit);
    $this -> mIte -> addCnd('del != "Y"');
    if (is_numeric($this -> mArcStatus)) {
      $this -> mIte -> addCnd('webstatus<'.$this -> mArcStatus);
    }
    //$this -> addUserConditions(); // UserCondition in Job/list
  }

  protected function addUserConditions() {
    $lUid = CCor_Usr::getAuthId();
    $lSql = 'SELECT * FROM al_cnd WHERE usr_id='.$lUid.' AND mand='.MID;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      // wenn Feld 'Cond' empty, No Condition
      if ($lRow['cond'] !== ''){
        $lArr = explode(';', $lRow['cond']);
        foreach ($lArr as $lVal) {
          list($lField, $lOp, $lValue) = explode(' ', $lVal, 3);

          if ('supplier' == $lField) {
            $lORNew = array();
            $lValue = esc($lValue);
            $lORNew[0] = 'project_supplier'.$lOp.$lValue;
            $lValueBoth = $this -> mDefs['supplier']['NoChoice'];//NoChoice wird im Jobfield angezeigt, ist aber nicht änderbar!
            $lValueBoth = esc($lValueBoth);
            $lORNew[1] = 'project_supplier'.'='.$lValueBoth;
            $lORCnd = '('.implode(' OR ', $lORNew).')';
            $this -> mIte -> addCnd($lORCnd);
          } else {
            $this -> mIte -> addCondition($lField, $lOp, $lValue);
          }
        }
      }
    }
  }

  protected function addCondition($aAlias, $aOp, $aValue) {
    $this -> mIte -> addCondition($aAlias, $aOp, $aValue);
  }

  protected function getTdMor() {
    $lPid = $this -> getInt('id');
    $this -> mMoreId = getNum('t');
    $lRet = '<a class="nav" onclick="loadSub(\''.$this -> mMoreId.'\','.$lPid.')">';
    $lRet.= '...</a>';
    return $this -> tdc($lRet);
  }

  protected function afterRow() {
    $lRet = parent::afterRow();
    $lRet.= '<tr style="display:none" id="'.$this -> mMoreId.'"><td class="td1 tg" id="'.$this -> mMoreId.'l">&nbsp;</td>';
    $lCol = $this -> mColCnt -1;
    $lRet.= '<td class="td1 p16" id="'.$this -> mMoreId.'r" colspan="'.$lCol.'"><div><img src="img/pag/ajx.gif" alt="" /></div></td>';
    $lRet.= '</tr>'.LF;
    return $lRet;
  }

  protected function getSubState($aSta, $lDisplay = 0) {
    $lProWebstatus = $this -> getCurInt();
    $lProId = $this -> getVal($this -> mIdField);

    $lDis = $aSta['display'];
    $lStatus = $aSta['status'];
    $lSta = $this -> mProStatus[$lProId][$lDis];
    $lStaAll = $this -> mProStatusAll[$lProId][$lDis];

    $lStaDis = CCor_Cfg::get('status.display', 'progressbar');
    switch($lStaDis){
    	case 'progressbar':
    if ($lDis <= $lDisplay OR $lDis == $this -> mAutoProStatus[$lProId]) {
      $lImg = 'b';
    } else {
      $lImg = 'l';
    }
          break;
    	case 'activeonly':
    	  if ($lDis == $lDisplay OR $lDis == $this -> mAutoProStatus[$lProId]) {
    	    $lImg = 'b';
    	  } elseif ($lDis != $lDisplay) {
    	    $lImg = 'l';
    	  }
    	  break;
    }
    if (0 < $lSta['count'] OR ( in_array($lDis, $this -> mStatusClosed[$lProId]['pro']) AND (!$lStaAll['afore'] OR $lStaAll['after']) )) {
      $lImg = 'h';
    }

    return $lImg;
  }

  // not_used: Idee: ein Projekt besteht aus dem Entwurf und dem Briefing. Danach können als Status alle Job-
  // typen durchlaufen werden, in beliebiger Reihenfolge. Es endet mit Abgeschlossen und dem Archiv. Um nicht
  // alle Übergangsmöglichkeiten zwischen den Stati mit Steps nachzubilden, wurde eine weitere Logik eingebaut:
  // Immer wenn ein Job gestartet wird, wird in der_job_sub_ der jeweilige Jobstatus (rep_state) auf 1 gesetzt.
  // -> Anzeige halbcoloriertes Icon. Wird der Pro-Status auf Jobtyp beendet gesetzt ->rep_state=2,ganzes Icon
  // im CRP heißt rep_state rep_sta (7 Buchstaben). Intern wird d. Projekt immer im Status Briefing betrachtet -
  // solange es zw. Briefing und Abgeschlossen steht.
  protected function getTdWebstatus() {
    $lRet = '';
    $lProWebstatus = $this -> getCurInt();
    $lProId = $this -> getVal($this -> mIdField);
    $lProDis = $this -> mCrpStatus[$lProWebstatus];

    if (!$this -> mViewJoblist AND !empty($this -> mSubAmount) AND !empty($this -> mJobsAmount)) {
      $lGap = (10 > $this -> mSubAmount[$lProId] ? NB : '');
      $lSubAmount = $this -> mSubAmount[$lProId];
      $lGap.= (10 > $this -> mJobsAmount[$lProId] ? NB : '');
      $lJobsAmount = $this -> mJobsAmount[$lProId];
      $lPrjInfo = NB.$lSubAmount.'/'.$lJobsAmount.$lGap;
    } else {
      $lPrjInfo = '';
    }
    if (isset($this -> mProStatus[$lProId]) AND !empty($this -> mProStatus[$lProId])) {

      if (!$this -> mViewJoblist AND 0 < $lProDis) {
        if ($lProDis >= $this -> mAutoProStatus[$lProId] OR !$this -> mNoStatusFromStep[$lProDis]) {
          $lDisplay = $lProDis;
        } else {
          $lDisplay = $this -> mAutoProStatus[$lProId];
        }
      } else {
        $lDisplay = $lProDis;
      }

      $lNam = '[unknown]';
      foreach ($this -> mCrp as $lRow) {
        $lDis = $lRow['display'];

        $lImg = $lDis;
        $lImg.= $this -> getSubState($lRow, $lDisplay);
        if ($lDisplay >= $lRow['display']) {
          $lNam = $lRow['name_'.LAN];
        }
        $lStatus = $lRow['status'];
        $lBorder = '';
		$lPath = CApp_Crpimage::getSrcPath($this->mSrc, 'img/crp/'.$lImg.'.gif');
        $lRet.= img($lPath,  array('style' => 'margin-right:1px;'.$lBorder));
      }
      $lRet.= $lPrjInfo;
      $lRet.= NB.htm($lNam);
      return $this -> tda($lRet);
    } else {
      return parent::getTdWebstatus($lPrjInfo);
    }
  }

  /** Project can be deleted :
   * -- If Configuration Variable 'job-pro.del' is TRUE
   * -- AND current User has Right 'Delete'
   * -- AND Project has no Assigment Job.
   */
  protected function getTdDel() {
    $lJid = $this ->getInt('id');
    $lAllJobs = CCor_Cfg::get('all-jobs');

    if ($this -> mUsr ->canDelete('job-pro')){


        /** First check if Project has Project Items. If Yes
         *  Second check if Project has Jobs assignment.
         *
         * First: IF Project has no Project-Items, can be deleted
         *
         */
        $lSql = 'Select id,pro_id,' ;
        foreach ($lAllJobs as $lKey){
          $lSql.= 'jobid_'.$lKey.',';
        }
        $lSql = substr($lSql,0,-1);
        $lSql.= ' FROM al_job_sub_'.MID;
        $lSql.= ' WHERE pro_id = "'.$lJid.'" ';

        $lQry = new CCor_Qry($lSql);
        if (!$lRow = $lQry->getAssoc()){
          return CInc_Htm_List::getTdDel();
        } else {
          foreach ($lQry as $lRow){
            foreach ($lAllJobs as $lKey){
              $lSpalte = 'jobid_'.$lKey;
              if  ($lRow-> $lSpalte != ''){
                // Project has a Job assigned and can not be deleted.
                return $this -> td();
              }
            }
          }
        }
        return CInc_Htm_List::getTdDel();
    }
  }

  protected function getArcStatus() {
    $lRet = '';
    $lArcStatus = '';

    if (is_numeric($this -> mCrpId)) {
      $lSql = "SELECT s.status";
      $lSql.= " FROM al_crp_status AS s";
      $lSql.= " LEFT JOIN al_crp_step AS x";
      $lSql.= " ON s.id=x.to_id";
      $lSql.= " WHERE x.mand=".MID;
      $lSql.= " AND x.trans LIKE 'pro2arc'";
      $lSql.= " AND x.crp_id=".$this -> mCrpId;
      $lSql.= " AND x.to_id IS NOT NULL";
      $lSql.= " LIMIT 0,1;";
      $lArcStatus = CCor_Qry::getInt($lSql);
    }

    if (is_numeric($lArcStatus)) {
      $lRet = $lArcStatus;
    } else {
      $this -> dbg('Missing Archive Event in CRP', mlWarn);
    }

    return $lRet;
  }
}