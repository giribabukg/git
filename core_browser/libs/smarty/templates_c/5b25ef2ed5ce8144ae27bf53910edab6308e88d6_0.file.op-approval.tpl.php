<?php
/* Smarty version 3.1.29, created on 2016-12-06 20:22:31
  from "/var/www/html/core/templates/op-approval.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_58471e073d47e0_93861758',
  'file_dependency' => 
  array (
    '5b25ef2ed5ce8144ae27bf53910edab6308e88d6' => 
    array (
      0 => '/var/www/html/core/templates/op-approval.tpl',
      1 => 1481055697,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_58471e073d47e0_93861758 ($_smarty_tpl) {
if (!is_callable('smarty_modifier_date_format')) require_once '/var/www/html/core//libs/smarty/plugins/modifier.date_format.php';
?>
<!-- correction -->
<div class="row">
	<div class="col-sm-6">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('APPROVAL_VIA');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['operation']->value['USR00'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('APPROVAL_TYPE');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['operation']->value['USR02'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('APPROVAL_DATE');?>
</strong></td>
					<td><?php echo smarty_modifier_date_format(strtotime($_smarty_tpl->tpl_vars['operation']->value['USR08']),@constant('DATE_FORMAT'));?>
</td>
				</tr>
				</tr>
			</tbody>
		</table>
	</div>
	
	<div class="col-sm-6">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('APPROVAL_BY');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['operation']->value['USR01'];?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<?php if ($_smarty_tpl->tpl_vars['values']->value['ZLP_CORR_APPR_MEMO'] != '') {?>
<div class="row">
	<div class="col-sm-12">
		<table class="table table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-12">
						<?php echo nl2br($_smarty_tpl->tpl_vars['values']->value['ZLP_CORR_APPR_MEMO']);?>

					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<?php }
}
}
