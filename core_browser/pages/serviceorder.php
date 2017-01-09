<?php
require_once(WWW_DIR.'libs/php-barcode-generator/BarcodeGenerator.php');
require_once(WWW_DIR.'libs/php-barcode-generator/BarcodeGeneratorPNG.php');

if (isset($_GET['id']) && !empty($_GET['id']))
{
	$serviceorder = new ServiceOrder();
	
	$lookup = $serviceorder->get($_GET['id']);
	
	if ($lookup === false)
	{
		$page->smarty->assign('errorCode', $serviceorder->errorCode);
		$page->smarty->assign('errorMsg', $serviceorder->errorMsg);
	}
	else
	{
		$nameFields = array();
		//if (!empty($serviceorder->sku['MATNR']))
		//	$nameFields[] = $serviceorder->sku['MATNR']; //Sku number
		if (!empty($serviceorder->sku['ZZBRAND']))
			$nameFields[] = $serviceorder->sku['ZZBRAND']; //Brand Description
		if (!empty($serviceorder->sku['ZZSUBBR']))
			$nameFields[] = $serviceorder->sku['ZZSUBBR']; //Subbrand Description
		if (!empty($serviceorder->sku['ZZCATEG']))
			$nameFields[] = $serviceorder->sku['ZZCATEG']; //Category
		//if (!empty($serviceorder->serviceOrderHead['SHORT_TEXT']))
		//	$nameFields[] = $serviceorder->serviceOrderHead['SHORT_TEXT']; //SKU Description
		if (!empty($serviceorder->salesOrderCharacteristics['ZLP_COUNTRY_OF_SALES']))
			$nameFields[] = $serviceorder->salesOrderCharacteristics['ZLP_COUNTRY_OF_SALES']; //Country
		if (!empty($serviceorder->salesOrderCharacteristics['ZLP_PACK_SIZE']))
			$nameFields[] = $serviceorder->salesOrderCharacteristics['ZLP_PACK_SIZE']; //Packsize
		if (!empty($serviceorder->salesOrderCharacteristics['ZLP_PACK_TYPE']))
			$nameFields[] = $serviceorder->salesOrderCharacteristics['ZLP_PACK_TYPE']; //Packtype
				
		$serviceOrderName = implode(' ', $nameFields);
		$page->smarty->assign('serviceOrderName', $serviceOrderName);
		$page->smarty->assign('meta_title', intval($serviceorder->serviceOrderHead['ORDERID']).' '.$serviceOrderName.' - ');
		
		//$serviceorder->printr( $serviceorder->serviceOrderHead );
		if ( (int) $serviceorder->serviceOrderHead['FINISH_DATE'] != 0)
		{
			$dueDate = date_create_from_format('Ymd', $serviceorder->serviceOrderHead['FINISH_DATE']);
			$serviceorder->serviceOrderHead['due_date'] = date_format($dueDate, DATE_FORMAT);
		}
		$page->smarty->assign('serviceOrderHead', $serviceorder->serviceOrderHead);
	
		//$serviceorder->printr( $serviceorder->salesOrderHead );
		if ( (int) $serviceorder->salesOrderHead['PURCH_DATE'] != 0)
		{
			$poDate = date_create_from_format('Ymd', $serviceorder->salesOrderHead['PURCH_DATE']);
			$serviceorder->salesOrderHead['po_date'] = date_format($poDate, DATE_FORMAT);
		}
		$page->smarty->assign('salesOrderHead', $serviceorder->salesOrderHead);
	
		//$serviceorder->printr( $serviceorder->salesOrderItem );
		$page->smarty->assign('salesOrderItem', $serviceorder->salesOrderItem);
		
		$operations = array();
		$operationKey = array();
		foreach($serviceorder->operations as $opIdx=>$opArr)
		{
			$start_ymd = date_create_from_format('Ymd', $opArr['EARL_SCHED_START_DATE']);
			$opArr['start_ts'] = date_format($start_ymd, DATE_FORMAT).' '.$page->time_format($opArr['EARL_SCHED_START_TIME']);
			
			$end_ymd = date_create_from_format('Ymd', $opArr['LATE_SCHED_FIN_DATE']);
			$opArr['end_ts'] = date_format($end_ymd, DATE_FORMAT).' '.$page->time_format($opArr['LATE_SCHED_FIN_TIME']);
		
			$operations[$opIdx] = $opArr;
			
			//operations by standard text key
			$operationKey[$opArr['STANDARD_TEXT_KEY']][] = $opArr;
			
		}
		//$serviceorder->printr( $operations );
		$page->smarty->assign('operations', $operations);
		$page->smarty->assign('operationKey', $operationKey);
		
		//$serviceorder->printr( $serviceorder->partners );
		$page->smarty->assign('partners', $serviceorder->partners);
		$page->smarty->assign('contacts', $serviceorder->contacts);
	
		//Barcodes
		$page->smarty->assign('barcodes', $serviceorder->barcodes);
		$page->smarty->assign('barcodeCount', sizeof($serviceorder->barcodes));
		
		//Generate Sales/Service order barcodes
		$generator = new Picqer\Barcode\BarcodeGeneratorPNG();
		$salesOrderBarcode = $generator->getBarcode(intval($serviceorder->serviceOrderHead['SALES_ORD']), $generator::TYPE_CODE_39, 1, 15);
		$serviceOrderBarcode = $generator->getBarcode(intval($serviceorder->serviceOrderHead['ORDERID']), $generator::TYPE_CODE_39, 1, 20);
		
		$page->smarty->assign('salesOrderBarcode', base64_encode($salesOrderBarcode));
		$page->smarty->assign('serviceOrderBarcode', base64_encode($serviceOrderBarcode));
	
		//Colors
		$colors = array();
		$colorAttrs = $serviceorder->array_keys_multi($serviceorder->colors);
		
		//Group colors by the attributes
		foreach($serviceorder->colors as $crArrIdx=>$crArr)
		{
			foreach($colorAttrs as $attrk=>$attrv)
			{
				if (isset($crArr[$attrk]))
					$colors[$attrk][$crArrIdx] = $crArr[$attrk];
				else
					$colors[$attrk][$crArrIdx] = '';
			}
		}
		//Remove attributes that have no values for any color
		foreach($colors as $crArrIdx=>$crArr)
		{
			$emptyrow = true;
			foreach($crArr as $crVal)
			{
				if ($crVal != '')
					$emptyrow = false;
			}
	
			if ($emptyrow === true)
				unset($colors[$crArrIdx]);
		}	
		$page->smarty->assign('colors', $colors);
		$page->smarty->assign('colorCount', sizeof($serviceorder->colors));
		
		$page->smarty->assign('sku', $serviceorder->sku);
		$page->smarty->assign('components', $serviceorder->components);
		$page->smarty->assign('custmatinfo', $serviceorder->custmatinfo);
		$page->smarty->assign('values', $serviceorder->salesOrderCharacteristics);
		$page->smarty->assign('customerFields', $serviceorder->customerFields);
	
		$recentItems = $_SESSION['recent'] + array($serviceorder->serviceOrderHead['ORDERID']=>$serviceOrderName);
		$_SESSION['recent'] = array_slice($recentItems, -25, null, true);
	}
		
	$page->content = $page->smarty->fetch($page->view.'.tpl');

}
else
{
	$page->smarty->assign('recentItems', array_reverse($_SESSION['recent'], true));
	
	$page->content = $page->smarty->fetch('index.tpl');
}

$page->render();

?>