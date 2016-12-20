<?php
class CInc_Rep_Apldays_Res extends CRep_Base_Res {

  public function __construct($aSrc) {
    $this -> mSrc = $aSrc;
    $this -> mTbl = 'al_job_apl_loop';
    parent::__construct($aSrc, $this -> mTbl);
  }
  
  protected function addColumns() {
    $this -> addCol('jobid');
    $this -> addCol('num');
    $this -> addCol('start_date');
    $this -> addCol('close_date');
  }
  
  public function getResult() {
    $lRet = $lRange = $lCrpTypes = array();
    
    $lSize = $this -> groupSize();
    for($lNum=$lSize; $lNum >= 0; $lNum--){
	  $lDate = (!empty($this -> mTo)) ? date("Y-m-d", strtotime($this -> mTo)) : date("Y-m-d");
      if($this -> mPeriod == 'month'){
	    $lDate = date("Y-m-01", strtotime($lDate));
	  }
      $lRange[] = date($this -> mFmt[$this -> mPeriod], strtotime($lDate. ' -'.$lNum.' '.$this -> mPeriod));
    }
    $lRange = array_unique($lRange);
    
    //find all critical paths with stages
    $lCrps = CCor_Cfg::get('report.jobs');
    $lSql = "SELECT id, code, name_".LAN." as name from al_crp_master WHERE mand=".MID;
    $lQry = new CCor_Qry($lSql);
    foreach($lQry as $lRows){    
      if(in_array($lRows['code'], $lCrps)){  
        $lCrpTypes[$lRows['code']] = $lRows['name'];
      }
    }
    
    foreach($lCrpTypes as $lSrc => $lName){ 
        $this -> mIte = $this -> getIterator($this -> mTbl);
        $this -> addColumns();
        $this->addCondition('src', '=', $lSrc);
        $this->addCondition('mand', '=', MID);
        $this->addCondition('status', '=', 'closed');
        $this->addCondition('start_date', '!=', '0000-00-00');
        $this->addCondition('close_date', '!=', '0000-00-00');
        $lRes = $this -> mIte -> getArray();
        
        $lNumRet = $lTotal = array();
        $lThreshold = 9;
        foreach($lRes as $lIdx => $lEntry) {
          $lStart = strtotime($lEntry['start_date']);
          $lEnd = strtotime($lEntry['close_date']);
          $lDiff = floor(($lEnd - $lStart)/(60*60*24));

          $lJid = $lEntry['jobid'];
          $lDate = date($this -> mFmt[$this -> mPeriod], strtotime($lEntry['close_date']));

          if(in_array($lDate, $lRange)){
            $lDiff = (intval($lDiff) > $lThreshold) ? ($lThreshold+1)."+" : $lDiff;
            if(array_key_exists(intval($lDiff), $lNumRet) || (intval($lDiff) > $lThreshold && array_key_exists($lDiff, $lNumRet))){
              if(!array_key_exists($lDate, $lNumRet[$lDiff])){
                $lNumRet[$lDiff][$lDate] = 1;
              } else {
                $lNumRet[$lDiff][$lDate] = intval($lNumRet[$lDiff][$lDate]) + 1;
              }
            } else {
              $lNumRet[$lDiff][$lDate] = 1;
            }
            ksort($lNumRet[$lDiff]);
          }
        }

        //missing days and months??
        for($i=0; $i <= ($lThreshold+1); $i++){
          $lKey = ($i > $lThreshold) ? ($lThreshold+1)."+" : $i;
          if(!array_key_exists($lKey, $lNumRet)){
            $lNumRet[$lKey] = array();
          }
          foreach($lRange as $lGroup){
            if(!array_key_exists($lGroup, $lNumRet[$lKey])){
              $lNumRet[$lKey][$lGroup] = 0;
            }
          }
          ksort($lNumRet[$lKey]);
        }
        
        //calculate totals and percentages
        foreach($lNumRet as $lDiff => $lArr){
          foreach($lArr as $lDates => $lNum){
            $lPrev = (array_key_exists($lDates, $lTotal)) ? $lTotal[$lDates] : 0;
            $lTotal[$lDates] = $lPrev + $lNum;
          }
        } 
        
        foreach($lNumRet as $lDiff => $lArr){
          foreach($lArr as $lDates => $lNum){
            $lNumRet[$lDiff][$lDates] = array(
              'jobs' => $lNum,
              'per' => ($lTotal[$lDates] == 0) ? 0 : ($lNum / $lTotal[$lDates]) * 100
            );
          }
        }
        ksort($lNumRet);
        
        $lRet['data'][$lSrc]['name'] = $lName;
        $lRet['data'][$lSrc]['results'] = $lNumRet;
    }
    
    $lRet = $this -> produceChart($lRet);
    
    return $lRet;
  }
  
  protected function produceChart($aRet) { 
    $lCats = $lSeries = array();
    $lColours = array("#f8a526", "#792f79", "#ed4a49", "#459845", "#275772", "#e69e4a", "#abd155", "#84bfe8", "#8c7665", "#2b9485","#2c3f48");
    
    foreach($aRet['data'] as $lSrc => $lArr){      
      foreach($lArr['results'] as $lDays => $lVals){
        $lData = array();
        $lColour = $lColours[intval($lDays)];
        foreach($lVals as $lRange => $lVal){
          if(!in_array($lRange, $lCats)){
            $lCats[] = $lRange;
          }
          $lData[] = $lVal['per'];
        }
        
        $lSeries[] = array("name" => $lDays, "stack" => $lArr['name'], "color" => $lColour, "data" => $lData);
      }
    }

    $lParams = array(
      "xAxis" => array(
        "title" => array("text" => $this -> mXaxis[$this -> mPeriod]),
      ),
      "yAxis" => array(
        "title" => array("text" => "Percentage of Jobs"),
        "allowDecimals" => "false",
        "floor" => "0",
        "ceiling" => "100",
      ),
      "plotOptions" => array( 
        "column" => array( 
          "dataLabels" => array("enabled" => "false"),
          "tooltip" => array(
            "pointFormat" => "<span style=\"color:{series.color}\">{series.name} Day(s)</span>: <b>{point.y:.0f}</b><br/>"
          ),
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