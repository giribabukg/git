<?php
class CInc_Gru_List extends CHtm_List {

  public function __construct($aParent = -1, $aBuffered = 0, $aStart = -1, $aUntil = -1) {
    parent::__construct('gru');
    $this -> setAtt('class', 'tbl w800');
    $this -> mTitle = lan('gru.menu');
    $lUsr = CCor_Usr::getInstance();
    $this -> mBuffered = $aBuffered;
    $this -> mStart = $aStart;
    $this -> mUntil = $aUntil;

    $this -> mLevel = 0;
    $this -> mMaxLevel = 10;

    $this -> addColumn('mor', '',false, array('width' => 16));
    $this -> addColumn('name',   'Name', false, array('colspan' => $this->mMaxLevel));
    $this -> addColumn('admlvl',   lan('usr.adminlevel'), false);
    $this -> addColumn('mem',  '', false, array('width' => 30));
    if ($this -> mCanInsert) {
      $this -> addColumn('ins', '', false, array('width' => 16));
      $this -> addBtn(lan('gru.new'), "go('index.php?act=gru.new')", '<i class="ico-w16 ico-w16-plus"></i>');
      $this -> addBtn(lan('gru.newExt'), "go('index.php?act=gru.newExt')", '<i class="ico-w16 ico-w16-plus"></i>');
    }
    if($lUsr ->canRead('invite-usr')) {
      $this -> addColumn('inv',  '', false, array('width' => 30));
    }
    if ($this -> mCanDelete) {
      $this -> addDel();
    } 
    $this -> getPrefs();
    $this -> mIte = new CCor_TblIte('al_gru');
    $this -> addBtn(lan('lib.expandall'), 'Flow.expandAllTree(this,\'hi\',1)','<i class="ico-w16 ico-w16-nav-down-lo"></i>');
    $this -> addBtn(lan('lib.collapseall'), 'Flow.collapseAllTree(this, \'hi\', \'root\')','<i class="ico-w16 ico-w16-nav-up-lo"></i>');

    $this -> getMemberCount();

    $this -> addPanel('ca2', '| Status');
    $this -> addPanel('sta', $this -> getFilterMenu());

    if (empty($this -> mFil)) {
      $this -> mFil = 'all';
    }

    if (!empty($this -> mFil)) {
      if ('act' == $this -> mFil) {
        $this -> getGroups($aParent ,'N');
      } elseif ('del' == $this -> mFil) {
        $this -> mIte -> addCnd($this -> getDelCnd());
      }
      else {
        $this ->getGroups($aParent);
      }
      $this -> getMemberCount();
    }
    else {
      $this -> getGroups($aParent);
    }

    $this -> mColCnt = $this -> mColCnt + $this -> mMaxLevel -1;
    $this -> mParents = array();
    $this -> mAdmLvlHtbItems = CCor_Res::get('htb', 'admlvl');

    if($aParent != -1) {
      array_push($this->mParents, 'pr'.$aParent);
      if($aParent === 0) {
        array_push($this->mParents, 'root');
      }
    }
  }
  
  protected function getParentGru($aGroupId) {
    $lGrpIds = array();
    $i = 0;
    while ($aGroupId != 0) {
      $lGrpIds[$i] = $aGroupId;
      $lQry = new CCor_Qry('SELECT parent_id,del FROM al_gru WHERE id = ' . $aGroupId .' ');
      foreach ($lQry as $lRow) {
        $lGpId = $aGroupId;
        $aGroupId = $lRow['parent_id'];
        $lGrpStatus = $lRow['del'];
        if ($lGrpStatus == 'Y'){
          $lDelGrp[] = $lGpId;
        }
      }
      $i++ ;
    }
    return $lDelGrp;
   }
  
  protected function getDelCnd() {
    $lUsr = CCor_Usr::getInstance();
    $Any = new CCor_Anyusr($lUsr -> getAuthId());
    $lUsrAdminLevel = $Any -> getAdminLevel();
    $lQry = new CCor_Qry('SELECT id,mand,parent_id,name, admin_level, typ,del FROM al_gru WHERE mand IN(0,'.MID.') AND del = "Y" AND (admin_level >= '.$lUsrAdminLevel.' OR admin_level = 0) ORDER BY name');
    foreach ($lQry as $lRow) {
      //if parent is deactive and somechild also deactive then shows only to parent
      $lResult [] = end($this -> getParentGru($lRow['id']));
    }    
    $lRet = 'del="Y" AND ';
    $lRet.= 'id IN ('.implode(",",$lResult).')';
    return $lRet;
  }
  
  protected function getFilterMenu() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="gru.fil" />'.LF;
    $lRet.= '<select name="val" size="1" onchange="this.form.submit()">'.LF;
    $lArr['all'] = '[all]';
    $lArr['act'] = 'Active Groups';
    $lArr['del'] = 'Inactive Groups';
  
