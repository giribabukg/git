<?php
class CInc_Api_Xchange_Logger extends CCor_Obj {
	
	public function logMsg($aTyp, $aLvl, $aMsg = '', $aFile){
		$lDat = array();
		$lDat['filename'] = $aFile;
		$lDat['mand'] = MID;
		$lDat['typ'] = $aTyp;
		$lDat['lvl'] = $aLvl;
		$lDat['msg'] = $aMsg;
	
		$lSql = 'INSERT INTO al_xchange_log SET ';
		foreach($lDat as $lKey => $lVal){
			$lSql .= $lKey . '=' . esc($lVal) . ',';
		}
		$lSql = strip($lSql) . ';';
		return CCor_Qry::exec($lSql);
	}
}