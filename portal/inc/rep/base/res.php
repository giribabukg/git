<?php
class CInc_Rep_Base_Res extends CCor_Obj {

  protected $mPeriod;
  protected $mFrom;
  protected $mTo;
	
  public function __construct($aSrc, $aTbl) {
    $this -> mSrc = $aSrc;

    $this -> mIte = $this -> getIterator($aTbl);
    $this -> mDef = CCor_Res::getByKey('alias', 'fie');
    $this -> mPlain = new CHtm_Fie_Plain();
    
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref('rep.src', $this -> mSrc);
    
    $this -> addColumns();
    $this -> getFilter();
  }

  protected function getIterator($aTbl) {
    return new CRep_Iterator($aTbl);
  }

  protected function addCol($aAlias) {
    $this -> mIte -> addField($aAlias);
  }

  protected function addCondition($aCol, $aOp, $aVal) {
    $this -> mIte -> addCondition($aCol, $aOp, addslashes($aVal));
  }
  
  protected function getFilter(){
  	$lUsr = CCor_Usr::getInstance();
    $lFil = $lUsr -> getPref('rep.fil', array());
  	if($lFil == ""){
    	$lFil = array();
    } else {
      if(gettype($lFil) == 'string'){
        $lFil = unserialize($lFil);
      }
    }
    $this -> mFrom = (array_key_exists("from", $lFil)) ? $lFil["from"] : '';
    $this -> mTo = (array_key_exists("to", $lFil)) ? $lFil["to"] : '';
    
    $this -> mPeriod = (array_key_exists("period", $lFil)) ? $lFil["period"] : 'week';
    
    $this -> mFmt = array('week' => 'W-Y', 'month' => 'Y-m', 'day' => 'Y-m-d');
    $this -> mXaxis = array('week' => 'Weekly', 'month' => 'Monthly', 'day' => 'Daily');
  }
  
  protected function groupSize() {
  	$lFrom = $lStrFrom = strtotime($this -> mFrom, 0);
  	$lTo  = $lStrTo = strtotime($this -> mTo, 0);
  	
  	if($lStrTo > 0 && empty($lFrom) !== FALSE) {
  	  $lFrom = strtotime("-11 ".$this->mPeriod, $lStrTo);
  	}
  	if($lStrFrom > 0 && empty($lTo) !== FALSE) {
  	  $lTo = time();
  	}
  	$lDiff = $lTo - $lFrom;
  	
  	switch ($this -> mPeriod){
  		case "month":
  			$lNum = floor($lDiff / 2592000) - 1;
  			break;
  		case "week":
  		    $lNum = floor($lDiff / 604800);
  			//$lNum = floor($lDiff / 68400 / 7);
  			break;
  		case "day":
  			$lNum = floor($lDiff / (60*60*24));
  			break;
  	}
  	
  	return ($lNum > 0) ? $lNum : 11; //11 back but start from 0
  }

  public function setOptions($aOpt = array()) {
    if (!empty($aOpt)) {
      foreach ($aOpt as $lKey => $lVal) {
        $this -> setOpt($lKey, $lVal);
      }
    }
  }

  protected function setOpt($aKey, $aVal) {
    $this -> dbg('Set Option '.$aKey.' = '.$aVal);
    $this -> mOpt[$aKey] = $aVal;
    if (empty($aVal)) {
      return;
    }
    $lFnc = 'setOpt'.$aKey;
    if ($this -> hasMethod($lFnc)) {
      $this -> $lFnc($aVal);
    }
  }

  protected function setOptGru1($aVal) {
    $this -> mIte -> addOrder($aVal);
    $this -> addCol($aVal);
  }

  protected function setOptGru2($aVal) {
    $this -> mIte -> addOrder($aVal);
    $this -> addCol($aVal);
  }

  protected function setOptMarke($aVal) {
    $this -> mIte -> addCondition('marke', 'like', '%'.addslashes($aVal).'%');
  }

  protected function setOptSorte($aVal) {
    $this -> mIte -> addCondition('sorte', 'like', '%'.addslashes($aVal).'%');
  }

