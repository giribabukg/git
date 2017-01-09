<!-- artistic retouching -->
<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_AR_TEMPLATE1')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_AR_TEMPLATE1']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AR_TEMPLATE2')}</strong></td>
					<td>{$values['ZLP_AR_TEMPLATE2']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AR_TEMPLATE3')}</strong></td>
					<td>{$values['ZLP_AR_TEMPLATE3']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AR_REFJOB')}</strong></td>
					<td>{$values['ZLP_AR_REFJOB']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AR_MASK')}</strong></td>
					<td>{$values['ZLP_AR_MASK']}</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="col-sm-8">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th>{$text->__('ZLP_AR_NOTES')}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{$values['ZLP_AR_NOTES']|nl2br}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>