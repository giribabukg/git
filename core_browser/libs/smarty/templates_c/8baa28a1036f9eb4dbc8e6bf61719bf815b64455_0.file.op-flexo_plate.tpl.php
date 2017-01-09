<?php
/* Smarty version 3.1.29, created on 2016-10-12 15:34:55
  from "/var/www/html/core/templates/op-flexo_plate.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_57fe581f87a6a0_83019872',
  'file_dependency' => 
  array (
    '8baa28a1036f9eb4dbc8e6bf61719bf815b64455' => 
    array (
      0 => '/var/www/html/core/templates/op-flexo_plate.tpl',
      1 => 1476219005,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_57fe581f87a6a0_83019872 ($_smarty_tpl) {
?>
<!-- flexo plate -->
<div class="row">
	<div class="col-sm-6">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_FP_NO_DIGITAL_PLATES');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_FP_NO_DIGITAL_PLATES'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_FP_SIZE_01');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_FP_SIZE_01'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_FP_NO_ANALOG_PLATES');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_FP_NO_ANALOG_PLATES'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_FP_SIZE_02');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_FP_SIZE_02'];?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<div class="col-sm-6">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_FP_FILM_EXPOSURE');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_FP_FILM_EXPOSURE'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_FP_SIZE_03');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_FP_SIZE_03'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_FP_PLATE_CATEGORY');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_FP_PLATE_CATEGORY'];?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_COLOR_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_PLATE_TYPE_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_PLATE_THICKNESS_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_FP_MIN_DOT_HELD_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_RELIEF_DEPTH');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_FP_DIGI_CAP_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_FP_NO_OF_PLATES_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_FP_ROTATION_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_GES_F_STAG_CUT_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_FP_OUTPUT_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_FP_FLAT_TOP_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_FP_DIGITAL_ANALOGUE_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_DO_DGC_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_FP_REMARKS_01');?>
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
$_tmp4=ob_get_clean();
$_smarty_tpl->tpl_vars['id'] = new Smarty_Variable($_tmp4, null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, 'id', 0);?>
				<tr>
					<td class="swatch" style='background-color:#<?php echo $_smarty_tpl->tpl_vars['values']->value["ColorValue_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
;'><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_COLOR_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_PLATE_TYPE_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_PLATE_THICKNESS_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_FP_MIN_DOT_HELD_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_MT_RELIEF_DEPTH_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_FP_DIGI_CAP_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_FP_NO_OF_PLATES_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_FP_ROTATION_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_GES_F_STAG_CUT_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_FP_OUTPUT_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_FP_FLAT_TOP_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_FP_DIGITAL_ANALOGUE_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_DO_DGC_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_FP_REMARKS_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
 <?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_REMARK_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
				</tr>
				<?php }
}
?>

			</tbody>
			<?php }?>
		</table>
	</div>
	
</div> <!-- End row --><?php }
}
