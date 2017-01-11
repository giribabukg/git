<?php
class CApi_Xchange_Xmlparser extends CCust_Api_Xchange_Xmlparser {
	
	protected function getFields() {
		if (!isset($this->mFields)) {
			$this->createFields();
		}
		return $this->mFields;
	}

	protected function createFields() {
		$this->mFields = array();
		
		$this->addField('corecusid', '//IDOC/E1KNA1M/KUNNR');
		$this->addField('pro_printer', '//IDOC/E1KNA1M/NAME1');
		$this->addField('city', '//IDOC/E1KNA1M/ORT01');
		$this->addField('zip_code', '//IDOC/E1KNA1M/PSTLZ');
		$this->addField('country', '//IDOC/E1KNA1M/LAND1');
		$this->addField('pro_address', '//IDOC/E1KNA1M/STRAS');
		$this->addField('pro_telephone', '//IDOC/E1KNA1M/TELF1');
		$this->addField('core_cust_class', '//IDOC/E1KNA1M/KUKLA');
		#$this->addField('', 'pro_contact');
		#$this->addField('', 'email');
		#$this->addField('', 'house_no');
	}

	public function parse($aXml, $aFile) {
		try {		
			$lRet = $this->doParse($aXml, $aFile);
			return $lRet;
		} catch (Exception $exc) {
			$this->msg();
		}
	}

	protected function doParse($aXml, $aFile) {
		$this->mDoc = simplexml_load_string($aXml);
		
		$lName = $this->mDoc->getName();
		
		if ($lName != 'Job') {
			$lRet = $this->doParseOrders();
			return ($lRet ? $this->doInsert($lRet, $aFile) : false);
		}

		return false;
	}
	
	protected function doParseOrders() {
		$lRet = array();
		$lBase = $this->mDoc;
		
		//header information
		$lFields = $this->getFields();
		foreach ($lFields as $lAlias => $lRow) { 
			$lSource = $lRow['src'];
			$lFmt = $lRow['fmt'];

			$lFunc = 'format' . $lFmt;
			if($this->hasMethod($lFunc)){
			  $lRet[$lAlias] = $this->$lFunc($lSource);
			} else {
  			  $lNode = $lBase->xpath($lSource);
  			  if(!empty($lNode)){
  			    foreach($lNode as $lVal){
      		      $lRet[$lAlias] = (string) trim($lVal);
  			    }
      		  }
			}
		}
		
		return $lRet;
	}
	
	protected function doInsert($aData, $aFileName = ''){
	  $lOutcome = false;
	  $lTbl = 'al_xchange_jobs_' . MID;
	  $lDat = $aData;
	  $lDat ['x_src'] = 'pro';
	  $lDat ['x_xml'] = $aFileName;
	
	  $lSet = '';
	  foreach($lDat as $lKey => $lVal) {
	    $lSet .= $lKey . '=' . esc($lVal) . ',';
	  }
	  
	  $lCorId = explode(" ", $lDat['corecusid']);
	  if(sizeof($lCorId) > 1){
	    $lCorId[] = $lDat['corecusid'];
	  }
	
	  $lFound = false;
	  $lSql = 'SELECT * FROM '.$lTbl.' WHERE corecusid REGEXP "'.implode('|', $lCorId).'"';
	  $lQry = new CCor_Qry($lSql);
	  foreach ($lQry as $lRows) {
	    $lFound = true;
	    $lSet.= 'x_update_date='.esc(date('Y-m-d H:i:s')).',';
	    if($lRows['x_status'] == 'assigned' || $lRows['x_status'] == 'update done')
	      $lSet.= 'x_status="update",';
	
	    $lSql = 'UPDATE '.$lTbl.' SET '.$lSet;
	    $lSql = strip($lSql).' WHERE id='.$lRows['id'].';';
	    $lOutcome = CCor_Qry::exec($lSql);
	  }
	
	  if($lFound === false){
	    $lSet.= 'x_import_date='.esc(date('Y-m-d H:i:s')).',';
	    $lSql = 'INSERT INTO '.$lTbl.' SET '.$lSet;
	    $lSql = strip($lSql).';';
	
	    $lOutcome = CCor_Qry::exec($lSql);
	  }
	
	  return $lOutcome;
	}
		
	
}
