<?php
require_once('Mpx.php');
require_once('ColorValues.php');

class ServiceOrder {
	
	public $serviceOrderHead = array();
	
	public $salesOrderHead = array();
	public $salesOrderItem = array();
	
	public $operations = array();
	
	public $components = array();
	
	public $partners = array();
	public $contacts = array();
	
	public $barcodes = array();
	
	public $colors = array();
	
	public $salesOrderCharacteristics = array();
	
	public $sku = array();
	
	public $custmatinfo = array();
	
	public $customerFields = array();
	
	public $orderId = '';
	public $requestTimestamp = 0;
	
	public $errorCode = 0;
	public $errorMsg = '';
	
	private $mpx;
	
	function __construct()
	{
		$this->mpx = new Mpx();
	}
	
	public function printr($arr)
	{
		echo '<pre>';
		print_r($arr);
		echo '</pre>';
	}
	
	public function array_keys_multi($inputArr)
	{
		$outArr = array();
		foreach($inputArr as $arrItem)
		{
			foreach($arrItem as $itemKey=>$itemValue)
				$outArr[$itemKey] = '';
		}			
		return $outArr;
	}
	
	public function isValidOrderId($id)
	{
		return ctype_digit($id);
	}
	
	public function parseId($id)
	{
		if (!$this->isValidOrderId($id))
		{
			$this->errorCode = 1;
			$this->errorMsg = 'Invalid Order ID provided';
			return false;
		}
				
		$this->orderId = $id;
		
		return true;
	}
	
	public function get($id)
	{
		if ($this->parseId($id) === false)
			return false;
		
		$svcObj = $this->mpx->fetchUnifiedData($id);

		if ($svcObj === false)
		{
			$this->errorCode = $this->mpx->errorCode;
			$this->errorMsg = $this->mpx->errorMsg;
			return false;
		}
		$this->parse($svcObj);
		
	}
	
	public function parse(&$svcObj)
	{
		//$this->printr($svcObj);
		
		$this->requestTimestamp = (int) $svcObj->request_timestamp;
		
		$this->parseServiceOrderHead($svcObj);
		//$this->printr($this->serviceOrderHead);
		
		$this->parseSalesOrderHead($svcObj);
		//$this->printr($this->salesOrderHead);
		
		$this->parseSalesOrderItem($svcObj);
		//$this->printr($this->salesOrderItem);
		
		$this->parseOperations($svcObj);
		//$this->printr($this->operations);
		
		$this->parseComponents($svcObj);
		//$this->printr($this->components);
		
		$this->parsePartners($svcObj);
		//$this->printr($this->partners);
		//$this->printr($this->contacts);
		
		$this->parseBarcodes($svcObj);
		//$this->printr($this->barcodes);
		
		$this->parseColors($svcObj);
		//$this->printr($this->colors);
		
		$this->parseSalesOrderCharacteristics($svcObj);
		//$this->printr($this->salesOrderCharacteristics);
		//$this->printr($this->customerFields);
		
		$this->parseSku($svcObj);
		//$this->printr($this->sku);
		
		$this->parseCustmatinfo($svcObj);
		//$this->printr($this->custmatinfo);
		
		$this->projectId = $this->serviceOrderHead['ORDERID'];
		
		return $this;
	
	}
	
	private function parseAttributeChildren(&$attrObj)
	{
		$tmpArr = array();
		foreach($attrObj->children($this->mpx->mpxNS) as $attribute)
			$tmpArr[$attribute->getName()] = (string) $attribute ;
			
		return $tmpArr;
	}
	
	private function parseServiceOrderHead(&$svcObj)
	{
		foreach($svcObj->ServiceOrder->ServiceOrderHead->attributes() as $key=>$val)
		{
			$this->serviceOrderHead[$key] = (string) $val;
		}
	}
	
	private function parseSalesOrderHead(&$svcObj)
	{
		foreach($svcObj->SalesOrder->SalesOrderHead->attributes() as $key=>$val)
		{
			$this->salesOrderHead[$key] = (string) $val;
		}
	}
	
	private function parseSalesOrderItem(&$svcObj)
	{
		foreach($svcObj->SalesOrder->SalesOrderItem->attributes() as $key=>$val)
		{
			$this->salesOrderItem[$key] = (string) $val;
		}
	}
	
	private function parseOperations(&$svcObj)
	{
		foreach($svcObj->ServiceOrder->Operation->children() as $operation)
		{	
			$activityId = (int) $operation->attributes()->ACTIVITY;
			
			foreach($operation->attributes() as $key=>$val)
			{
				$this->operations[$activityId][$key] = (string) $val ;
			}
		}
		ksort($this->operations);
	}
	
	private function parseComponents(&$svcObj)
	{
		foreach($svcObj->ServiceOrder->Component as $component)
		{	
			$activityId = (string) $component->attributes()->ACTIVITY;
			$itemNumber = (int) $component->attributes()->ITEM_NUMBER;
			
			foreach($component->attributes() as $key=>$val)
			{
				$this->components[$activityId][$itemNumber][$key] = (string) $val ;
			}
		}
		ksort($this->components);
	}
	
