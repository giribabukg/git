<!-- proof 1 -->
<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_AV_TEMPLATE')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_AV_TEMPLATE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AV_TEXT')}</strong></td>
					<td>{$values['ZLP_AV_TEXT']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AV_POSITION')}</strong></td>
					<td>{$values['ZLP_AV_POSITION']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AV_FONT_SIZE')}</strong></td>
					<td>{$values['ZLP_AV_FONT_SIZE']}</td>
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
					<td>{$values['ZLP_AV_NOTES_02']|nl2br}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>