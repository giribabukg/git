<?php
/* Smarty version 3.1.29, created on 2017-01-09 08:33:02
  from "/Applications/MAMP/htdocs/git/core_browser/templates/color_rotation.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_58734abeae4104_52370133',
  'file_dependency' => 
  array (
    'f0d09d38aefe9e2b2cd5f24646a6845b5f097a07' => 
    array (
      0 => '/Applications/MAMP/htdocs/git/core_browser/templates/color_rotation.tpl',
      1 => 1483681730,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_58734abeae4104_52370133 ($_smarty_tpl) {
?>
<!-- colour rotation -->
<div id="colorrotation" class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="10" class="header"><span class="fa fa-tint"></span>&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['text']->value->__('COLOR_ROTATION');?>
</th>
				</tr>
				<tr>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_COLOR_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_LINE_SCREEN_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_CONT_SCR_CT_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_TECH_SCR_CT_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_DOT_TYPE_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_ANGLE_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_DISTORTION_L_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_DISTORTION_C_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_NEW_01');?>
</th>
					<th class="text-center"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_CLICHE_NUMBER_01');?>
</th>					
				</tr>
				<tr>
					<th>&nbsp;</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('CDI_ZLP_AG_F_PLATE_TYPE_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('CDI_ZLP_AG_F_PLATE_THICKNESS_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_FP_DIGI_CAP_01');?>
</th>
					<th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_FP_NO_OF_PLATES_01');?>
</th>
					<th colspan="5"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_REMARK_01');?>
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
					<td rowspan="2" class="swatch" style='background-color:#<?php echo $_smarty_tpl->tpl_vars['values']->value["ColorValue_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
;'><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_COLOR_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_LINE_SCREEN_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_CONT_SCR_CT_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_TECH_SCR_CT_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_DOT_TYPE_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_ANGLE_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_DISTORTION_L_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_DISTORTION_C_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_NEW_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td class="text-center"><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_CLICHE_NUMBER_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>					
				</tr>
				<tr>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_PLATE_TYPE_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_PLATE_THICKNESS_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_FP_DIGI_CAP_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_FP_NO_OF_PLATES_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td>
					<td colspan="5"><em><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_REMARK_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</em></td>
				</tr>
				<?php }
}
?>

			</tbody>
			<?php }?>
		</table>
	</div>
</div><?php }
}
