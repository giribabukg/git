<!-- step and repeat flexibles -->
<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_CAD_F_PRINT_HEIGHT')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_CAD_F_PRINT_HEIGHT']} mm</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GES_F_1UP_RADIAL')}</strong></td>
					<td>{$values['ZLP_GES_F_1UP_RADIAL']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GES_F_RAP_RADIAL')}</strong></td>
					<td>{$values['ZLP_GES_F_RAP_RADIAL']} mm</td>
				</tr>				
				<tr>
					<td><strong>{$text->__('ZLP_GES_F_STAG_RADIAL')}</strong></td>
					<td>{$values['ZLP_GES_F_STAG_RADIAL']} mm</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_AG_F_REPEAT_LENGTH')}</strong></td>
					<td>{$values['ZLP_AG_F_REPEAT_LENGTH']} mm</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GES_F_OVERALL_LENGTH')}</strong></td>
					<td>{$values['ZLP_GES_F_OVERALL_LENGTH']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GES_F_MOUNTED_01')}</strong></td>
					<td>{$values['ZLP_GES_F_MOUNTED_01']}</td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_CAD_F_PRINT_WIDTH')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_CAD_F_PRINT_WIDTH']} mm</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GES_F_1UP_AXIAL')}</strong></td>
					<td>{$values['ZLP_GES_F_1UP_AXIAL']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GES_F_RAP_AXIAL')}</strong></td>
					<td>{$values['ZLP_GES_F_RAP_AXIAL']} mm</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GES_F_STAG_AXIAL')}</strong></td>
					<td>{$values['ZLP_GES_F_STAG_AXIAL']} mm</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_F_TOTAL_PRINT_WIDTH')}</strong></td>
					<td>{$values['ZLP_CAD_F_TOTAL_PRINT_WIDTH']} mm</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GES_F_OVERALL_WIDTH')}</strong></td>
					<td>{$values['ZLP_GES_F_OVERALL_WIDTH']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GES_F_MOUNTED_02')}</strong></td>
					<td>{$values['ZLP_GES_F_MOUNTED_02']}</td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_CAD_F_DIE_CUT_NO')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_CAD_F_DIE_CUT_NO']} mm</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GES_F_1UP_TOTAL')}</strong></td>
					<td>{$values['ZLP_GES_F_1UP_TOTAL']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GES_F_SCAT_PRINT')}</strong></td>
					<td>{$values['ZLP_GES_F_SCAT_PRINT']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GES_F_LAYOUT_POS')}</strong></td>
					<td>{$values['ZLP_GES_F_LAYOUT_POS']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GES_F_OFFSET')}</strong></td>
					<td>{$values['ZLP_GES_F_OFFSET']} mm</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GES_GAP_F_CIRCUMFERENCE')}</strong></td>
					<td>{$values['ZLP_GES_GAP_F_CIRCUMFERENCE']} mm</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GES_F_MULTIUP')}</strong></td>
					<td>{$values['ZLP_GES_F_MULTIUP']}</td>
				</tr>
			</tbody>
		</table>

	</div>
</div> <!-- End row -->
<div class="row">
	<div class="col-sm-8">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th>{$text->__('ZLP_AG_F_COLOR_01')}</th>
					<th class="text-center">{$text->__('ZLP_GES_F_END_TO_END_1')}</th>
					<th class="text-center">{$text->__('ZLP_GES_F_STAG_CUT_1')}</th>
				</tr>
			</thead>
			{if $colorCount > 0}
			<tbody>
				{for $cid=1 to $colorCount}
				{$id = {$cid|str_pad:2:0:$smarty.const.STR_PAD_LEFT}}
				<tr>
					<td class="swatch" style='background-color:#{$values["ColorValue_$id"]};'>{$values["ZLP_AG_F_COLOR_$id"]}</td>
					<td class="text-center">{$values["ZLP_GES_F_END_TO_END_$id"]}</td>
					<td class="text-center">{$values["ZLP_GES_F_STAG_CUT_$id"]}</td>
				</tr>
				{/for}
			</tbody>
			{/if}
		</table>
	</div>
	
	<div class="col-sm-4">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th>{$text->__('ZLP_GES_F_NOTES')}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{$values['ZLP_GES_F_NOTES']|nl2br}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="8">{$text->__('MOTIF_ARRANGEMENT')}</th>
				</tr>
				<tr>
					<th>Lane 1</th>
					<th>Lane 2</th>
					<th>Lane 3</th>
					<th>Lane 4</th>
					<th>Lane 5</th>
					<th>Lane 6</th>
					<th>Lane 7</th>
					<th>Lane 8</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{$values['ZLP_GES_F_1UP_MIX_B1P1']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B2P1']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B3P1']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B4P1']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B5P1']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B6P1']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B7P1']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B8P1']}</td>
				</tr>
				<tr>
					<td>{$values['ZLP_GES_F_1UP_MIX_B1P2']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B2P2']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B3P2']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B4P2']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B5P2']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B6P2']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B7P2']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B8P2']}</td>
				</tr>
				<tr>
					<td>{$values['ZLP_GES_F_1UP_MIX_B1P3']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B2P3']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B3P3']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B4P3']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B5P3']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B6P3']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B7P3']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B8P3']}</td>
				</tr>
				<tr>
					<td>{$values['ZLP_GES_F_1UP_MIX_B1P4']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B2P4']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B3P4']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B4P4']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B5P4']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B6P4']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B7P4']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B8P4']}</td>
				</tr>
				{if $values['ZLP_GES_F_1UP_MIX_B1P5'] != ''}
				<tr>
					<td>{$values['ZLP_GES_F_1UP_MIX_B1P5']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B2P5']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B3P5']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B4P5']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B5P5']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B6P5']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B7P5']}</td>
					<td>{$values['ZLP_GES_F_1UP_MIX_B8P5']}</td>
				</tr>
				{/if}
			</tbody>
		</table>
	</div>
