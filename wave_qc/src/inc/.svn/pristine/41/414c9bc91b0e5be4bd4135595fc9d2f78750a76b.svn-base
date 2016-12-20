<?php
class CInc_Gru_List extends CHtm_List {

  public function __construct() {
    parent::__construct('gru');
    $this -> setAtt('class', 'tbl w800');
    $this -> mTitle = lan('gru.menu');

    $this -> mLevel = 0;
    $this -> mMaxLevel = 10;

    $this -> addColumn('mor', '',false, array('width' => 16));
    $this -> addColumn('name',   'Name', false, array('colspan' => $this->mMaxLevel));
    $this -> addColumn('admlvl',   lan('usr.adminlevel'), false);
    $this -> addColumn('mem',  '', false, array('width' => 30));
    if ($this -> mCanInsert) {
      $this -> addColumn('ins', '', false, array('width' => 16));
	  $this -> addBtn(lan('gru.new'), "go('index.php?act=gru.new')", 'img/ico/16/plus.gif');
    }
    if ($this -> mCanDelete) {
      $this -> addDel();
    }

    $this -> addBtn(lan('lib.expandall'), 'Flow.expandAllTree(this,\'hi\')','img/ico/16/nav-down-lo.gif');
    $this -> addBtn(lan('lib.collapseall'), 'Flow.collapseAllTree(this, \'hi\', \'root\')','img/ico/16/nav-up-lo.gif');
    $this -> getGroups();
    $this -> getMemberCount();

    $this -> mColCnt = $this -> mColCnt + $this -> mMaxLevel -1;
    $this -> mParents = array();
    $this -> mAdmLvlHtbItems = CCor_Res::get('htb', 'admlvl');
  }

  protected function getGroups(){
    $lUsr = CCor_Usr::getInstance();
    $Any = new CCor_Anyusr($lUsr -> getAuthId());
    $lUsrAdminLevel = $Any -> getAdminLevel();
    $lQry = new CCor_Qry('SELECT id,parent_id,name, admin_level FROM al_gru WHERE mand IN(0,'.MID.') AND (admin_level >= '.$lUsrAdminLevel.' OR admin_level = 0) ORDER BY name');
    foreach ($lQry as $lRow) {
      $this -> mArr[$lRow['parent_id']][] = $lRow;
      $this -> mGids[] = $lRow['id'];
    }

    $lUsrMem = CCor_Usr::getInstance();
    if($lUsrMem -> getVal('mand') == MID){
    	$lQry = new CCor_Qry('SELECT id FROM al_gru WHERE mand IN(0,'.MID.') AND name="'.MANDATOR_NAME.'";');
    	foreach ($lQry as $lRow) {
    		foreach($this -> mArr[0] as $lKey => $lGru){
    			if($lGru['id'] !== $lRow['id'])
    				unset($this->mArr[0][$lKey]);
    		}
    	}
    }

    $this -> mIte = $this -> mArr[0];
  }

  protected function getMemberCount() {
    if (empty($this->mArr)) return;
    $lGids = collect($this->mArr, 'id');

    $lSql = 'SELECT m.gid,COUNT(DISTINCT(m.uid)) AS num FROM al_usr_mem m, al_usr u '; #, al_usr_mand um ';
    $lSql.= 'WHERE m.uid=u.id AND u.del="N" ';
    $lSql.= 'AND m.gid IN ('.implode(',',$this->mGids).') ';
    $lSql.= 'AND m.mand IN (0,'.MID.') ';
    #$lSql.= 'AND (m.uid=um.uid AND (um.mand IN (0,'.MID.')) OR u.mand=0) ';
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
    #$lRet.= '<td class="td1 w16">'.$this->mLevel.'</td>';
    if (!empty($this->mLevel)) {
      #$lRet.= '<td class="td1 tg w16">'.img('img/d.gif', array('width' => 16));
      for ($i=0; $i<$this->mLevel; $i++) {
        $lRet.= '<td class="td1 tg w16">'.img('img/d.gif', array('width' => 16)).'</td>';
      }
    }
    if (empty($this -> mArr[$lId])) {
      $this -> mMoreId = NULL;
      $lRet.= '<td class="td1 w16">'.img('img/d.gif', array('width' => 16));
      $lRet.= '</td>';
      return $lRet;
    } else {
      $this -> mMoreId = getNum('t');
      $lTrId = 'pr'.$this->getInt('id');

      $lRet.= '<td class="td1 w16">';
      $lRet.= '<a class="nav" onclick="Flow.togTrTree(this,\''.$lTrId.'\')">';
      $lRet.= '...</a>';
      $lRet.= '</td>';
      return $lRet;
    }
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
    $lGrpId = $this->getVal("id");
    $lAdminLevelDesc = '';
    if ($lAdminLevel != 0) {
      $lAdminLevelDesc = $this -> mAdmLvlHtbItems[$lAdminLevel];
      $lContent = $lAdminLevel;
    } else $lContent = $lAdminLevel;

    $lRet = '<td class="td1 w16 ac" data-toggle="tooltip" data-tooltip-head="" data-tooltip-body="'.$lAdminLevelDesc.'">';
    $lRet.= $lContent;
    $lRet.= '</td>';
    return $lRet;
  }

  protected function getTdIns() {
    $lId = $this -> getInt('id');
    $lRet = '<td class="td1 w16 ac">';
    $lRet.= '<a href="index.php?act=gru.new&amp;mid='.$lId.'" class="nav">';
    $lRet.= img('img/ico/16/plus.gif');
    $lRet.= '</a>';
    $lRet.= '</td>';
    return $lRet;
  }

  protected function getSubList($aParentId) {
    $lRet = '';
    $this -> mLevel++;
    array_push($this->mParents, 'pr'.$aParentId);
    #$lRet.= '<tr class="togtr" id="'.$this -> mMoreId.'" style="display:table-row">'.LF;
    #$lRet.= '<td class="td1 tg">&nbsp;</td>'.LF;
    #$lRet.= '<td class="p0" colspan="4">';
    #$lRet.= '<table cellpadding="2" cellspacing="0" border="0" class="w100p">';
    $lOldIte = $this -> mIte;
    $this -> mIte = $this -> mArr[$aParentId];
    $lRet.= $this -> getRows();
    $this -> mIte = $lOldIte;
    #$lRet.= '</table>';
    #$lRet.= '</td>';
    #$lRet.= '</tr>'.LF;
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


}