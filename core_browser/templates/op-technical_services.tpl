<!-- technical services -->
<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="2">{$text->__('PROOF_PROFILE')}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_TS_PROFILE')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_TS_PROFILE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_TS_PROFILE_NAME')}</strong></td>
					<td>{$values['ZLP_TS_PROFILE_NAME']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_TS_PROFILE_ID')}</strong></td>
					<td>{$values['ZLP_TS_PROFILE_ID']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_TS_PROOF')}</strong></td>
					<td>{$values['ZLP_TS_PROOF']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_TS_REFERENCE')}</strong></td>
					<td>{$values['ZLP_TS_REFERENCE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_TS_CMYK')}</strong></td>
					<td>{$values['ZLP_TS_CMYK']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_TS_SPOT_COLORS')}</strong></td>
					<td>{$values['ZLP_TS_SPOT_COLORS']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_TS_DGC')}</strong></td>
					<td>{$values['ZLP_TS_DGC']}</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="col-sm-8">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{$values['ZLP_TS_NOTES_01']|nl2br}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="2">{$text->__('PRINTING_APPROVAL')}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_TS_INSTIGATION')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_TS_INSTIGATION']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_TS_CONTACT_PRINTER')}</strong></td>
					<td>{$values['ZLP_TS_CONTACT_PRINTER']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_TS_CONTACT_CUSTOMER')}</strong></td>
					<td>{$values['ZLP_TS_CONTACT_CUSTOMER']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_TS_REF_ARTWORK')}</strong></td>
					<td>{$values['ZLP_TS_REF_ARTWORK']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_TS_REF_SPOT_COLORS')}</strong></td>
					<td>{$values['ZLP_TS_REF_SPOT_COLORS']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_TS_PRINT_ANALYSIS')}</strong></td>
					<td>{$values['ZLP_TS_PRINT_ANALYSIS']}</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="col-sm-8">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{$values['ZLP_TS_NOTES_02']|nl2br}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>