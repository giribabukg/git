<!-- data input -->
<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="2">{$text->__('CUSTOMER_FILES')}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_AV_REF_JOB_NR')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_AV_REF_JOB_NR']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AV_AMOUNT')}</strong></td>
					<td>{$values['ZLP_AV_AMOUNT']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AV_DATA_SOURCE')}</strong></td>
					<td>{$values['ZLP_AV_DATA_SOURCE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AV_CONVERT_TO')}</strong></td>
					<td>{$values['ZLP_AV_CONVERT_TO']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AV_CUST_HARDCOPY_NO')}</strong></td>
					<td>{$values['ZLP_AV_CUST_HARDCOPY_NO']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AV_CUST_HARDCOPY_SIZE')}</strong></td>
					<td>{$values['ZLP_AV_CUST_HARDCOPY_SIZE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AV_KEEP_LIVE_TEXT')}</strong></td>
					<td>{$values['ZLP_AV_KEEP_LIVE_TEXT']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AV_PDF_UPLOAD')}</strong></td>
					<td>{$values['ZLP_AV_PDF_UPLOAD']}</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="2">{$text->__('CUSTOMER_FILES')}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_AV_RULES_GUIDELINES')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_AV_RULES_GUIDELINES']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AV_DATA_ENTRY_CONFORM')}</strong></td>
					<td>{$values['ZLP_AV_DATA_ENTRY_CONFORM']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AV_DATA_HANDLED_CONFORM')}</strong></td>
					<td>{$values['ZLP_AV_DATA_HANDLED_CONFORM']}</td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<div class="col-sm-8">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th>{$text->__('ZLP_AV_NOTES_01')}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{$values['ZLP_AV_NOTES_01']|nl2br}</td>
				</tr>
			</tbody>
		</table>
	</div>
	
</div> <!-- End row -->