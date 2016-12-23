<?php
class CInc_Api_Xchange_Excelparser extends CCor_Obj {

	protected function getFields(){
		if(! isset($this->mFields)){
			$this->createFields();
		}
		return $this->mFields;
	}

	protected function createFields(){
		$this->mFields = array();
		
		$this->addField('site', 'E13', 'sitecode'); // MBS Site
		$this->addField('eam_project_name', 'E9'); // EAM Project Name
		$this->addField('epap_number', 'E12'); // EPAP Number(s)
		$this->addField('land', 'E18'); // Countries
		$this->addField('brand', 'E21'); // Brand
		$this->addField('cutter_guide', 'E23'); // Cutter Guide
		$this->addField('pack_type', 'E22'); // Pack Type
		$this->addField('ppm_no', 'E10'); // PPM Number
		$this->addField('prod_site', 'E13', 'prodsitecode'); // Production Site
		$this->addField('languages', 'E19,E20'); // Languages
		$this->addField('func_variety', 'E25'); // Functional Variety
		$this->addField('spec_no', 'E24'); // Spec Number
		$this->addField('printer', 'E26', 'printergroup'); // Printer Name
		$this->addField('printer_site', 'E27'); // Printer Site
		$this->addField('ddl_08', 'H9', 'dates'); // File to Printer Date
		$this->addField('per_05', 'E11', 'userlastfirstname'); // SAC
		$this->addField('per_07', 'I14,I15,I16,I17,I18,I19,I20,I21,I22,I23', 'marketsabbs'); // Market(s)
		$this->addField('comm1', 'G30'); // Additional
	}

	protected function addField($aAlias, $aSource, $aFormatter = null){
		$this->mFields[$aAlias] = array(
				'src' => $aSource,
				'fmt' => $aFormatter
		);
	}

	protected function getJobFields(){
		if(! isset($this->mJobFields)){
			$this->createJobFields();
		}
		return $this->mJobFields;
	}

	protected function createJobFields(){
		$this->mJobFields = array();
		
		// Each job
		$this->addJobField('pack_code', 'C'); // Pack Code
		$this->addJobField('barcode_number', 'K'); // Barcode Number
		$this->addJobField('base_file', 'O'); // Base File Ref
		$this->addJobField('variety', 'F'); // Variety
		$this->addJobField('size', 'I'); // Size
	}

	protected function addJobField($aAlias, $aSource, $aFormatter = null){
		// 36-43 (8 jobs)
		$this->mJobFields[$aAlias] = array(
				'src' => $aSource,
				'fmt' => $aFormatter,
				'cell' => $aCell 
		);
	}

	public function parse($aExcel){
		try{
			$lRet = $this->doParse($aExcel);
			return $lRet;
		} catch(Exception $exc){
			$this->msg();
		}
	}

	protected function doParse($aExcel){
		try{
			require_once 'Office/PHPExcel.php';
			require_once 'Office/PHPExcel/IOFactory.php';
			
			// Load first worksheet of excel document
			$lFileType = PHPExcel_IOFactory::identify($aExcel);
			$lReader = PHPExcel_IOFactory::createReader($lFileType);
			$lReader->setReadDataOnly(true);
			$lPHPExcel = $lReader->load($aExcel);
			
			// Get all General information used in each job
			$lGen = array();
			$lFields = $this->getFields();
			foreach($lFields as $lAlias => $lRow){
				$lSource = explode(",", $lRow['src']);
				$lFormatHelper = $lRow['fmt'];
				
				$lVal = array();
				foreach($lSource as $lSrc){
					$lTmpVal = $lPHPExcel->getActiveSheet()->getCell($lSrc)->getValue();
					$lFmt = $lRow['fmt'];
					if(! empty($lFmt)){
						$lFunc = 'format' . $lFmt;
						if($this->hasMethod($lFunc)){
							$lVal[] = $this->$lFunc($lTmpVal);
						}
					} else{
						$lVal[] = $lTmpVal;
					}
				}
				
				$lGen[$lAlias] = implode(", ", $lVal);
			}
			
			// Get all specific job (max. 8 jobs)
			$lJobFields = $this->getJobFields();
			for($i = 36; $i < 44; $i ++){ // each row (job)
				$lTmpJob = $lGen;
				$lJobCol = $lPHPExcel->getActiveSheet()->getCell('B' . $i)->getValue();
				
				if($lJobCol !== ""){
					foreach($lJobFields as $lAlias => $lRow){
						$lSrc = $lRow['src'] . $i;
						$lFormatHelper = $lRow['fmt'];
						
						$lVal = $lPHPExcel->getActiveSheet()->getCell($lSrc)->getValue();
						$lFmt = $lRow['fmt'];
						if(! empty($lFmt)){
							$lFunc = 'format' . $lFmt;
							if($this->hasMethod($lFunc)){
								$lVal = $this->$lFunc($lVal);
							}
						}
						
						$lTmpJob[$lAlias] = $lVal;
					}
					
					$lRes = $this->doInsertItem($lTmpJob, ''); // $aExcel);
					if($lRes == false){
						return false;
					}
				}
			}
			
			return true;
		} catch(Exception $e){
			$this->msg('Error loading file "' . pathinfo($aExcel, PATHINFO_BASENAME) . '": ' . $e->getMessage());
			return false;
		}
	}