    $lFil = (isset($this -> mFil)) ? $this -> mFil : '';
    foreach ($lArr as $lKey => $lVal) {
      $lRet.= '<option value="'.htm($lKey).'" ';
      if ($lKey == $lFil) {
        $lRet.= ' selected="selected"';
      }
      $lRet.= '>'.htm($lVal).'</option>'.LF;
    }
    $lRet.= '</select>'.LF;
    $lRet.= '</form>';
    return $lRet;
  }
  
  protected function getGroups($aParent = -1, $aStatus = NULL) {
    if($this->mBuffered === 0) {
      $this->getGroupsUnBuffered($aParent, $aStatus);
    }
    else {
      $this->getGroupsBuffered($aParent, $aStatus);
    }
  }

  protected function getGroupsBuffered($aParent = -1, $aStatus = NULL) {
    $lParent = '';
    $lStatus = ($aStatus=='act') ? ' AND del <> "Y"':'';
    if ($aParent != -1) {
      $lParent = ' AND parent_id = '.$aParent;
    }
    else {
      $aParent = 0;
    }
    $lUsr = CCor_Usr::getInstance();
    $Any = new CCor_Anyusr($lUsr -> getAuthId());
    $lUsrAdminLevel = $Any -> getAdminLevel();
    $lLimit = ' Limit ' . $this->mStart . ', ' . $this->mUntil;
    $lQry = new CCor_Qry('SELECT id,parent_id,name, admin_level, typ,del FROM al_gru WHERE mand IN(0,'.MID.')'. $lParent.$lStatus.' AND (admin_level >= '.$lUsrAdminLevel.' OR admin_level = 0) ORDER BY name'. $lLimit);

    foreach ($lQry as $lRow) {
      $this -> mArr[$lRow['parent_id']][] = $lRow;
      $this -> mGids[] = $lRow['id'];
    }


    $this -> mIte = $this -> mArr[$aParent];
  }

  protected function getGroupsUnBuffered($aParent = -1, $aStatus = NULL) {
    //Status
    if($aStatus !== NULL) {
      $lParam['del'] = $aStatus;
    }
    //Parent
    if($aParent !== -1) {
      $lParam['parent_id'] = $aParent;
    }
    else {
      $aParent = 0;
    }
    //Admin Level
    $lUsr = CCor_Usr::getInstance();
    $Any = new CCor_Anyusr($lUsr -> getAuthId());
    $lParam['admin_level'] = $Any -> getAdminLevel();
    //MAND
    $lParam['mand'] = MID;
    $lGru = new CCor_Res_Gru();
    $lGru = $lGru->get($lParam);

    foreach ($lGru as $lRow) {
      $this -> mArr[$lRow['parent_id']][] = $lRow;
      $this -> mGids[] = $lRow['id'];
    }

    $this -> mIte = $this -> mArr[$aParent];
  }
  
  protected function getTdDel() {
      $lVal = $this -> getVal('del');
      $lRet = '<td class="'.$this -> mCls.' nw w16" align="right">';
      if ('N' == $lVal) {
        $lRet.= '<a class="nav" href="javascript:Flow.Std.cnf(\''.$this -> getDelLink().'\', \'cnfDel\')">';
        $lRet.= '<i class="ico-w16 ico-w16-flag-03"></i>';
      } else {
        $lUrl = 'index.php?act=gru.react&amp;id='.$this -> getVal('id');
        $lRet.= '<a class="nav" href="javascript:Flow.Std.cnf(\''.$lUrl.'\', \'cnfReactivate\')">';
        $lRet.= '<i class="ico-w16 ico-w16-flag-00"></i>';
      }
      $lRet.= '</a>';
      $lRet.= '</td>'.LF;
      return $lRet;
    }

  protected function getMemberCount() {
    if (empty($this->mArr)) return;
    $lGids = collect($this->mArr, 'id');

    $lSql = 'SELECT m.gid,COUNT(DISTINCT(m.uid)) AS num FROM al_usr_mem m, al_usr u '; #, al_usr_mand um ';
    $lSql.= 'WHERE m.uid=u.id AND u.del="N" ';
    $lSql.= 'AND m.gid IN ('.implode(',',$this->mGids).') ';
    $lSql.= 'AND m.mand IN (0,'.MID.') ';
    $lSql.= 'GROUP BY m.gid';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this->mMemCount[$lRow['gid']] = $lRow['num'];
    }
  }

  protected function getTrTag() {
    $lParents = implode(' ', $this->mParents);
    $lClass = (empty($this -> mParents)) ? 'root' : implode(' ', $this->mParents);
    $lTrId = 'pr'.$this->getInt('id');
    return '<tr class="hi '.$lClass.'" id="'.$lTrId.'" style="display:table-row">';
  }

  protected function getTdMor() {
    $lId = $this -> getInt('id');
    $lRet = '';
    $lGru = CCor_Res::get('gru', array('parent_id' => $lId));

    if (!empty($this->mLevel)) {
      for ($i=0; $i<$this->mLevel; $i++) {
        $lRet.= '<td class="td1 tg w16">'.img('img/d.gif', array('width' => 16)).'</td>';
      }
    }
    $this -> mMoreId = getNum('t');
    $lTrId = 'pr'.$this->getInt('id');
    $lRet.= '<td class="td1 w16 mor">';
      if(!empty($lGru) && CCor_Cfg::get("ajxGroupList")) {
        $lRet.= '<a class="nav" onclick="Flow.loadTrTree(this,\''.$lId.'\' ,\''.$lTrId.'\', \''.($this->mLevel+1).'\', 0)">...</a>';
      } else if (!empty($lGru)) {
        $lRet.= '<a class="nav" onclick="Flow.togTrTree(this,\''.$lTrId.'\')">';
        $lRet.= '...</a>';
      }
    $lRet.= '</td>';
    return $lRet;
  }

  protected function getTdInv() {
    $lId = $this -> getInt('id');
    $lLnk = "index.php?act=gru.invExt&amp;gru=".$lId;
    
    $lRet = '<td class="td1 w16">';
      if($this->getVal('typ') == "ext") {
        $lRet.= '<a href="'.$lLnk.'" class="nav">';
          $lRet.= '<i class="ico-w16 ico-w16-usr"></i>';
        $lRet.= '</a>';
      }
    $lRet .= '</td>';

    return $lRet;
  }

  protected function getTdName() {
    $lNam = $this -> getCurVal();
    $lPar = $this -> getVal('parent_id');
    $lAdminLevel = $this -> getVal('admin_level');
    $lColSpan = $this->mMaxLevel - $this->mLevel;
    if ($lPar == 0) {
      $lRet = '<td class="td2 nw w100p b" colspan="'.$lColSpan.'">';
    } else {
      $lRet = '<td class="td1 nw w100p" colspan="'.$lColSpan.'">';
    }
    $lRet.= $this -> a(htm($lNam));
    $lRet.= '</td>';
    return $lRet;
  }

  protected function getTdAdmlvl() {
    $lAdminLevel = $this -> getVal('admin_level');
    $lGrpId = $this -> getVal("id");
    $lAdminLevelDesc = '';
    if($lAdminLevel != 0) {
      $lAdminLevelDesc = $this -> mAdmLvlHtbItems[$lAdminLevel];
      $lContent = $lAdminLevel;
    } else
      $lContent = $lAdminLevel;
    
    $lRet = '<td class="td1 w16 ac" data-toggle="tooltip" data-tooltip-head="" data-tooltip-body="' .$lAdminLevelDesc . '">';
    $lRet .= $lContent;
    $lRet .= '</td>';
    return $lRet;
  }

  protected function getTdIns() {
    $lId = $this -> getInt('id');

    if($this -> getVal("typ") == "ext") {
      $lLnk = "index.php?act=gru.newExt&amp;pid=".$lId;
    }
    else {
      $lLnk = "index.php?act=gru.new&amp;pid=".$lId;
    }
    $lRet = '<td class="td1 w16 ac">';
    $lRet.= '<a href="'.$lLnk.'" class="nav">';
    $lRet.= '<i class="ico-w16 ico-w16-plus"></i>';
    $lRet.= '</a>';
    $lRet.= '</td>';
    return $lRet;
  }

  protected function getSubList($aParentId) {
    $lRet = '';
    $this -> mLevel++;
    array_push($this->mParents, 'pr'.$aParentId);

    $lOldIte = $this -> mIte;
    $this -> mIte = $this -> mArr[$aParentId];
    $lRet.= $this -> getRows();
    $this -> mIte = $lOldIte;
    
    array_pop($this->mParents);
    $this -> mLevel--;
    return $lRet;
  }

  protected function getTdMem() {
    $lId = $this -> getInt('id');
    if (empty($this -> mMemCount[$lId]) && empty($this -> mMem[$lId])) {
      return '<td class="td2 w30 cr" style="min-width:30px;">'.NB.'</td>';
    }
    $lRet = '<td class="td2 w30 ar" style="min-width:30px; color:#999;"';
    $lRet.= ' onmouseover="Flow.grpMemTip(this, '.$lId.')"';
    $lRet.= ' onmouseout="Flow.hideTip();"';
    $lRet.= '>'.$this->mMemCount[$lId].'</td>';
    return $lRet;
  }

  protected function afterRow() {
    $this -> incCtr();
    if ($this -> mMoreId) {
      $lId = $this -> getInt('id');
      return $this -> getSubList($lId);
    } else {
      return '';
    }
  }

  public function getOnlyRows($aLvl, $aCls) {
    $this->mLevel = $aLvl;
    array_push($this->mParents, $aCls);
    return $this->getRows();
  }
}