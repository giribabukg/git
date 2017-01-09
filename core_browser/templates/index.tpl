<!-- index -->
<div class="page-header">
	<div class="row">
		<div class="col-lg-8">
			<h3>{$text->__('SERVICE_ORDER_SEARCH')}...</h3>
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
		<h5>{$text->__('SERVICE_ORDER_RECENT')}:</h5>
		{if $recentItems|@count > 0}
			<ul>
			{foreach $recentItems as $spId=>$spName}
				<li><a href="?id={$spId}">{$spId|intval}&nbsp;-&nbsp;{$spName}</a></li>
			{/foreach}
			</ul>
		{else}
			<p>None</p>
		{/if}
	</div>
</div>