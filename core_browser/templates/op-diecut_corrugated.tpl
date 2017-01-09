<!-- diecut corrugated -->
<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_CAD_F_DATA_SOURCE')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_CAD_F_DATA_SOURCE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_F_DIE_CUT_NO')}</strong></td>
					<td>{$values['ZLP_CAD_F_DIE_CUT_NO']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_F_CUTTING_NO')}</strong></td>
					<td>{$values['ZLP_CAD_F_CUTTING_NO']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_F_TOOL_NO')}</strong></td>
					<td>{$values['ZLP_CAD_F_TOOL_NO']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_F_PREV_DIE_CUT_NO')}</strong></td>
					<td>{$values['ZLP_CAD_F_PREV_DIE_CUT_NO']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_F_PREV_JOB_NO')}</strong></td>
					<td>{$values['ZLP_CAD_F_PREV_JOB_NO']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_F_CAD_VIEW')}</strong></td>
					<td>{$values['ZLP_CAD_F_CAD_VIEW']}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_CAD_F_IN_LINE_TO')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_CAD_F_IN_LINE_TO']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_F_PASS_FILE')}</strong></td>
					<td>{$values['ZLP_CAD_F_PASS_FILE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_C_3D_CAD')}</strong></td>
					<td>{$values['ZLP_CAD_C_3D_CAD']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_C_CONSTRUCTION')}</strong></td>
					<td>{$values['ZLP_CAD_C_CONSTRUCTION']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_C_ADHERING')}</strong></td>
					<td>{$values['ZLP_CAD_C_ADHERING']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_F_PRINT_VIEW')}</strong></td>
					<td>{$values['ZLP_CAD_F_PRINT_VIEW']}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th>{$text->__('ZLP_CAD_F_NOTES')}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{$values['ZLP_CAD_F_NOTES']|nl2br}</td>
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
					<th colspan="2">{$text->__('PROCESSING_SACK')}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_CAD_C_RS1')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_CAD_C_RS1']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_C_SF_01')}</strong></td>
					<td>{$values['ZLP_CAD_C_SF_01']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_C_VS')}</strong></td>
					<td>{$values['ZLP_CAD_C_VS']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_C_SF_02')}</strong></td>
					<td>{$values['ZLP_CAD_C_SF_02']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_C_RS2')}</strong></td>
					<td>{$values['ZLP_CAD_C_RS2']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_C_ADH_BACK')}</strong></td>
					<td>{$values['ZLP_CAD_C_ADH_BACK']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_C_ADH_SIDE')}</strong></td>
					<td>{$values['ZLP_CAD_C_ADH_SIDE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_C_BOTTOM_HGT')}</strong></td>
					<td>{$values['ZLP_CAD_C_BOTTOM_HGT']}</td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="2">{$text->__('PROCESSING_WFK')}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_CAD_C_GLUE_LAP')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_CAD_C_GLUE_LAP']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_C_SIDE_01')}</strong></td>
					<td>{$values['ZLP_CAD_C_SIDE_01']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_C_SIDE_02')}</strong></td>
					<td>{$values['ZLP_CAD_C_SIDE_02']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_C_SIDE_03')}</strong></td>
					<td>{$values['ZLP_CAD_C_SIDE_03']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_C_SIDE_04')}</strong></td>
					<td>{$values['ZLP_CAD_C_SIDE_04']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_C_HEIGHT')}</strong></td>
					<td>{$values['ZLP_CAD_C_HEIGHT']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_C_LID')}</strong></td>
					<td>{$values['ZLP_CAD_C_LID']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_C_BOTTOM')}</strong></td>
					<td>{$values['ZLP_CAD_C_BOTTOM']}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4">&nbsp;</div>
	
</div> <!-- End row -->