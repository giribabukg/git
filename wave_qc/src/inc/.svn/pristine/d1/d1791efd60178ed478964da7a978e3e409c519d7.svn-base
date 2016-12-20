<?php
class CInc_Conditions_List extends CHtm_List {

  public function __construct() {
    parent::__construct('conditions');

    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('conditions.menu');

    $this -> mNewLnk = 'index.php?act=conditions.new&amp;type=';

    $this -> addCtr();
    $this -> addColumn('more', '', FALSE, array('width' => 16));
    $this -> addColumn('name', lan('lib.name'), TRUE);
    $this -> addColumn('type', lan('lib.type'), TRUE);
    $this -> addCpy();
    if ($this -> mCanDelete) {
      $this -> addDel();
    }
    $this -> addIsThisInUse();

    $this -> mDefaultOrder = 'name';

    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_cond c');
    $this -> mIte -> addCnd('mand='.MID);

    $this -> mType = '';
    if (!empty($this -> mSer)) {
      if (!empty($this -> mSer['name'])) {
        $lVal = '"%'.addslashes($this -> mSer['name']).'%"';
        $lCnd = '(c.name LIKE '.$lVal.' OR ';
        $lCnd.= 'c.type LIKE '.$lVal.')';
        $this -> mIte -> addCnd($lCnd);
      }
      if (!empty($this -> mSer['type'])) {
        $this -> mType = $this -> mSer['type'];
        $lCnd = '(c.type='.esc($this -> mSer['type']).')';
        $this -> mIte -> addCnd($lCnd);
      }
    }

    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);

    $this -> mMaxLines = $this -> mIte -> getCount();

    $this -> addPanel('new', $this -> getNewMenu());
    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());
    $this -> addPanel('cap', '|');
    $this -> addPanel('fil', $this -> getSearchForm());
  }

  protected function getNewMenu() {
    $lMenu = new CHtm_Menu(lan('conditions.new'));
    $lImg = '<i class="ico-w16 ico-w16-plus"></i>';

    $lReg = new CApp_Condition_Registry();
    $lType = $lReg -> getConditions();
    foreach ($lType as $lKey => $lRow) {
      $lMenu -> addItem($this -> mNewLnk.$lKey, $lRow['name'].' '.lan('lib.condition'), $lImg);
      $this -> mType[$lKey] = $lRow['name'];
    }
    $lRet = '<div class="p8">'.$lMenu -> getContent().'</div>';
    return $lRet;
  }

  protected function getSearchForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="conditions.ser" />'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
    $lRet.= '<td>'.htm(lan('lib.search')).'</td>';
    $lVal = (isset($this -> mSer['name'])) ? htm($this -> mSer['name']) : '';
    $lRet.= '<td><input type="text" name="val[name]" class="inp" value="'.$lVal.'" /></td>'.LF;
    $lRet.= '<td>'.btn(lan('lib.search'), '', '', 'submit').'</td>';
    if (!empty($this -> mSer)) {
      $lRet.= '<td>'.btn(lan('lib.show_all'), 'go("index.php?act=conditions.clser")').'</td>';
    }
    $lRet.= '</tr></table>';
    $lRet.= '</form>'.LF;
    return $lRet;
  }

  protected function getTdMore() {
    $this -> mMoreId = getNum('t');
    $lLnk = '<a class="nav" onclick="Flow.Std.togTr(\''.$this -> mMoreId.'\')">...</a>';
    return $this -> tdClass($lLnk, 'w16 ac');
  }

  protected function getTdType() {
    $lType = $this -> getCurVal();
    if (isset($this -> mType[$lType])) {
      $lType = $this -> mType[$lType];
    }
    return $this -> tda($lType);
  }

  protected function getTdCopy() {
    $lId = $this -> getInt('id');
    $lRet = '<a href="index.php?act=conditions.copy&id='.$lId.'" class="nav">';
    $lRet.= img('img/ico/16/copy.gif');
    $lRet.= '</a>';
    return $this -> td($lRet);
  }

  protected function getTdInuse() {
    $lId = $this -> getVal($this -> mIdField);

    $lTip = '';

    $lIsInUseInSteps = FALSE;
    $lIsInUseInEvents = FALSE;
    $lIsInUseInNested = FALSE;

    $lInStep = 'SELECT * FROM al_crp_step WHERE cond='.$lId;
    $lQry = new CCor_Qry($lInStep);
    foreach ($lQry as $lRow) {
      $lTip.='#Step: ';
      $lTip.= $lRow['name_'.LAN].BR;
      if ($lRow) $lIsInUseInSteps = TRUE;
    }

    $lInEventAction = 'SELECT * FROM al_eve_act WHERE cond_id='.$lId;
    $lQry = new CCor_Qry($lInEventAction);
    foreach ($lQry as $lRow) {
      $lTip.='#EventID: ';
      $lTip.= $lRow['eve_id'].BR;
      if ($lRow) $lIsInUseInEvents = TRUE;
    }

    $lInNestedCond = "SELECT * FROM al_cond WHERE params like '%:\"c\";s:%%:\"".$lId."\";%'";
    $lQry = new CCor_Qry($lInNestedCond);
    foreach ($lQry as $lRow) {
      if (!($lRow['type'] == 'complex')) continue;
      $lTip.= '#Nested Cond: ';
      $lTip.= $lRow['name'].BR;
      if ($lRow) $lIsInUseInNested = TRUE;
    }

    $lImg = ($lIsInUseInSteps || $lIsInUseInEvents || $lIsInUseInNested) ? 'img/ico/16/flag-03.gif' : 'img/ico/16/flag-00.gif';
    $lDis = img($lImg, array('data-toggle' => 'tooltip', 'data-tooltip-head' => lan('lib.inuse'), 'data-tooltip-body' => $lTip));

    $lRet.= $lDis;

    return $this -> td($lRet);
  }

  protected function afterRow() {
    $lRet = parent::afterRow();
    if ($this -> mMoreId) {
      $lRet.= '<tr id="'.$this -> mMoreId.'" class="togtr" style="display:none">'.LF;
      $lRet.= '<td class="td1 tg">&nbsp;</td>'.LF;
      $lRet.= '<td class="td1 p8"'.$this -> getColspan().'>';
      $lRet.= $this -> getDetails();
      $lRet.= '</td>'.LF;
      $lRet.= '</tr>'.LF;
    }
    return $lRet;
  }

  protected function getDetails() {
    $lType = $this -> getVal('type');
    $lParams = $this -> getVal('params');

    $lFac = new CApp_Condition_Registry();
    $lObj = $lFac -> factory($lType);
    $lObj -> setParams($lParams);
    $lParamToStr = $lObj -> paramToString();
    $lParamToSQL = $lObj -> paramToSQL();
    return $lParamToStr."<br><br>".lan('lib.sql').": ".$lParamToSQL;
  }
}