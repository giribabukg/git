<?php
/* Smarty version 3.1.29, created on 2016-10-12 15:34:55
  from "/var/www/html/core/templates/op-barcode5.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_57fe581f5ccf08_96144537',
  'file_dependency' => 
  array (
    'f37cc8afc7e5f71b725b3f24aae397070d5b9647' => 
    array (
      0 => '/var/www/html/core/templates/op-barcode5.tpl',
      1 => 1470329229,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_57fe581f5ccf08_96144537 ($_smarty_tpl) {
?>
<!-- barcode 5 -->
<div class="row">
	<div class="col-sm-6">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BC_CODE_TYPE');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['barcodes']->value[4]['ZLP_BC_CODE_TYPE'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BC_CODE_NUMBER');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['barcodes']->value[4]['ZLP_BC_CODE_NUMBER'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BC_RESOLUTION');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['barcodes']->value[4]['ZLP_BC_RESOLUTION'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BC_BWR');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['barcodes']->value[4]['ZLP_BC_BWR'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BC_MAGNIFICATION');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['barcodes']->value[4]['ZLP_BC_MAGNIFICATION'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BC_NARROW_BAR');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['barcodes']->value[4]['ZLP_BC_NARROW_BAR'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BC_RATIO');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['barcodes']->value[4]['ZLP_BC_RATIO'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BC_DEVICE_COMPENSATION');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['barcodes']->value[4]['ZLP_BC_DEVICE_COMPENSATION'];?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-6">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BC_SUB_TYPE');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['barcodes']->value[4]['ZLP_BC_SUB_TYPE'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BC_CODE_COLOR');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['barcodes']->value[4]['ZLP_BC_CODE_COLOR'];?>
</td>
				</tr>
				
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BC_BCKGRND_COLOR');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['barcodes']->value[4]['ZLP_BC_BCKGRND_COLOR'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BC_TEST_PROTOCOL');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['barcodes']->value[4]['ZLP_BC_TEST_PROTOCOL'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BC_CODE_SIZE');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['barcodes']->value[4]['ZLP_BC_CODE_SIZE'];?>
</td>
				</tr>
				<tr>
					<td colspan="2"><?php echo nl2br($_smarty_tpl->tpl_vars['barcodes']->value[4]['ZLP_BC_MEMO_TXT']);?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
</div> <!-- End row --><?php }
}
