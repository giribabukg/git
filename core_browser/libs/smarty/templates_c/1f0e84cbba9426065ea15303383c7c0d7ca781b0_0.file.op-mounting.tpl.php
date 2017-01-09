<?php
/* Smarty version 3.1.29, created on 2016-10-12 15:36:22
  from "/var/www/html/core/templates/op-mounting.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_57fe58766315b7_13547659',
  'file_dependency' => 
  array (
    '1f0e84cbba9426065ea15303383c7c0d7ca781b0' => 
    array (
      0 => '/var/www/html/core/templates/op-mounting.tpl',
      1 => 1470431068,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_57fe58766315b7_13547659 ($_smarty_tpl) {
?>
<!-- mounting -->
<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_MOUNTING_TYPE');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_MOUNTING_TYPE'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_PRESS_PROOF');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_PRESS_PROOF'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_PRESS_PROOF_TYPE');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_PRESS_PROOF_TYPE'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_PLOTTING_ON');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_PLOTTING_ON'];?>
</td>
				</tr>
				
			</tbody>
		</table>

	</div>
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_MOUNTINGS_NO');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_MOUNTINGS_NO'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_FOILS_HARD_NO');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_FOILS_HARD_NO'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_FOILS_FOAM_NO');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_FOILS_FOAM_NO'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_CLICHES_NOT_MOUNTED');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_CLICHES_NOT_MOUNTED'];?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_REMARKS_01');?>
</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo nl2br($_smarty_tpl->tpl_vars['values']->value['ZLP_MT_REMARKS_01']);?>
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
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_PLATE_TYPE_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_PLATE_THICKNESS_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_FILM_HEIGHT_FIX');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_TOTAL_CLICHES_SET_UP');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_MOUNTING_FOIL');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_ADHESIVE_FOIL');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_FOAM');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_REAL_CLICHE_HEIGHT');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_RELIEF_DEPTH');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_CLICHE_NUMBER_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_LINE_SCREEN_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_DISTORTION_L_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_DISTORTION_C_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_REMARK_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_PATCH_01');?>
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
$_tmp1=ob_get_clean();
$_smarty_tpl->tpl_vars['id'] = new Smarty_Variable($_tmp1, null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, 'id', 0);?>
				<tr>
					<td class="swatch" style='background-color:#<?php echo $_smarty_tpl->tpl_vars['values']->value["ColorValue_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
;'><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_COLOR_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_PLATE_TYPE_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_PLATE_THICKNESS_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_MT_FILM_HEIGHT_FIX_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_MT_TOTAL_CLICHES_SET_UP_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_MT_MOUNTING_FOIL_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_MT_ADHESIVE_FOIL_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_MT_FOAM_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_MT_REAL_CLICHE_HEIGHT_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_MT_RELIEF_DEPTH_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_CLICHE_NUMBER_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_LINE_SCREEN_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_DISTORTION_L_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_DISTORTION_C_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_REMARK_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_PATCH_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
				</tr>
				<?php }
}
?>

			</tbody>
			<?php }?>
		</table>
	</div>
</div><!-- End row -->

<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="2"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('MOUNTING_FOIL');?>
</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_FOIL_WIDTH_FIX');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_FOIL_WIDTH_FIX'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_FOIL_WIDTH_MIN');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_FOIL_WIDTH_MIN'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_FOIL_WIDTH_MAX');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_FOIL_WIDTH_MAX'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_FOIL_HEIGHT_FIX');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_FOIL_HEIGHT_FIX'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_FOIL_HEIGHT_MIN');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_FOIL_HEIGHT_MIN'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_FOIL_HEIGHT_MAX');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_FOIL_HEIGHT_MAX'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_FOIL_CUT_FROM');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_FOIL_CUT_FROM'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_GALLEY_PROOF_MOUNTING');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_GALLEY_PROOF_MOUNTING'];?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="2"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('MOUNTING_SLAT');?>
</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_CLIP_SYSTEM');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_CLIP_SYSTEM'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_SLAT_TYPE_FRONT');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_SLAT_TYPE_FRONT'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_SLAT_TYPE_BACK');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_SLAT_TYPE_BACK'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_FOIL_ARRANGEMENT');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_FOIL_ARRANGEMENT'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_CENTERLINE');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_CENTERLINE'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_SLAT_FIXATION');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_SLAT_FIXATION'];?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_REMARKS_02');?>
</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo nl2br($_smarty_tpl->tpl_vars['values']->value['ZLP_MT_REMARKS_02']);?>
</td>
				</tr>
			</tbody>
		</table>
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="2"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('TEXTILE_TAPE');?>
</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_TEXTILE_TAPE_COLOR');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_TEXTILE_TAPE_COLOR'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_TEXTILE_TAPE_WIDTH');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_TEXTILE_TAPE_WIDTH'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_TEXTILE_TAPE_STAND');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_TEXTILE_TAPE_STAND'];?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
</div> <!-- End row -->

<div class="row">
	<div class="col-sm-4">
		
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="2"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('MOUNTING_FOIL_CONFIGURATION');?>
</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_DIST_SLAT_TO_CUT');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_DIST_SLAT_TO_CUT'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_DIST_SLAT_TO_CUT');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_DIST_FOIL_TO_CUT'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_DIST_LEFT_RIGHT_TO_CUT');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_DIST_LEFT_RIGHT_TO_CUT'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_DIST_SLAT_TO_PRINT');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_DIST_SLAT_TO_PRINT'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_GLUEING_EDGE');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_GLUEING_EDGE'];?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4">
		
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="2"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('PULL_BANDS');?>
</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('PULL_BANDS');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_GES_F_ALIGNMENT_BAR'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_PULL_BANDS_WIDTH');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_PULL_BANDS_WIDTH'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_PULL_BANDS_THICKNESS');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_PULL_BANDS_THICKNESS'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_GES_F_ALIGNMENT_BAR_POS');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_GES_F_ALIGNMENT_BAR_POS'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_PULL_BANDS_TYPE');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_PULL_BANDS_TYPE'];?>
</td>
				</tr>
			</tbody>
		</table>
		
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="2"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_GES_F_PRINT_CTRL_ELEMENTS');?>
</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo nl2br($_smarty_tpl->tpl_vars['values']->value['ZLP_GES_F_PRINT_CTRL_ELEMENTS']);?>
</td>
				</tr>
				<?php if ($_smarty_tpl->tpl_vars['values']->value['ZLP_MT_PULL_BANDS_PRINTCTRL_02'] != '') {?>
				<tr>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_PULL_BANDS_PRINTCTRL_02'];?>
</td>
				</tr>
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['values']->value['ZLP_MT_PULL_BANDS_PRINTCTRL_03'] != '') {?>
				<tr>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_PULL_BANDS_PRINTCTRL_03'];?>
</td>
				</tr>
				<?php }?>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="2"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('EYES_RIVETS');?>
</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_EYES');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_EYES'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_EYES_DISTANCE');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_EYES_DISTANCE'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_EYES_SIZE');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_EYES_SIZE'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_EYES_DIST_FROM_FOIL');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_EYES_DIST_FROM_FOIL'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_EYES_FOR_ARCHIVING');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_EYES_FOR_ARCHIVING'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_EYES_DISTANCE_ARCHIVE');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_EYES_DISTANCE_ARCHIVE'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_EYES_SIZE_ARCHIVE');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_EYES_SIZE_ARCHIVE'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_EYES_DIST_FOIL_ARCHIVE');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_EYES_DIST_FOIL_ARCHIVE'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_RIVET');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_RIVET'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_RIVET_DISTANCE');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_RIVET_DISTANCE'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_RIVET_DISTANCE_WIDTH');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_RIVET_DISTANCE_WIDTH'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_RIVET_DIST_FROM_FOIL');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_RIVET_DIST_FROM_FOIL'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_DRAW_ORIGIN');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_DRAW_ORIGIN'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_MT_PACKAGING_TYPE');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_MT_PACKAGING_TYPE'];?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
</div> <!-- End row --><?php }
}
