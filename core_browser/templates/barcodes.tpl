<div id="barcodes" class="row">
	<div class="col-sm-12">
			<table class="table table-striped table-hover table-condensed">
				<thead>
					<tr>
						<th colspan="12" class="header"><span class="fa fa-barcode"></span>&nbsp;&nbsp;Barcodes</th>
					</tr>
					<tr>
						<th>{$text->__('ZLP_BC_CODE_TYPE')}</th>
						<th>{$text->__('ZLP_BC_CODE_NUMBER')}</th>
						<th>{$text->__('ZLP_BC_RESOLUTION')}</th>
						<th>{$text->__('ZLP_BC_BWR')}</th>
						<th>{$text->__('ZLP_BC_MAGNIFICATION')}</th>
						<th>{$text->__('ZLP_BC_NARROW_BAR')}</th>
						<th>{$text->__('ZLP_BC_RATIO')}</th>
						<th>{$text->__('ZLP_BC_DEVICE_COMPENSATION')}</th>
						<th>{$text->__('ZLP_BC_CODE_COLOR')}</th>
						<th>{$text->__('ZLP_BC_BCKGRND_COLOR')}</th>
						<th>{$text->__('ZLP_BC_SUB_TYPE')}</th>
						<th>{$text->__('ZLP_BC_TEST_PROTOCOL')}</th>
					</tr>
				</thead>
				<tbody>
					{foreach $barcodes as $key=>$barcode}
						{if $barcode['ZLP_BC_CODE_TYPE'] != ''}
						<tr>
							<td>{$barcode['ZLP_BC_CODE_TYPE']}</td>
							<td>{$barcode['ZLP_BC_CODE_NUMBER']}</td>
							<td>{$barcode['ZLP_BC_RESOLUTION']}</td>
							<td>{$barcode['ZLP_BC_BWR']}</td>
							<td>{$barcode['ZLP_BC_MAGNIFICATION']}</td>
							<td>{$barcode['ZLP_BC_NARROW_BAR']}</td>
							<td>{$barcode['ZLP_BC_RATIO']}</td>
							<td>{$barcode['ZLP_BC_DEVICE_COMPENSATION']}</td>
							<td>{$barcode['ZLP_BC_CODE_COLOR']}</td>
							<td>{$barcode['ZLP_BC_BCKGRND_COLOR']}</td>
							<td>{$barcode['ZLP_BC_SUB_TYPE']}</td>
							<td>{$barcode['ZLP_BC_TEST_PROTOCOL']}</td>
						</tr>
						{/if}
					{/foreach}
				</tbody>
			</table>
	</div>
</div>