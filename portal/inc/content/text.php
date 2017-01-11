<?php
class CInc_Content_Text extends CCor_Obj {
	
	
	public function getTextContent($aContAli) {
		$lSql='SELECT content_'.LAN.' FROM al_text_content WHERE alias="'.$aContAli.'"';
		return CCor_Qry::getStr($lSql);
	}
	
	public function getTextLabel($aContAli) {
		$lSql = 'SELECT name_'.LAN.' FROM al_text_content WHERE alias="'.$aContAli.'"';
		return CCor_Qry::getStr($lSql);
	}

}