<!-- photography -->
<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_PH_CONCEPT')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_PH_CONCEPT']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_PH_PICTURES')}</strong></td>
					<td>{$values['ZLP_PH_PICTURES']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_PH_BRIEFING')}</strong></td>
					<td>{$values['ZLP_PH_BRIEFING']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_PH_REFJOB')}</strong></td>
					<td>{$values['ZLP_PH_REFJOB']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_PH_DIRECTOR')}</strong></td>
					<td>{$values['ZLP_PH_DIRECTOR']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_PH_ELEMENTS')}</strong></td>
					<td>{$values['ZLP_PH_ELEMENTS']}</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="col-sm-8">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th>{$text->__('ZLP_PH_NOTES')}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{$values['ZLP_PH_NOTES']|nl2br}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>