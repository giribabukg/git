<!-- step and repeat corrugated -->
<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_CAD_F_DIE_CUT_NO')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_CAD_F_DIE_CUT_NO']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GES_F_1UP_TOTAL')}</strong></td>
					<td>{$values['ZLP_GES_F_1UP_TOTAL']}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_GES_F_MULTIUP')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_GES_F_MULTIUP']}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_GES_F_1UP_RADIAL')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_GES_F_1UP_RADIAL']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GES_F_1UP_AXIAL')}</strong></td>
					<td>{$values['ZLP_GES_F_1UP_AXIAL']}</td>
				</tr>
			</tbody>
		</table>
	</div>
				
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_GES_F_RAP_RADIAL')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_GES_F_RAP_RADIAL']} mm</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GES_F_RAP_AXIAL')}</strong></td>
					<td>{$values['ZLP_GES_F_RAP_AXIAL']} mm</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_GES_F_STAG_RADIAL')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_GES_F_STAG_RADIAL']} mm</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GES_F_STAG_AXIAL')}</strong></td>
					<td>{$values['ZLP_GES_F_STAG_AXIAL']} mm</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_GES_F_REG_MARK_TYPE')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_GES_F_REG_MARK_TYPE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GES_F_REG_MARK_POS')}</strong></td>
					<td>{$values['ZLP_GES_F_REG_MARK_POS']} mm</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GES_F_ALIGNMENT_BAR')}</strong></td>
					<td>{$values['ZLP_GES_F_ALIGNMENT_BAR']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GES_F_ALIGNMENT_BAR_POS')}</strong></td>
					<td>{$values['ZLP_GES_F_ALIGNMENT_BAR_POS']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GES_C_1UP_MARK')}</strong></td>
					<td>{$values['ZLP_GES_C_1UP_MARK']}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th>{$text->__('ZLP_GES_F_PRINT_CTRL_ELEMENTS')}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{$values['ZLP_GES_F_PRINT_CTRL_ELEMENTS']|nl2br}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th>{$text->__('ZLP_GES_C_NOTES')}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{$values['ZLP_GES_C_NOTES']|nl2br}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div> <!-- End row -->