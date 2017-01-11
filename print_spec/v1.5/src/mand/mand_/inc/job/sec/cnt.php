<?php
class CJob_Sec_Cnt extends CCust_Job_Sec_Cnt {

	public function __construct(ICor_Req $aReq, $aMod, $aAct) {
		parent::__construct($aReq, $aMod, $aAct);
	}
	
	protected function actStep3622() {
		$this->selectAplDialog('apl-pdl');
	}
	
	protected function actCnf3622() {
		$this->confirmNewAplDialog('apl-pdl');
	}
}
