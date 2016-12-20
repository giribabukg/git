<?php
class CInc_Xchange_Joblist extends CHtm_List {

  public function __construct() {
    parent::__construct('xchange');
    $this -> setAtt('width', '100%');
    $this->mTitle = lan('xchange.menu').' - Jobs';
    $this -> mLppLnk = $this -> mStdLink.'.joblpp&amp;lpp=';

    $this->mXfields = new CXchange_Jobfields();

    $this->getPrefs('xchange.job');

    $this -> mOrdLnk = $this -> mStdLink.'.jobord&amp;fie=';
    $this -> mDelLnk = $this -> mStdLink.'.jobdel&amp;id=';
    $this -> mLppLnk = $this -> mStdLink.'.joblpp&amp;lpp=';

    $this->mStatusFilter = (isset($this -> mFil['x_status'])) ? $this -> mFil['x_status'] : '';
    $this->addFields();
    $this->getIterator();
    $this->addPanels();

    $this->mJobFields = CCor_Res::extract('alias', 'name_'.LAN, 'fie');
  }

  protected function addFields() {
    $this->addColumn('mor');
    $this->addColumn('id', 'ID', true);
    $this->addColumn('x_jobid', 'Job ID', true);
    $this->addCustomColumns();
    $this->addField(fie('x_import_date', 'Import', 'datetime'))->setSortable(true);
    $this->addField(fie('x_update_date', 'Updated', 'datetime'))->setSortable(true);
    $this->addField(fie('x_assign_date', 'Assigned', 'datetime'))->setSortable(true);
    $this->addColumn('x_status', 'Status', true);
    $this->addDel();
  }

  protected function addCustomColumns() {
    $lArr = $this->mXfields->getTableFields();

    foreach ($lArr as $lKey => $lCaption) {
      $this->addColumn($lKey, $lCaption, true);
    }
  }

  protected function getStateArray() {
    $lRet = array();
    $lRet['all']      = '[all]';
    $lRet['active']   = '[active]';
    $lRet['new']      = 'New';
    $lRet['update']   = 'Updated';
    $lRet['assigned'] = 'Assigned';
    $lRet['deleted']  = 'Deleted';
    return $lRet;
  }

  protected function getIterator() {
    $this->mIte = new CCor_TblIte('al_xchange_jobs_'.MID);
    $this->mIte->setOrder($this->mOrd, $this->mDir);
    $this->mIte->setLimit($this->mPage * $this->mLpp, $this->mLpp);
    if (!empty($this->mStatusFilter)) {
      switch ($this->mStatusFilter) {
        case 'all' : break;
        case 'new'      :
        case 'update'   :
        case 'assigned' :
        case 'deleted'  :
          $this->mIte->addCnd('x_status='.esc($this->mStatusFilter));
          break;
        case 'active' :
          $this->mIte->addCnd('x_status!="deleted"');
          break;
      }
    }
    if (!empty($this->mSer)) {

      $lTerm = $this->mSer['name'];
      $lLike = ' LIKE "%'.mysql_real_escape_string($lTerm).'%"';
      $lFields = $this->mXfields->getSearchFields();
      if (!empty($lFields)) {
        $lCnd = array();
        foreach ($lFields as $lAlias => $lDummy) {
          $lCnd[] = '('.$lAlias.$lLike.')';
        }
        $lCndSql = '('.implode(' OR ', $lCnd).')';
        $this->mIte->addCnd($lCndSql);
      }

    }
    $this -> mMaxLines = $this -> mIte -> getCount();
  }

