<?php
class CInc_Health_Messagelist extends CCor_Ren {

  public function __construct () {
    $this->init();
  }

  protected function init () {
    $this->loadSystems();
    $this->loadServices();
  }

  protected function loadSystems () {
    $this->mSystems = array();
    $lSql = 'SELECT * FROM pf_systems ORDER BY name';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this->mSystems[$lRow['id']] = $lRow;
    }
  }

  protected function loadServices () {
    $this->mServices = array();
    $this->mSysServices = array();
    $lSql = 'SELECT * FROM pf_services ORDER BY system_id,sort_order';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this->mServices[$lRow['id']] = $lRow;
      $this->mSysServices[$lRow['system_id']][$lRow['id']] = $lRow;
    }
  }

  protected function getCont () {
    $lRet = '';
    $lRet .= '<ul id="health-messages">';
    $lSql = 'SELECT * FROM pf_messages ORDER BY insert_datetime DESC LIMIT 10';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lRet .= $this->getContRow($lRow);
    }
    $lRet .= '</ul>';
    return $lRet;
  }

  protected function getContRow ($aRow) {
    $lRet = '';
    $lRet .= '<li class="health-message health-state-' . $aRow['msg_state'] . '">';
    $lRet .= '<b>';
    $lService = $this->mServices[$aRow['service_id']];
    $lSystem = $this->mSystems[$lService['system_id']];
    
    $lRet .= '<div class="health-service-name">';
    $lRet .= htm($lSystem['name'] . ' :: ' . $lService['name']);
    $lRet .= '</div>';
    
    $lToday = date('Y-m-d');
    $lDate = $aRow['insert_datetime'];
    if (substr($lDate, 0, 10) == $lToday) {
      $lDate = substr($lDate, - 8);
    }
    $lRet .= '<div class="health-service-date">' . $lDate . '</div>';
    $lRet .= '</b>';
    $lRet .= htm($aRow['subject']);
    $lRet .= '</li>';
    return $lRet;
  }
}