<?php
date_default_timezone_set('UTC');
ini_set("auto_detect_line_endings", true);

define('WWW_TOP', dirname($_SERVER['PHP_SELF']) == '/' ? '' : dirname($_SERVER['PHP_SELF']));
define('WWW_DIR', realpath(dirname(__FILE__)).'/');
define('SMARTY_DIR', WWW_DIR.'/libs/smarty/');