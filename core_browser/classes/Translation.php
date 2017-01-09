<?php

class Translation
{      
	private $language = 'en';
	
	function __construct($language)
	{
		if (!empty($language))
			$this->language = $language;
			
		require_once(WWW_DIR.'languages/'.$this->language.'.php');
	}
	
	function __($key)
	{
		if (defined($key))
			return constant($key);
		else
			return $key;
	}
	
}