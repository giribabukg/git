<?php
class CInc_Fla_List extends CHtm_List {

  /**
   * Registry for action types
   *
   * @var CApp_Event_Action_Registry
   */
  protected $mReg;
  protected $mEventsStr = '';
  protected $mEvent = array();
  protected $mAction = array();
  protected $mAllEventNames = array();

  public function __construct() {
    parent::__construct('fla');
    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('flag.menu');

    $this -> addColumn('more','', false, array('width' => 16));
    $this -> addColumn('name_'.LAN, 'Name', TRUE);
    $this -> mDefaultOrder = 'name_'.LAN;

    if ($this -> mCanDelete) {
      $this -> addDel();
    }

    $this -> addBtn(lan('flag.new'), "go('index.php?act=fla.new')", '<i class="ico-w16 ico-w16-plus"></i>');

    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_fla');
    $this -> mIte -> addCnd('mand='.MID);
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);
    $this -> mMaxLines = $this -> mIte -> getCount();

    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());

    $this -> preLoad();
    $this -> mReg = new CApp_Event_Action_Registry();
  }

  protected function preLoad() {
    $lAllEvents = array();
    $this -> mIte = $this -> mIte -> getArray('id');
    foreach ($this -> mIte as $lRow) {
      $lFla = intval($lRow['id']);
      $this -> mEvent[$lFla] = array();
      $lEvent = array();

      $lEvent['flag.eve.activate'] = intval($lRow['eve_act']);
      $lEvent['flag.eve.confirm'] = intval($lRow['eve_conf']);
      $lEvent['flag.eve.mandatory'] = intval($lRow['eve_mand']);
      foreach ($lEvent as $lK => $lEve) {
        if (0 < $lEve) {
          $lAllEvents[ $lEve ] = $lEve;
          $this -> mEvent[$lFla][ $lK ] = $lEve;
        }
      }
    }

    if (!empty($lAllEvents)) {
      $lEvent = array_map('esc', $lAllEvents);
      $this -> mEventsStr = implode(',', $lEvent);

      $this -> mAllEventNames = CCor_Res::extract('id', 'name_'.LAN, 'eve');
      $this -> getActions();
    }
  }

  protected function getActions() {
    $lSql = 'SELECT * FROM al_eve_act WHERE mand='.MID.' AND eve_id IN ('.$this -> mEventsStr.') ORDER BY pos';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lEve = intval($lRow['eve_id']);
      $this -> mAction[$lEve][] = $lRow;
    }
  }

  protected function getTdMore() {
    $lFlaId = $this -> getInt('id');
    if (!empty($this -> mEvent[$lFlaId])) {
      $lRet = '';
      $this -> mMoreId = getNum('t');
      $lRet = '<a class="nav" onclick="Flow.Std.togTr(\''.$this -> mMoreId.'\')">';
      $lRet.= '...</a>';
      return $this -> tdClass($lRet, 'w16');
    } else {
      $this -> mMoreId = NULL;
      return $this -> td();
    }
  }

  protected function afterRow() {
    $lFlaId = $this -> getInt('id');
    $lRet = parent::afterRow();
    if ($this -> mMoreId) {
      $lRet.= '<tr id="'.$this -> mMoreId.'" class="togtr" style="display:none">'.LF;
      $lRet.= '<td class="td1 tg">&nbsp;</td>'.LF;
      $lRet.= '<td class="td1 p8"'.$this -> getColspan().'>';

      $i = 1;
      foreach ($this -> mEvent[$lFlaId] as $lEveType => $lEvent) {
        if (1 < $i++) {
          $lRet.= BR;
        }
        $lRet.= '<a href="index.php?act=eve-act&amp;id='.$lEvent.'" class="nav">'.lan($lEveType).': ';
        if (isset($this -> mAllEventNames[$lEvent])) {
          $lNam = $this -> mAllEventNames[$lEvent];
          $lRet.= htm($lNam);
        }
        $lRet.= '</a>';
        $lRet.= $this -> getActionTable($lEvent);
      }
      $lRet.= '</td>'.LF;
      $lRet.= '</tr>'.LF;
    }
    return $lRet;
  }

  protected function getActionTable($aEvent) {
    if (empty($this -> mAction[$aEvent])) return '';
    $lArr = $this -> mAction[$aEvent];
    $lRet = BR;
    $lRet.= '<table cellpadding="2" cellspacing="0" class="tbl">'.LF;
    $lRet.= '<tr>';
    #$lRet.= '<td class="th3 w16">&nbsp;</td>';
    $lRet.= '<td class="th3 w16">&nbsp;</td>';
    $lRet.= '<td class="th3 w100">'.lan('lib.pos').'</td>';
    $lRet.= '<td class="th3 w100">Action</td>';
    $lRet.= '<td class="th3 w100p">Parameters</td>';
    #$lRet.= '<td class="th3">Assertion</td>';
    $lRet.= '</tr>'.LF;
    foreach ($lArr as $lRow) {
      $lPar = unserialize($lRow['param']);
      $lTyp = $lRow['typ'];
      $lLnk = 'index.php?act=eve-act.edt&amp;id='.$aEvent.'&amp;sid='.$lRow['id'];

      $lRet.= '<tr>';
      $lDet = $this -> mReg -> getParamDetails($lTyp, $lPar);
      if (empty($lDet)) {
        #$lRet.= '<td class="td1">&nbsp;</td>';
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
        $lRet.= img('img/ico/16/flag-03.gif');
      } else {
        $lRet.= img('img/ico/16/flag-00.gif');
      }
      $lRet.= '</td>';
      $lRet.= '<td class="td1 nw">'.($lRow['pos']+1).'</td>';
      $lRet.= '<td class="td1 nw">';
      $lRet.= '<a href="'.$lLnk.'">';
      $lRet.= htm($this -> mReg -> getName($lTyp)).'</a></td>';
      $lRet.= '<td class="td1 nw">'.htm($this -> mReg -> paramToString($lTyp, $lPar)).'</td>';
      #$lRet.= '<td class="td1 nw">&nbsp;</td>';
      $lRet.= '</tr>'.LF;

      if (!empty($lDet)) {
        $lRet.= '<tr class="togTr">';
        $lRet.= '<tr id="'.$lTog.'" class="togtr" style="display:none">'.LF;
        $lRet.= '<td class="td1 tg">&nbsp;</td>'.LF;
        $lRet.= '<td class="td1 p8" colspan="3">';
        $lRet.= htm($lDet);
        $lRet.= '</td>'.LF;
        $lRet.= '</tr>'.LF;
      }
    }
    $lRet.= '</table>'.LF;
    return $lRet;
  }
}