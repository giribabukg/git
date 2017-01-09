<?php
/* Smarty version 3.1.29, created on 2016-10-12 15:34:55
  from "/var/www/html/core/templates/op-proof6.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_57fe581f7b4b91_17074022',
  'file_dependency' => 
  array (
    '112e4ec4521b6af518e31a5d1783a79bb7367420' => 
    array (
      0 => '/var/www/html/core/templates/op-proof6.tpl',
      1 => 1470329235,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_57fe581f7b4b91_17074022 ($_smarty_tpl) {
?>
<!-- proof 5 -->
<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_PROOF_PROCESS_11');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_PROOF_PROCESS_16'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_PROOF_NUMBER_11');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_PROOF_NUMBER_16'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_PROOF_SIZE_11');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_PROOF_SIZE_16'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_PROOF_1UP_11');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_PROOF_1UP_16'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_PROOF_LEGEND_11');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_PROOF_LEGEND_16'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BB_COL_PROFILE');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_BB_COL_PROFILE'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_PROOF_PROFILE_NAME_11');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_PROOF_PROFILE_NAME_16'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_SUBSTRATE');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_AG_F_SUBSTRATE'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_C_SURFACE');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_AG_C_SURFACE'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_PROOF_PROFILE_SUBSTRATE_11');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_PROOF_SUBSTRATE_16'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_PROOF_SHIPPING_11');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_PROOF_SHIPPING_16'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_PROOF_PROD_PLANT_11');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_PROOF_PROD_PLANT_16'];?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_PROOF_SHIP_ADDRESS_11');?>
</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo nl2br($_smarty_tpl->tpl_vars['values']->value['ZLP_PROOF_SHIP_ADDRESS_16']);?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_PROOF_NOTES_11');?>
</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo nl2br($_smarty_tpl->tpl_vars['values']->value['ZLP_PROOF_NOTES_16']);?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
</div><?php }
}
