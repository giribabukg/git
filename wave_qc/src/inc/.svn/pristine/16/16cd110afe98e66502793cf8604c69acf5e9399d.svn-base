<?php
class CInc_Eve_List extends CHtm_List {

  /**
   * Registry for action types
   *
   * @var CApp_Event_Action_Registry
   */
  protected $mReg;

  public function __construct() {
    parent::__construct('eve');
    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('eve.menu');
    $this -> mUsr = CCor_Usr::getInstance();

    $this -> addColumn('more','', false, array('width' => 16));
    $this -> addColumn('name_'.LAN, 'Name', TRUE);
    $this -> mDefaultOrder = 'name_'.LAN;
    if ($this -> mCanInsert) {
		$this -> addColumn('copy','', false, array('width' => 16));
		$this -> addBtn(lan('eve.new'), "go('index.php?act=eve.new')", '<i class="ico-w16 ico-w16-plus"></i>');
	}
    if ($this -> mCanDelete) {
      $this -> addDel();
    }
    $this -> addIsThisInUse();

    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_eve e');
    $this -> mIte -> addCnd('mand='.MID);
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this->setGroup('typ');

    $this -> mTyp = '';
    if (!empty($this -> mSer)) {
      $lName = (empty($this -> mSer['name'])) ? '' : trim($this -> mSer['name']);
      if (!empty($lName)) {
        $lDelim = ' /\\';
        $lTok = strtok($lName, $lDelim);

        $lCnd = '((';
        while ($lTok !== false) {
          $lVal = '"%'.addslashes($lTok).'%"';
          $lCnd.= 'e.name_'.LAN.' LIKE '.$lVal.' AND ';
          $lTok = strtok($lDelim);
        }
        $lCnd.= '1) OR e.typ LIKE "%'.addslashes($lName).'%")';
        $this -> mIte -> addCnd($lCnd);
      }
      if (!empty($this -> mSer['typ'])) {
        $this -> mTyp = $this -> mSer['typ'];
        $lCnd = '(e.typ='.esc($this -> mSer['typ']).')';
        $this -> mIte -> addCnd($lCnd);
      }
    }
    $this->mTypes = CCor_Res::extract('code', 'name', 'evetype');
    $this->mTypeFields = CCor_Res::extract('code', 'fields', 'evetype');

    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);
    $this -> mMaxLines = $this -> mIte -> getCount();
    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());
    $this -> addPanel('cap', '|');
    $this -> addPanel('fil', $this -> getSearchForm());

    $this -> getActions();

    if ($this -> mUsr -> canRead('csv-exp')) {
      $this -> addPanel('cap2', '|');
      $this -> addPanel('exp', $this -> setExcelExportButton());
    }
    $this -> mReg = new CApp_Event_Action_Registry();
  }

  protected function getFilterBar() {
    $lRet = '';
    $lRet.= '<tr>'.LF;
    $lRet.= '<td class="sub p0"'.$this -> getColspan().'>';
    $lRet.= $this -> setFilterReportsPanel();
    $lRet.= '</td>';
    $lRet.= '</tr>'.LF;
    return $lRet;
  }

  protected function getSearchForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="eve.ser" />'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;


    $lTyp = (isset($this -> mSer['typ'])) ? $this -> mSer['typ'] : '';
    $lArr = CCor_Res::extract('code', 'name', 'evetype');
    $lRet.= '<td>'.htm(lan('lib.type')).'</td>';
    $lRet.= '<td>';
    $lRet.= '<select name="val[typ]">';
    $lRet.= '<option value="">&nbsp;</option>';
    foreach ($lArr as $lKey => $lVal) {
      $lRet.= '<option value="'.htm($lKey).'"';
      if ($lKey == $lTyp) $lRet.= ' selected="selected"';
      $lRet.='>'.htm($lVal).'</option>';
    }
    $lRet.= '</select>';
    $lRet.= '</td>'.LF;


    $lRet.= '<td>'.htm(lan('lib.search')).'</td>';
    $lVal = (isset($this -> mSer['name'])) ? htm($this -> mSer['name']) : '';
    $lRet.= '<td><input type="text" name="val[name]" class="inp" value="'.$lVal.'" /></td>'.LF;


    #if (!empty($this->mTyp)) {
    #  $lRet.= $this->getTypeSearchFields($this->mTyp);
    #}
    $lRet.= '<td>'.btn(lan('lib.search'),'','','submit').'</td>';
    if (!empty($this -> mSer)) {
      $lRet.= '<td>'.btn(lan('lib.show_all'),'go("index.php?act=eve.clser")').'</td>';
    }
    $lRet.= '</tr></table>';
    $lRet.= '</form>'.LF;

    return $lRet;
  }

  protected function getTypeSearchFields($aTyp) {
    $lTypDesc = $this->mTypeFields[$aTyp];
    if (empty($lTypDesc)) return '';

    $this->mJobFields = CCor_Res::getByKey('alias', 'fie');
    $lFac = new CHtm_Fie_Fac();

    $lRet = '';
    #$lRet = '<input type="hidden" name="val[typ]" value="'.htm($aTyp).'" />'.LF;
    $lFields = explode(',', $lTypDesc);
    foreach ($lFields as $lAlias) {
      if (isset($this->mJobFields[$lAlias])) {
        $lField = $this->mJobFields[$lAlias];

        $lExpAlias = 'info_'.$lAlias;
        $lField['alias'] = $lExpAlias;
        $lValue = '';
        if (isset($this->mSer[$lExpAlias])) {
          $lValue = $this->mSer[$lExpAlias];
        }

        $lRet.= '<td>';
        $lRet.= $lField['name_en'];
        $lRet.= '</td>';

        $lRet.= '<td>';
        $lRet.= $lFac->getInput($lField, $lValue, fsSearch);
        $lRet.= '</td>';
      }
    }
    return $lRet;
  }

  protected function getActions() {
    $this -> mSub = array();
    $lSql = 'SELECT * FROM al_eve_act WHERE mand='.MID.' ORDER BY pos';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lEve = intval($lRow['eve_id']);
      $this -> mSub[$lEve][] = $lRow;
    }
  }

  protected function getGroupHeader() {
    $lRet = '';
    if (!empty($this -> mGrp)) {
      $lNew = $this -> getVal($this -> mGrp);
      if ($lNew !== $this -> mOldGrp) {
        $this->mGrpIdx = getNum('g');
        $lDisplay = isset($this->mTypes[$lNew]) ? $this->mTypes[$lNew] : $lNew;
        $lRet = TR;
        $lRet.= '<td class="tg1 w16 ac">';
        $lRet.= '<a href="javascript:Flow.toggleLines(\''.$this->mGrpIdx.'\')" class="nav">...</a>';
        $lRet.= '</td>';
        $lRet.= '<td class="tg1" '.($this -> getColspan()).'>';
        $lRet.= htm($lDisplay).NB;
        $lRet.= '</td>';
        $lRet.= _TR;
        $this -> mOldGrp = $lNew;
        $this -> mCls = 'td1';
      }
    }
    return $lRet;
  }

  protected function getTrTag() {
    return '<tr class="hi '.$this->mGrpIdx.'">';
  }

  protected function getTdMore() {
    $lEve = $this -> getInt('id');
    if (!empty($this -> mSub[$lEve])) {
      $lRet = '';
      $this -> mMoreId = getNum('t');
      $lRet = '<a class="nav" onclick="Flow.Std.togTr(\''.$this -> mMoreId.'\')">';
      $lRet.= '...</a>';
      return $this -> tdClass($lRet, 'w16 ac');
    } else {
      $this -> mMoreId = NULL;
      return $this -> td();
    }
  }

  protected function afterRow() {
    $lRet = parent::afterRow();
    if ($this -> mMoreId) {
      $lRet.= '<tr id="'.$this -> mMoreId.'" class="togtr" style="display:none">'.LF;
      $lRet.= '<td class="td1 tg">&nbsp;</td>'.LF;
      $lRet.= '<td class="td1 p8"'.$this -> getColspan().'>';
      $lRet.= $this -> getActionTable();
      $lRet.= '</td>'.LF;
      $lRet.= '</tr>'.LF;
    }
    return $lRet;
  }

  protected function getActionTable() {
    $lEve = $this -> getInt('id');
    $lArr = $this -> mSub[$lEve];
    if (empty($lArr)) return '';
    $lRet = '<table cellpadding="2" cellspacing="0" class="tbl">'.LF;
    $lRet.= '<tr>';
    $lRet.= '<td class="th3 w16">&nbsp;</td>';
    $lRet.= '<td class="th3 w16">&nbsp;</td>';
    $lRet.= '<td class="th3 w100">'.lan('lib.pos').'</td>';
    $lRet.= '<td class="th3 w100">Action</td>';
    $lRet.= '<td class="th3 w100p">Parameters</td>';
    $lRet.= '<td class="th3">Duration</td>';
    $lRet.= '</tr>'.LF;
    foreach ($lArr as $lRow) {
      $lPar = unserialize($lRow['param']);
      $lTyp = $lRow['typ'];
      if ($this -> mCanEdit) $lLnk = 'index.php?act=eve-act.edt&amp;id='.$lEve.'&amp;sid='.$lRow['id'];

      $lRet.= '<tr>';
      $lDet = $this -> mReg -> getParamDetails($lTyp, $lPar);
      if (empty($lDet)) {
        $lRet.= '<td class="td1">&nbsp;</td>';
      } else {
        $lTog = getNum('t');
        $lRet.= '<td class="td1">';
        $lRet.= '<a class="nav" onclick="Flow.Std.togTr(\''.$lTog.'\')">';
        $lRet.= '...</a>';
        $lRet.= '</td>';
      }
      $lRet.= '<td class="td1">';
      $lAct = $lRow['active'];
      if ($lAct) {
        $lRet.= '<i class="ico-w16 ico-w16-flag-03"></i>';
      } else {
        $lRet.= '<i class="ico-w16 ico-w16-flag-00"></i>';
      }
      $lRet.= '</td>';

      $lRet.= '<td class="td1 ac">';
      if ($this -> mCanEdit) $lRet.= '<a href="'.$lLnk.'" class="db">';
      if (EVENT_DEFER_POSITION == $lRow['pos']) {
        $lRet.= lan('lib.eve.deferred');
      } else {
        $lRet.= ($lRow['pos']+1);
      }
      $lRet.= '</a></td>';

      $lRet.= '<td class="td1 nw">';
      if ($this -> mCanEdit) $lRet.= '<a href="'.$lLnk.'" class="db">';
      $lRet.= htm($this -> mReg -> getName($lTyp)).'</a></td>';

      $lRet.= '<td class="td1 nw">';
      if ($this -> mCanEdit) $lRet.= '<a href="'.$lLnk.'" class="db">';
      $lRet.= htm($this -> mReg -> paramToString($lTyp, $lPar)).'</a></td>';
      $lRet.= '<td class="td1 ac">';
      if ($this -> mCanEdit) $lRet.= '<a href="'.$lLnk.'" class="db">';
      $lRet.= $lRow['dur'].'</a></td>';

      $lRet.= '</tr>'.LF;

      if (!empty($lDet)) {
        $lRet.= '<tr id="'.$lTog.'" class="togTr" style="display:none">'.LF;
        $lRet.= '<td class="td1 tg">&nbsp;</td>'.LF;
        $lRet.= '<td class="td1 p8" colspan="4">';
        $lRet.= htm($lDet);
        $lRet.= '</td>'.LF;
        $lRet.= '</tr>'.LF;
      }
    }
    //footer
    $lFunc = $this -> countFunction($lArr);
    $lRet.= '<tr>';
    $lRet.= '<td class="td1" colspan="5">&nbsp;</td>';
    $lRet.= '<td class="td1 nw ac">'.$lFunc['val'].'</td>';
    $lRet.= '</tr>'.LF;

    $lRet.= '</table>'.LF;
    return $lRet;
  }

  protected function getTdCopy() {
    $lId = $this -> getInt('id');
    $lRet = '<a href="index.php?act=eve.copy&id='.$lId.'" class="nav">';
    $lRet.= '<i class="ico-w16 ico-w16-copy"></i>';
    $lRet.= '</a>';
    return $this -> td($lRet);
  }

  protected function getTdInuse() {
  	$lId = $this -> getVal($this -> mIdField);
  	$lInStep = CCor_Qry::getInt('SELECT id FROM al_crp_step WHERE event='.$lId);
  	$lInFlag = CCor_Qry::getInt('SELECT id FROM al_fla WHERE eve_act='.$lId);
  	$lInApl = CCor_Qry::getStr('SELECT typ FROM al_eve WHERE id='.$lId);
  	$lInAplType = CCor_Qry::getStr('SELECT event_completed FROM al_apl_types WHERE event_completed='.$lId);
  	$lRet = '<td class="'.$this -> mCls.' nw w16 ac">';
  	if (!empty($lInStep) OR !empty($lInFlag) OR !empty($lInAplType) OR (substr($lInApl, 0,3) == 'apl')) {
  		$lRet.= '<i class="ico-w16 ico-w16-flag-03"></i>';
  	}
  	else $lRet.= '<i class="ico-w16 ico-w16-flag-00"></i>';
  	$lRet.= '</td>'.LF;
  	return $lRet;
  }

  protected function countFunction($aArr) {
    return CEve_Act_Cnt::countDurationTime($aArr);
  }

  protected function getExportList() {
    $lEventsArray = array();
    $lEventsData = array();

    foreach ($this -> mIte as $lEventId => $lEventInfo) {
      $lEventsArray[$lEventId] = $lEventInfo;
      $lEventsData = array();
      if (empty($this -> mSub[$lEventId])) continue;
      foreach ($this -> mSub[$lEventId] as $lKey => $lVal) {
        $lPar = unserialize($lVal['param']);
        $lTyp = $lVal['typ'];
        if ($lTyp == 'email_gru') {
          $lUserSelection = CCor_Res::extract('id', 'fullname', 'usr', array('gru' => $lPar['sid']));
        }
        elseif ($lTyp == 'email_usr') {
          $lUserSelection = CCor_Res::extract('id', 'fullname', 'usr', array('id' => $lPar['sid']));
        }
        elseif ($lTyp == 'email_rol') {
          $lRoleField = CCor_Res::getByKey('alias', 'fie', array('alias' => $lPar['sid']));
          $lUserSelection = $lRoleField[$lPar['sid']]['name_'.LAN];
        }
        else $lUserSelection = '';
        $lEventsData[] = array(
            'typ' => $lTyp,
            'pos' => $lVal['pos']+1,
            'param' => $this -> mReg -> paramToString($lTyp, $lPar),
            'dur' => $lVal['dur'],
            'members' => $lUserSelection);
      }
      $lEventsArray[$lEventId] = $lEventsArray[$lEventId] + array('actions' => $lEventsData);
    }
    $this -> mExportArray = $lEventsArray;
  }

  protected function setExcelExportButton() {
    $lResCsv = 'go("index.php?act=eve.xlsexp")';
    $this -> addBtn('Export-Workflows Data', $lResCsv, '<i class="ico-w16 ico-w16-excel"></i>', true);
  }

  protected function setFilterReportsPanel() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="eve.reportexp" />'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
    $lRet.= '<td class="caption w50 p0">'.lan('eve-apl.report').'</td>';

    $lFac = new CHtm_Fie_Fac();
    $lDefFrom = fie('from', 'From', 'date');
    $lDefTo = fie('to', 'To', 'date');
    $lValFrom = (isset($this -> mFil['from'])) ? htm($this -> mFil['from']) : '';
    $lValTo = (isset($this -> mFil['to'])) ? htm($this -> mFil['to']) : '';
    $lRet.= '<td>';
    $lRet.= '<table>';
    $lRet.= '<tr>';
    $lRet.= '<td>From:</td>';
    $lRet.= '<td>'.$lFac->getInput($lDefFrom,$lValFrom).'</td>';
    $lRet.= '</tr>';
    $lRet.= '<tr>';
    $lRet.= '<td>To:</td>';
    $lRet.= '<td>'.LF.$lFac->getInput($lDefTo,$lValTo).'</td>';
    $lRet.= '</tr>';
    $lRet.= '</table></td>';

    $lRet.= '<td>'.btn('Export Report','','<i class="ico-w16 ico-w16-excel"></i>','submit').'</td>';
    $lRet.= '</tr></table>';
    $lRet.= '</form>'.LF;

    return $lRet;

  }

  public function getExcel() {
    $this->getActions();
    $this->getExportList();
    $lXls = new CApi_Xls_Writer();

    $this -> removeColumn('ctr');
    $this -> removeColumn('del');
    $this -> removeColumn('cpy');
    $this -> removeColumn('more');

    $lXls -> addField('Event_name', 'Approval workflow');
    $lXls -> addField('Event_typ', 'Action');
    $lXls -> addField('Event_pos', 'Position');
    $lXls -> addField('Event_param', 'Param');
    $lXls -> addField('Event_dur', 'Duration');
    $lXls -> addField('Event_member', 'Members');

    $lXls -> writeCaptions();
    $lXls -> switchStyle();

    foreach ($this -> mExportArray as $this -> mRow) {
      $lVal = $this -> mRow['name_en'];
      $lXls -> writeAsString($this -> mRow['name_'.LAN]);
      if (!isset($this -> mRow['actions'])) {
        $lXls -> newLine();
        continue;
      }
      foreach ($this -> mRow['actions'] as $lKey => $lVal) {
        $lXls -> writeAsString($lVal['typ']);
        $lXls -> writeAsString($lVal['pos']);
        $lXls -> writeAsString($lVal['param']);
        $lXls -> writeAsString($lVal['dur']);

        if ($lVal['typ'] == 'email_rol') $lVal['members'] = array('role_alias'=>$lVal['members']);
        if (empty($lVal['members'])) $lVal['members'] = array('empty'=>'empty');
        foreach ($lVal['members'] as $lTmp => $lUserNames) {
          $lXls -> writeAsString($lUserNames);
          $lXls -> newLine();
          $lXls -> incX();
          $lXls -> incX();
          $lXls -> incX();
          $lXls -> incX();
          $lXls -> incX();
        }
        $lXls -> newLine();
        $lXls -> incX();
      }
      $lXls -> newLine();
      $this -> mCtr++;
      $lXls -> switchStyle();
    }

    return $lXls;
  }

  public function getReportAplCounts($aArray='') {
    $lEventIds = array();
    foreach ($this -> mIte as $lKey => $lVal) {
      $lEventIds[] = $lKey;
    }
    $lEventsNames = CCor_Res::extract('id','name_en', 'eve');

    if (!empty($aArray['from'])) $lStartDateFrom = date('Y-m-d', strtotime($aArray['from']));
    if (!empty($aArray['to'])) $lStartDateTo = date('Y-m-d', strtotime($aArray['to']));
    $lSql = 'SELECT rep.event_id, rep.event_prefix, apl.src, apl.status, apl.completed, count(*) AS Count ';
    $lSql.= 'FROM al_job_apl_loop_events rep ';
    $lSql.= 'LEFT OUTER JOIN al_job_apl_loop apl ON apl.id=rep.loop_id ';
    $lSql.= 'WHERE event_id IN('.implode(',', $lEventIds).')';
    if (!empty($lStartDateFrom)) $lSql.= ' AND apl.start_date > '.esc($lStartDateFrom);
    if (!empty($lStartDateTo)) $lSql.= ' AND apl.start_date < '.esc($lStartDateTo);
    $lSql.= ' GROUP BY rep.event_id, apl.status, apl.completed';
    $lSql.= ' ORDER BY Count DESC';
    $lQry = new CCor_Qry($lSql); #print_r($lSql);exit;

    $lXls = new CApi_Xls_Writer();

    $lXls -> addField('event_name', 'Approval Workflow');
    $lXls -> addField('job_type', 'Job Type');
    $lXls -> addField('prefix', 'Country-Prefix');
    $lXls -> addField('status', 'Status');
    $lXls -> addField('completed', 'Completed');
    $lXls -> addField('num', 'Counter');

    $lXls -> writeCaptions();
    $lXls -> switchStyle();

    foreach ($lQry as $lRow) {
      $lXls -> writeAsString($lEventsNames[$lRow['event_id']]);
      $lJobType = lan('job-'.$lRow['src'].'.menu');
      $lXls -> writeAsString($lJobType);
      $lXls -> writeAsString($lRow['event_prefix']);
      $lXls -> writeAsString($lRow['status']);
      $lCancelled = ('Y' == $lRow['completed']) ? 'Yes' : 'No';
      $lXls -> writeAsString($lCancelled);
      $lXls -> writeAsString($lRow['Count']);
      $lXls -> newLine();
      $lXls -> switchStyle();
    }
    return $lXls;
  }

}