<!-- flexo plate -->
<div class="row">
	<div class="col-sm-6">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_FP_NO_DIGITAL_PLATES')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_FP_NO_DIGITAL_PLATES']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_FP_SIZE_01')}</strong></td>
					<td>{$values['ZLP_FP_SIZE_01']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_FP_NO_ANALOG_PLATES')}</strong></td>
					<td>{$values['ZLP_FP_NO_ANALOG_PLATES']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_FP_SIZE_02')}</strong></td>
					<td>{$values['ZLP_FP_SIZE_02']}</td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<div class="col-sm-6">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_FP_FILM_EXPOSURE')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_FP_FILM_EXPOSURE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_FP_SIZE_03')}</strong></td>
					<td>{$values['ZLP_FP_SIZE_03']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_FP_PLATE_CATEGORY')}</strong></td>
					<td>{$values['ZLP_FP_PLATE_CATEGORY']}</td>
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
					<th>{$text->__('ZLP_AG_F_COLOR_01')}</th>
					<th>{$text->__('ZLP_AG_F_PLATE_TYPE_01')}</th>
					<th>{$text->__('ZLP_AG_F_PLATE_THICKNESS_01')}</th>
					<th>{$text->__('ZLP_FP_MIN_DOT_HELD_01')}</th>
					<th>{$text->__('ZLP_MT_RELIEF_DEPTH')}</th>
					<th>{$text->__('ZLP_FP_DIGI_CAP_01')}</th>
					<th>{$text->__('ZLP_FP_NO_OF_PLATES_01')}</th>
					<th>{$text->__('ZLP_FP_ROTATION_01')}</th>
					<th>{$text->__('ZLP_GES_F_STAG_CUT_01')}</th>
					<th>{$text->__('ZLP_FP_OUTPUT_01')}</th>
					<th>{$text->__('ZLP_FP_FLAT_TOP_01')}</th>
					<th>{$text->__('ZLP_FP_DIGITAL_ANALOGUE_01')}</th>
					<th>{$text->__('ZLP_DO_DGC_01')}</th>
					<th>{$text->__('ZLP_FP_REMARKS_01')}</th>
				</tr>
			</thead>
			{if $colorCount > 0}
			<tbody>
				{for $cid=1 to $colorCount}
				{$id = {$cid|str_pad:2:0:$smarty.const.STR_PAD_LEFT}}
				<tr>
					<td class="swatch" style='background-color:#{$values["ColorValue_$id"]};'>{$values["ZLP_AG_F_COLOR_$id"]}</td>
					<td>{$values["ZLP_AG_F_PLATE_TYPE_$id"]}</td>
					<td>{$values["ZLP_AG_F_PLATE_THICKNESS_$id"]}</td>
					<td>{$values["ZLP_FP_MIN_DOT_HELD_$id"]}</td>
					<td>{$values["ZLP_MT_RELIEF_DEPTH_$id"]}</td>
					<td>{$values["ZLP_FP_DIGI_CAP_$id"]}</td>
					<td>{$values["ZLP_FP_NO_OF_PLATES_$id"]}</td>
					<td>{$values["ZLP_FP_ROTATION_$id"]}</td>
					<td>{$values["ZLP_GES_F_STAG_CUT_$id"]}</td>
					<td>{$values["ZLP_FP_OUTPUT_$id"]}</td>
					<td>{$values["ZLP_FP_FLAT_TOP_$id"]}</td>
					<td>{$values["ZLP_FP_DIGITAL_ANALOGUE_$id"]}</td>
					<td>{$values["ZLP_DO_DGC_$id"]}</td>
					<td>{$values["ZLP_FP_REMARKS_$id"]} {$values["ZLP_AG_F_REMARK_$id"]}</td>
				</tr>
				{/for}
			</tbody>
			{/if}
		</table>
	</div>
	
</div> <!-- End row -->