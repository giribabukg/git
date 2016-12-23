<?php
class CInc_Rep_Cpsta_Res extends CRep_Base_Res {

  public function __construct($aSrc) {
    $this -> mSrc = $aSrc;
    $this -> mTbl = 'al_job_shadow_'.MID;
    parent::__construct($aSrc, $this -> mTbl);
  }
  
  protected function addColumns() {
    $this -> addCol('jobid');
    $this -> addCol('src');
    $this -> addCol('webstatus');
  }
  
  public function getResult() {
    $lRet = array();
    
    //find all critical paths with stages
    $lCrps = CCor_Cfg::get('report.jobs');
    $lSql = "SELECT id, code, name_".LAN." as name from al_crp_master WHERE mand=".MID;
    $lQry = new CCor_Qry($lSql);
    foreach($lQry as $lRows){  
      $lId = $lRows['id'];
      $lSrc = $lRows['code'];
      
      if(in_array($lSrc, $lCrps)){
        $lSqlStages = "SELECT status, display, name_".LAN." from al_crp_status WHERE crp_id=".$lId." AND mand=".MID." ORDER BY display ASC";
        $lQryStages = new CCor_Qry($lSqlStages);
        foreach($lQryStages as $lRowsStages){
          $lStage = $lRowsStages['status'];
          $lDisplay = $lRowsStages['display'];
          $lLabel = $lRowsStages['name_'.LAN];
          
          if($lStage < 200){
            $lRet['data'][$lSrc][$lStage] = array(
              'name' => $lRows['name'],
              'stage' => $lDisplay,
              'label' => $lLabel,
              'value' => 0,
              'time' => 0
            );
          }
        }
      }
    }
    
    $this -> mIte = $this -> getIterator($this -> mTbl);
    $this -> addColumns();
    $lRes = $this -> mIte -> getArray();
    foreach($lRes as $lJob){
      $lSrc = $lJob['src'];
      $lStatus = $lJob['webstatus'];
      
      if(in_array($lSrc, $lCrps) && ($lStatus > 0 && $lStatus < 200)){
        if(in_array( $lStatus, array_keys($lRet['data'][$lSrc]) )){
          $lRet['data'][$lSrc][$lStatus]['value'] = intval($lRet['data'][$lSrc][$lStatus]['value']) + 1;
        }
      }
    }
    
    $lRet = $this -> constructJs($lRet);
    
    return $lRet;
  }
  
  protected function constructJs($aRet) {
    $lRet = '';
    
    $lParams = array(
      "chart" => array("plotBackgroundColor" => "null", "plotBorderWidth" => "null", "plotShadow" => "false"),
      "yAxis" => array(
        "title" => array("text" => "Percentage of Jobs"),
        "allowDecimals" => "false",
        "floor" => "0",
        "ceiling" => "100",
      ),
      "plotOptions" => array( 
        "pie" => array( 
          "allowPointSelect" => "true",
          "dataLabels" => array(
          	"enabled" => "true",
            "format" => "<b>{point.name}</b>",
            "showInLegend" => "false"
          ),
          "tooltip" => array(
            "headerFormat" => "<span style=\"font-size: 14px\">{point.key}</span><br/>",
            "pointFormat" => "<span style=\"color:{point.color}\">{series.name}</span>: <b>{point.y:.0f}</b> ({point.percentage:.1f}%)<br/>",
    	  ),
    	)
      )
    );
    
    foreach($aRet['data'] as $lSrc => $lStages){
      $lSeries = $lData = array();
      $lAttr = array(
        "class" => (sizeof($aRet['data']) > 2) ? 'fl w50p' : 'fl w100p',
        "id" => "container_".$lSrc
      );
      
      foreach($lStages as $lIdx => $lStage){
        $lData[] = array( $lStage['stage'].': '.$lStage['label'], $lStage['value']);
      }
      
      $lSeries[] = array("name" => "Jobs in status", "data" => $lData);
      
      $lChart = new CRep_Highcharts("pie");
      $lChart -> setTitle('<div class="captxt w100p tac th1">'.$lStages[10]['name'].'</div>');
      $lChart -> setParams($lParams);
      $lChart -> removeParam('yAxis');
      $lChart -> removeParam('tooltip');
      $lChart -> setSeries($lSeries);
      
      $lRet.= $lChart -> getContent($lAttr);
    }

    return $lRet;
  }

}