<!-- genspec corrugated -->
<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="2">{$text->__('PRINTING_MATERIAL')}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_AG_C_CORRUGATED_TYPE')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_AG_C_CORRUGATED_TYPE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AG_C_SURFACE')}</strong></td>
					<td>{$values['ZLP_AG_C_SURFACE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AG_C_SHEET_FORMAT')}</strong></td>
					<td>{$values['ZLP_AG_C_SHEET_FORMAT']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AG_C_PRINTSIZE_MAX_WIDTH')}</strong></td>
					<td>{$values['ZLP_AG_C_PRINTSIZE_MAX_WIDTH']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AG_C_PRINTSIZE_MAX_HEIGHT')}</strong></td>
					<td>{$values['ZLP_AG_C_PRINTSIZE_MAX_HEIGHT']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AG_C_PRINTSIZE_MIN_WIDTH')}</strong></td>
					<td>{$values['ZLP_AG_C_PRINTSIZE_MIN_WIDTH']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AG_C_PRINTSIZE_MIN_HEIGHT')}</strong></td>
					<td>{$values['ZLP_AG_C_PRINTSIZE_MIN_HEIGHT']}</td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="2">{$text->__('COLOURS')}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_ANZF_VS')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_ANZF_VS']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AG_F_MACHINE_COLORS')}</strong></td>
					<td>{$values['ZLP_AG_F_MACHINE_COLORS']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_REP_F_MAX_INK_COVERAGE')}</strong></td>
					<td>{$values['ZLP_REP_F_MAX_INK_COVERAGE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AG_F_ANGLE_TYPE')}</strong></td>
					<td>{$values['ZLP_AG_F_ANGLE_TYPE']}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="2">{$text->__('CUSTOMER_SPECIFIC_FIELDS')}</th>
				</tr>
			</thead>
			<tbody>
				{foreach $customerFields as $customerField}
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$customerField['label']}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$customerField['value']}</td>
				</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
</div> <!-- End row -->
<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th>{$text->__('ZLP_AG_NOTES')}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{$values['ZLP_AG_NOTES']|nl2br}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_CAD_F_DIE_CUT_NAME')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_CAD_F_DIE_CUT_NAME']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_F_DIE_CUT_NO')}</strong></td>
					<td>{$values['ZLP_CAD_F_DIE_CUT_NO']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AG_BUSINESS_CATEGORY')}</strong></td>
					<td>{$values['ZLP_AG_BUSINESS_CATEGORY']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_WEBCENTER_ID')}</strong></td>
					<td>{$values['ZLP_WEBCENTER_ID']}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_BB_COL_PROFILE')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_BB_COL_PROFILE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_DO_DGC')}</strong></td>
					<td>{$values['ZLP_DO_DGC']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AG_REF_JOB')}</strong></td>
					<td>{$values['ZLP_AG_REF_JOB']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_BB_VERSION')}</strong></td>
					<td>{$values['ZLP_BB_VERSION']}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div> <!-- End row -->