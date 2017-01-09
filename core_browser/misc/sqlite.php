<?php
require_once('config.php');
require_once(WWW_DIR.'classes/Db.php');
require_once(WWW_DIR.'classes/ServiceOrder.php');

$db = new Db();

//create db
//$schema = file_get_contents(WWW_DIR.'db/core.sql');
//$db->exec($schema);

//prep insert queries
$core = $db->prepare("INSERT OR REPLACE INTO core (orderid, salesorderid, customerid, customer, brand, subbrand, category, packsize, packtype, printer, brandowner, agency, supplier, description) 
                VALUES (:orderid, :salesorderid, :customerid, :customer, :brand, :subbrand, :category, :packsize, :packtype, :printer, :brandowner, :agency, :supplier, :description)");

$core->bindParam(':orderid', $orderid);
$core->bindParam(':salesorderid', $salesorderid);
$core->bindParam(':customerid', $customerid);
$core->bindParam(':customer', $customer);
$core->bindParam(':brand', $brand);
$core->bindParam(':subbrand', $subbrand);
$core->bindParam(':category', $category);
$core->bindParam(':packsize', $packsize);
$core->bindParam(':packtype', $packtype);
$core->bindParam(':printer', $printer);
$core->bindParam(':brandowner', $brandowner);
$core->bindParam(':agency', $agency);
$core->bindParam(':supplier', $supplier);
$core->bindParam(':description', $description);

$barcodes = $db->prepare("INSERT OR REPLACE INTO barcodes (orderid, barcode1, barcode2, barcode3, barcode4, barcode5, barcode6) 
                VALUES (:orderid, :barcode1, :barcode2, :barcode3, :barcode4, :barcode5, :barcode6)");

$barcodes->bindParam(':orderid', $orderid);
$barcodes->bindParam(':barcode1', $barcode1);
$barcodes->bindParam(':barcode2', $barcode2);
$barcodes->bindParam(':barcode3', $barcode3);
$barcodes->bindParam(':barcode4', $barcode4);
$barcodes->bindParam(':barcode5', $barcode5);
$barcodes->bindParam(':barcode6', $barcode6);

$customerfields = $db->prepare("INSERT OR REPLACE INTO customerfields (orderid, field1_label, field1_value, field2_label, field2_value, field3_label, field3_value, field4_label, field4_value, field5_label, field5_value) 
                VALUES (:orderid, :field1_label, :field1_value, :field2_label, :field2_value, :field3_label, :field3_value, :field4_label, :field4_value, :field5_label, :field5_value)");

$customerfields->bindParam(':orderid', $orderid);
$customerfields->bindParam(':field1_label', $field1_label);
$customerfields->bindParam(':field1_value', $field1_value);
$customerfields->bindParam(':field2_label', $field2_label);
$customerfields->bindParam(':field2_value', $field2_value);
$customerfields->bindParam(':field3_label', $field3_label);
$customerfields->bindParam(':field3_value', $field3_value);
$customerfields->bindParam(':field4_label', $field4_label);
$customerfields->bindParam(':field4_value', $field4_value);
$customerfields->bindParam(':field5_label', $field5_label);
$customerfields->bindParam(':field5_value', $field5_value);

//find updated xml files within last 6 minutes (cron is set to 5 mins but lets add an extra minute to make sure we capture everything that has changed)
$cmd = 'find '.WWW_DIR.'xmlstore/root -mmin -6 -name *.xml';

exec($cmd, $output);

//import to db
foreach($output as $order)
{
	$serviceorder = new ServiceOrder();
	$lid = intval(basename($order));
	$lookup = $serviceorder->get($lid);

	if ($lookup === false)
	{
		echo $lid.': '.$serviceorder->errorMsg.PHP_EOL;
		continue;
	}
	
	echo date('Y-m-d H:i:s').': '.$serviceorder->serviceOrderHead['ORDERID'].PHP_EOL;
	$orderid = intval($serviceorder->serviceOrderHead['ORDERID']);
	$salesorderid = intval($serviceorder->serviceOrderHead['SALES_ORD']);
	$customerid = @$serviceorder->contacts['AG']['PARTNER_KEY'];
	$customer = @$serviceorder->contacts['AG']['NAME_LIST'];
	$brand = @$serviceorder->sku['ZZBRAND'];
	$subbrand = @$serviceorder->sku['ZZSUBBR'];
	$category = @$serviceorder->sku['ZZCATEG'];
	$packsize = @$serviceorder->salesOrderCharacteristics['ZLP_PACK_SIZE'];
	$packtype = @$serviceorder->salesOrderCharacteristics['ZLP_PACK_TYPE'];
	$printer = @$serviceorder->partners['ZN']['NAME_LIST'];
	$brandowner = @$serviceorder->partners['ZO']['NAME_LIST'];
	$agency = @$serviceorder->partners['ZR']['NAME_LIST'];
	$supplier = @$serviceorder->partners['ZS']['NAME_LIST'];
	$description = @$serviceorder->serviceOrderHead['SHORT_TEXT'];

	$barcode1 = @$serviceorder->barcodes[0]['ZLP_BC_CODE_NUMBER'];
	$barcode2 = @$serviceorder->barcodes[1]['ZLP_BC_CODE_NUMBER'];
	$barcode3 = @$serviceorder->barcodes[2]['ZLP_BC_CODE_NUMBER'];
	$barcode4 = @$serviceorder->barcodes[3]['ZLP_BC_CODE_NUMBER'];
	$barcode5 = @$serviceorder->barcodes[4]['ZLP_BC_CODE_NUMBER'];
	$barcode6 = @$serviceorder->barcodes[5]['ZLP_BC_CODE_NUMBER'];

	$field1_label = @$serviceorder->customerFields['ZLP_CUST_SPEC_FIELD_01']['label'];
	$field1_value = @$serviceorder->customerFields['ZLP_CUST_SPEC_FIELD_01']['value'];
	$field2_label = @$serviceorder->customerFields['ZLP_CUST_SPEC_FIELD_02']['label'];
	$field2_value = @$serviceorder->customerFields['ZLP_CUST_SPEC_FIELD_02']['value'];
	$field3_label = @$serviceorder->customerFields['ZLP_CUST_SPEC_FIELD_03']['label'];
	$field3_value = @$serviceorder->customerFields['ZLP_CUST_SPEC_FIELD_03']['value'];
	$field4_label = @$serviceorder->customerFields['ZLP_CUST_SPEC_FIELD_04']['label'];
	$field4_value = @$serviceorder->customerFields['ZLP_CUST_SPEC_FIELD_04']['value'];
	$field5_label = @$serviceorder->customerFields['ZLP_CUST_SPEC_FIELD_05']['label'];
	$field5_value = @$serviceorder->customerFields['ZLP_CUST_SPEC_FIELD_05']['value'];

	$core->execute();
	$barcodes->execute();
	$customerfields->execute();
}