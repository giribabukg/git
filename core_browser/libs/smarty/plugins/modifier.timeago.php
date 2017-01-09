<?php 
/** 
 * Smarty plugin 
 * @package Smarty 
 * @subpackage plugins 
 */ 


/** 
 * Smarty date modifier plugin 
 * Purpose:  converts unix timestamps or datetime strings to words 
 * Type:     modifier<br> 
 * Name:     timeAgo<br> 
 * @author   Stephan Otto 
 * @param string 
 * @return string 
 */ 
function smarty_modifier_timeAgo( $date) 
{ 
	if ($date == "")
		return "n/a";
		
	$sec = time() - $date;
	if ($sec <= 0) { return '0 seconds'; }
	if ($sec < 60) { return $sec.' seconds'; }
	
	$min = $sec/60;
	if ($min < 60) { return floor($min+0.5).' minutes'; } 
	
	$hrs = $min/60;
	if ($hrs < 24) { return round($hrs, 1).'  hours'; }
	
	$days = $sec/60/60/24;
	$result = round($days, 1).' days';
	return $result;
} 

?> 