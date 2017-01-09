<?php
/* Smarty version 3.1.29, created on 2016-10-12 15:34:55
  from "/var/www/html/core/templates/op-data_output.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_57fe581f809844_31017445',
  'file_dependency' => 
  array (
    'c6d2e0aa3c60762e556198c4197a06adc1c28a67' => 
    array (
      0 => '/var/www/html/core/templates/op-data_output.tpl',
      1 => 1470329231,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_57fe581f809844_31017445 ($_smarty_tpl) {
?>
<!-- data output -->
<div class="row">
	<div class="col-sm-6">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_DO_PRINTING_TYPE');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_DO_PRINTING_TYPE'];?>
</td>
				</tr>
				
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_DO_DGC_01');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_DO_DGC'];?>
</td>
				</tr>
				<!--
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_DO_PLATE_SLEEVE_01');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_DO_PLATE_SLEEVE_01'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_DO_DATATYPE_01');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_DO_DATATYPE_01'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_DO_RESOLUTION_01');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_DO_RESOLUTION_01'];?>
</td>
				</tr>
				-->
			</tbody>
		</table>
	</div>
	<div class="col-sm-6">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_DD_NOTES');?>
</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo nl2br($_smarty_tpl->tpl_vars['values']->value['ZLP_DD_NOTES']);?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
</div> <!-- End row -->

<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_COLOR_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_DO_PLATE_SLEEVE_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_DO_DATATYPE_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_DO_RESOLUTION_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_DISTORTION_L_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_DISTORTION_C_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_GES_F_END_TO_END_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_GES_F_STAG_CUT_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_DO_PROD_PLANT_01');?>
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
$_tmp3=ob_get_clean();
$_smarty_tpl->tpl_vars['id'] = new Smarty_Variable($_tmp3, null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, 'id', 0);?>
				<tr>
					<td rowspan="2" class="swatch" style='background-color:#<?php echo $_smarty_tpl->tpl_vars['values']->value["ColorValue_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
;'><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_COLOR_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_DO_PLATE_SLEEVE_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_DO_DATATYPE_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_DO_RESOLUTION_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_DISTORTION_L_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_DISTORTION_C_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_GES_F_END_TO_END_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_GES_F_STAG_CUT_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_DO_PROD_PLANT_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
				</tr>
				<tr>
					<td colspan="4"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_DO_DGC_01');?>
: <?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_DO_DGC_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td colspan="4"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_DO_LENFILE_NAME_01');?>
: <?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_DO_LENFILE_NAME_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
				</tr>
				<?php }
}
?>

			</tbody>
			<?php }?>
		</table>
	</div>
</div><!-- End row --><?php }
}
