<?php
require_once(SMARTY_DIR.'/Smarty.class.php');
require_once(WWW_DIR.'classes/Translation.php');

class Page {

	public $title = '';
	public $head = '';
	public $foot = '';
	public $content = '';
	public $page = '';
	public $view = '';
	public $language = 'en';
	public $translation = null;
	public $page_template = 'basepage.tpl'; 
	public $smarty = '';
	public $serverurl = '';
	public $pageTime = '';
	
	private $startTime = '';
	private $endTime = '';
	private $allowedLanguages = array('en', 'de');
	
	function __construct()
	{
		$this->startTime = microtime(true);
		
		//Set cookie and session params
		$sessLifetime = 604800; //1 week
		ini_set("session.use_trans_sid", 0);
		ini_set("session.use_only_cookies", 1);
		session_set_cookie_params($sessLifetime, '/', $_SERVER['SERVER_NAME']);
		session_start();
		setcookie(session_name(),session_id(),time()+$sessLifetime, '/', $_SERVER['SERVER_NAME']);
		
		//recent items list
		if (!isset($_SESSION['recent']))
			$_SESSION['recent'] = array();
		
		//language
		if (isset($_GET['lang']) && in_array($_GET['lang'], $this->allowedLanguages))
		{
			$this->language = $_GET['lang'];
			setcookie('sor_lang', $this->language,time()+$sessLifetime, '/', $_SERVER['SERVER_NAME']);
		}
		else if (isset($_COOKIE['sor_lang']) && in_array($_COOKIE['sor_lang'], $this->allowedLanguages))
		{
			$this->language = $_COOKIE['sor_lang'];
			setcookie('sor_lang', $this->language,time()+$sessLifetime, '/', $_SERVER['SERVER_NAME']);
		}
		else
		{
			list($locale) = explode('_', locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']));
			
			if (in_array($locale, $this->allowedLanguages))
			{
				$this->language = $locale;
			}
		}
		
		$this->translation = new Translation($this->language);
		
		$this->smarty = new Smarty();
		$this->smarty->setCaching(Smarty::CACHING_OFF);
		$this->smarty->template_dir = WWW_DIR.'templates/';
		$this->smarty->compile_dir = SMARTY_DIR.'templates_c/';
		$this->smarty->config_dir = SMARTY_DIR.'configs/';
		$this->smarty->cache_dir = SMARTY_DIR.'cache/';	
		$this->smarty->error_reporting = (E_ALL - E_NOTICE);
		
		if (isset($_SERVER["SERVER_NAME"]))
		{
			$this->serverurl = (isset($_SERVER["HTTPS"]) ? "https://" : "http://").$_SERVER["SERVER_NAME"].(!in_array($_SERVER["SERVER_PORT"], array('80', '443')) ? ":".$_SERVER["SERVER_PORT"] : "").WWW_TOP.'/';
		}
		
		$this->smarty->assign('serverroot', $this->serverurl);
		
		$this->page = (isset($_REQUEST['page'])) ? $_REQUEST['page'] : 'serviceorder';
		$this->view = (isset($_REQUEST['view']) && in_array($_REQUEST['view'], array('serviceorder', 'cdiworksheet'))) ? $_REQUEST['view'] : 'serviceorder';
		
		$this->smarty->assign('page', $this);
		$this->smarty->assign('text', $this->translation);
	}
	
	public function addToHead($str)
	{
		$this->head .= $str;
	}
	
	public function addToFoot($str)
	{
		$this->foot .= $str;
	}
	
	public function render() 
	{			
		$this->endTime = microtime(true);
		$this->pageTime = round(($this->endTime - $this->startTime), 4);
		
		$this->smarty->display($this->page_template);
	}
	
	public function show404($msg='')
	{
		header("HTTP/1.1 404 Not Found");
		if ($msg != '') {
			echo $msg;
		} else {
			echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
			<html><head>
			<title>404 Not Found</title>
			</head><body>
			<h1>Not Found</h1>
			<p>The requested URL '.$_SERVER['REQUEST_URI'].' was not found on this server.</p>
			<hr>
			<address>'.$_SERVER['SERVER_SIGNATURE'].'</address>
			</body></html>';
		}
		die();
	}
	
	public function isPostBack()
	{
		return (strtoupper($_SERVER["REQUEST_METHOD"]) === "POST");	
	}
	
	public function time_format($hms)
	{  
		if (strlen($hms) == 6)
		{
			$bits = array();
			$bits[] = substr($hms, 0, 2);
			$bits[] = substr($hms, 2, 2);
			$bits[] = substr($hms, 4, 2);
			
			return implode(':', $bits);
		}

		return $hms;
	}
}