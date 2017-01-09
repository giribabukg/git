<?php
/* Smarty version 3.1.29, created on 2017-01-09 08:33:02
  from "/Applications/MAMP/htdocs/git/core_browser/templates/op-genspec_flexibles.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_58734abec4bca7_49497471',
  'file_dependency' => 
  array (
    '4f343a592569f33b63f0afbeb486acd3f57230f5' => 
    array (
      0 => '/Applications/MAMP/htdocs/git/core_browser/templates/op-genspec_flexibles.tpl',
      1 => 1483681729,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_58734abec4bca7_49497471 ($_smarty_tpl) {
?>
<!-- genspec flexibles -->
<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="2"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('PRINTING_MATERIAL');?>
</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_SUBSTRATE');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_AG_F_SUBSTRATE'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_C_SURFACE');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_AG_C_SURFACE'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_PRINT_METHOD');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['text']->value->__($_smarty_tpl->tpl_vars['values']->value['ZLP_AG_F_PRINT_METHOD']);?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_CYLINDER');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_AG_F_CYLINDER'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_REPEAT_LENGTH');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_AG_F_REPEAT_LENGTH'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_CAD_F_MATERIAL_WIDTH');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_CAD_F_MATERIAL_WIDTH'];?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="2"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('COLOURS');?>
</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_ANZF_VS');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_ANZF_VS'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_MACHINE_COLORS');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_AG_F_MACHINE_COLORS'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_REP_F_MAX_INK_COVERAGE');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_REP_F_MAX_INK_COVERAGE'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_ANGLE_TYPE');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_AG_F_ANGLE_TYPE'];?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="2"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('CUSTOMER_SPECIFIC_FIELDS');?>
</th>
				</tr>
			</thead>
			<tbody>
				<?php
$_from = $_smarty_tpl->tpl_vars['customerFields']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_customerField_0_saved_item = isset($_smarty_tpl->tpl_vars['customerField']) ? $_smarty_tpl->tpl_vars['customerField'] : false;
$_smarty_tpl->tpl_vars['customerField'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['customerField']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['customerField']->value) {
$_smarty_tpl->tpl_vars['customerField']->_loop = true;
$__foreach_customerField_0_saved_local_item = $_smarty_tpl->tpl_vars['customerField'];
?>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['customerField']->value['label'];?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['customerField']->value['value'];?>
</td>
				</tr>
				<?php
$_smarty_tpl->tpl_vars['customerField'] = $__foreach_customerField_0_saved_local_item;
}
if ($__foreach_customerField_0_saved_item) {
$_smarty_tpl->tpl_vars['customerField'] = $__foreach_customerField_0_saved_item;
}
?>
			</tbody>
		</table>
	</div>
</div> <!-- End row -->
<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_NOTES');?>
</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo nl2br($_smarty_tpl->tpl_vars['values']->value['ZLP_AG_NOTES']);?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_CAD_F_DIE_CUT_NAME');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_CAD_F_DIE_CUT_NAME'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_CAD_F_DIE_CUT_NO');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_CAD_F_DIE_CUT_NO'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_BUSINESS_CATEGORY');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_AG_BUSINESS_CATEGORY'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_WEBCENTER_ID');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_WEBCENTER_ID'];?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BB_COL_PROFILE');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_BB_COL_PROFILE'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_DO_DGC');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_DO_DGC'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_REF_JOB');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_AG_REF_JOB'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BB_VERSION');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_BB_VERSION'];?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
</div> <!-- End row --><?php }
}
