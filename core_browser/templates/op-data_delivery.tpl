<!-- data delivery -->
<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_DD_REF_JOB_NR')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_DD_REF_JOB_NR']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_DD_DATA_SOURCE')}</strong></td>
					<td>{$values['ZLP_DD_DATA_SOURCE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_DD_FINAL_FILE')}</strong></td>
					<td>{$values['ZLP_DD_FINAL_FILE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_DD_FILE_TRANSFER')}</strong></td>
					<td>{$values['ZLP_DD_FILE_TRANSFER']}</td>
				</tr>
			</tbody>
		</table>

	</div>
	<div class="col-sm-4">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th>{$text->__('ZLP_DD_ADDRESS')}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{$values['ZLP_DD_ADDRESS']|nl2br}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div> <!-- End row -->