</div>
<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_CAD_F_SCANNING_MARK')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_CAD_F_SCANNING_MARK']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_F_SIZE')}</strong></td>
					<td>{$values['ZLP_CAD_F_SIZE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_F_COLOR')}</strong></td>
					<td>{$values['ZLP_CAD_F_COLOR']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_F_POSITION')}</strong></td>
					<td>{$values['ZLP_CAD_F_POSITION']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_F_WHITE_UNDERLAY')}</strong></td>
					<td>{$values['ZLP_CAD_F_WHITE_UNDERLAY']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_CAD_F_KEY_MARK')}</strong></td>
					<td>{$values['ZLP_CAD_F_KEY_MARK']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GES_F_1UP_MARK')}</strong></td>
					<td>{$values['ZLP_GES_F_1UP_MARK']}</td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_GES_F_ALIGNMENT_BAR')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_GES_F_ALIGNMENT_BAR']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GES_F_ALIGNMENT_BAR_SIZE')}</strong></td>
					<td>{$values['ZLP_GES_F_ALIGNMENT_BAR_SIZE']} mm</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GES_F_ALIGNMENT_BAR_COLOR')}</strong></td>
					<td>{$values['ZLP_GES_F_ALIGNMENT_BAR_COLOR']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GES_F_ALIGNMENT_BAR_POS')}</strong></td>
					<td>{$values['ZLP_GES_F_ALIGNMENT_BAR_POS']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_PULL_BANDS_TYPE')}</strong></td>
					<td>{$values['ZLP_MT_PULL_BANDS_TYPE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GES_F_REG_MARK_TYPE')}</strong></td>
					<td>{$values['ZLP_GES_F_REG_MARK_TYPE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GES_F_REG_MARK_POS')}</strong></td>
					<td>{$values['ZLP_GES_F_REG_MARK_POS']}</td>
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
	
</div>