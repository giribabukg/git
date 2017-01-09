<?php
/* Smarty version 3.1.29, created on 2016-10-12 15:34:55
  from "/var/www/html/core/templates/basepage.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_57fe581f91ae92_55550737',
  'file_dependency' => 
  array (
    'd4eba5a55a4ee122bccaba865d05e28d9d8391ef' => 
    array (
      0 => '/var/www/html/core/templates/basepage.tpl',
      1 => 1470330767,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_57fe581f91ae92_55550737 ($_smarty_tpl) {
?>
<!DOCTYPE html>
<html lang="<?php echo $_smarty_tpl->tpl_vars['page']->value->language;?>
">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="author" content="Gareth Evans" />
    <title><?php echo $_smarty_tpl->tpl_vars['meta_title']->value;
echo $_smarty_tpl->tpl_vars['text']->value->__('SITE_NAME');?>
</title>
    <link rel="stylesheet" href="<?php echo @constant('WWW_TOP');?>
/templates/css/bootstrap-glyphicons.css" />
    <link rel="stylesheet" href="<?php echo @constant('WWW_TOP');?>
/templates/css/bootstrap.css" media="all" />
    <link rel="stylesheet" href="<?php echo @constant('WWW_TOP');?>
/templates/css/site.css" media="all" />
    <link rel="stylesheet" href="<?php echo @constant('WWW_TOP');?>
/templates/css/languages.min.css" media="all" />
    <link rel="stylesheet" href="<?php echo @constant('WWW_TOP');?>
/templates/css/font-awesome.min.css" />
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <?php echo '<script'; ?>
 src="<?php echo @constant('WWW_TOP');?>
/templates/scripts/html5shiv.js"><?php echo '</script'; ?>
>
      <?php echo '<script'; ?>
 src="<?php echo @constant('WWW_TOP');?>
/templates/scripts/respond.min.js"><?php echo '</script'; ?>
>
    <![endif]-->
	<?php echo '<script'; ?>
 src="<?php echo @constant('WWW_TOP');?>
/templates/scripts/jquery-1.10.2.min.js"><?php echo '</script'; ?>
>
    <?php echo $_smarty_tpl->tpl_vars['page']->value->head;?>

  </head>
<body class="<?php echo $_smarty_tpl->tpl_vars['page']->value->page;?>
">

<!-- Header -->
<div class="navbar navbar-default navbar-fixed-top">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="<?php echo @constant('WWW_TOP');?>
"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('SITE_NAME');?>
</a>
		</div>
		<div class="navbar-collapse collapse navbar-responsive-collapse">
			<ul class="nav navbar-nav navbar-left">
				<li class="divider-vertical"></li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Language <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li><a href="?lang=de&view=<?php echo $_smarty_tpl->tpl_vars['page']->value->view;?>
&id=<?php echo $_smarty_tpl->tpl_vars['serviceOrderHead']->value['ORDERID'];?>
"><span class="lang-xs lang-lbl" lang="de"></span></a></li>
						<li><a href="?lang=en&view=<?php echo $_smarty_tpl->tpl_vars['page']->value->view;?>
&id=<?php echo $_smarty_tpl->tpl_vars['serviceOrderHead']->value['ORDERID'];?>
"><span class="lang-xs lang-lbl" lang="en"></span></a></li>
					</ul>
				</li>
			</ul>
			<?php if ($_smarty_tpl->tpl_vars['serviceOrderHead']->value['ORDERID'] != '') {?>
			<ul class="nav navbar-nav navbar-left">
				<li class="divider-vertical"></li>
				<li>
					<a href="?page=export&type=pdf&view=<?php echo $_smarty_tpl->tpl_vars['page']->value->view;?>
&id=<?php echo $_smarty_tpl->tpl_vars['serviceOrderHead']->value['ORDERID'];?>
&lang=<?php echo $_smarty_tpl->tpl_vars['page']->value->language;?>
"><span class="fa fa-file-pdf-o"></span>&nbsp;&nbsp;PDF</a>
				</li>
				<li>
					<a href="?page=export&type=pdf&view=cdiworksheet&id=<?php echo $_smarty_tpl->tpl_vars['serviceOrderHead']->value['ORDERID'];?>
&lang=<?php echo $_smarty_tpl->tpl_vars['page']->value->language;?>
"><span class="fa fa-file-pdf-o"></span>&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['text']->value->__('CDI_WORKSHEET');?>
</a>
				</li>
			</ul>
			<?php }?>
			<form class="navbar-form navbar-right" role="search" method="get">
				<div class="form-group">
					<input type="text" name="id" class="form-control" placeholder="<?php echo $_smarty_tpl->tpl_vars['text']->value->__('SERVICE_ORDER_SEARCH');?>
">
				</div>
					<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search" title="Search"></span></button>
			</form>

		</div>
	</div>
</div>
<!-- End Header -->

<div class="container-fluid">
	<div class="clearfix">&nbsp;</div>
	<noscript>
		<div class="page-header alert alert-warning">
			<h4><span class="glyphicon glyphicon-warning-sign"></span>&nbsp;&nbsp;Warning: Javascript not enabled.</h4>
			<p>You must have Javascript enabled to use this website. Please enable Javascript in your web browser and refresh the page.</p>
		</div>
	</noscript>
	<?php echo $_smarty_tpl->tpl_vars['page']->value->content;?>

	<p>&nbsp;</p>
</div>
<a class="scrollup" href="#">Scroll</a>

<div id="footer" class="hidden-print">
  <div class="container-fluid">
	<p>Page generated in <?php echo $_smarty_tpl->tpl_vars['page']->value->pageTime;?>
 seconds.</p>
  </div>
</div>

<?php echo '<script'; ?>
 src="<?php echo @constant('WWW_TOP');?>
/templates/scripts/bootstrap.min.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="<?php echo @constant('WWW_TOP');?>
/templates/scripts/moment.min.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="<?php echo @constant('WWW_TOP');?>
/templates/scripts/site.js"><?php echo '</script'; ?>
>
<?php echo $_smarty_tpl->tpl_vars['page']->value->foot;?>


</body>
</html><?php }
}