  protected function addPanels() {
    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());
    $this -> addPanel('sca', '| Status filter');
    $this -> addPanel('fil', $this->getStatusFilterForm());
    $this -> addPanel('cap', '| Search');
    $this -> addPanel('ser', $this->getSearchForm());
  }

  protected function getNavBar() {
    if (!$this -> mNavBar) {
      return '';
    }
    $lNav = new CHtm_NavBar($this -> mMod, $this -> mPage, $this -> mMaxLines, $this -> mLpp);
    $lNav -> setParam('act', $this -> mMod.'.jobpage');
    return $lNav -> getContent();
  }

  protected function getStatusFilterForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="'.$this -> mMod.'.jobfil" />'.LF;
    $lArr = $this->getStateArray();
    $lVal = (isset($this -> mFil['x_status'])) ? $this -> mFil['x_status'] : '';
    $lRet.= getSelect('val[x_status]', $lArr, $lVal, array('onchange' => 'this.form.submit()'));
    $lRet.= '</form>';
    return $lRet;
  }

  protected function getSearchForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="'.$this -> mMod.'.jobser" />'.LF;
    $lVal = (isset($this -> mSer['name'])) ? $this -> mSer['name'] : '';
    $lRet.= '<table><tr>';
    $lRet.= '<td><input type="text" name="val[name]" value="'.htm($lVal).'" /></td>';
    $lRet.= '<td>'.btn(lan('lib.search'),'','','submit').'</td>';
    if (!empty($this -> mSer)) {
      $lRet.= '<td>'.btn(lan('lib.show_all'),'go("index.php?act='.$this -> mMod.'.jobclser")').'</td>';
    }
    $lRet.= '</tr></table>';
    $lRet.= '</form>';
    return $lRet;
  }


  protected function getDelLink() {
    $lDelLink = $this -> mDelLnk;
    $lStatus = $this->getVal('x_status');
    if ($lStatus == 'deleted') {
      $lDelLink = 'index.php?act='.$this->mMod.'.jobundel&id=';
    }
    $lId = $this -> getVal($this -> mIdField);
    $lRet = $lDelLink.$lId;
    return $lRet;
  }

  protected function getTdMor() {
    $lRet = '';
    $this -> mMoreId = getNum('t');
    $lRet = '<a class="nav" onclick="Flow.Std.togTr(\''.$this -> mMoreId.'\')">';
    $lRet.= '...</a>';
    return $this -> tdClass($lRet, 'w16 ac');
  }

  protected function afterRow() {
    $lRet = parent::afterRow();
    $lRet.= '<tr style="display:none" id="'.$this -> mMoreId.'"><td class="td1 tg">&nbsp;</td>';
    $lCol = $this -> mColCnt -1;
    $lRet.= '<td class="td1 p16" colspan="'.$lCol.'">';

    $lRet.= $this->getAfterRowContent();

    $lRet.= '</td></tr>'.LF;
    return $lRet;
  }

  protected function getAfterRowContent() {
    $lRet = '';
    $lXml = $this->getVal('x_xml');

    $lRet.= '<div class="" style="float:left; margin-right:16px;">'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" class="tbl">'.LF;
    $lHeader = '<tr><td class="th2">Field</td><td class="th2">Value</td></tr>'.LF;

    $lRet.= $lHeader;

    foreach ($this->mRow as $lKey => $lVal) {
      if ($lKey == 'x_xml') continue;
      $lName = $lKey;
      if (isset($this->mJobFields[$lKey])) {
        $lName = $this->mJobFields[$lKey];
      }
      $lRet.= '<tr>';
      $lRet.= '<td class="td2">'.htm($lName).'</td>';
      $lRet.= '<td class="td1">'.shortStr(htm($lVal), 40).'</td>';
      $lRet.= '</tr>'.LF;

      if ($lKey == 'x_assign_date') {
        $lRet.= $lHeader;
      }
    }
    $lRet.= '</table>'.LF;
    $lRet.= '</div>';

    $lRet.= '<div class="box p16" style="float:left">'.('<pre>'.htm($lXml).'</pre>').'</div>';
    $lRet.= $this->getAssignButtons();
    return $lRet;
  }

  protected function getLikelyJids() {
    $lKeyField = CXchange_Jobfields::getSearchField();
    $lKeyVal = $this->getVal($lKeyField);
    if (empty($lKeyVal)) return array();
    $lSql = 'SELECT x_jobid,x_src FROM al_xchange_jobs_'.MID.' ';
    $lSql.= 'WHERE '.$lKeyField.'='.esc($lKeyVal).' ';
    $lSql.= 'AND x_jobid<>"" ';
    $lSql.= 'GROUP BY x_jobid';
    $lQry = new CCor_Qry($lSql);
    $lRet = array();
    foreach ($lQry as $lRow) {
      $lRet[] = $lRow;
    }
    return $lRet;
  }

  protected function getAssignButtons() {
    $lRet = '';
    $lRet.= '<div class="p16" style="float:left">';
    $lJid = $this->getVal('x_jobid');
    $lAtt = array('class' => 'btn w200');

    if (empty($lJid)) {
      $lRet.= btn('Create Job', 'go(\'index.php?act=job-art.newxchange&xid='.$this->getVal('id').'\')', 'img/ico/16/ok.gif', 'button', $lAtt).BR.BR;
      $lDstJid = $this->getLikelyJids();
      if (!empty($lDstJid)) {
        foreach($lDstJid as $lRow) {
          $lSrc = $lRow['x_src'];
          $lJid = $lRow['x_jobid'];
          $lRet.= btn('Assign to '.$lJid, 'go(\'index.php?act=xchange.assign&xid='.$this->getVal('id').'&src='.$lSrc.'&jid='.$lJid.'\')', 'img/ico/16/next-hi.gif', 'button', $lAtt).BR.BR;
        }
      }
      $lRet.= btn('Assign to existing job', 'go(\'index.php?act=xchange.assign&xid='.$this->getVal('id').'\')', 'img/ico/16/next-hi.gif', 'button', $lAtt);
    } else {
      $lRet.= btn('Re-Assign to existing job', 'go(\'index.php?act=xchange.assign&xid='.$this->getVal('id').'\')', 'img/ico/16/next-hi.gif', 'button', $lAtt).BR.BR;
      $lRet.= btn('Go to job', 'go(\'index.php?act=job-'.$this->getVal('x_src').'.edt&jobid='.$lJid.'\')', 'img/ico/16/process_doit.gif', 'button', $lAtt);

    }
    $lRet.= '</div>';
    return $lRet;
  }



}