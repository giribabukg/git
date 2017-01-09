<!-- barcode 3 -->
<div class="row">
	<div class="col-sm-6">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_BC_CODE_TYPE')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$barcodes[2]['ZLP_BC_CODE_TYPE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_BC_CODE_NUMBER')}</strong></td>
					<td>{$barcodes[2]['ZLP_BC_CODE_NUMBER']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_BC_RESOLUTION')}</strong></td>
					<td>{$barcodes[2]['ZLP_BC_RESOLUTION']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_BC_BWR')}</strong></td>
					<td>{$barcodes[2]['ZLP_BC_BWR']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_BC_MAGNIFICATION')}</strong></td>
					<td>{$barcodes[2]['ZLP_BC_MAGNIFICATION']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_BC_NARROW_BAR')}</strong></td>
					<td>{$barcodes[2]['ZLP_BC_NARROW_BAR']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_BC_RATIO')}</strong></td>
					<td>{$barcodes[2]['ZLP_BC_RATIO']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_BC_DEVICE_COMPENSATION')}</strong></td>
					<td>{$barcodes[2]['ZLP_BC_DEVICE_COMPENSATION']}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-6">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_BC_SUB_TYPE')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$barcodes[2]['ZLP_BC_SUB_TYPE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_BC_CODE_COLOR')}</strong></td>
					<td>{$barcodes[2]['ZLP_BC_CODE_COLOR']}</td>
				</tr>
				
				<tr>
					<td><strong>{$text->__('ZLP_BC_BCKGRND_COLOR')}</strong></td>
					<td>{$barcodes[2]['ZLP_BC_BCKGRND_COLOR']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_BC_TEST_PROTOCOL')}</strong></td>
					<td>{$barcodes[2]['ZLP_BC_TEST_PROTOCOL']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_BC_CODE_SIZE')}</strong></td>
					<td>{$barcodes[2]['ZLP_BC_CODE_SIZE']}</td>
				</tr>
				<tr>
					<td colspan="2">{$barcodes[2]['ZLP_BC_MEMO_TXT']|nl2br}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div> <!-- End row -->