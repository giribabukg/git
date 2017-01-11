<?php
class CInc_Crp_Sta_List extends CHtm_List {

  protected $mUsrId = 0;

  public function __construct($aCrpId) {
    parent::__construct('crp-sta');

    $this -> mUsrId = CCor_Usr::getAuthId();

    $this -> getPriv('crp');
    $this -> mCid = intval($aCrpId);
    $this -> setAtt('class', 'tbl w700');
    $this -> mTitle = lan('crp.menu');
    $this -> mStdLnk = 'index.php?act=crp-sta.edt&amp;cid='.$this -> mCid.'&amp;id=';
    $this -> mDelLnk = 'index.php?act=crp-sta.del&amp;cid='.$this -> mCid.'&amp;id=';
    if (!$this -> mCanEdit) {
      $this -> mStdLnk = '';
    }

    $this -> mDefaultOrder = 'display';
    #$this -> addColumn('mor', '', FALSE, array('width' => '16'));
    $this -> addColumn('to', '', FALSE, array('width' => '16'));
    if (1 == $this -> mUsrId) {
      $this -> addColumn('display', '', FALSE, array('width' => '50'));
      $this -> addColumn('name', lan('lib.name'), FALSE, array('width' => '80%'));
    } else {
      $this -> addColumn('display', '', FALSE, array('width' => '16'));
      $this -> addColumn('name', lan('lib.name'), FALSE, array('width' => '100%'));
    }

    if ($this -> mCanDelete) {
      $this -> addDel();
    }

    if ($this -> mCanInsert) {
      $this -> addBtn(lan('crp-sta.new'), "go('index.php?act=crp-sta.new&id=".$this -> mCid."')", 'img/ico/16/plus.gif');
      $this -> addBtn(lan('crp-stp.new'), "go('index.php?act=crp-sta.newstp&id=".$this -> mCid."')", 'img/ico/16/plus.gif');
    }

    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_crp_status');
    $this -> mIte -> addCnd('mand='.MID);
    $this -> mIte -> addCnd('crp_id='.$this -> mCid);
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mMaxLines = $this -> mIte -> getCount();

    $this -> mSta = $this -> mIte -> getArray('id');
    $this -> getSteps();
    $this -> getEvents();
    $this -> mReg = new CApp_Event_Action_Registry();

    $lJs = 'function overCrp(aId) {'.LF;
    $lJs.= '$(aId).addClassName("cy"); '.LF;
    $lJs.= '}'.LF;
    $lJs.= 'function outCrp(aId) {'.LF;
    $lJs.= '$(aId).removeClassName("cy"); '.LF;
    $lJs.= '}'.LF;
    $lJs.= 'function toOverCrp(aId) {'.LF;
    $lJs.= '$$(".stp_"+aId).each(function(aTd){aTd.addClassName("cl")}); '.LF;
    $lJs.= '}'.LF;
    $lJs.= 'function toOutCrp(aId) {'.LF;
    $lJs.= '$$(".stp_"+aId).each(function(aTd){aTd.removeClassName("cl")}); '.LF;
    $lJs.= '}'.LF;
    $lPag = CHtm_Page::getInstance();
    $lPag -> addJs($lJs);

    $this -> mFlags = CCor_Res::extract('id', 'name_'.LAN, 'fla');;

  }

