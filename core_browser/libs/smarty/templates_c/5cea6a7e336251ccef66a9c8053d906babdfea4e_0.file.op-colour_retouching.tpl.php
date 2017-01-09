<?php
/* Smarty version 3.1.29, created on 2016-10-12 15:34:55
  from "/var/www/html/core/templates/op-colour_retouching.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_57fe581f635396_98147630',
  'file_dependency' => 
  array (
    '5cea6a7e336251ccef66a9c8053d906babdfea4e' => 
    array (
      0 => '/var/www/html/core/templates/op-colour_retouching.tpl',
      1 => 1476219005,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_57fe581f635396_98147630 ($_smarty_tpl) {
?>
<!-- colour retouching -->
<div class="row">
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
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BB_REF_JOB');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_BB_REF_JOB'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BB_COL_SEPARATION');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_BB_COL_SEPARATION'];?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('BLACK_SEPARATION');?>
</th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BB_SKELET_BLACK');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_BB_SKELET_BLACK'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BB_TRAVEL');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_BB_TRAVEL'];?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('COLOR_SETUP');?>
</th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BB_CMY_CUT');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_BB_CMY_CUT'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BB_CMY_TRAV');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_BB_CMY_TRAV'];?>
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
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('TONAL_VALUE');?>
</th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BB_MIN_DOT');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_BB_MIN_DOT'];?>
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
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BB_TEMPLATE');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_BB_TEMPLATE'];?>
</td>
				</tr>
			</tbody>
		</table>
		
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('3D');?>
</th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BB_3D_IMG');?>
</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7"><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_BB_3D_IMG'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BB_NEW_VIEW');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_BB_NEW_VIEW'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BB_ADAPT');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_BB_ADAPT'];?>
</td>
				</tr>
				<tr>
					<td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BB_ADAPT_FROM');?>
</strong></td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_BB_ADAPT_FROM'];?>
</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="col-sm-8">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_BB_MEMO_TXT');?>
</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo nl2br($_smarty_tpl->tpl_vars['values']->value['ZLP_BB_MEMO_TXT']);?>
</td>
				</tr>
			</tbody>
		</table>
	</div>
</div> <!-- End row --><?php }
}
