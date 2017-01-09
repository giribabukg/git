<?php
/* Smarty version 3.1.29, created on 2016-10-12 15:34:55
  from "/var/www/html/core/templates/op-mockup.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_57fe581f8efaa2_46717818',
  'file_dependency' => 
  array (
    '68195cd6ba79de8d233a65bc85436e2da56664d8' => 
    array (
      0 => '/var/www/html/core/templates/op-mockup.tpl',
      1 => 1476285664,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_57fe581f8efaa2_46717818 ($_smarty_tpl) {
?>
<!-- mockup -->
<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MC_AMOUNT');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MC_AMOUNT'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MC_PURCHASING');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MC_PURCHASING'];?>
</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="col-sm-8">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MC_NOTES');?>
</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo nl2br($_smarty_tpl->tpl_vars['values']->value['ZLP_MC_NOTES']);?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
</div><?php }
}