	private function parsePartners(&$svcObj)
	{
		$contacts = array();
		
		foreach($svcObj->ServiceOrder->Partner->children() as $partner)
		{
			//$this->printr($partner);
			$nodeName = $partner->getName();
			foreach($partner->attributes() as $key=>$val)
				$contacts[$nodeName][$key] = (string) $val ;
		}
		
		foreach($svcObj->SalesOrder->SalesOrderPartner->children() as $partner)
		{
			//$this->printr($partner);
			$nodeName = $partner->getName();
			foreach($partner->attributes() as $key=>$val)
				$contacts[$nodeName][$key] = (string) $val ;
		}
		
		foreach($contacts as $key=>$partnerArr)
		{		
			switch($key) {
				case 'AG':
					$this->contacts[$key] = $partnerArr;
					$this->contacts[$key]['ROLE_DESCRIPTION'] = 'Sold-to party';
					break;
				case 'AP':
					$this->contacts[$key] = $partnerArr;
					$this->contacts[$key]['ROLE_DESCRIPTION'] = 'Contact person';
					break;
				case 'RE':
					//$this->partners[$key]['ROLE_DESCRIPTION'] = 'Bill-to party';
					break;
				case 'RG':
					//$this->partners[$key]['ROLE_DESCRIPTION'] = 'Payer';
					break;
				case 'VE':
					$this->contacts[$key] = $partnerArr;
					$this->contacts[$key]['ROLE_DESCRIPTION'] = 'Sales Employee';
					break;
				case 'WE':
					$this->contacts[$key] = $partnerArr;
					$this->contacts[$key]['ROLE_DESCRIPTION'] = 'Ship-to party';
					break;
				case 'ZM':
					$this->contacts[$key] = $partnerArr;
					$this->contacts[$key]['ROLE_DESCRIPTION'] = 'Person respons.';
					break;
				case 'ZN':
					$this->partners[$key] = $partnerArr;
					$this->partners[$key]['ROLE_DESCRIPTION'] = 'Printer';
					break;
				case 'ZO':
					$this->partners[$key] = $partnerArr;
					$this->partners[$key]['ROLE_DESCRIPTION'] = 'Brandowner';
					break;
				case 'ZR':
					$this->partners[$key] = $partnerArr;
					$this->partners[$key]['ROLE_DESCRIPTION'] = 'Agency';
					break;
				case 'ZS':
					$this->partners[$key] = $partnerArr;
					$this->partners[$key]['ROLE_DESCRIPTION'] = 'Supplier';
					break;
			}
		}
	}
	
	private function parseBarcodes(&$svcObj)
	{
		$x = 0;
		foreach($svcObj->SalesOrder->Barcode->children() as $barcode)
		{
			foreach($barcode->attributes() as $key=>$val)
			{				
				$this->barcodes[$x][$key] = (string) $val ;
			}
			
			$memoNode = 'ZLP_BC_MEMO_TXT_'.str_pad($x+1, 2, 0, STR_PAD_LEFT);
			if (isset($svcObj->SalesOrder->SalesOrderCharacteristics->{$memoNode}))
				$this->barcodes[$x]['ZLP_BC_MEMO_TXT'] = (string) $svcObj->SalesOrder->SalesOrderCharacteristics->{$memoNode}->attributes()->VALUE;
				
			$x++;
		}
	}
	
	private function parseColors(&$svcObj)
	{
		$x = 0;
		$colorValues = new ColorValues();
		
		foreach($svcObj->SalesOrder->Color->children() as $color)
		{
			foreach($color->attributes() as $key=>$val)
			{
				//$key = str_replace(array('ZLP_', 'AG_', 'F_'), '', $key);
				//$key = str_replace('_', ' ', $key);
				//$key = ucwords(strtolower($key));
				$this->colors[$x][$key] = (string) $val ;
				
				if ($key == 'Color')
					$this->colors[$x]['ColorValue'] = $colorValues->lookup( (string) $val );
			}
			$x++;
		}
	}
	
	private function parseSalesOrderCharacteristics(&$svcObj)
	{
		foreach($svcObj->SalesOrder->SalesOrderCharacteristics->children() as $characteristic)
		{	
			$nodeName = $characteristic->getName();
			$this->salesOrderCharacteristics[$nodeName] = (string) $characteristic['VALUE'];
			
			if (preg_match('/ZLP_CUST_SPEC_FIELD_LABEL_(\d+)/', $nodeName, $fieldNum))
			{			
				$valueNode = (string) $svcObj->SalesOrder->SalesOrderCharacteristics->{'ZLP_CUST_SPEC_FIELD_VALUE_'.$fieldNum[1]}['VALUE'];
				$this->customerFields['ZLP_CUST_SPEC_FIELD_'.$fieldNum[1]] = array('label'=> (string) $characteristic['VALUE'], 'value'=> $valueNode);
				//$this->printr($characteristic);
			}
		}
		
		$colorValues = new ColorValues();
		
		$x = 1;
		foreach($svcObj->SalesOrder->Color->children() as $color)
		{			
			$inkNum = str_pad($x, 2, 0, STR_PAD_LEFT);
						
			foreach($color->attributes() as $key=>$val)
			{
				$chrcKey = $key.'_'.$inkNum;
				
				$this->salesOrderCharacteristics[$chrcKey] = (string) $val ;
				
				if ($key == 'ZLP_AG_F_COLOR')
					$this->salesOrderCharacteristics['ColorValue_'.$inkNum] = $colorValues->lookup( (string) $val );
			}
				
			$x++;
		}
		
	}
	
	private function parseSku(&$svcObj)
	{
		foreach($svcObj->SalesOrder->SKU->attributes() as $key=>$val)
		{
			$this->sku[$key] = (string) $val;
		}
	}
	
	private function parseCustmatinfo(&$svcObj)
	{
		foreach($svcObj->SalesOrder->CustMatInfo->attributes() as $key=>$val)
		{
			$this->custmatinfo[$key] = (string) $val;
		}
	}
	
}
?>