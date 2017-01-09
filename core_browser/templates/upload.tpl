<!-- upload -->
{if $errorMsg != ''}
<div class="page-header alert alert-danger">
	<h4><span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;&nbsp;An Error Occurred:</h4>
	<p>{$errorMsg} (#{$errorCode})</p>
</div>
{else}
<div class="row">
		<div class="col-lg-6">
			<h3>Upload an XML file...</h3>
			<form class="form-horizontal" enctype="multipart/form-data"  method="post">
				<input type="hidden" name="page" value="upload"/>
				<div class="form-group">
					<div class="col-lg-4">
						<input name="xmlfile" type="file" />
					</div>
					<div class="col-lg-8">
						<button type="submit" class="btn btn-primary btn-xs">Go</button>
					</div>
				</div>
			</form>
		</div>
	</div>
{/if}