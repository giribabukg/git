<?php
/* Smarty version 3.1.29, created on 2016-10-12 15:34:55
  from "/var/www/html/core/templates/barcodes.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_57fe581f3ad3d8_34259826',
  'file_dependency' => 
  array (
    '176a2e3579667f61be479ea86d98dc835b004543' => 
    array (
      0 => '/var/www/html/core/templates/barcodes.tpl',
      1 => 1470329222,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_57fe581f3ad3d8_34259826 ($_smarty_tpl) {
?>
<div id="barcodes" class="row">
	<div class="col-sm-12">
			<table class="table table-striped table-hover table-condensed">
				<thead>
					<tr>
						<th colspan="12" class="header"><span class="fa fa-barcode"></span>&nbsp;&nbsp;Barcodes</th>
					</tr>
					<tr>
						<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BC_CODE_TYPE');?>
</th>
						<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BC_CODE_NUMBER');?>
</th>
						<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BC_RESOLUTION');?>
</th>
						<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BC_BWR');?>
</th>
						<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BC_MAGNIFICATION');?>
</th>
						<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BC_NARROW_BAR');?>
</th>
						<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BC_RATIO');?>
</th>
						<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BC_DEVICE_COMPENSATION');?>
</th>
						<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BC_CODE_COLOR');?>
</th>
						<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BC_BCKGRND_COLOR');?>
</th>
						<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BC_SUB_TYPE');?>
</th>
						<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BC_TEST_PROTOCOL');?>
</th>
					</tr>
				</thead>
				<tbody>
					<?php
$_from = $_smarty_tpl->tpl_vars['barcodes']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_barcode_0_saved_item = isset($_smarty_tpl->tpl_vars['barcode']) ? $_smarty_tpl->tpl_vars['barcode'] : false;
$__foreach_barcode_0_saved_key = isset($_smarty_tpl->tpl_vars['key']) ? $_smarty_tpl->tpl_vars['key'] : false;
$_smarty_tpl->tpl_vars['barcode'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['key'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['barcode']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['barcode']->value) {
$_smarty_tpl->tpl_vars['barcode']->_loop = true;
$__foreach_barcode_0_saved_local_item = $_smarty_tpl->tpl_vars['barcode'];
?>
						<?php if ($_smarty_tpl->tpl_vars['barcode']->value['ZLP_BC_CODE_TYPE'] != '') {?>
						<tr>
							<td><?php echo $_smarty_tpl->tpl_vars['barcode']->value['ZLP_BC_CODE_TYPE'];?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['barcode']->value['ZLP_BC_CODE_NUMBER'];?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['barcode']->value['ZLP_BC_RESOLUTION'];?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['barcode']->value['ZLP_BC_BWR'];?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['barcode']->value['ZLP_BC_MAGNIFICATION'];?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['barcode']->value['ZLP_BC_NARROW_BAR'];?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['barcode']->value['ZLP_BC_RATIO'];?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['barcode']->value['ZLP_BC_DEVICE_COMPENSATION'];?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['barcode']->value['ZLP_BC_CODE_COLOR'];?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['barcode']->value['ZLP_BC_BCKGRND_COLOR'];?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['barcode']->value['ZLP_BC_SUB_TYPE'];?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['barcode']->value['ZLP_BC_TEST_PROTOCOL'];?>
</td>
						</tr>
						<?php }?>
					<?php
$_smarty_tpl->tpl_vars['barcode'] = $__foreach_barcode_0_saved_local_item;
}
if ($__foreach_barcode_0_saved_item) {
$_smarty_tpl->tpl_vars['barcode'] = $__foreach_barcode_0_saved_item;
}
if ($__foreach_barcode_0_saved_key) {
$_smarty_tpl->tpl_vars['key'] = $__foreach_barcode_0_saved_key;
}
?>
				</tbody>
			</table>
	</div>
</div><?php }
}