	protected function doInsertJob($aData, $aExcel = ''){
		$lOutcome = false;
		$lTbl = 'al_xchange_jobs_' . MID;
		$lDat = $aData;
		$lDat['x_src'] = 'com';
		// lDat ['x_xml'] = $aExcel;
		
		$lSet = '';
		foreach($lDat as $lKey => $lVal){
			$lSet .= $lKey . '=' . esc($lVal) . ',';
		}
		
		$lFound = false;
		$lPckCde = $lDat['pack_code'];
		$lSql = 'SELECT * FROM ' . $lTbl . ' WHERE pack_code=' . esc($lPckCde);
		$lQry = new CCor_Qry($lSql);
		foreach($lQry as $lRows){
			$lFound = true;
			$lSet .= 'x_update_date=' . esc(date('Y-m-d H:i:s')) . ',';
			if($lRows['x_status'] == 'assigned')
				$lSet .= 'x_status="update",';
			
			$lSql = 'UPDATE ' . $lTbl . ' SET ' . $lSet;
			$lSql = strip($lSql) . ' WHERE id=' . $lRows['id'] . ';';
			$lOutcome = CCor_Qry::exec($lSql);
		}
		
		if($lFound === false){
			$lSet .= 'x_import_date=' . esc(date('Y-m-d H:i:s')) . ',';
			$lSql = 'INSERT INTO ' . $lTbl . ' SET ' . $lSet;
			$lSql = strip($lSql) . ';';
			
			$lOutcome = CCor_Qry::exec($lSql);
		}
		
		return $lOutcome;
	}
	
	/* -----------FUNCTIONS-------------- */
	protected function formatUserlastfirst($aVal){
		$lUsr = CCor_Res::extract('fullname', 'id', 'usr');
		if(! isset($lUsr[$aVal])){
			$this->msg('Xchange: Unknown user ' . $aVal);
			return null;
		}
		return $lUsr[$aVal];
	}

	protected function formatProdsitecode($aVal){
		// abbreviation from helptable => mps
		$lAbb = explode(" - ", $aVal);
		
		return $lAbb[0];
	}

	protected function formatPrintergroup($aVal){
		// find group for printer name
		$lGru = CCor_Res::extract('name', 'id', 'gru', 67);
		// ar_dump($lGru);
		if(! isset($lGru[$aVal])){
			$this->msg('Xchange: Unknown category ' . $aVal);
			return null;
		}
		return $lGru[$aVal];
	}

	protected function formatDates($aVal){
		if(empty($aVal))
			return '';
		$lArr = explode(' ', $aVal);
		$lArr = explode('-', $lArr[0]);
		return $lArr[0] . '/' . $lArr[1] . '/' . $lArr[2];
	}

	protected function formatUserlastfirstname($aVal){
		// find user id from name (last, first)
		$lUsr = CCor_Res::extract('fullname', 'id', 'usr');
		if(! isset($lUsr[$aVal])){
			$this->msg('Xchange: Unknown user ' . $aVal);
			return null;
		}
		return $lUsr[$aVal];
	}

	protected function formatMarketsabbs($aVal){
		// abbreviation from helptable => mmkt
		return $aVal;
	}
}