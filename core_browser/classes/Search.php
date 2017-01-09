<?php
require_once(WWW_DIR.'classes/Db.php');
require_once(WWW_DIR.'classes/Mpx.php');

class Search
{      
	private $db = null;
	private $mpx = null;
	
	function __construct()
	{
		$this->db = new Db();	
		$this->mpx = new Mpx();
	}
	
	private function formatResult($stmt, $incXml)
	{
		$results = array();
		
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{			
			if ($incXml === true)
			{
				$xmlObj = $this->mpx->fetchUnifiedData($row['orderid']);
				
				if ($xmlObj === false)
				{
					$row['xmldata'] = '<error_code>'.htmlentities($this->mpx->errorCode).'</error_code><message>'.htmlentities($this->mpx->errorMsg).'</message>';
				}
				else
				{
					$row['xmldata'] = $xmlObj->ServiceOrder->asXml().$xmlObj->SalesOrder->asXml();
				}
			}
			$results[] = $row;
		}
		
		return $results;
	}
	
	public function barcodes($code, $incXml=false)
	{	
		$sql = "SELECT * FROM barcodes LEFT JOIN core ON core.orderid = barcodes.orderid WHERE barcodes.barcode1 = :code OR barcodes.barcode2 = :code OR barcodes.barcode3 = :code OR barcodes.barcode4 = :code OR barcodes.barcode5 = :code OR barcodes.barcode6 = :code";
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':code', $code);
		$stmt->execute();
		
		return $this->formatResult($stmt, $incXml);
	}
	
	public function customerfields($field, $value, $incXml=false)
	{		
		$sql = "SELECT * FROM customerfields LEFT JOIN core ON core.orderid = customerfields.orderid WHERE (field1_label = :label AND field1_value = :value) OR (field2_label = :label AND field2_value = :value) OR (field3_label = :label AND field3_value = :value) OR (field4_label = :label AND field4_value = :value) OR (field5_label = :label AND field5_value = :value)";
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':label', $field);
		$stmt->bindValue(':value', $value);
		$stmt->execute();
		
		return $this->formatResult($stmt, $incXml);
	}
	
	public function orders($params, $incXml=false)
	{
		$filters = array();
		
		foreach($params as $key=>$val)
			$filters[] = $key." = ? ";
			
		$sql = "SELECT * FROM core WHERE ".implode($filters, ' AND ');
		
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array_values($params));
		//$stmt->debugDumpParams();
		//print_r(array_values($params));
		return $this->formatResult($stmt, $incXml);
	}
	
	public function fetch($orderid)
	{
		$xmlObj = $this->mpx->fetchUnifiedData($orderid);
				
		if ($xmlObj === false)
			return '<results><error_code>'.htmlentities($this->mpx->errorCode).'</error_code><message>'.htmlentities($this->mpx->errorMsg).'</message></results>';
		else
			return $xmlObj->asXml();
	}
}