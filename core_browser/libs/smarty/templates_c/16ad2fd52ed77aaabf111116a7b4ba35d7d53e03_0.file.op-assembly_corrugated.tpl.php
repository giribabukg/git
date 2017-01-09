<?php
/* Smarty version 3.1.29, created on 2016-10-13 05:52:04
  from "/var/www/html/core/templates/op-assembly_corrugated.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_57ff2104b35d82_17548353',
  'file_dependency' => 
  array (
    '16ad2fd52ed77aaabf111116a7b4ba35d7d53e03' => 
    array (
      0 => '/var/www/html/core/templates/op-assembly_corrugated.tpl',
      1 => 1476219004,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_57ff2104b35d82_17548353 ($_smarty_tpl) {
?>
<!-- assembly corrugated -->
<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="2"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('TRAPPING_BLEED');?>
</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_REP_F_MIN_TRAP');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_REP_F_MIN_TRAP'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_REP_F_MAX_TRAP');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_REP_F_MAX_TRAP'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_REP_F_STD_TRAP');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_REP_F_STD_TRAP'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_REP_F_VARNISH_TRAP');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_REP_F_VARNISH_TRAP'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_REP_C_GENERAL_BLEED');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_REP_C_GENERAL_BLEED'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_REP_C_MIN_DIST_DIE_CUT');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_REP_C_MIN_DIST_DIE_CUT'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_REP_C_BLEED_TRAP_LAP');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_REP_C_BLEED_TRAP_LAP'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_REP_C_BLEED_TRAP_TOP_BOT');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_REP_C_BLEED_TRAP_TOP_BOT'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_REP_C_MIN_DIST_CREASING');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_REP_C_MIN_DIST_CREASING'];?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="2"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ARTWORK_INFORMATION');?>
</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_REP_F_MIN_LINE_POS');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_REP_F_MIN_LINE_POS'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_REP_F_MIN_LINE_NEG');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_REP_F_MIN_LINE_NEG'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_REP_F_MIN_TXT_SIZE_POS');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_REP_F_MIN_TXT_SIZE_POS'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_REP_F_MIN_TXT_SIZE_NEG');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_REP_F_MIN_TXT_SIZE_NEG'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BB_MIN_DOT');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_BB_MIN_DOT'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_REP_F_MAX_TON_VAL');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_REP_F_MAX_TON_VAL'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_REP_F_MAX_INK_COVERAGE');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_REP_F_MAX_INK_COVERAGE'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_REP_C_RESY');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_REP_C_RESY'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_REP_C_FSC_LOGO');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_REP_C_FSC_LOGO'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_REP_C_FSC_LOGO_VAR');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_REP_C_FSC_LOGO_VAR'];?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="2"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_GES_F_PRINT_CTRL_ELEMENTS');?>
</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_GES_F_REG_MARK_TYPE');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_GES_F_REG_MARK_TYPE'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_GES_F_REG_MARK_POS');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_GES_F_REG_MARK_POS'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_GES_F_ALIGNMENT_BAR');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_GES_F_ALIGNMENT_BAR'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_GES_F_ALIGNMENT_BAR_POS');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_GES_F_ALIGNMENT_BAR_POS'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_GES_C_1UP_MARK');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_GES_C_1UP_MARK'];?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
</div> <!-- End row -->

<div class="row">
	<div class="col-sm-8">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_REP_C_NOTES');?>
</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo nl2br($_smarty_tpl->tpl_vars['values']->value['ZLP_REP_C_NOTES']);?>
</td>
				</tr>
			</tbody>
		</table>

	</div>
	<div class="col-sm-4">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_GES_F_PRINT_CTRL_ELEMENTS');?>
</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo nl2br($_smarty_tpl->tpl_vars['values']->value['ZLP_GES_F_PRINT_CTRL_ELEMENTS']);?>
</td>
				</tr>
			</tbody>
		</table>

	</div>
</div> <!-- End row --><?php }
}
