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
		
		#$param = new Inc_Api_Xchange_Base();
		#$param->
		
		$lClient = $this->getParam('client');
		$this->msg('Client: '.$lClient);
		
        //MASTER ARTWORK (Beiersdorf)
        $this->addField('customer_po_number', 'ProjectId');
        $this->addField('customer_description', 'ProjectName');
        $this->addField('brand', 'BrandName');
        $this->addField('sub_brand', 'SubbrandName');
        $this->addField('category', 'BrandGroupName');
        $this->addField('csf9_value', 'ProductRangeName');
        $this->addField('csf3_value', 'SpgrId');
        $this->addField('service_order_name', 'SpgrName');
        $this->addField('csf8_value', 'FullgutNart');
        $this->addField('csf1_value', 'JbmUserFullName');
        $this->addField('csf2_value', 'PackDevUserFullName');
        $this->addField('customer_material_no', 'ArtworkNumber');
        $this->addField('pack_type', 'ArtworkType');
        $this->addField('pack_size', 'TemplateType');
        //$this->addField('', 'ArtworkStatus');
        //$this->addField('', 'LoopNumber');
        
        //LOCAL ARTWORK (Beiersdorf)
        $this->addField('country_of_sales', 'Cluster');
        $this->addField('csf4_value', 'LeadCountryName');
        $this->addField('csf5_value', 'NartId');
        $this->addField('code1', 'EanCode');
        $this->addField('code2', 'EanType');
        $this->addField('csf6_value', 'PackagingNart');
        
        //Country - Language (Beiersdorf)
        $this->addField('csf7_value', 'Country/Code');
        $this->addField('csf10_value', 'Language/Name');
        
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
      		      
      		      //Country (Beiersdorf)
      		      if($lAlias == "csf7_value"){
      		        $lCou7[] = (string) trim($lVal);
      		      }
      		      
      		      //Language (Beiersdorf)
      		      if($lAlias == "csf10_value"){
      		        $lLan10[] = (string) trim($lVal);
      		      }
      		     
  			    }
  			    $lRet['csf7_value'] = implode(", ", $lCou7);
  			    $lRet['csf10_value'] = implode(", ", $lLan10);
      		  }
			}
		}

		return $lRet;
	}
	
	protected function doInsert($aData, $aFileName = ''){
	  $lOutcome = false;
	  $lTbl = 'al_xchange_jobs_' . MID;
	  $lDat = $aData;
	  $lDat ['x_src'] = 'tra';
	  $lDat ['x_xml'] = $aFileName;
	
	  $lSet = '';
	  
	  foreach($lDat as $lKey => $lVal) {
	    $lSet .= $lKey . '=' . esc($lVal) . ',';
	  }
	  
	  $lCorId = explode(" ", $lDat['customer_material_no']); 
	  if(sizeof($lCorId) > 1){
	    $lCorId[] = $lDat['customer_material_no'];
	  }
	
	  $lFound = false;
	  $lSql = 'SELECT * FROM '.$lTbl.' WHERE customer_material_no REGEXP "'.implode('|', $lCorId).'"';
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
	
	protected function getParam($aKey, $aDefault = null) {
	  return (isset($this->mParams[$aKey])) ? $this->mParams[$aKey] : $aDefault;
	}
	
}
