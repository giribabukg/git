<?php
/* Smarty version 3.1.29, created on 2016-10-12 15:34:55
  from "/var/www/html/core/templates/op-creative.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_57fe581f8d1448_50108981',
  'file_dependency' => 
  array (
    '064c05f6a64644864f8a6e976949d8aeac617db7' => 
    array (
      0 => '/var/www/html/core/templates/op-creative.tpl',
      1 => 1470329231,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_57fe581f8d1448_50108981 ($_smarty_tpl) {
if (!is_callable('smarty_modifier_date_format')) require_once '/var/www/html/core//libs/smarty/plugins/modifier.date_format.php';
?>
<!-- creative -->
<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_CR_DESIGNTYPE');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_CR_DESIGNTYPE'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_CR_PURCHASE');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_CR_PURCHASE'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_CR_DESIGNS');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_CR_DESIGNS'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_CR_BRIEFINGDATE');?>
</strong></td>
					<td><?php echo smarty_modifier_date_format(strtotime(floatval($_smarty_tpl->tpl_vars['values']->value['ZLP_CR_BRIEFINGDATE'])),@constant('DATE_FORMAT'));?>
</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="col-sm-8">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_CR_NOTES');?>
</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo nl2br($_smarty_tpl->tpl_vars['values']->value['ZLP_CR_NOTES']);?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
</div><?php }
}
