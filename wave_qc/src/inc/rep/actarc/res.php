<?php
class CInc_Rep_Actarc_Res extends CRep_Base_Res {

  public function __construct($aSrc) {
    $this -> mSrc = $aSrc;
    $this -> mTbl = 'al_job_his';
    parent::__construct($aSrc, $this -> mTbl);
  }
  
  protected function addColumns() {
    $this -> addCol('src_id');
    $this -> addCol('datum');
    $this -> addCol('to_status');
  }
  
  public function getResult() {
    $lRet = array();
    
    $lSize = $this -> groupSize();
    for($lNum=$lSize; $lNum >= 0; $lNum--){
	  $lDate = (!empty($this -> mTo)) ? date("Y-m-d", strtotime($this -> mTo)) : date("Y-m-d");
      if($this -> mPeriod == 'month'){
	    $lDate = date("Y-m-01", strtotime($lDate));
	  }
      $lIndex = date($this -> mFmt[$this -> mPeriod], strtotime($lDate. ' -'.$lNum.' '.$this -> mPeriod));
      $lRet['data']['created'][$lIndex] = 0;
      $lRet['data']['archived'][$lIndex] = 0;
    }

    $this -> mIte = $this -> getIterator($this -> mTbl);
    $this -> addColumns();
    $this -> mIte -> addCnd('to_status IN (10,200)');
    $this->addCondition('mand', '=', MID);
    $this->addCondition('src', '!=', 'pro');
    $lRes = $this -> mIte -> getArray();
      
    foreach($lRes as $lJob){
      $lDatum = $lJob['datum'];
      $lIdx = date($this -> mFmt[$this -> mPeriod], strtotime($lDatum));
    
      $lToStatus = $lJob['to_status'];
      $lType = ($lJob['to_status'] == 10) ? 'created' : 'archived';
      
      if($this -> inBetweenDates($this -> mFmt[$this -> mPeriod], $lDatum, array_keys($lRet['data']['created']))){
        $lRet['data'][$lType][$lIdx] = intval($lRet['data'][$lType][$lIdx]) + 1;
      }
    }
    ksort($lRet['data']);
    
    $lRet = $this -> produceChart($lRet);
    
    return $lRet;
  }
  
  protected function inBetweenDates($aFmt, $aDate, $aDateRange){
    $lDateRange = date($aFmt, strtotime($aDate));
    return in_array($lDateRange, $aDateRange);
  }
  
  protected function produceChart($aRet) {
    $lSeries = array();

    foreach($aRet['data'] as $lKey => $lData){
      $lSeries[] = array("name" => ucfirst($lKey), "data" => $lData);
    }
    $lCats = array_keys($aRet['data']['created']);

    $lParams = array(
      "xAxis" => array(
        "title" => array("text" => $this -> mXaxis[$this -> mPeriod]),
      ),
      "yAxis" => array(
        "title" => array("text" => "Number of Jobs"),
      ),
      "plotOptions" => array( 
        "area" => array( 
          "marker" => array(
            "enables" => "false",
            "symbol" => "circle",
            "radius" => "2",
            "states" => array("hover" => array("enabled" => "true"))
          ),
          "tooltip" => array(
              "headerFormat" => "<span style=\"font-size: 14px\">{point.x}</span><br/>",
          	  "pointFormat" => "<span style=\"color:{series.color}\">{series.name}</span>: <b>{point.y}</b><br/>"
    	  ),
        ),
      )
    );
    
    $lChart = new CRep_Highcharts("area");
    $lChart -> removeParam("plotOptions");
    $lChart -> setParams($lParams);
    $lChart -> setCategories($lCats);
    $lChart -> setSeries($lSeries);
    
    return $lChart -> getContent();   
  }

}