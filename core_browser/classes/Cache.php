<?php

class Cache {

	public $ttl = 43200; //12 hours
	
	private $cacheLoc = '';
	private $cacheExt = '.cache';
	private $enabled = false;
	
	function __construct()
	{
		$this->cacheLoc = realpath(dirname(__FILE__).'/../cache').'/';
		
		if (is_dir($this->cacheLoc) && is_writable($this->cacheLoc))
			$this->enabled = true;
	}
	
	public function exists($key)
	{
		//Check for cached data
		$cacheFile = $this->cacheFile($key);
		
		if (file_exists($cacheFile))
		{
			//clearstatcache();
			if (time() - filemtime($cacheFile) < $this->ttl) { 
				return true;
			}
		}
		
		return false;
	}
	
	public function store($key, $data)
	{
		return file_put_contents($this->cacheFile($key), $data);
	}
	
	public function fetch($key)
	{
		return file_get_contents($this->cacheFile($key));
	}
	
	public function delete($key)
	{
		return unlink($this->cacheFile($key));
	}
	
	public function prune()
	{
		foreach(glob($this->cacheLoc.'*'.$this->cacheExt) as $cacheFile)
			if (time() - filemtime($cacheFile) >= $this->ttl) 
				@unlink($cacheFile);
	}
	
	public function enabled()
	{
		return $this->enabled;
	}
	
	private function cacheFile($key)
	{
		return $this->cacheLoc.$key.$this->cacheExt;
	}
	
}
?>