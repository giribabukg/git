<?php
/**
 * @package xchange
 * @author Geoffrey Emmans
 *
 */
class CInc_Xchange_List extends CHtm_List {

  public function __construct() {
    parent::__construct('xchange');
    $this -> setAtt('width', '100%');
    $this->mTitle = lan('xchange.menu');
    $this->getPrefs();
    $this->mStatusFilter = (isset($this -> mFil['x_status'])) ? $this -> mFil['x_status'] : 'new+assigned';
    $this->addFields();
    $this->getIterator();
    $this->addPanels();

    $this->mJobFields = CCor_Res::extract('alias', 'name_'.LAN, 'fie');
  }

  protected function addFields() {
    $this->addColumn('mor');
    $this->addColumn('id', 'ID', true);
    $this->addCustomColumns();
    $this->addField(fie('x_import_date', 'Import', 'datetime'))->setSortable(true);
    $this->addField(fie('x_assign_date', 'Assigned', 'datetime'))->setSortable(true);
    $this->addColumn('x_jobid', 'ProjectID', true);
    $this->addColumn('x_status', 'Status', true);
    $this->addDel();
  }

  protected function addCustomColumns() {
    $this->addColumn('project_no', 'ProjectNo.', true);
    $this->addColumn('project_name', 'ProjectName', true);
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
    $this->mIte = new CCor_TblIte('al_xchange_projects_'.MID);
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
    $this -> mMaxLines = $this -> mIte -> getCount();
  }

  protected function addPanels() {
    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());
    $this -> addPanel('sca', '| Status filter');
    $this -> addPanel('fil', $this->getStatusFilterForm());

  }

  protected function getStatusFilterForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="'.$this -> mMod.'.fil" />'.LF;
    $lArr = $this->getStateArray();
    $lVal = (isset($this -> mFil['x_status'])) ? $this -> mFil['x_status'] : 'new+assigned';
    $lRet.= getSelect('val[x_status]', $lArr, $lVal, array('onchange' => 'this.form.submit()'));
    $lRet.= '</form>';
    return $lRet;
  }

  protected function getDelLink() {
    $lDelLink = $this -> mDelLnk;
    $lStatus = $this->getVal('x_status');
    if ($lStatus == 'deleted') {
      $lDelLink = 'index.php?act='.$this->mMod.'.undel&id=';
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
    return $lRet;
  }


}