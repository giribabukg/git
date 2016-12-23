<?php
class CInc_Rep_Reahold_Res extends CRep_Base_Res {

 public function __construct($aSrc) {
    $this -> mSrc = $aSrc;
    $this -> mTbl = 'al_job_shadow_'.MID;
    parent::__construct($aSrc, $this -> mTbl);
  }
  
  protected function addColumns() {
    $this -> addCol('jobid');
    $this -> addCol('src');
  }
  
  public function getResult() {
  	$lRet = $lRange = array();
    
    $lSize = $this -> groupSize();
    for($lNum=$lSize; $lNum >= 0; $lNum--){
	  $lDate = (!empty($this -> mTo)) ? date("Y-m-d", strtotime($this -> mTo)) : date("Y-m-d");
      if($this -> mPeriod == 'month'){
	    $lDate = date("Y-m-01", strtotime($lDate));
	  }
      $lRange[] = date($this -> mFmt[$this -> mPeriod], strtotime($lDate. ' -'.$lNum.' '.$this -> mPeriod));
    }
    $lRange = array_unique($lRange);
   $this -> mIte = $this -> getIterator($this -> mTbl);
   $this -> addColumns();
   $this->addCondition('onhold', '!=', '');
   $lRes = $this -> mIte -> getArray();
   
   foreach($lRes as $lIdx => $lEntry) {
   	$lJid = $lEntry['jobid'];
   		$lSqlHistory = "SELECT datum, msg FROM al_job_his WHERE src_id = '".$lJid."' AND subject = 'Set on hold'";
	    $lQryHistory = new CCor_Qry($lSqlHistory);
	    foreach($lQryHistory as $lRowsHistory){
	        
	     	$lMsg = $lRowsHistory['msg'];
	     	$lPiecesMsg = explode(",", $lMsg);
	     	$lDate = date($this -> mFmt[$this -> mPeriod], strtotime($lRowsHistory['datum']));
	     	if(in_array($lDate, $lRange)){
	     		$lRet['reason'][$lPiecesMsg[0]][$lDate][] = $lJid;
    			$lRet['number'][$lDate][] = $lJid;
	     	}
    	}
   }
   ksort($lRet['reason']);
   
    foreach($lRange as $lIdx => $lDate ){
    	if(!array_key_exists($lDate, $lRet['number'])){
    		$lRet['number'][$lDate] = array();
    	}
    	 ksort($lRet['number']);
    }
    
    foreach($lRet['reason'] as $lReason => $lArr ){
    	$lKeys = array_keys($lArr);
    	$lDiff = array_diff($lRange, $lKeys);
    	
    	foreach($lDiff as $lIdx => $lDate){
    		
    		$lRet['reason'][$lReason][$lDate] = array();
    	}
    	ksort($lRet['reason'][$lReason]);
    }
    
    $lRet = $this -> produceChart($lRet);
    
    return $lRet;
  }
  
  protected function produceChart($aRet) {
    $lCats = $lSeries = array();
    $lColours = array('art' => "#f8a526", 'rep' => "#792f79", 'mis' => "#ed4a49", 'com' => "#459845", 'sec' => "#275772", 'tra' => "#e69e4a", 'adm' => "#abd155", 'pro' => "#84bfe8");
    
    
    $lCount = 0;
    foreach($aRet['reason'] as $lReason => $lArr){
    	$lData = array();
    	foreach($lArr as $lDate => $lMas){
    		if(!in_array($lDate,$lCats)){
    			$lCats[] = $lDate;
    		}
    		
    		$lData[] = sizeof($lMas);
    		
    	}
    	$lSeries[] = array("name" => $lReason, "data" => $lData);
    	
    }
	
    $lParams = array(
      "xAxis" => array(
        "title" => array("text" => $this -> mXaxis[$this -> mPeriod]),
      ),
      "yAxis" => array(
        "title" => array("text" => "Number of Jobs on Hold"),
      ),
      "plotOptions" => array( 
        "column" => array( 
          "dataLabels" => array("enabled" => "false"),
          "tooltip" => array(
            "pointFormat" => "<span style=\"color:{series.color}\">{series.name} Day(s)</span>: <b>{point.y:.0f}</b> ({point.percentage:.0f}%)<br/>"
          ),
          "pointPadding" => "0.2", 
          "borderWidth" => "0",
    	)
      )
    );
    
    $lChart = new CRep_Highcharts("column");
    $lChart -> setParams($lParams);
    $lChart -> setCategories($lCats);
    $lChart -> setSeries($lSeries);
    
    return $lChart -> getContent(); 
  }
	
}