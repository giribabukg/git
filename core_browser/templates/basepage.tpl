<!DOCTYPE html>
<html lang="{$page->language}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="author" content="Gareth Evans" />
    <title>{$meta_title}{$text->__('SITE_NAME')}</title>
    <link rel="stylesheet" href="{$smarty.const.WWW_TOP}/templates/css/bootstrap-glyphicons.css" />
    <link rel="stylesheet" href="{$smarty.const.WWW_TOP}/templates/css/bootstrap.css" media="all" />
    <link rel="stylesheet" href="{$smarty.const.WWW_TOP}/templates/css/site.css" media="all" />
    <link rel="stylesheet" href="{$smarty.const.WWW_TOP}/templates/css/languages.min.css" media="all" />
    <link rel="stylesheet" href="{$smarty.const.WWW_TOP}/templates/css/font-awesome.min.css" />
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="{$smarty.const.WWW_TOP}/templates/scripts/html5shiv.js"></script>
      <script src="{$smarty.const.WWW_TOP}/templates/scripts/respond.min.js"></script>
    <![endif]-->
	<script src="{$smarty.const.WWW_TOP}/templates/scripts/jquery-1.10.2.min.js"></script>
    {$page->head}
  </head>
<body class="{$page->page}">

<!-- Header -->
<div class="navbar navbar-default navbar-fixed-top">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="{$smarty.const.WWW_TOP}">{$text->__('SITE_NAME')}</a>
		</div>
		<div class="navbar-collapse collapse navbar-responsive-collapse">
			<ul class="nav navbar-nav navbar-left">
				<li class="divider-vertical"></li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Language <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li><a href="?lang=de&view={$page->view}&id={$serviceOrderHead['ORDERID']}"><span class="lang-xs lang-lbl" lang="de"></span></a></li>
						<li><a href="?lang=en&view={$page->view}&id={$serviceOrderHead['ORDERID']}"><span class="lang-xs lang-lbl" lang="en"></span></a></li>
					</ul>
				</li>
			</ul>
			{if $serviceOrderHead['ORDERID'] != ''}
			<ul class="nav navbar-nav navbar-left">
				<li class="divider-vertical"></li>
				<li>
					<a href="?page=export&type=pdf&view={$page->view}&id={$serviceOrderHead['ORDERID']}&lang={$page->language}"><span class="fa fa-file-pdf-o"></span>&nbsp;&nbsp;PDF</a>
				</li>
				<li>
					<a href="?page=export&type=pdf&view=cdiworksheet&id={$serviceOrderHead['ORDERID']}&lang={$page->language}"><span class="fa fa-file-pdf-o"></span>&nbsp;&nbsp;{$text->__('CDI_WORKSHEET')}</a>
				</li>
			</ul>
			{/if}
			<form class="navbar-form navbar-right" role="search" method="get">
				<div class="form-group">
					<input type="text" name="id" class="form-control" placeholder="{$text->__('SERVICE_ORDER_SEARCH')}">
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
	{$page->content}
	<p>&nbsp;</p>
</div>
<a class="scrollup" href="#">Scroll</a>

<div id="footer" class="hidden-print">
  <div class="container-fluid">
	<p>Page generated in {$page->pageTime} seconds.</p>
  </div>
</div>

<script src="{$smarty.const.WWW_TOP}/templates/scripts/bootstrap.min.js"></script>
<script src="{$smarty.const.WWW_TOP}/templates/scripts/moment.min.js"></script>
<script src="{$smarty.const.WWW_TOP}/templates/scripts/site.js"></script>
{$page->foot}

</body>
</html>