  protected function setOptLand_besteller($aVal) {
    $this -> mIte -> addCondition('land_besteller', '=', addslashes($aVal));
  }

  protected function setOptRegional_code($aVal) {
    $this -> mIte -> addCondition('regional_code', '=', addslashes($aVal));
  }

  protected function setOptRegion_vertrieb($aVal) {
    $this -> mIte -> addCondition('region_vertrieb', '=', addslashes($aVal));
  }

  protected function setOptProd_art($aVal) {
    $this -> mIte -> addCondition('prod_art', '=', addslashes($aVal));
  }

  protected function setOptSrc($aVal) {
    $this -> mIte -> addCondition('src', '=', addslashes($aVal));
  }

  protected function getOpt($aKey, $aStd = NULL) {
    return (isset($this -> mOpt[$aKey])) ? $this -> mOpt[$aKey] : $aStd;
  }

  protected function getPlain($aKey, $aVal) {
    $lDef = $this -> mDef[$aKey];
    return $this -> mPlain -> getPlain($lDef, $aVal);
  }

  public function getResult() {
    $lRet = array();
    $lRes = $this -> mIte -> getArray();
    foreach ($lRes as $lRow) {
      $lRet[] = $this -> getItem($lRow);
    }
    return $lRet;
  }

  protected function getItem($aRow) {
    $lRet = array();
    foreach ($aRow as $lKey => $lVal) {
      if (isset($this -> mDef[$lKey])) {
        $lVal = $this -> getPlain($lKey, $lVal);
      }
      $lRet[$lKey] = $lVal;
    }
    $lRet['num'] = 1;
    return $lRet;
  }

  public function saveToCache() {
    $lRes = $this -> getResult();
    $lKey = $this -> mSrc.'_'.md5(serialize($this -> mOpt));
    $lCache = CCor_Cache::getInstance('reports');
    $lCache -> set($lKey, array('req' => $this -> mOpt, 'res' => $lRes));
    return $lKey;
  }

  protected function getWeekRange($lDate) {
    $lDate = strtotime($lDate);
    $lStartOfWeek = (date('w', $lDate) == 0) ? $lDate : strtotime('last monday', $lDate);

    return array(date('Y-m-d', $lStartOfWeek), date('Y-m-d', strtotime('next sunday', $lStartOfWeek)));
  }
  
  protected function getRange($aPeriod) {
    $lRet = array();
    
  	for($lNum=12; $lNum >= 0; $lNum--){
      $lDate = date("Y-m-d");
      $lDate = date('Y-m-d', strtotime($lDate.' -'.$lNum.' '.$aPeriod));
      $lDate = strtotime($lDate);
      
      switch($aPeriod){
        case 'month':
          $lStart = strtotime(date('Y-m-01', $lDate));
          $lEnd = strtotime(date('Y-m-t', $lDate));
          break;
        case 'week':
          $lStart = (date('w', $lDate) == 0) ? $lDate : strtotime('last monday', $lDate);
          $lEnd = strtotime('next sunday', $lStart);
          break;
        case 'day':
          $lStart = $lEnd = $lDate;
          break;
      }
      
      $lRet[$lNum] = array( date('Y-m-d', $lStart), date('Y-m-d', $lEnd) );
    }
    
    return $lRet;
  }
  
  protected function addOrdinalNumberSuffix($aNum) {
    if (!in_array(($aNum % 100),array(11,12,13))){
      switch ($aNum % 10) {
        // Handle 1st, 2nd, 3rd
        case 1:  return $aNum.'st';
        case 2:  return $aNum.'nd';
        case 3:  return $aNum.'rd';
      }
    }
    
    return $aNum.'th';
  }
  
  protected function getNetworkerConnection() {
	  $lCfg = CCor_Cfg::getInstance();
	  $lConfig = array(
	      'host' => $lCfg -> get('db.networker.ip'),
	      'user' => $lCfg -> get('db.networker.user'), 
	      'pass' => $lCfg -> get('db.networker.pass'), 
	      'name' => $lCfg -> get('db.networker.name')
	  );
	  
	  $lNetworker = new CCor_Anysql();
	  $lNetworker->setConfig($lConfig);
	  
	  return $lNetworker;
  }

}