  protected function getSteps() {
    $this -> mStp = array();
    $this -> mEveId = array();
    $lArr = array_keys($this -> mSta);
    if (empty($lArr)) return;
    $lSql = 'SELECT stp.id,stp.from_id,stp.name_en,stp.name_de,stp.event,stp.flags,sta.display,stp.flag_act,stp.flag_stp';
    $lSql.= ' FROM al_crp_step stp, al_crp_status sta WHERE sta.mand='.MID.' AND stp.from_id IN ('.implode(',', $lArr).') AND stp.to_id=sta.id ORDER BY sta.display';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> mStp[$lRow['from_id']][] = $lRow;
      if (!empty($lRow['event'])) {
        $this -> mEveId[$lRow['event']] = 1;
      }
    }
  }

  protected function getEvents() {
    $this -> mEve = CCor_Res::extract('id', 'name_en', 'eve');
    $this -> mEveAct = array();
    $lArr = $this -> mEveId;
    $this -> dump($lArr);
    if (!empty($lArr)) {
      $lArr = implode(',', array_keys($lArr));
      $lQry = new CCor_Qry('SELECT * FROM al_eve_act WHERE mand='.MID.' AND eve_id IN ('.$lArr.') ORDER BY pos');
      foreach ($lQry as $lRow) {
        $this -> mEveAct[$lRow['eve_id']][] = $lRow;
      }
    }
  }

  protected function getTdTo() {
    $lDis = $this -> getInt('display');
    $lRet = '<td class="td1" id="sta_'.$lDis.'">';
    $lRet.= NB;
    $lRet.= '</td>';
    return $lRet;
  }

  protected function getTdName() {
    $lVal = htm($this -> getVal('name_'.LAN));
    $lDis = $this -> getInt('display');
    #$lRet = '<td class="td1" id="sta_'.$lDis.'" ';
    $lRet = '<td class="td1" ';
    $lRet.= 'onmouseover="toOverCrp('.$lDis.')" ';
    $lRet.= 'onmouseout="toOutCrp('.$lDis.')">';
    $lRet.= $this -> a($lVal);
    $lRet.= '</td>';
    return $lRet;
  }

  protected function getTdDisplay() {
    $lDis = $this -> getInt('display');
    $lPro = $this -> getInt('pro_con');
    $lRet = '<td class="td1 ac">';
    $lImg = img('img/crp/'.$lDis.'b.gif');
    if (1 == $this -> mUsrId) {
      $lImg.= NB.'->'.NB.img('img/crp/'.$lPro.'b.gif');
    }
    $lRet.= $this -> a($lImg, false);
    $lRet.= '</td>';
    return $lRet;
  }

  protected function getEventActionList($aEvent) {
    $lEve = intval($aEvent);
    if (!isset($this -> mEveAct[$lEve]) OR empty($this -> mEveAct[$lEve])) return '';

    $lArr = $this -> mEveAct[$lEve];
    $lRet = '<br /><table cellpadding="2" cellspacing="0" class="tbl">'.LF;
    $lRet.= '<tr>';
    $lRet.= '<td class="th3 w16">&nbsp;</td>';
    $lRet.= '<td class="th3 w100">'.lan('lib.pos').'</td>';
    $lRet.= '<td class="th3 w100">Action</td>';
    $lRet.= '<td class="th3 w100p">Parameters</td>';
    #$lRet.= '<td class="th3">Assertion</td>';
    $lRet.= '</tr>'.LF;
    foreach ($lArr as $lRow) {
      $lPar = unserialize($lRow['param']);
      $lTyp = $lRow['typ'];
      $lLnk = 'index.php?act=eve-act.edt&amp;id='.$lEve.'&amp;sid='.$lRow['id'];

      $lRet.= '<tr>';
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
      if ($this -> mCanEdit) {
        $lRet.= '<a href="'.$lLnk.'">';
        $lRet.= htm($this -> mReg -> getName($lTyp)).'</a></td>';
      } else {
        $lRet.= htm($this -> mReg -> getName($lTyp)).'</td>';
      }
      $lRet.= '<td class="td1 nw">'.htm($this -> mReg -> paramToString($lTyp, $lPar)).'</td>';
      #$lRet.= '<td class="td1 nw">&nbsp;</td>';
      $lRet.= '</tr>'.LF;
    }
    $lRet.= '</table>'.LF;
    return $lRet;
  }

  protected function getFlags($aFla) {
    $lRet = '';
    if (bitset($aFla, sfAmend)) {
      $lRet.= 'AR ';
    }
    if (bitset($aFla, sfAmendDecide)) {
      $lRet.= 'BP ';
    }
    if (bitset($aFla, sfUploadFile)) {
      $lRet.= 'UF ';
    }
    if (bitset($aFla, sfStartApl)) {
      $lRet.= 'APL+ ';
    }
    if (bitset($aFla, sfCloseApl)) {
      $lRet.= 'APL- ';
    }
    if (bitset($aFla, sfSelectAnnots)) {
      $lRet.= 'WE ';
    }
    return $lRet;
  }

  protected function afterRow() {
    $lId = $this -> getVal('id');
    if (empty($this -> mStp[$lId])) return;

    $lRet = '';
    foreach ($this -> mStp[$lId] as $lRow) {
      $lDis = $lRow['display'];
      $lRet.= '<tr>';
      $lRet.= '<td class="td1 stp_'.$lDis.'">';
      $lEve = $lRow['event'];
      if (empty($lEve)) {
        $lRet.= NB;
      } else {
        $lMor = getNum('t');
        $lRet.= '<a class="nav" onclick="Flow.Std.togTr(\''.$lMor.'\')">';
        $lRet.= '...</a>';
      }
      $lRet.= '</td>';
      $lRet.= '<td class="td2 ac">';
      $lRet.= img('img/crp/'.$lRow['display'].'l.gif');
      $lRet.= '</td>';
      $lRet.= '<td class="td2">';
      if ($this -> mCanEdit) {
        $lTdLink= '<a href="index.php?act=crp-sta.edtstp&amp;cid='.$this -> mCid.'&amp;id='.$lRow['id'].'" ';
        $lTdLink.= 'onmouseover="overCrp(\'sta_'.$lRow['display'].'\')" ';
        $lTdLink.= 'onmouseout="outCrp(\'sta_'.$lRow['display'].'\')" ';
        #$lTdLink.= 'class="db">';

        $lRet.= '<table width="100%"><tr><td class="nw ac">';
        $lRet.= $lTdLink;
        $lRet.= 'class="di">';
        $lRet.= htm($lRow['name_'.LAN]);
        $lRet.= '</a>';

        if (!empty($lRow['flags'])) {
          $lRet.= NB.$this -> getFlags($lRow['flags']);
        }
        //sollen die Flags wie Events eingebaut werden, vielleicht mittels fla_list->getActionTable($aEvent)
        $i = 1;
        $lTrennzeichen = '';
        if (!empty($lRow['flag_act'])) {
          $lRet.= '</td><td class="nw ac">';
          $lRet.= $lTdLink;
          $lRet.= 'class="di">';
          $lRet.= '('.lan('flag.activate').':';
          $lRet.= '</a>';

          $lRet.= '</td><td class="nw ac">';
          $lFlag = explode(',', $lRow['flag_act']);
          foreach ($lFlag as $lF) {
            if (1 < $i++) { $lTrennzeichen = ','; }
            $lRet.= '<a href="index.php?act=fla.edt&amp;id='.$lF.'" ';
            $lRet.= 'class="di">';//display inline verhindert den Zeilenumbruch
            $lRet.= $lTrennzeichen.NB.'<u>'.htm($this -> mFlags[$lF]).'</u>';
            $lRet.= '</a>';
          }
          $lRet.= ')';
        }

        $i = 1;
        $lTrennzeichen = '';
        if (!empty($lRow['flag_stp'])) {
          $lRet.= '</td><td class="nw ac">';
          $lRet.= $lTdLink;
          $lRet.= 'class="di">';
          $lRet.= '('.lan('flag.deactiv').':';
          $lRet.= '</a>';
          $lRet.= '</td><td class="nw ac">';
          $lFlag = explode(',', $lRow['flag_stp']);
          foreach ($lFlag as $lF) {
            if (1 < $i++) { $lTrennzeichen = ','; }
            $lRet.= '<a href="index.php?act=fla.edt&amp;id='.$lF.'" ';
            $lRet.= 'class="di">';//display inline verhindert den Zeilenumbruch
            $lRet.= $lTrennzeichen.NB.'<u>'.htm($this -> mFlags[$lF]).'</u>';
            $lRet.= '</a>';
          }
          $lRet.= ')';
        }
        $lRet.= '</td><td class="w100p">';
        $lRet.= $lTdLink;
        $lRet.= 'class="db">';
        $lRet.= NB.'</a>';
        $lRet.= '</td></tr></table>';
      } else {
        $lRet.= htm($lRow['name_'.LAN]);
      }
      $lRet.= '</td>';
      if ($this -> mCanDelete) {
        $lRet.= '<td class="td2 nw w16" align="right">';
        $lRet.= '<a class="nav" href="javascript:cnfDel(\'index.php?act=crp-sta.delstp&amp;cid='.$this -> mCid.'&amp;id='.$lRow['id'].'\', \''.LAN.'\')">';
        $lRet.= img('img/ico/16/del.gif');
        $lRet.= '</a>';
        $lRet.= '</td>'.LF;
      }
      $lRet.= '</tr>';
      if (!empty($lEve)) {
        $lRet.= '<tr style="display:none" id="'.$lMor.'">';
        $lRet.= '<td class="td1 tg">&nbsp;</td>';
        $lRet.= '<td class="td1 p8" colspan="3">';
        $lRet.= '<a href="index.php?act=eve-act&amp;id='.$lEve.'" class="nav">'.lan('lib.event').': ';
        if (isset($this -> mEve[$lEve])) {
          $lNam = $this -> mEve[$lEve];
          $lRet.= htm($lNam);
        }
        $lRet.= '</a>';
        $lRet.= $this -> getEventActionList($lEve);
        $lRet.= '</td>';
        $lRet.= '</tr>'.LF;
      }
    }
    return $lRet;
  }

}