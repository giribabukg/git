<div class="row">
	<div class="col-sm-12">
			<table class="table table-striped table-hover table-condensed">
				<thead>
					<tr>
						<th colspan="{$barcodeCount+1}" class="header"><span class="fa fa-barcode"></span>&nbsp;&nbsp;Barcodes ({$barcodeCount} codes)</th>
					</tr>
				</thead>
				<tbody>
					{foreach $barcodes as $key=>$values}
						<tr>
							<td class="col-sm-2"><strong>{$key}</strong></td>
							{foreach $values as $value}
								<td class="col-sm-{10/$barcodeCount|round}">{$value}</td>
							{/foreach}
						</tr>
					{/foreach}
				</tbody>
			</table>
	</div>
</div> <!-- End row -->