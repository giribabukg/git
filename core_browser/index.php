<?php
require_once('config.php');

//Include required classes/libs
require_once(WWW_DIR.'classes/Page.php');
require_once(WWW_DIR.'classes/ServiceOrder.php');

$page = new Page();

if (file_exists(WWW_DIR.'pages/'.$page->page.'.php'))
	include(WWW_DIR.'pages/'.$page->page.'.php');
else
	$page->show404();
?>