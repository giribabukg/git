<?php
/* Smarty version 3.1.29, created on 2017-01-09 07:10:14
  from "/Applications/MAMP/htdocs/git/core_browser/templates/index.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_587337566c1f90_97498970',
  'file_dependency' => 
  array (
    '312117d307f138d648fee56aa9c51474fe695dcb' => 
    array (
      0 => '/Applications/MAMP/htdocs/git/core_browser/templates/index.tpl',
      1 => 1483681729,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_587337566c1f90_97498970 ($_smarty_tpl) {
?>
<!-- index -->
<div class="page-header">
	<div class="row">
		<div class="col-lg-8">
			<h3><?php echo $_smarty_tpl->tpl_vars['text']->value->__('SERVICE_ORDER_SEARCH');?>
...</h3>
			<form class="form-horizontal" method="get">
				<div class="form-group">
					<div class="col-lg-3">
						<input type="text" class="input-lg" name="id" placeholder="Service Order ID...">
					</div>
					<div class="col-lg-9">
						<button type="submit" class="btn btn-primary">Go</button>
					</div>
				</div>
			</form>
		</div>
		<!--<div class="col-lg-6">
			<h3>Upload an XML file...</h3>
			<form class="form-horizontal" enctype="multipart/form-data"  method="post">
				<input type="hidden" name="page" value="upload"/>
				<div class="form-group">
					<div class="col-lg-4">
						<input name="xmlfile" type="file" />
					</div>
					<div class="col-lg-8">
						<button type="submit" class="btn btn-primary btn-xs">Upload</button>
					</div>
				</div>
			</form>
		</div>-->
	</div>
</div>
<hr />
<div class="row">
	<div class="col-lg-12">
		<h5><?php echo $_smarty_tpl->tpl_vars['text']->value->__('SERVICE_ORDER_RECENT');?>
:</h5>
		<?php if (count($_smarty_tpl->tpl_vars['recentItems']->value) > 0) {?>
			<ul>
			<?php
$_from = $_smarty_tpl->tpl_vars['recentItems']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_spName_0_saved_item = isset($_smarty_tpl->tpl_vars['spName']) ? $_smarty_tpl->tpl_vars['spName'] : false;
$__foreach_spName_0_saved_key = isset($_smarty_tpl->tpl_vars['spId']) ? $_smarty_tpl->tpl_vars['spId'] : false;
$_smarty_tpl->tpl_vars['spName'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['spId'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['spName']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['spId']->value => $_smarty_tpl->tpl_vars['spName']->value) {
$_smarty_tpl->tpl_vars['spName']->_loop = true;
$__foreach_spName_0_saved_local_item = $_smarty_tpl->tpl_vars['spName'];
?>
				<li><a href="?id=<?php echo $_smarty_tpl->tpl_vars['spId']->value;?>
"><?php echo intval($_smarty_tpl->tpl_vars['spId']->value);?>
&nbsp;-&nbsp;<?php echo $_smarty_tpl->tpl_vars['spName']->value;?>
</a></li>
			<?php
$_smarty_tpl->tpl_vars['spName'] = $__foreach_spName_0_saved_local_item;
}
if ($__foreach_spName_0_saved_item) {
$_smarty_tpl->tpl_vars['spName'] = $__foreach_spName_0_saved_item;
}
if ($__foreach_spName_0_saved_key) {
$_smarty_tpl->tpl_vars['spId'] = $__foreach_spName_0_saved_key;
}
?>
			</ul>
		<?php } else { ?>
			<p>None</p>
		<?php }?>
	</div>
</div><?php }
}
