<!-- production art -->
<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_AW_BASED_ON')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_AW_BASED_ON']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AW_ADAPTION')}</strong></td>
					<td>{$values['ZLP_AW_ADAPTION']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AW_PURCHASE')}</strong></td>
					<td>{$values['ZLP_AW_PURCHASE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AW_COR_LAYOUT')}</strong></td>
					<td>{$values['ZLP_AW_COR_LAYOUT']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AW_COR_TEXT')}</strong></td>
					<td>{$values['ZLP_AW_COR_TEXT']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AW_COR_IMAGE')}</strong></td>
					<td>{$values['ZLP_AW_COR_IMAGE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_F_PRINT_HEIGHT')}</strong></td>
					<td>{$values['ZLP_CAD_F_PRINT_HEIGHT']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_F_PRINT_WIDTH')}</strong></td>
					<td>{$values['ZLP_CAD_F_PRINT_WIDTH']}</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="col-sm-8">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th>{$text->__('ZLP_AW_NOTES')}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{$values['ZLP_AW_NOTES']|nl2br}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>