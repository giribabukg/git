<?php
class CInc_Crp_Sta_List extends CHtm_List {

  protected $mUsrId = 0;
  protected $mCrpCode = '';

  public function __construct($aCrpId) {
    parent::__construct('crp-sta');

    $this -> mUsrId = CCor_Usr::getAuthId();

    $this -> getPriv('crp');
    $this -> mCid = intval($aCrpId);
    $lCrp = CCor_Res::extract('id', 'code', 'crpmaster');
    $this -> mCrpCode = ((!empty($lCrp) AND isset($lCrp[$this -> mCid])) ? $lCrp[$this -> mCid] : '' );

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

    $this -> addColumn('member', "", FALSE, array("width" => "5"), "");

    if ($this -> mCanDelete) {
      $this -> addDel();
    }

    if ($this -> mCanInsert) {
      $this -> addBtn(lan('crp-sta.new'), "go('index.php?act=crp-sta.new&id=".$this -> mCid."')", 'img/ico/16/plus.gif');
      $this -> addBtn(lan('crp-stp.new'), "go('index.php?act=crp-sta.newstp&id=".$this -> mCid."')", 'img/ico/16/plus.gif');
      $this -> addBtn(lan('crp-stp.new-independent'), "go('index.php?act=crp-sta.newstpind&id=".$this -> mCid."')", 'img/ico/16/plus.gif');
    }

    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_crp_status');
    $this -> mIte -> addCnd('mand='.MID);
    $this -> mIte -> addCnd('crp_id='.$this -> mCid);
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mMaxLines = $this -> mIte -> getCount();

    $this -> addBtn(lan('crp.export'), "go('index.php?act=crp-sta.export&id=".$this -> mCid."','tab')", 'img/ico/16/next-hi.gif');
    if ($this -> mCanInsert && $this->mMaxLines == 0) {
      $this -> addBtn(lan('crp.import'), "go('index.php?act=crp-sta.import&id=".$this -> mCid."')", 'img/ico/16/back-hi.gif');
    }

    $this -> mSta = $this -> mIte -> getArray('id');
    $this->mIndependent = new CCor_Dat;
    $this->mIndependent['name_'.LAN] = lan('crp.independent');
    $this->mSta[0] = $this->mIndependent;
    
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
  
  protected function getRows() {
    $lRet = parent::getRows();
    if (empty($this -> mStp[0])) {
      return $lRet;
    }
    $this->mRow = $this->mIndependent;
    $this->mStdLnk = '';
    $this->mCls = 'th2';
    $lRet.= $this -> beforeRow();
    $lRet.= $this -> getRow();
    $lRet.= $this -> afterRow();
    return $lRet;
  }
  
  protected function getTrTag() {
    $lDis = $this -> getInt('display');
    if (empty($lDis)) {
      return '<tr>';
    } else {
      return parent::getTrTag();
    }
  }
  
  protected function getTdDel() {
    $lId = $this->getInt('id');
    if (empty($lId)) {
      return $this->td(NB);
    } else {
      return parent::getTdDel();
    }
  }

  protected function getMembers($lStpId) {
    if($lStpId === NULL) {
      $lStpId = $this->getVal("id");
    }
    $lQry = new CCor_Qry();

    //Roles
    $lSql = "SELECT DISTINCT role_id FROM al_rol_rig_stp WHERE stp_id = '". $lStpId ."'";
    $lRes = $lQry->query($lSql);
    $lRig = $lQry->getAssocs();
    $lAllRoles = CCor_Res::extract("id", "name", "rol");
    $lRoles = "<ul>";
    foreach($lRig as $lRow) {
      $lRoles .= "<li>". $lAllRoles[$lRow["role_id"]] ."</li>";
    }
    $lRoles .= "</ul>";

    //Groups
    $lSql = "SELECT DISTINCT gru_id FROM al_gru_rig_stp WHERE stp_id = '". $lStpId ."'";
    $lRes = $lQry->query($lSql);
    $lRig = $lQry->getAssocs();
    $lAllGroups = CCor_Res::extract("id", "name", "gru");
    $lGroups = "<ul>";
    foreach($lRig as $lRow) {
      $lGroups .= "<li>". $lAllGroups[$lRow["gru_id"]] ."</li>";
    }
    $lGroups .= "</ul>";

    //Users
    $lSql = "SELECT DISTINCT usr_id FROM al_usr_rig_stp WHERE stp_id = '". $lStpId ."'";
    $lRes = $lQry->query($lSql);
    $lRig = $lQry->getAssocs();
    $lAllUsers = CCor_Res::extract("id", "user", "usr");
    $lUser = "<ul>";
    foreach($lRig as $lRow) {
      $lUser .= "<li>". $lAllUsers[$lRow["usr_id"]] ."</li>";
    }
    $lUser .= "</ul>";

    //Tooltip
    $lTooltip = '<p class=\'th1\'>'.lan("rol.menu").'</p>';
    $lTooltip .= '<p>'.$lRoles.'</p>';
    $lTooltip .= '<p class=\'th1\'>'.lan("gru.menu").'</p>';
    $lTooltip .= '<p>'.$lGroups.'</p>';
    $lTooltip .= '<p class=\'th1\'>'.lan("usr.menu").'</p>';
    $lTooltip .= '<p>'.$lUser.'</p>';
    $lRet = '<td class="td2" data-toggle="tooltip" data-tooltip-head="" data-tooltip-body="'.$lTooltip.'">';
    $lRet .= '<img src="img/ico/16/usr.gif">';
    $lRet .= '</td>';

    return $lRet;
  }

  protected function getSteps() {
    $this -> mStp = array();
    $this -> mEveId = array();
    $lArr = array_keys($this -> mSta);
    if (empty($lArr)) return;
    $lSql = 'SELECT stp.id,stp.from_id';
    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $lSql.= ',stp.name_'.$lLang;
    }
    $lSql.= ',stp.event,stp.flags,sta.display,stp.flag_act,stp.flag_stp,stp.cond ';
    $lSql.= 'FROM al_crp_step stp, al_crp_status sta ';
    $lSql.= 'WHERE sta.mand='.MID.' AND stp.from_id IN ('.implode(',', $lArr).') AND stp.to_id=sta.id ';
    $lSql.= 'AND stp.crp_id='.$this -> mCid.' '; 
    $lSql.= 'ORDER BY sta.display';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> mStp[$lRow['from_id']][] = $lRow;
      if (!empty($lRow['event'])) {
        $this -> mEveId[$lRow['event']] = 1;
      }
    }
    
