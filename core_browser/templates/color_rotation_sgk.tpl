<!-- colour rotation sgk -->
<div class="row" id="colorRotation">
	<div class="col-lg-12">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="{$colorCount+1}"><h4><span class="fa fa-tint" />&nbsp;&nbsp;Color Rotation Details ({$colorCount} colors)</h4></th>
				</tr>
			</thead>
			<tbody>
				{if $colorCount <= 5}{$numCols = 2}{else}{$numCols = 1}{/if}
				{foreach $colors as $key=>$values}
					{if $key != 'ColorValue'}
					<tr>
						<td class="col-xs-2"><strong>{$key}</strong></td>
						{foreach $values as $valIdx=>$value}
							<td class="col-md-{$numCols} text-center{if $key == 'Color'} swatch" style="background-color:#{$colors['ColorValue'][$valIdx]};"{else}"{/if}>{$value}</td>
						{/foreach}
					</tr>
					{/if}						
				{/foreach}
			</tbody>
		</table>
			<table class="table table-striped table-hover table-condensed">
				<thead>
					<tr>
						<th colspan="{$barcodeCount+1}"><h4><span class="fa fa-barcode" />&nbsp;&nbsp;Barcodes ({$barcodeCount} codes)</h4></th>
					</tr>
				</thead>
				<tbody>
					{foreach $barcodes as $key=>$values}
						<tr>
							<td class="col-xs-4"><strong>{$key}</strong></td>
							{foreach $values as $value}
								<td class="col-md-{8/$barcodeCount|round}">{$value}</td>
							{/foreach}
						</tr>
					{/foreach}
				</tbody>
			</table>
	</div>
</div> <!-- End row -->