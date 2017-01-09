<?php
/* Smarty version 3.1.29, created on 2016-10-12 15:34:55
  from "/var/www/html/core/templates/op-components.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_57fe581f418e59_07106864',
  'file_dependency' => 
  array (
    'cb0c8d04b7a05697757fdf425b5bc19b326ab524' => 
    array (
      0 => '/var/www/html/core/templates/op-components.tpl',
      1 => 1470329230,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_57fe581f418e59_07106864 ($_smarty_tpl) {
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
