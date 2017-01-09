<?php
require_once(WWW_DIR.'classes/Cache.php');

class Mpx {
	
	//private $unifiedUrl = 'http://corpmpx03t.amer.schawk.com:9192/?AppName=Generic&ReqLevel=%s&ReferenceId=';
	private $unifiedUrl = 'http://c3corpmpx01p.schawk.com:9192/?AppName=Generic&ReqLevel=%s&ReferenceId=';
	private $onDemandJdfUrl = 'http://c3corpmpx01p.schawk.com:9984/MPX?SubProjectID=%s&LocationID=%s';
	
	public $errorCode = 0;
	public $errorMsg = '';
	
	public $backstageSystems = array(
			63052	=>	'Chennai Production',
			//93006	=>	'Kalamazoo Production',
			63030	=>	'Penang Production',
			//63033	=>	'Shanghai Production',
			83011	=>	'Toronto Production',
			83202	=>	'Toronto R&D'
		);
		
	public function xmlPath($id)
	{
		$int = (int) $id;
		$level1 = substr($int, 0, 3);
		$level2 = substr($int, 0, 6);
		return $level1.'/'.$level2.'/'.$id;
	}
	
	public function fetchUnifiedData($id)
	{	
		$fromCache = false;
		
		$cache = new Cache();
		$cache->prune();
		
		if ($cache->enabled() && $cache->exists($id))
		{
			//Load from cache
			$xmlData = $cache->fetch($id);
			$fromCache = true;
		}
		else
		{
			if (strpos($id, '-') === false)
				$url = sprintf($this->unifiedUrl, 'Project');
			else
				$url = sprintf($this->unifiedUrl, 'SubProject');
			
			//Fetch fresh
			//$xmlData = $this->curlFetch($url.$id);
			$len = strlen($id);
			if ($len == 9)
				$id = str_pad($id, 12, "0", STR_PAD_LEFT);
			
			$xmlData = @file_get_contents(WWW_DIR.'xmlstore/root/'.$this->xmlPath($id).'.xml');	
			
			//Check http request was ok
			if ($xmlData === false)
			{
				$this->errorCode = 2;
				$this->errorMsg = 'Service Order not found';
				return false;
			}
		}
	
		//Check returned xml is valid
		libxml_use_internal_errors(true);
		$xmlObj = simplexml_load_string($xmlData);
		if ($xmlObj === false)
		{
			$this->errorCode = 1;
			$this->errorMsg = 'Unable to parse XML from PO/PI';
			foreach(libxml_get_errors() as $error)
				$this->errorMsg .= '<br/>'.$error->message;
			
			return false;
		}
		else
		{
			//XML is valid
			
			if ($fromCache === false)
			{
				//Add request timestamp to xml
				$xmlObj->addChild('request_timestamp', time());
			}
		}
		
		//Check for MPX error
		if ($xmlObj->error_code != 0)
		{
			$this->errorCode = $xmlObj->error_code;
			$this->errorMsg = $xmlObj->message;
			return false;
		}
		
		//Save to cache
		if ($cache->enabled() && $fromCache === false)
			$cache->store($id, $xmlObj->asXML());
	
		return $xmlObj;
	}

	public function curlFetch($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 180);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		
		$buffer = curl_exec($ch);
		$errCode = curl_errno($ch);
		$errMsg = curl_error($ch);
		
		curl_close($ch);
		
		if ($errCode == 0)
		{
			return $buffer;
		}
		else
		{
			$this->errorCode = $errCode;
			$this->errorMsg = $errMsg;
			return false;
		}			
	}

}


?>