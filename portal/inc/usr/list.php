<?php
class CInc_Usr_List extends CHtm_List {

  public function __construct($aMod = NULL) {
    if(!isset($aMod))
      $this -> mMod = 'usr';
    else
      $this -> mMod = $aMod;
    $this -> m2Act = $this -> mMod;

    parent::__construct($this -> mMod);
    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('usr.menu');

    $this -> addCtr();

    //CopyMem
    $lUsr = CCor_Usr::getInstance();
    $lCopyMem = intval($lUsr->getPref('usr.copymem'));
    if (!empty($lCopyMem)) {
      $lName = 'User ID '.$lCopyMem;
      $lQry = new CCor_Qry('SELECT firstname, lastname FROM al_usr WHERE id='.$lCopyMem);
      if ($lRow = $lQry->getAssoc()) {
        $lName= cat($lRow['firstname'], $lRow['lastname']);
      }
      $lGroups = array();
      //$lRemGruAdmin = "Admins";
      $lQry->query('SELECT g.name FROM al_gru g,al_usr_mem m WHERE m.uid='.$lCopyMem.' AND g.admin_level = 0 AND m.mand IN (0,'.MID.') AND g.id=m.gid ORDER BY g.name');
      foreach ($lQry as $lRow) {
        $lGroups[] = $lRow['name'];
      }
      $lMessage = 'Copied Membership from '.$lName;
      if (!empty($lGroups)) {
        $lMessage.= ' ('.implode(', ',$lGroups).')';
      }
      $this->msg($lMessage, mtUser);
      $this->addColumn('check');
    }

    #$this -> addColumn('flags',     '', FALSE, array('width' => '16'));
    $this -> addColumn('anrede',     lan('lib.salutation'), TRUE, array('width' => '16'));
    $this -> addColumn('firstname',  lan('lib.firstname'), TRUE);
    $this -> addColumn('lastname',   lan('lib.lastname'), TRUE);
    $this -> addColumn('company',    lan('lib.company'),  TRUE);
    $this -> addColumn('location',   lan('lib.location'), TRUE);
    $this -> addColumn('department', lan('usr.department'), TRUE);
    $this -> addColumn('phone',      lan('lib.phone'), TRUE);
    $this -> addColumn('email',      lan('lib.email'), TRUE);
    $this -> addColumn('mem',        lan('usr-mem.menu'), FALSE);
    $this -> addColumn('created',    lan('lib.createdate'), TRUE);
    $this -> addColumn('lastlogin',  lan('usr.lastlogin'), TRUE);
    $this -> addColumn('admlvl',     lan('usr.adminlevel'), false);

    if ($this -> mCanDelete) {
      $lWCAvailable = CCor_Cfg::get("wec.available", TRUE);
      if($lWCAvailable) {
        $this -> addColumn('wec');
        $lSql = 'SELECT uid FROM al_usr_info WHERE iid="wec_uid"';
        $lQry = new CCor_Qry($lSql);

        foreach ($lQry as $lRow) {
          $this -> mWec[$lRow['uid']] = true;
        }
      }
      $this -> addDel();
    }

    //Copy Memberships
    if ($this -> mCanInsert) {
      $this -> addBtn(lan('usr.new'), "go('index.php?act=".$this -> m2Act.".new')", 'img/ico/16/plus.gif');
      $this -> addColumn('copymem');
    }
    if($lUsr ->canRead('invite-usr')) {
      $this -> addBtn(lan('inv.usr'), "go('index.php?act=gru.invExt')", 'img/ico/16/plus.gif');
    }
    if (!empty($lCopyMem)) {
      $this -> addBtn(lan('usr.mem.paste'), "Flow.user.pasteMem(false)", 'img/ico/16/check-hi.gif');
      $this -> addBtn(lan('usr.mem.add'), "Flow.user.pasteMem(true)", 'img/ico/16/check-hi.gif');
      $this -> addBtn(lan('usr.mem.stopCpy').' ('.$lName.')', "go('index.php?act=".$this -> m2Act.".stopCopy')", 'img/ico/16/cancel.gif');
    }

    $this -> getPrefs();
    $this -> mIte = $this->getIterator();

    if (!empty($this -> mSer['name'])) {
      $lVal = $this -> mSer['name'];
      if (strpos($lVal, ',') !== FALSE) {
        $lLis = explode(',', $lVal);
        foreach ($lLis as $lValue) {
          $this -> mIte -> addCnd($this -> getCnd($lValue));
        }
      } else {
        $this -> mIte -> addCnd($this -> getCnd($lVal));
      }
    }
    $this -> mOpt = $lUsr -> getPref($this -> mMod.'.opt', 'act');
    if ('act' == $this -> mOpt) {
      $this -> mIte -> addCnd('del="N"');
    } else if ('del' == $this -> mOpt) {
      $this -> mIte -> addCnd('del="Y"');
    }

    if ($lUsr -> canRead('csv-exp')) {
      $this -> addPanel('cap2', '|');
      $this -> addPanel('exp', $this -> setExcelExportButton());
    }

    $this -> mIte -> setGroupBy('u.id');
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);

    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);

    $this -> mMaxLines = $this -> mIte -> getCount();
    #$this -> mIte -> setGroupBy('u.id');

    //$this -> mIte = $this -> mIte -> getArray();

    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());
    $this -> addPanel('cap', '| '.htmlan('gru.menu'));
    $this -> addPanel('fil', $this -> getFilterMenu());
    $this -> addPanel('ca2', '| Status');
    $this -> addPanel('sta', $this -> getStatusFilterMenu());
    $this -> addPanel('sca', '| '.htmlan('lib.search'));
    $this -> addPanel('ser', $this -> getSearchForm());

    $this -> mDate = new CCor_Date();
    $this -> mDateFmt = $lUsr -> getPref('date.fmt.xxl', lan('lib.date.long'));
    $this -> mAdmLvlHtbItems = CCor_Res::get('htb', 'admlvl');
  }

  protected function onBeforeContent() {
    $this->getMemberships();
    return parent::onBeforeContent();
  }

  protected function getIterator() {
    if (empty($this -> mFil['gru'])) {
      $lRet = new CCor_TblIte('al_usr u, al_usr_mand m');
      $lRet -> addCnd('m.mand IN (0,'.MID.')');
      $lRet -> addCnd('m.uid=u.id');
    } else {
      $lRet = new CCor_TblIte('al_usr u, al_usr_mand m, al_usr_mem g');
      $lRet -> addCnd('m.mand IN(0,'.MID.')');
      $lRet -> addCnd('m.uid=u.id');
      $lRet -> addCnd('g.uid=u.id');
      $lRet -> addCnd('g.gid='.intval($this -> mFil['gru']));
    }
    return $lRet;
  }

  protected function getParentGruSta($aGroupId){
    if (isset($this->mParentGruStaCache[$aGroupId])) {
      return $this->mParentGruStaCache[$aGroupId];
    }
    $lGroupId = intval($aGroupId);
    while ($lGroupId != 0) {
      $lQry = new CCor_Qry('SELECT parent_id,del FROM al_gru WHERE id = ' . $lGroupId .' ');
      $lRow = $lQry->getDat();
      if (!$lRow) {
        $this->mParentGruStaCache[$lGroupId] = true;
        // parent group not found, we can return true
        break;
      }
      $lParentId = $lRow['parent_id'];
      $lGrpStatus = $lRow['del'];
      if ($lGrpStatus == 'Y') {
        $this->mParentGruStaCache[$lGroupId] = false;
        $this->mParentGruStaCache[$aGroupId] = false;
        return false;
      }
      if ($lGroupId == $lParentId) {
        // not likely but if a group has itself as a parent, we'd have an infinite loop
        $this->mParentGruStaCache[$lGroupId] = true;
        break;
      }
      $lGroupId = $lParentId;
    }
    $this->mParentGruStaCache[$aGroupId] = true;
    return true;
  }

  public function getMemberships() {
    $this -> mMem = array();
    $lArr = array();
    foreach ($this -> mIte as $lRow) {
      $lArr[] = $lRow['id'];
    }
    if (!empty($lArr)) {
      $lQry = new CCor_Qry('SELECT m.uid,g.code,g.name,g.del,g.id as id FROM al_usr_mem m, al_gru g WHERE g.id=m.gid AND g.del="N" AND m.uid IN ('.implode(',', $lArr).') ORDER BY g.name');
      foreach ($lQry as $lRow) {
        //if child active and its parent deactivate then child will not shown
        $lResult= $this -> getParentGruSta($lRow['id']);
        if ($lResult==true)
        $this -> mMem[$lRow['uid']][] = $lRow;
      }
    }
  }

  protected function getCnd($aVal) {
    $lVal = addslashes(trim($aVal));
    $lRet = '(';
    $lRet.= '(firstname LIKE "%'.$lVal.'%") OR ';
    $lRet.= '(lastname LIKE "%'.$lVal.'%") OR ';
    $lRet.= '(company LIKE "%'.$lVal.'%") OR ';
    $lRet.= '(location LIKE "%'.$lVal.'%")';
    $lRet.= ')';
    return $lRet;
  }

  protected function getFilterMenu() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="usr.fil" />'.LF;
    $lRet.= '<select name="val[gru]" size="1" onchange="this.form.submit()">'.LF;
    #$lSrc = CCor_Res::extract('id', 'name', 'gru', array('parent_id' => 0));
    $lSrc = CCor_Res::extract('id', 'name', 'gru', array('gid' => 0));
    $lFil = (isset($this -> mFil['gru'])) ? $this -> mFil['gru'] : '';
    $lRet.= '<option value="">&nbsp;</option>'.LF;
    foreach ($lSrc as $lKey => $lVal) {
      $lRet.= '<option value="'.$lKey.'" ';
      if ($lKey == $lFil) {
        $lRet.= ' selected="selected"';
      }
      $lRet.= '>'.htm($lVal).'</option>'.LF;
      $lRet.= $this->getSubGroupOptions($lKey, 1, $lFil);
    }
    $lRet.= '</select>'.LF;
    $lRet.= '</form>';
    return $lRet;
  }

  protected function getSubGroupOptions($aParentId, $aIndent, $aSelected) {
    $lRet = '';
    $lSub = CCor_Res::extract('id', 'name', 'gru', array('gid' => $aParentId));
    foreach ($lSub as $lSid => $lNam) {
      $lRet.= '<option value="'.$lSid.'"';
      if ($lSid == $aSelected) {
        $lRet.= ' selected="selected"';
      }
      $lRet.= '>';
      $lRet.= str_repeat(NB, $aIndent * 2).'- ';
      $lRet.= htm($lNam).'</option>'.LF;
      $lRet.= $this->getSubGroupOptions($lSid, $aIndent + 1, $aSelected);
    }
    return $lRet;
  }

  protected function getStatusFilterMenu() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="'.$this -> m2Act.'.opt" />'.LF;
    $lArr = array();
    $lArr['all'] = '[all]';
    $lArr['act'] = 'Active Users';
    $lArr['del'] = 'Inactive Users';
    $lRet.= getSelect('val', $lArr, $this -> mOpt, array('onchange' => 'this.form.submit()'));
    $lRet.= '</form>';
    return $lRet;
  }

  protected function getSearchForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="'.$this -> m2Act.'.ser" />'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
    $lVal = (isset($this -> mSer['name'])) ? htm($this -> mSer['name']) : '';
    $lRet.= '<td><input id="lis_ser" type="text" name="val[name]" class="inp w200" value="'.$lVal.'" /></td>'.LF;
    $lRet.= '<td>'.btn(lan('lib.search'),'','','submit').'</td>';
    if (!empty($this -> mSer)) {
      $lRet.= '<td>'.btn(lan('lib.show_all'),'go("index.php?act='.$this -> m2Act.'.clser")').'</td>';
    }
    $lRet.= '</tr></table>';
    $lRet.= '</form>'.LF;

    $lJs = autoCpl('lis_ser', 'usr.ajx', $arr = array("cust" => "item.value"));
    $lJs.= 'jQuery(function(){ $("lis_ser").focus(); $("lis_ser").select() });';
    $lPag = CHtm_Page::getInstance();
    $lPag -> addJs($lJs);

    return $lRet;
  }

  protected function getTdFlags() {
    $lRet = '';
    $lFla = intval($this -> getCurVal());
    // password sent?
    if (bitSet($lFla, 1)) {
      $lImg = '03';
    } else {
      $lImg = '00';
    }
    // deleted?
    $lDel = $this -> getVal('del');
    if ('Y' == $lDel) {
      $lImg = '01';
    }
    $lRet.= img('img/ico/16/flag-'.$lImg.'.gif');
    if (CCor_Usr::getAuthId() == 1) {
      $lRet = '<a href="index.php?act='.$this -> m2Act.'.act&amp;id='.$this -> getVal('id').'" class="nav">'.$lRet.'</a>';
    }
    return $this -> tdc($lRet);
  }

  protected function getTdMem() {
    $lId = $this -> getInt('id');
    if (!empty($this -> mMem[$lId])) {
      $lArr = $this -> mMem[$lId];
      $lTip = '';
      $lDis = array();
      foreach ($lArr as $lRow) {
        $lTip.= htm($lRow['name']).BR;
        $lCod = $lRow['code'];
        if (empty($lCod)) {
          $lCod = '['.substr($lRow['name'], 0, 3).'...]';
        }
        $lDis[] = $lCod;
      }
      $lRet = '';
      $lDis = implode(', ', $lDis);
      if (strlen($lDis) > 20) {
        $lDis = substr($lDis, 0, 20).'...';
      }
      $lRet.= htm($lDis);

      return $this -> td($lRet,$lId,array('data-toggle' => 'tooltip', 'data-tooltip-head' => lan('usr-mem.menu'), 'data-tooltip-body' => $lTip));
    } else {
      return $this -> td('');
    }
  }

  protected function getExcelMem() {
    $lId = $this -> getInt('id');
    if (!empty($this -> mMem[$lId])) {
      $lArr = $this -> mMem[$lId];
      $lTip = '';
      $lDis = array();
      foreach ($lArr as $lRow) {
        $lDis[] = utf8_decode($lRow['name']);
      }
      $lDis = implode(', ', $lDis);
      return $lDis;
    } else {
      return '';
    }
  }

  protected function getTdCreated() {
    $lVal = $this -> getCurVal();
    $this -> mDate -> setSql($lVal);
    return $this -> tda($this -> mDate -> getFmt($this -> mDateFmt));
  }

  protected function getTdLastlogin() {
    $lVal = $this -> getCurVal();
    $this -> mDatetime = new CCor_Datetime($lVal);
    return $this -> tda($this -> mDatetime -> getFmt( lan('lib.datetime.long') ));
  }

  protected function getTdDel() {
    $lVal = $this -> getVal('del');
    $lRet = '<td class="'.$this -> mCls.' nw w16" align="right">';
    if ('N' == $lVal) {
      $lRet.= '<a class="nav" href="javascript:Flow.Std.cnf(\''.$this -> getDelLink().'\', \'cnfDel\')">';
    } else {
      $lUrl = 'index.php?act='.$this -> m2Act.'.undel&amp;id='.$this -> getVal('id');
      $lRet.= '<a class="nav" href="javascript:Flow.Std.cnf(\''.$lUrl.'\', \'cnfUnDel\')">';
    }
    $lRet.= img('img/ico/16/del.gif');
    $lRet.= '</a>';
    $lRet.= '</td>'.LF;
    return $lRet;
  }

  protected function getTdWec() {
    $lUid = $this -> getInt('id');
    if (isset($this -> mWec[$lUid])) {
      return $this -> td();
    }
    $lRet = '<a class="nav" href="index.php?act='.$this -> m2Act.'.wecusr&id='.$lUid.'">';
    $lRet.= img('img/ico/16/mt-16.gif').'</a>';
    return $this -> td($lRet);
  }

  protected function getTdCopymem() {
    $lUid = $this -> getInt('id');
    $lRet = '<a class="nav" href="index.php?act='.$this -> m2Act.'.copymem&id='.$lUid.'">';
    $lRet.= img('img/ico/16/copy.gif').'</a>';
    return $this -> td($lRet);
  }

  protected function getTdCheck() {
    $lUid = $this -> getInt('id');
    $lRet = '<input type="checkbox" class="cb" value="'.$lUid.'" />';
    return $this -> td($lRet);
  }

  protected function getTdAdmlvl() {
    $lUid = $this -> getInt('id');
    $Any = new CCor_Anyusr($lUid);
    $lUsrAdminLevel = $Any -> getAdminLevel();
    if ($lUsrAdminLevel != 0) {
      $lAdminLevelDesc = $this -> mAdmLvlHtbItems[$lUsrAdminLevel];
      $lContent = toolTip($lAdminLevelDesc, 'Admin Level').$lUsrAdminLevel.'</span>';
    } else $lContent = $lUsrAdminLevel;
    return $this -> td($lContent);;
  }

  protected function getExcelAdmlvl() {
    $lUid = $this -> getInt('id');
    $Any = new CCor_Anyusr($lUid);
    $lUsrAdminLevel = $Any -> getAdminLevel();

    if ($lUsrAdminLevel != 0) {
      $lAdminLevelDesc = $this -> mAdmLvlHtbItems[$lUsrAdminLevel];
      return $lContent = $lAdminLevelDesc;
   }
   else return $lUsrAdminLevel;
  }

  protected function setExcelExportButton() {
    $lResCsv = 'go("index.php?act=usr.xlsexp")';
    $this -> addBtn('Export-User-List', $lResCsv, 'img/ico/16/excel.gif', true);
  }

  public function getExcel() {

    $lXls = new CApi_Xls_Writer();
    $this->getMemberships();

    $this -> removeColumn('ctr');
    $this -> removeColumn('del');
    $this -> removeColumn('copymem');
    $this -> removeColumn('wec');

    $lXls -> addField('anrede', lan('lib.salutation'));
    $lXls -> addField('firstname', lan('lib.firstname'));
    $lXls -> addField('lastname', lan('lib.lastname'));
    $lXls -> addField('company', lan('lib.company'));
    $lXls -> addField('location', lan('lib.location'));
    $lXls -> addField('department', lan('usr.department'));
    $lXls -> addField('phone', lan('lib.phone'));
    $lXls -> addField('email', lan('lib.email'));
    $lXls -> addField('mem', lan('usr-mem.menu'));
    $lXls -> addField('created', lan('lib.createdate'));
    $lXls -> addField('lastlogin', lan('usr.lastlogin'));
    $lXls -> addField('del', 'Status');
    $lXls -> addField('admlvl', lan('usr.adminlevel'));
    $lXls -> writeCaptions();
    $lXls -> switchStyle();


    foreach ($this -> mIte  as $this -> mRow) {

      $lUserStatus = ($this -> mRow['del'] == 'N') ? 'Active' : 'Inactive';

      $lXls -> writeAsString($this -> mRow['anrede']);
      $lXls -> writeAsString(utf8_decode($this -> mRow['firstname']));
      $lXls -> writeAsString(utf8_decode($this -> mRow['lastname']));
      $lXls -> writeAsString(utf8_decode($this -> mRow['company']));
      $lXls -> writeAsString(utf8_decode($this -> mRow['location']));
      $lXls -> writeAsString(utf8_decode($this -> mRow['department']));
      $lXls -> writeAsString($this -> mRow['phone']);
      $lXls -> writeAsString($this -> mRow['email']);
      $lXls -> writeAsString($this->getExcelMem());
      $lXls -> writeAsString($this -> mRow['created']);
      $lXls -> writeAsString($this -> mRow['lastlogin']);
      $lXls -> writeAsString($lUserStatus);
      $lXls -> writeAsString($this->getExcelAdmlvl());
      $lXls -> newLine();
      $this -> mCtr++;
      $lXls -> switchStyle();
    }

    return $lXls;
  }

}
