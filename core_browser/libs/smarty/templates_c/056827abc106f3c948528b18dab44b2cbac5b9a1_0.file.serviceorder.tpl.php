<?php
/* Smarty version 3.1.29, created on 2016-12-06 20:22:31
  from "/var/www/html/core/templates/serviceorder.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_58471e073b47d5_44297191',
  'file_dependency' => 
  array (
    '056827abc106f3c948528b18dab44b2cbac5b9a1' => 
    array (
      0 => '/var/www/html/core/templates/serviceorder.tpl',
      1 => 1481055697,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:color_rotation.tpl' => 1,
    'file:barcodes.tpl' => 1,
    'file:op-genspec_corrugated.tpl' => 1,
    'file:op-genspec_flexibles.tpl' => 1,
    'file:op-diecut_corrugated.tpl' => 1,
    'file:op-diecut_flexibles.tpl' => 1,
    'file:op-data_input.tpl' => 1,
    'file:op-assembly_corrugated.tpl' => 1,
    'file:op-assembly_flexibles.tpl' => 1,
    'file:op-steprepeat_corrugated.tpl' => 1,
    'file:op-barcode1.tpl' => 1,
    'file:op-barcode2.tpl' => 1,
    'file:op-barcode3.tpl' => 1,
    'file:op-barcode4.tpl' => 1,
    'file:op-barcode5.tpl' => 1,
    'file:op-barcode6.tpl' => 1,
    'file:op-colour_retouching.tpl' => 1,
    'file:op-data_delivery.tpl' => 1,
    'file:op-data_output.tpl' => 1,
    'file:op-flexo_plate.tpl' => 1,
    'file:op-offset_plate.tpl' => 1,
    'file:op-proof1.tpl' => 1,
    'file:op-proof2.tpl' => 1,
    'file:op-proof3.tpl' => 1,
    'file:op-proof4.tpl' => 1,
    'file:op-proof5.tpl' => 1,
    'file:op-proof6.tpl' => 1,
    'file:op-mounting.tpl' => 1,
    'file:op-steprepeat_flexibles.tpl' => 1,
    'file:op-gravure_cylinder.tpl' => 1,
    'file:op-correction.tpl' => 1,
    'file:op-approval.tpl' => 1,
    'file:op-typesetting.tpl' => 1,
    'file:op-production_art.tpl' => 1,
    'file:op-artistic_retouching.tpl' => 1,
    'file:op-creative.tpl' => 1,
    'file:op-photography.tpl' => 1,
    'file:op-mockup.tpl' => 1,
    'file:op-technical_services.tpl' => 1,
    'file:op-components.tpl' => 1,
  ),
),false)) {
function content_58471e073b47d5_44297191 ($_smarty_tpl) {
if (!is_callable('smarty_modifier_regex_replace')) require_once '/var/www/html/core//libs/smarty/plugins/modifier.regex_replace.php';
?>
	<?php if ($_smarty_tpl->tpl_vars['errorMsg']->value != '') {?>
	<div class="page-header alert alert-danger">
		<h4><span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['text']->value->__('ERROR_OCCURRED');?>
:</h4>
		<p><?php echo $_smarty_tpl->tpl_vars['errorMsg']->value;?>
 (#<?php echo $_smarty_tpl->tpl_vars['errorCode']->value;?>
)</p>
	</div>
	<?php } else { ?>
	<div class="page-header"> <div class="row"> <div class="col-xs-8 col-sm-8"> <h3><?php echo $_smarty_tpl->tpl_vars['serviceOrderHead']->value['SHORT_TEXT'];?>
</h3> <h4><?php echo $_smarty_tpl->tpl_vars['serviceOrderName']->value;?>
</h4> <h5><?php echo $_smarty_tpl->tpl_vars['salesOrderHead']->value['PURCH_NO'];
if ($_smarty_tpl->tpl_vars['salesOrderHead']->value['PURCH_NO'] != '' && $_smarty_tpl->tpl_vars['salesOrderHead']->value['po_date'] != '') {?> - <?php }
echo $_smarty_tpl->tpl_vars['salesOrderHead']->value['po_date'];?>
</h5> </div> <div class="col-xs-4 col-sm-4"> <h3 class="text-right"><?php echo intval($_smarty_tpl->tpl_vars['serviceOrderHead']->value['ORDERID']);?>
<br/><img src="data:image/png;base64,<?php echo $_smarty_tpl->tpl_vars['serviceOrderBarcode']->value;?>
" /></h3> <h5 class="text-right"><?php echo $_smarty_tpl->tpl_vars['text']->value->__($_smarty_tpl->tpl_vars['salesOrderItem']->value['USAGE']);
if ($_smarty_tpl->tpl_vars['values']->value['ZLP_AG_REF_JOB'] != '') {?> (<?php echo trim($_smarty_tpl->tpl_vars['values']->value['ZLP_AG_REF_JOB']);?>
)<?php }?></h5><?php if ($_smarty_tpl->tpl_vars['values']->value['ZLP_ACCOUNTING_TXT'] != '') {?><h5 class="text-right"><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_ACCOUNTING_TXT'];?>
</h5><?php }?></div> </div> </div> <div id="orderhead" class="row"> <div class="col-sm-4"> <table class="table table-striped table-hover table-condensed"> <thead> <tr> <th colspan="2" class="header"><span class="fa fa-info-circle"></span>&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['text']->value->__('ORDER_DETAILS');?>
</th> </tr> </thead> <tbody> <tr> <td class="col-xs-5 col-sm-6 col-md-4"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('SALES_ORDER');?>
</strong></td> <td class="col-xs-7 col-sm-6 col-md-8"><?php echo intval($_smarty_tpl->tpl_vars['serviceOrderHead']->value['SALES_ORD']);?>
<img src="data:image/png;base64,<?php echo $_smarty_tpl->tpl_vars['salesOrderBarcode']->value;?>
" class="pull-right" /></td> </tr> <tr> <td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('SKU_MATERIAL_NUMBER');?>
</strong></td> <td><?php echo $_smarty_tpl->tpl_vars['sku']->value['MATNR'];?>
</td> </tr> <tr> <td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('CUSTOMER_MATERIAL_NUMBER');?>
</strong></td> <td><?php echo $_smarty_tpl->tpl_vars['custmatinfo']->value['KDMAT'];?>
</td> </tr> <tr> <td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('CUSTOMER_DESCRIPTION');?>
</strong></td> <td><?php echo $_smarty_tpl->tpl_vars['custmatinfo']->value['POSTX'];?>
</td> </tr> <tr> <td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('PRINT_METHOD');?>
</strong></td> <td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_PRINT_METHOD'];?>
</td> </tr> <tr> <td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('PRINT_MACHINE');?>
</strong></td> <td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MTH_PRINTER_SPEC_NAME'];?>
</td> </tr> <tr> <td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('DUE_DATE');?>
</strong></td> <td><?php echo $_smarty_tpl->tpl_vars['serviceOrderHead']->value['due_date'];?>
</td> </tr> </tbody> </table> </div> <div class="col-sm-4"> <table class="table table-striped table-hover table-condensed"> <thead> <tr> <th colspan="2" class="header"><span class="glyphicon glyphicon-phone-alt"></span>&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['text']->value->__('CONTACTS');?>
</th> </tr> </thead> <tbody> <tr> <td class="col-xs-5 col-sm-6 col-md-4"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('CONTACTS_AG');?>
</strong></td> <td class="col-xs-7 col-sm-6 col-md-8"><?php if ($_smarty_tpl->tpl_vars['contacts']->value['AG']['NAME_LIST'] != '') {
echo $_smarty_tpl->tpl_vars['contacts']->value['AG']['NAME_LIST'];
} else {
echo $_smarty_tpl->tpl_vars['contacts']->value['AG']['NAME'];
}
if ($_smarty_tpl->tpl_vars['contacts']->value['AG']['ADRESSDATA'] != '') {?><br /><small><?php echo smarty_modifier_regex_replace($_smarty_tpl->tpl_vars['contacts']->value['AG']['ADRESSDATA'],"/\s+/"," ");?>
</small><?php }?></td> </tr> <tr> <td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('CONTACTS_AP');?>
</strong></td> <td><?php if ($_smarty_tpl->tpl_vars['contacts']->value['AP']['NAME_LIST'] != '') {
echo $_smarty_tpl->tpl_vars['contacts']->value['AP']['NAME_LIST'];
} else {
echo $_smarty_tpl->tpl_vars['contacts']->value['AP']['NAME'];
}?></td> </tr> <tr> <td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('CONTACTS_WE');?>
</strong></td> <td><?php if ($_smarty_tpl->tpl_vars['contacts']->value['WE']['NAME_LIST'] != '') {
echo $_smarty_tpl->tpl_vars['contacts']->value['WE']['NAME_LIST'];
} else {
echo $_smarty_tpl->tpl_vars['contacts']->value['WE']['NAME'];
}
if ($_smarty_tpl->tpl_vars['contacts']->value['WE']['ADRESSDATA'] != '') {?><br /><small><?php echo smarty_modifier_regex_replace($_smarty_tpl->tpl_vars['contacts']->value['WE']['ADRESSDATA'],"/\s+/"," ");?>
</small><?php }?></td> </tr> <tr> <td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('CONTACTS_ZM');?>
</strong></td> <td><?php if ($_smarty_tpl->tpl_vars['contacts']->value['ZM']['NAME_LIST'] != '') {
echo $_smarty_tpl->tpl_vars['contacts']->value['ZM']['NAME_LIST'];
} else {
echo $_smarty_tpl->tpl_vars['contacts']->value['ZM']['NAME'];
}?></td> </tr> <tr> <td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('CONTACTS_VE');?>
</strong></td> <td><?php if ($_smarty_tpl->tpl_vars['contacts']->value['VE']['NAME_LIST'] != '') {
echo $_smarty_tpl->tpl_vars['contacts']->value['VE']['NAME_LIST'];
} else {
echo $_smarty_tpl->tpl_vars['contacts']->value['VE']['NAME'];
}?></td> </tr> </tbody> </table> </div> <div class="col-sm-4"> <table class="table table-striped table-hover table-condensed"> <thead> <tr> <th colspan="2" class="header"><span class="fa fa-user"></span>&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['text']->value->__('PARTNERS');?>
</th> </tr> </thead> <tbody> <tr> <td class="col-xs-5 col-sm-6 col-md-4"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('PARTNERS_ZN');?>
</strong></td> <td class="col-xs-7 col-sm-6 col-md-8"><?php if ($_smarty_tpl->tpl_vars['partners']->value['ZN']['NAME_LIST'] != '') {
echo $_smarty_tpl->tpl_vars['partners']->value['ZN']['NAME_LIST'];
} else {
echo $_smarty_tpl->tpl_vars['partners']->value['ZN']['NAME'];
}
if ($_smarty_tpl->tpl_vars['partners']->value['ZN']['ADRESSDATA'] != '') {?><br /><small><?php echo smarty_modifier_regex_replace($_smarty_tpl->tpl_vars['partners']->value['ZN']['ADRESSDATA'],"/\s+/"," ");?>
</small><?php }?></td> </tr> <tr> <td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('PARTNERS_ZO');?>
</strong></td> <td><?php if ($_smarty_tpl->tpl_vars['partners']->value['ZO']['NAME_LIST'] != '') {
echo $_smarty_tpl->tpl_vars['partners']->value['ZO']['NAME_LIST'];
} else {
echo $_smarty_tpl->tpl_vars['partners']->value['ZO']['NAME'];
}
if ($_smarty_tpl->tpl_vars['partners']->value['ZN']['ADRESSDATA'] != '') {?><br /><small><?php echo smarty_modifier_regex_replace($_smarty_tpl->tpl_vars['partners']->value['ZO']['ADRESSDATA'],"/\s+/"," ");?>
</small><?php }?></td> </tr> <tr> <td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('PARTNERS_ZR');?>
</strong></td> <td><?php if ($_smarty_tpl->tpl_vars['partners']->value['ZR']['NAME_LIST'] != '') {
echo $_smarty_tpl->tpl_vars['partners']->value['ZR']['NAME_LIST'];
} else {
echo $_smarty_tpl->tpl_vars['partners']->value['ZR']['NAME'];
}
if ($_smarty_tpl->tpl_vars['partners']->value['ZN']['ADRESSDATA'] != '') {?><br /><small><?php echo smarty_modifier_regex_replace($_smarty_tpl->tpl_vars['partners']->value['ZR']['ADRESSDATA'],"/\s+/"," ");?>
</small><?php }?></td> </tr> <tr> <td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('PARTNERS_ZS');?>
</strong></td> <td><?php if ($_smarty_tpl->tpl_vars['partners']->value['ZS']['NAME_LIST'] != '') {
echo $_smarty_tpl->tpl_vars['partners']->value['ZS']['NAME_LIST'];
} else {
echo $_smarty_tpl->tpl_vars['partners']->value['ZS']['NAME'];
}
if ($_smarty_tpl->tpl_vars['partners']->value['ZS']['ADRESSDATA'] != '') {?><br /><small><?php echo smarty_modifier_regex_replace($_smarty_tpl->tpl_vars['partners']->value['ZS']['ADRESSDATA'],"/\s+/"," ");?>
</small><?php }?></td> </tr> </tbody> </table> </div> </div><?php if ($_smarty_tpl->tpl_vars['colorCount']->value > 0) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:color_rotation.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
if ($_smarty_tpl->tpl_vars['barcodeCount']->value > 0) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:barcodes.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}?><div class="row"> <div class="col-xs-6 col-sm-8"><h4 class="section" id="operationHeader"><span class="fa fa-tasks"></span>&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['text']->value->__('OPERATIONS');?>
</h4></div> <div class="col-xs-6 col-sm-4 text-right"><h6><a href="#" class="toggleCnf"><span class="toggleCnfIcon glyphicon glyphicon-chevron-right"></span>&nbsp;<span class="toggleCnfCount"></span><?php echo $_smarty_tpl->tpl_vars['text']->value->__('CONFIRMED_OPERATIONS');?>
</a></h6></div> </div> <div class="row"> <div class="col-sm-12"> <div class="panel-group" id="accordion"><?php
$_from = $_smarty_tpl->tpl_vars['operations']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_operation_0_saved_item = isset($_smarty_tpl->tpl_vars['operation']) ? $_smarty_tpl->tpl_vars['operation'] : false;
$__foreach_operation_0_saved_key = isset($_smarty_tpl->tpl_vars['opIdx']) ? $_smarty_tpl->tpl_vars['opIdx'] : false;
$_smarty_tpl->tpl_vars['operation'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['opIdx'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['operation']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['opIdx']->value => $_smarty_tpl->tpl_vars['operation']->value) {
$_smarty_tpl->tpl_vars['operation']->_loop = true;
$__foreach_operation_0_saved_local_item = $_smarty_tpl->tpl_vars['operation'];
if (preg_match('/(^|\b)CNF/',$_smarty_tpl->tpl_vars['operation']->value['SYSTEM_STATUS_TEXT']) && ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] != '10001' && $_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] != '10002')) {
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 'CORRECT' || $_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 'APPROVE') {
$_smarty_tpl->tpl_vars["operationClass"] = new Smarty_Variable("hidden-print", null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, "operationClass", 0);
} else {
$_smarty_tpl->tpl_vars["operationClass"] = new Smarty_Variable("text-muted", null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, "operationClass", 0);
}
} else {
$_smarty_tpl->tpl_vars["operationClass"] = new Smarty_Variable('', null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, "operationClass", 0);
}?><div class="panel panel-default <?php echo $_smarty_tpl->tpl_vars['operationClass']->value;?>
"> <div class="panel-heading row"> <div class="col-sm-5"> <h4 class="panel-title"> <span class="panelhandle glyphicon glyphicon-chevron-right <?php echo $_smarty_tpl->tpl_vars['operationClass']->value;?>
"></span> <a data-toggle="collapse" href="#<?php echo md5(($_smarty_tpl->tpl_vars['opIdx']->value).('phase'));?>
" class="handle"> <span class="<?php echo $_smarty_tpl->tpl_vars['operationClass']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['operation']->value['ACTIVITY'];?>
 <?php echo $_smarty_tpl->tpl_vars['operation']->value['DESCRIPTION'];?>
</span> </a> </h4> </div> <div class="col-sm-2"><small class="text-muted"><?php echo $_smarty_tpl->tpl_vars['operation']->value['WORK_CNTR'];?>
 (<?php echo $_smarty_tpl->tpl_vars['operation']->value['PLANT'];?>
)</small></div> <div class="col-sm-1"><small class="text-muted"><?php echo $_smarty_tpl->tpl_vars['operation']->value['PERS_NO'];?>
</small></div> <div class="col-sm-2"><small class="text-muted"><?php if ($_smarty_tpl->tpl_vars['operation']->value['PERS_NAME'] != ',') {
echo $_smarty_tpl->tpl_vars['operation']->value['PERS_NAME'];
}?></small></div> <div class="col-sm-2"><small class="text-muted"><?php echo $_smarty_tpl->tpl_vars['operation']->value['end_ts'];?>
</small></div> </div> <div id="<?php echo md5(($_smarty_tpl->tpl_vars['opIdx']->value).('phase'));?>
" class="panel-collapse collapse"> <div class="panel-body"><?php if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 10001) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-genspec_corrugated.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 10002) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-genspec_flexibles.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 10201) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-diecut_corrugated.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 10202) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-diecut_flexibles.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 10100) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-data_input.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 30401) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-assembly_corrugated.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 30402) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-assembly_flexibles.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 30601) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-steprepeat_corrugated.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 30501) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-barcode1.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 30502) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-barcode2.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 30503) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-barcode3.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 30504) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-barcode4.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 30505) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-barcode5.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 30506) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-barcode6.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 30300) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-colour_retouching.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 50100) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-data_delivery.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 50110) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-data_output.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 50300) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-flexo_plate.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 50200) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-offset_plate.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 40101) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-proof1.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 40102) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-proof2.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 40103) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-proof3.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 40104) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-proof4.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 40105) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-proof5.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 40106) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-proof6.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 50500) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-mounting.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 30602) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-steprepeat_flexibles.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 50400) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-gravure_cylinder.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 'CORRECT') {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-correction.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 'APPROVE') {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-approval.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 10101) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-typesetting.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 30100) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-production_art.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 30200) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-artistic_retouching.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 20100) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-creative.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 20200) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-photography.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 60100) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-mockup.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['operation']->value['STANDARD_TEXT_KEY'] == 70100) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-technical_services.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
if ($_smarty_tpl->tpl_vars['components']->value[$_smarty_tpl->tpl_vars['operation']->value['ACTIVITY']]) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:op-components.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}?></div> </div> </div><?php
$_smarty_tpl->tpl_vars['operation'] = $__foreach_operation_0_saved_local_item;
}
if ($__foreach_operation_0_saved_item) {
$_smarty_tpl->tpl_vars['operation'] = $__foreach_operation_0_saved_item;
}
if ($__foreach_operation_0_saved_key) {
$_smarty_tpl->tpl_vars['opIdx'] = $__foreach_operation_0_saved_key;
}
?></div> </div> </div> <!-- End row -->
	<?php }
}
}
