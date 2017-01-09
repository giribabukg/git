<!-- mockup -->
<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_MC_AMOUNT')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_MC_AMOUNT']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MC_PURCHASING')}</strong></td>
					<td>{$values['ZLP_MC_PURCHASING']}</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="col-sm-8">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th>{$text->__('ZLP_MC_NOTES')}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{$values['ZLP_MC_NOTES']|nl2br}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>