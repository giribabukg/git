<?php
class CInc_Rep_Apls_Res extends CRep_Base_Res {

  public function __construct($aSrc) {
    $this -> mSrc = $aSrc;
    $this -> mTbl = 'al_job_apl_loop';
    parent::__construct($aSrc, $this -> mTbl);
  }
  
  protected function addColumns() {
    $this -> addCol('jobid');
    $this -> addCol('num');
    $this -> addCol('start_date');
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
        $lRes = $this -> mIte -> getArray();
        
        //creation date
        $lRangeRet = array();
        foreach($lRes as $lIdx => $lEntry) {
            $lJid = $lEntry['jobid'];
            $lNum = intval($lEntry['num']);
            $lDate = date($this -> mFmt[$this -> mPeriod], strtotime($lEntry['start_date']));
            
            if(in_array($lDate, $lRange)){
              if(array_key_exists($lDate, $lRangeRet)){
                if(array_key_exists($lJid, $lRangeRet[$lDate])){
                  if($lNum > $lRangeRet[$lDate][$lJid]){
                    $lRangeRet[$lDate][$lJid] = $lNum;
                  }
                } else {
                  $lRangeRet[$lDate][$lJid] = $lNum;
                }
              } else {
                $lRangeRet[$lDate][$lJid] = $lNum;
              }
            }
        }
        
        foreach($lRange as $lGroup){
          if(!array_key_exists($lGroup, $lRangeRet)){
            $lRangeRet[$lGroup] = array();
          }
        }

        foreach($lRangeRet as $lGroup => $lArr){
          $lSize = sizeof($lArr);
          $lApls = array_sum($lArr);
          $lVal = $lApls / $lSize;
            
          $lRet['data'][$lSrc]['name'] = $lName;
          $lRet['data'][$lSrc]['results'][$lGroup] = round($lVal, 1);
        }
        
        ksort($lRet['data'][$lSrc]['results']);
    }
        
    $lRet = $this -> produceChart($lRet);
    
    return $lRet;
  }
  
  protected function produceChart($aRet) {
    $lCats = $lSeries = array();
    $lColours = array('art' => "#f8a526", 'rep' => "#792f79", 'mis' => "#ed4a49", 'com' => "#459845", 'sec' => "#275772", 'tra' => "#e69e4a", 'adm' => "#abd155", 'pro' => "#84bfe8");
    
    foreach($aRet['data'] as $lSrc => $lArr){
      if(array_key_exists('name', $lArr)){
        $lData = array();
        foreach($lArr['results'] as $lDate => $lVal){
          if(!in_array($lDate, $lCats)){
            $lCats[] = $lDate;
          }
          $lData[] = $lVal;
        }
        
        $lSeries[] = array("name" => $lArr['name'], "data" => $lData, "color" => $lColours[$lSrc]);
      }
    }

    $lParams = array(
      "xAxis" => array(
        "title" => array("text" => $this -> mXaxis[$this -> mPeriod]),
      ),
      "yAxis" => array(
        "title" => array("text" => "Average Number of APLs"),
      ),
      "plotOptions" => array( 
        "column" => array( 
          "pointPadding" => "0.2", 
          "borderWidth" => "0", 
          "tooltip" => array(
            "pointFormat" => "<span style=\"color:{series.color}\">Average Approval Cycles:</span> {point.y}<br/>"
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