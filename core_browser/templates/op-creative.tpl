<!-- creative -->
<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_CR_DESIGNTYPE')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_CR_DESIGNTYPE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CR_PURCHASE')}</strong></td>
					<td>{$values['ZLP_CR_PURCHASE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CR_DESIGNS')}</strong></td>
					<td>{$values['ZLP_CR_DESIGNS']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CR_BRIEFINGDATE')}</strong></td>
					<td>{$values['ZLP_CR_BRIEFINGDATE']|floatval|strtotime|date_format:$smarty.const.DATE_FORMAT}</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="col-sm-8">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th>{$text->__('ZLP_CR_NOTES')}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{$values['ZLP_CR_NOTES']|nl2br}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>