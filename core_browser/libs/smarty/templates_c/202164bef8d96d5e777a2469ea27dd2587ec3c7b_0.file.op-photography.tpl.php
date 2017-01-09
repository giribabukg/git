<?php
/* Smarty version 3.1.29, created on 2016-10-12 15:34:55
  from "/var/www/html/core/templates/op-photography.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_57fe581f8e4a08_17721220',
  'file_dependency' => 
  array (
    '202164bef8d96d5e777a2469ea27dd2587ec3c7b' => 
    array (
      0 => '/var/www/html/core/templates/op-photography.tpl',
      1 => 1470329234,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_57fe581f8e4a08_17721220 ($_smarty_tpl) {
?>
<!-- photography -->
<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_PH_CONCEPT');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_PH_CONCEPT'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_PH_PICTURES');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_PH_PICTURES'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_PH_BRIEFING');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_PH_BRIEFING'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_PH_REFJOB');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_PH_REFJOB'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_PH_DIRECTOR');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_PH_DIRECTOR'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_PH_ELEMENTS');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_PH_ELEMENTS'];?>
</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="col-sm-8">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_PH_NOTES');?>
</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo nl2br($_smarty_tpl->tpl_vars['values']->value['ZLP_PH_NOTES']);?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
</div><?php }
}
