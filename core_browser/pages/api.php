<?php
require_once(WWW_DIR.'classes/Search.php');

/*
method = 
	barcodes
		value = code
	customerfields
		field = cf field
		value = value
	orders
		description = value
		brand = value
		subbrand = value
		category = value
		packsize = value
		packtype = value
		customer = value
		customerid = value
		printer = value
		brandowner = value
		agency = value
		supplier = value
	fetch
		orderid = value
*/

$allowedMethods = array('barcodes', 'customerfields', 'orders', 'fetch');

$allowedOrders = array('orderid', 'salesorderid', 'description', 'brand', 'subbrand', 'category', 'packsize', 'packtype', 'customer', 'customerid', 'printer', 'brandowner', 'agency', 'supplier');

if (!isset($_GET['method']) || !in_array($_GET['method'], $allowedMethods))
{
	$page->smarty->assign('errorCode', '601');
	$page->smarty->assign('errorMsg', 'Unknown API method requested. Valid methods are '.implode(', ', $allowedMethods));
}
else
{
	switch($_GET['method'])
	{
		case 'barcodes':
			if (empty($_GET['value']))
			{
				$page->smarty->assign('errorCode', '602');
				$page->smarty->assign('errorMsg', 'A value must be provided');
			}
			else
			{
				$search = new Search();
				$results = $search->barcodes($_GET['value'], true);
				$page->smarty->assign('results', $results);
			}
		break;
		case 'customerfields':
			if (empty($_GET['field']))
			{
				$page->smarty->assign('errorCode', '602');
				$page->smarty->assign('errorMsg', 'A field name must be provided');
			}
			elseif (empty($_GET['value']))
			{
				$page->smarty->assign('errorCode', '603');
				$page->smarty->assign('errorMsg', 'A value must be provided');
			}
			else
			{
				$search = new Search();
				$results = $search->customerfields($_GET['field'], $_GET['value'], true);
				$page->smarty->assign('results', $results);
			}
		break;
		case 'orders':
			$params = array();
			foreach($allowedOrders as $orderField)
			{
				if (isset($_GET[$orderField]) && $_GET[$orderField] != '')
				{		
					$params[$orderField] = $_GET[$orderField];
					if ($orderField == 'orderid')
						$params[$orderField] = intval($params[$orderField]);
				}
			}
			
			if (sizeof($params) == 0)
			{
				$page->smarty->assign('errorCode', '604');
				$page->smarty->assign('errorMsg', 'At least one valid parameter and value must be provided');
			}
			else
			{
				$search = new Search();
				$results = $search->orders($params, false);
				$page->smarty->assign('results', $results);
			}
		break;
		case 'fetch':
			if (empty($_GET['orderid']))
			{
				$page->smarty->assign('errorCode', '605');
				$page->smarty->assign('errorMsg', 'A valid order id must be provided');
			}
			else
			{
				$search = new Search();
				echo $search->fetch($_GET['orderid']);
				die();
			}
		break;
	}
}

$page->smarty->display('api.tpl');