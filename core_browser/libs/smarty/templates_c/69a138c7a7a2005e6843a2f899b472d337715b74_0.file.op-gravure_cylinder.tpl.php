<?php
/* Smarty version 3.1.29, created on 2016-10-12 15:34:55
  from "/var/www/html/core/templates/op-gravure_cylinder.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_57fe581f8bd491_70221043',
  'file_dependency' => 
  array (
    '69a138c7a7a2005e6843a2f899b472d337715b74' => 
    array (
      0 => '/var/www/html/core/templates/op-gravure_cylinder.tpl',
      1 => 1476219003,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_57fe581f8bd491_70221043 ($_smarty_tpl) {
if (!is_callable('smarty_modifier_date_format')) require_once '/var/www/html/core//libs/smarty/plugins/modifier.date_format.php';
?>
<!-- gravure cylinder -->
<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_COLOR_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_GC_USAGE_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_GC_PROCESS_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_CLICHE_NUMBER_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_NEW_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_GC_SCREEN_DEPTH_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_REMARK_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_GC_NOMINALSIZE_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_GC_CODE_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_GC_PROOF_01');?>
</th>
				</tr>
			</thead>
			<?php if ($_smarty_tpl->tpl_vars['colorCount']->value > 0) {?>
			<tbody>
				<?php
$_smarty_tpl->tpl_vars['cid'] = new Smarty_Variable;$_smarty_tpl->tpl_vars['cid']->step = 1;$_smarty_tpl->tpl_vars['cid']->total = (int) ceil(($_smarty_tpl->tpl_vars['cid']->step > 0 ? $_smarty_tpl->tpl_vars['colorCount']->value+1 - (1) : 1-($_smarty_tpl->tpl_vars['colorCount']->value)+1)/abs($_smarty_tpl->tpl_vars['cid']->step));
if ($_smarty_tpl->tpl_vars['cid']->total > 0) {
for ($_smarty_tpl->tpl_vars['cid']->value = 1, $_smarty_tpl->tpl_vars['cid']->iteration = 1;$_smarty_tpl->tpl_vars['cid']->iteration <= $_smarty_tpl->tpl_vars['cid']->total;$_smarty_tpl->tpl_vars['cid']->value += $_smarty_tpl->tpl_vars['cid']->step, $_smarty_tpl->tpl_vars['cid']->iteration++) {
$_smarty_tpl->tpl_vars['cid']->first = $_smarty_tpl->tpl_vars['cid']->iteration == 1;$_smarty_tpl->tpl_vars['cid']->last = $_smarty_tpl->tpl_vars['cid']->iteration == $_smarty_tpl->tpl_vars['cid']->total;?>
				<?php ob_start();
echo str_pad($_smarty_tpl->tpl_vars['cid']->value,2,0,@constant('STR_PAD_LEFT'));
$_tmp5=ob_get_clean();
$_smarty_tpl->tpl_vars['id'] = new Smarty_Variable($_tmp5, null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, 'id', 0);?>
				<tr>
					<td class="swatch" style='background-color:#<?php echo $_smarty_tpl->tpl_vars['values']->value["ColorValue_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
;'><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_COLOR_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_GC_USAGE_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_GC_PROCESS_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_CLICHE_NUMBER_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_NEW_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_GC_SCREEN_DEPTH_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_REMARK_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_GC_NOMINALSIZE_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_GC_CODE_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_GC_PROOF_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
				</tr>
				<?php }
}
?>

			</tbody>
			<?php }?>
		</table>
	</div>
</div>
<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_GC_STAGGERING');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_GC_STAGGERING'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_GC_AMT_NEW');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_GC_AMT_NEW'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_GC_AMT_STOCK');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_GC_AMT_STOCK'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_GC_AMT_OLD');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_GC_AMT_OLD'];?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_GC_OLD_CYL');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_GC_OLD_CYL'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_GC_OLD_CYL_DATE');?>
</strong></td>
					<td><?php echo smarty_modifier_date_format(strtotime(floatval($_smarty_tpl->tpl_vars['values']->value['ZLP_GC_OLD_CYL_DATE'])),@constant('DATE_FORMAT'));?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_GC_MATERIAL');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_GC_MATERIAL'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_GC_MATERIAL_DATE');?>
</strong></td>
					<td><?php echo smarty_modifier_date_format(strtotime(floatval($_smarty_tpl->tpl_vars['values']->value['ZLP_GC_MATERIAL_DATE'])),@constant('DATE_FORMAT'));?>
</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_GC_NOTES');?>
</strong></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo nl2br($_smarty_tpl->tpl_vars['values']->value['ZLP_GC_NOTES']);?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
</div><?php }
}
