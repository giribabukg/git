<?php
class CInc_Health_List extends CCor_Ren {
    
    public function __construct() {
        $this->init();
    }
    
    protected function init() {
        $this->loadSystems();
        $this->loadServices();
    }
    
    public function addJs() {
        $lJs = '';
        $lJs.= 'jQuery(function(){ Flow.health.initMonitorPage() });';
        $lPag = CHtm_Page::getInstance();
        $lPag->addJsSrc('js/health.js');
        $lPag->addJs($lJs);
    }
    
    protected function loadSystems() {
        $this->mSystems = array();
        $lSql = 'SELECT * FROM pf_systems ORDER BY name';
        $lQry = new CCor_Qry($lSql);
        foreach ($lQry as $lRow) {
            $this->mSystems[$lRow['id']] = $lRow;
        }
    }
    
    protected function loadServices() {
        $this->mServices = array();
        $this->mSysServices = array();
        $lSql = 'SELECT * FROM pf_services ORDER BY system_id,sort_order';
        $lQry = new CCor_Qry($lSql);
        foreach ($lQry as $lRow) {
            $this->mServices[$lRow['id']] = $lRow;
            $this->mSysServices[$lRow['system_id']][$lRow['id']] = $lRow;
        }
    }
    
    protected function loadDependencies() {
        
    }
    
    protected function getCont() {
        $lRet = '';
        $lRet.= $this->getContSystems();
        return $lRet;
    }
    
    protected function getContSystems() {
        $lRet = '';
        #$lRet.= '<a class="nav" href="javascript:Flow.health.runAll()">Test all</a>'.BR;
        #$lRet.= '<a class="nav" href="javascript:Flow.health.refresh()">Refresh all</a>'.BR;
        $lRet.= '<ul class="health-system-list">'.LF;
        foreach ($this->mSystems as $lId => $lRow) {
            $lRet.= '<li class="health-system">';
            $lRet.= '<div class="health-system-header" data-id="'.$lId.'">';
            $lRet.= htm($lRow['name']);
            
            $lRet.= '</div>';
            $lRet.= $this->getContServices($lId);  
            $lRet.= '</li>';
        }
        $lRet.= '</ul>';
        return $lRet;
    }
    
    protected function getContServices($aId) {
        if (empty($this->mSysServices[$aId])) return '';
        
        $lDat = new CCor_Datetime();
        
        $lRet = '';
        $lRet.= '<ul class="health-service-list">'.LF;
        foreach ($this->mSysServices[$aId] as $lId => $lRow) {
            $lRet.= '<li class="health-service hs'.$lId.'" data-sid="'.$lId.'">';
            
            $lImg = '03'; //green
            $lStat = $lRow['last_status'];
            if ('warn' == $lStat)  $lImg = '02';
            if ('error' == $lStat) $lImg = '01';
            if ('skip' == $lStat)  $lImg = '00';
            
            $lRet.= '<div class="health-service-state" data-state="'.$lStat.'">';
            $lRet.= img('ico/16/flag-'.$lImg.'.gif', array('id' => 'h-img-'.$lId));
            $lRet.= '</div>';
            
            $lRet.= '<div class="health-service-name">';            
            $lRet.= htm($lRow['name']);
            $lRet.= '</div>';
            
            $lRet.= '<div class="health-service-date">';
            $lLastUpdate = $lRow['last_update'];
            if (substr($lLastUpdate,0,10) == date('Y-m-d')) {
              $lLastUpdate = substr($lLastUpdate, -8);
            }
            $lRet.= $lLastUpdate;
            $lRet.= '</div>';
            
            $lRet.= '</li>';
        }
        $lRet.= '</ul>';
        return $lRet;
        
    }

}