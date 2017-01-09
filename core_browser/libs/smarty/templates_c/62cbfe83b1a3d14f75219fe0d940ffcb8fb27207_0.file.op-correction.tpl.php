<?php
/* Smarty version 3.1.29, created on 2016-12-06 23:20:08
  from "/var/www/html/core/templates/op-correction.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_584747a8465094_05788608',
  'file_dependency' => 
  array (
    '62cbfe83b1a3d14f75219fe0d940ffcb8fb27207' => 
    array (
      0 => '/var/www/html/core/templates/op-correction.tpl',
      1 => 1481055697,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_584747a8465094_05788608 ($_smarty_tpl) {
if (!is_callable('smarty_modifier_date_format')) require_once '/var/www/html/core//libs/smarty/plugins/modifier.date_format.php';
?>
<!-- correction -->
<div class="row">
	<div class="col-sm-6">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('CORRECTION_ON');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['operation']->value['USR00'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('CORRECTION_PER');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['operation']->value['USR02'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('CORRECTION_DATE');?>
</strong></td>
					<td><?php echo smarty_modifier_date_format(strtotime($_smarty_tpl->tpl_vars['operation']->value['USR08']),@constant('DATE_FORMAT'));?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('CORRECTION_NO');?>
</strong></td>
					<td><?php echo intval($_smarty_tpl->tpl_vars['operation']->value['USR04']);?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<div class="col-sm-6">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('CORRECTION_FROM');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['operation']->value['USR01'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('CORRECTION_COMPLETED');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['operation']->value['USR10'];?>
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
