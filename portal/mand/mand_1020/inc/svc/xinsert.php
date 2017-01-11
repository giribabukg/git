<?php
class CSvc_Xinsert extends CCust_Svc_Xinsert {

	protected function doExecute() {
		$lIgnore = array(
				'id', 'x_src', 'x_jobid', 'x_status', 
				'x_import_date','x_assign_date', 'x_update_date', 'x_xml', 'filename'
		);
		$lTbl = 'al_xchange_jobs_'.MID;
		
		//Insert new jobs
		$lSql = 'SELECT * FROM '.$lTbl.' WHERE x_status="new" ORDER BY id';
		$lQry = new CCor_Qry($lSql);
		foreach ($lQry as $lRow) {
		  $lMod = new CJob_Tra_Mod();
		  foreach ($lRow as $lKey => $lVal) {
		    if (in_array($lKey, $lIgnore)) continue;
		    
		    if(!empty($lVal)) {
		      $lMod->forceVal($lKey, $lVal);
		      $lMod->setOld($lKey, '');
		    }
		  }
		  $lRes = $lMod->insert();
			if ($lRes) {
				$lJid = $lMod->getInsertId();
				//Export automatically				
				if($lJob['webstatus'] == 10){
				  $lFac = new CJob_Fac('tra', $lJid);
				  $lJob = $lFac -> getDat();
				
				  #$lStep = new CJob_Art_Step($lJid, $lJob);
				  #$lMsg = 'JDF Exported Automatically.';
				  #$lHasStepped = $lStep -> doStep(559, $lMsg);
				}
			}
			
			if ($lRes) {				
			$lSql = 'UPDATE '.$lTbl.' SET x_status="assigned", x_jobid='. $lJid .',x_update_date=NOW() ';
			$lSql.= 'WHERE id='.esc($lRow['id']);
			CCor_Qry::exec($lSql);
			}
		}
		
		//Update existing jobs
		$lSql = 'SELECT * FROM '.$lTbl.' WHERE x_status="update" ORDER BY id LIMIT 0, 10';
		$lQry = new CCor_Qry($lSql);
		foreach ($lQry as $lRow) {
			$lJobId = $lRow['x_jobid'];			
			$lMod = new CJob_Tra_Mod($lJobId);
			
			$lFac = new CJob_Fac('tra', $lJobId);
			$lOld = $lFac -> getDat();
			
			foreach ($lRow as $lKey => $lVal) {
			  if (in_array($lKey, $lIgnore)) continue;
				
			  $lOldVal = (isset($lOld[$lKey])) ? $lOld[$lKey] : '';
			  if(!empty($lVal) && $lVal != $lOldVal) {
  				$lMod->forceVal($lKey, $lVal);
          	 	$lMod->setOld($lKey, $lOldVal);
          	  }
			}
			$lRes = $lMod->update();
			
			if ($lRes) {
			  $lSql = 'UPDATE '.$lTbl.' SET x_status="update done",x_update_date=NOW() ';
			  $lSql.= 'WHERE id='.esc($lRow['id']);
			  CCor_Qry::exec($lSql);
			}
		}

		return true;
	}
}