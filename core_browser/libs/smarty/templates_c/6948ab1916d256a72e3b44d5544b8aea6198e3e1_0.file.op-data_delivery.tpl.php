<?php
/* Smarty version 3.1.29, created on 2017-01-09 08:33:03
  from "/Applications/MAMP/htdocs/git/core_browser/templates/op-data_delivery.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_58734abf094866_27548471',
  'file_dependency' => 
  array (
    '6948ab1916d256a72e3b44d5544b8aea6198e3e1' => 
    array (
      0 => '/Applications/MAMP/htdocs/git/core_browser/templates/op-data_delivery.tpl',
      1 => 1483681728,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_58734abf094866_27548471 ($_smarty_tpl) {
?>
<!-- data delivery -->
<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_DD_REF_JOB_NR');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_DD_REF_JOB_NR'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_DD_DATA_SOURCE');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_DD_DATA_SOURCE'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_DD_FINAL_FILE');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_DD_FINAL_FILE'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_DD_FILE_TRANSFER');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_DD_FILE_TRANSFER'];?>
</td>
				</tr>
			</tbody>
		</table>

	</div>
	<div class="col-sm-4">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_DD_ADDRESS');?>
</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo nl2br($_smarty_tpl->tpl_vars['values']->value['ZLP_DD_ADDRESS']);?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
</div> <!-- End row --><?php }
}