    // Independent Status changes
    $lSql = 'SELECT id,event,flags,cond,name_'.LAN.' ';
    $lSql.= 'FROM al_crp_step  ';
    $lSql.= 'WHERE mand='.MID.' AND from_id=0 ';
    $lSql.= 'AND crp_id='.$this -> mCid.' '; 
    $lSql.= 'ORDER BY name_'.LAN;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> mStp[0][] = $lRow;
      if (!empty($lRow['event'])) {
        $this -> mEveId[$lRow['event']] = 1;
      }
    }
  }

  protected function getEvents() {
    $this -> mEve = CCor_Res::extract('id', 'name_'.LAN, 'eve');
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
    $lRet = '<td class="'.$this->mCls.'" id="sta_'.$lDis.'">';
    $lRet.= NB;
    $lRet.= '</td>';
    return $lRet;
  }

  protected function getTdName() {
    $lVal = htm($this -> getVal('name_'.LAN));
    $lDis = $this -> getInt('display');
    if (empty($lDis)) {
      $lRet = '<td class="th2 p4 b">'.$lVal.'</td>';
    } else {
      $lRet = '<td class="td1" ';
      $lRet.= 'onmouseover="toOverCrp('.$lDis.')" ';
      $lRet.= 'onmouseout="toOutCrp('.$lDis.')">';
      $lRet.= $this -> a($lVal);
      $lRet.= '</td>';
    }
    return $lRet;
  }

  protected function getTdDisplay() {
    $lDis = $this -> getInt('display');
    $lPro = $this -> getInt('pro_con');
    $lRet = '<td class="'.$this->mCls.' ac">';
	$lPath = CApp_Crpimage::getSrcPath($this -> mCrpCode, 'img/crp/'.$lDis.'b.gif');
    $lImg = img($lPath);
    if (1 == $this -> mUsrId AND 'pro' != $this -> mCrpCode) {
	  $lPath = CApp_Crpimage::getSrcPath($this -> mCrpCode, 'img/crp/'.$lPro.'b.gif');
      $lImg.= NB.'->'.NB.img($lPath);
    }
    if (empty($lDis)) $lImg = NB;
    $lRet.= $this -> a($lImg, false);
    $lRet.= '</td>';
    return $lRet;
  }

  protected function getEventActionList($aEvent, $aFlag) {
    $lEve = intval($aEvent);
    if (!isset($this -> mEveAct[$lEve]) OR empty($this -> mEveAct[$lEve])) return '';

    $Conds = CCor_Res::extract('id', 'name', 'cond');

    $lArr = $this -> mEveAct[$lEve];
    $lRet = '<br /><table cellpadding="2" cellspacing="0" class="tbl">'.LF;
    $lRet.= '<tr>';
    $lRet.= '<td class="th3 w16">&nbsp;</td>';
    $lRet.= '<td class="th3 w100">'.lan('lib.pos').'</td>';
    $lRet.= '<td class="th3 w100">Action</td>';
    $lRet.= '<td class="th3 w100p">Parameters</td>';
    $lRet.= '<td class="th3 w100p">Conditions</td>';
    $lRet.= '<td class="th3">Duration</td>';
    $lRet.= '</tr>'.LF;
    foreach ($lArr as $lRow) {
      $lPar = unserialize($lRow['param']);
      $lCondId = $lRow['cond_id'];
      $lCond = $lCondId > 0 ? $Conds[$lCondId] : '';
      $lTyp = $lRow['typ'];
      $lLnk = 'index.php?act=eve-act.edt&amp;id='.$lEve.'&amp;sid='.$lRow['id'];

      $lRet.= '<tr>';
      $lRet.= '<td class="td1">';
      $lAct = $lRow['active'];
      if ($lAct) {
        $lRet.= img('img/ico/16/flag-03.gif');//hat nix mit CRP-Flags zu tun
      } else {
        $lRet.= img('img/ico/16/flag-00.gif');
      }
      $lRet.= '</td>';
      $lRet.= '<td class="td1 nw">';
      if (EVENT_DEFER_POSITION == $lRow['pos']) {
        $lRet.= lan('lib.eve.deferred');
      } else {
        $lRet.= ($lRow['pos']+1);
      }
      $lRet.= '</td>';

      $lRet.= '<td class="td1 nw">';
      if ($this -> mCanEdit) {
        $lRet.= '<a href="'.$lLnk.'">';
        $lRet.= htm($this -> mReg -> getName($lTyp)).'</a></td>';
      } else {
        $lRet.= htm($this -> mReg -> getName($lTyp)).'</td>';
      }
      $lRet.= '<td class="td1 nw">'.htm($this -> mReg -> paramToString($lTyp, $lPar)).'</td>';
      $lRet.= '<td class="td1 nw">'.$lCond.'</td>';
      $lRet.= '<td class="td1 nw">'.$lRow['dur'].'</td>';
      $lRet.= '</tr>'.LF;
    }

    //footer
    if (bitset($aFlag, sfStartApl)) {
      $lFunc = $this -> countFunction($lArr);
      $lRet.= '<tr>';
      $lRet.= '<td class="td1" colspan="4">&nbsp;</td>';
      $lRet.= '<td class="td1 nw">'.$lFunc['val'].'</td>';
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
    $lId = $this -> getInt('id');
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
	  $lPath = CApp_Crpimage::getSrcPath($this -> mCrpCode, 'img/crp/'.$lRow['display'].'l.gif');
      $lRet.= img($lPath);
      $lRet.= '</td>';
      $lRet.= '<td class="td2">';
      if ($this -> mCanEdit) {
        $lRet.= '<table><tr><td class="nw">';
        $lRetLink = '<a href="index.php?act=crp-sta.edtstp&amp;cid='.$this -> mCid.'&amp;id='.$lRow['id'].'" ';
        $lRetLink.= 'onmouseover="overCrp(\'sta_'.$lRow['display'].'\')" ';
        $lRetLink.= 'onmouseout="outCrp(\'sta_'.$lRow['display'].'\')" ';
        $lRet.= $lRetLink;
        $lRet.= 'class="db">';
        $lRet.= htm($lRow['name_'.LAN]);
        if (!empty($lRow['flags'])) {
          $lRet.= NB.$this -> getFlags($lRow['flags']);
        }
        if ($lRow['cond'] != 0) {
          $lRet.= NB.'(Condition:';
          $lRet.= $this -> getConditionName($lRow['cond']);
          $lRet.= ')';
        }
        //sollen die Flags wie Events eingebaut werden, vielleicht mittels fla_list->getActionTable($aEvent)
        $i = 1;
        $lTrennzeichen = '';
        if (!empty($lRow['flag_act'])) {
          $lRet.= '('.lan('flag.activate').':';

          $lRet.= '</td><td class="nw">';

          $lFlag = explode(',', $lRow['flag_act']);
          foreach ($lFlag as $lF) {
            if (1 < $i++) {
              $lTrennzeichen = ',';
            }
            $lRet.= '<a href="index.php?act=fla.edt&amp;id='.$lF.'" ';
            $lRet.= 'class="di">';
            $lRet.= $lTrennzeichen.NB.'<u>'.htm($this -> mFlags[$lF]).'</u>';
            $lRet.= '</a>';
          }
          $lRet.= ')';
        }

        $lRet.= '</td><td class="nw">';
        $lRet.= $lRetLink;
        $lRet.= 'class="db">';
        $i = 1;
        $lTrennzeichen = '';
        if (!empty($lRow['flag_stp'])) {
          $lRet.= '('.lan('flag.deactiv').':';

          $lRet.= '</td><td class="nw">';

          $lFlag = explode(',', $lRow['flag_stp']);
          foreach ($lFlag as $lF) {
            if (1 < $i++) {
              $lTrennzeichen = ',';
            }
            $lRet.= '<a href="index.php?act=fla.edt&amp;id='.$lF.'" ';
            $lRet.= 'class="di">';
            $lRet.= $lTrennzeichen.NB.'<u>'.htm($this -> mFlags[$lF]).'</u>';
            $lRet.= '</a>';
          }
          $lRet.= ')';
        }

        $lRet.= '</td><td class="nw" width="100%">';
        $lRet.= $lRetLink;
        $lRet.= 'class="db">';
        $lRet.= NB.'</a></td>';
        $lRet.= '</td></tr></table>';
      } else {
        $lRet.= htm($lRow['name_'.LAN]);
      }


      $lRet .= $this->getMembers($lRow["id"]);


      if ($this -> mCanDelete) {
        $lRet.= '<td class="td2 nw w16 ac" align="right">';
        $lRet.= '<a class="nav" href="javascript:Flow.Std.cnf(\'index.php?act=crp-sta.delstp&amp;cid='.$this -> mCid.'&amp;id='.$lRow['id'].'\', \'cnfDel\')">';
        $lRet.= img('img/ico/16/del.gif');
        $lRet.= '</a>';
        $lRet.= '</td>'.LF;
      }
      $lRet.= '</tr>';
      if (!empty($lEve)) {
        $lRet.= '<tr style="display:none" id="'.$lMor.'">';
        $lRet.= '<td class="td1 tg">&nbsp;</td>';
        $lRet.= '<td class="td1 p8" colspan="4">';
        $lRet.= '<a href="index.php?act=eve-act&amp;id='.$lEve.'" class="nav">'.lan('lib.event').': ';
        if (isset($this -> mEve[$lEve])) {
          $lNam = $this -> mEve[$lEve];
          $lRet.= htm($lNam);
        }
        $lRet.= '</a>';
        $lRet.= $this -> getEventActionList($lEve, $lRow['flags']);
        $lRet.= '</td>';
        $lRet.= '</tr>'.LF;
      }
    }
    return $lRet;
  }

  protected function countFunction($aArr) {
    return CEve_Act_Cnt::countDurationTime($aArr);
  }

  protected function getConditionName($aConditionsId) {
    $lSql = 'SELECT name FROM al_cond WHERE id='.$aConditionsId;
    return CCor_Qry::getStr($lSql);
  }
}