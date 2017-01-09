<?php
/* Smarty version 3.1.29, created on 2017-01-09 08:33:03
  from "/Applications/MAMP/htdocs/git/core_browser/templates/op-components.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_58734abf062938_61160175',
  'file_dependency' => 
  array (
    '0cb002b50cdf4d400c7ae7fe49a52cf4df02e14d' => 
    array (
      0 => '/Applications/MAMP/htdocs/git/core_browser/templates/op-components.tpl',
      1 => 1483681730,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_58734abf062938_61160175 ($_smarty_tpl) {
?>
<!-- components -->
<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-hover table-condensed table-component">
			<thead>
				<tr>
					<th>Component</th>
					<th>Description</th>
					<th>Req. Quantity</th>
					<th>Quantity Withdrawn</th>
					<th>Unit</th>
					<th>ICt</th>
					<th>SLoc</th>
					<th>Plant</th>
				</tr>
			</thead>
			<tbody>
				<?php
$_from = $_smarty_tpl->tpl_vars['components']->value[$_smarty_tpl->tpl_vars['operation']->value['ACTIVITY']];
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_component_0_saved_item = isset($_smarty_tpl->tpl_vars['component']) ? $_smarty_tpl->tpl_vars['component'] : false;
$_smarty_tpl->tpl_vars['component'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['component']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['component']->value) {
$_smarty_tpl->tpl_vars['component']->_loop = true;
$__foreach_component_0_saved_local_item = $_smarty_tpl->tpl_vars['component'];
?>
				<tr>
					<td><?php echo intval($_smarty_tpl->tpl_vars['component']->value['MATERIAL']);?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['component']->value['ITEM_TEXT'];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['component']->value['ENTRY_QNT'];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['component']->value['WITHD_QUAN'];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['component']->value['ENTRY_UOM'];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['component']->value['ITEM_CAT'];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['component']->value['STGE_LOC'];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['component']->value['PLANT'];?>
</td>
				</tr>
				<?php
$_smarty_tpl->tpl_vars['component'] = $__foreach_component_0_saved_local_item;
}
if ($__foreach_component_0_saved_item) {
$_smarty_tpl->tpl_vars['component'] = $__foreach_component_0_saved_item;
}
?>
			</tbody>
		</table>
	</div>
</div><?php }
}
