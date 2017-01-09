<!-- mounting -->
<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_MT_MOUNTING_TYPE')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_MT_MOUNTING_TYPE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_PRESS_PROOF')}</strong></td>
					<td>{$values['ZLP_MT_PRESS_PROOF']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_PRESS_PROOF_TYPE')}</strong></td>
					<td>{$values['ZLP_MT_PRESS_PROOF_TYPE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_PLOTTING_ON')}</strong></td>
					<td>{$values['ZLP_MT_PLOTTING_ON']}</td>
				</tr>
				
			</tbody>
		</table>

	</div>
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_MT_MOUNTINGS_NO')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_MT_MOUNTINGS_NO']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_FOILS_HARD_NO')}</strong></td>
					<td>{$values['ZLP_MT_FOILS_HARD_NO']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_FOILS_FOAM_NO')}</strong></td>
					<td>{$values['ZLP_MT_FOILS_FOAM_NO']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_CLICHES_NOT_MOUNTED')}</strong></td>
					<td>{$values['ZLP_MT_CLICHES_NOT_MOUNTED']}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th>{$text->__('ZLP_MT_REMARKS_01')}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{$values['ZLP_MT_REMARKS_01']|nl2br}</td>
				</tr>
			</tbody>
		</table>

	</div>
</div> <!-- End row -->

<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th>{$text->__('ZLP_AG_F_COLOR_01')}</th>
					<th>{$text->__('ZLP_AG_F_PLATE_TYPE_01')}</th>
					<th>{$text->__('ZLP_AG_F_PLATE_THICKNESS_01')}</th>
					<th>{$text->__('ZLP_MT_FILM_HEIGHT_FIX')}</th>
					<th>{$text->__('ZLP_MT_TOTAL_CLICHES_SET_UP')}</th>
					<th>{$text->__('ZLP_MT_MOUNTING_FOIL')}</th>
					<th>{$text->__('ZLP_MT_ADHESIVE_FOIL')}</th>
					<th>{$text->__('ZLP_MT_FOAM')}</th>
					<th>{$text->__('ZLP_MT_REAL_CLICHE_HEIGHT')}</th>
					<th>{$text->__('ZLP_MT_RELIEF_DEPTH')}</th>
					<th>{$text->__('ZLP_AG_F_CLICHE_NUMBER_01')}</th>
					<th>{$text->__('ZLP_AG_F_LINE_SCREEN_01')}</th>
					<th>{$text->__('ZLP_AG_F_DISTORTION_L_01')}</th>
					<th>{$text->__('ZLP_AG_F_DISTORTION_C_01')}</th>
					<th>{$text->__('ZLP_AG_F_REMARK_01')}</th>
					<th>{$text->__('ZLP_AG_F_PATCH_01')}</th>
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
					<td>{$values["ZLP_MT_FILM_HEIGHT_FIX_$id"]}</td>
					<td>{$values["ZLP_MT_TOTAL_CLICHES_SET_UP_$id"]}</td>
					<td>{$values["ZLP_MT_MOUNTING_FOIL_$id"]}</td>
					<td>{$values["ZLP_MT_ADHESIVE_FOIL_$id"]}</td>
					<td>{$values["ZLP_MT_FOAM_$id"]}</td>
					<td>{$values["ZLP_MT_REAL_CLICHE_HEIGHT_$id"]}</td>
					<td>{$values["ZLP_MT_RELIEF_DEPTH_$id"]}</td>
					<td>{$values["ZLP_AG_F_CLICHE_NUMBER_$id"]}</td>
					<td>{$values["ZLP_AG_F_LINE_SCREEN_$id"]}</td>
					<td>{$values["ZLP_AG_F_DISTORTION_L_$id"]}</td>
					<td>{$values["ZLP_AG_F_DISTORTION_C_$id"]}</td>
					<td>{$values["ZLP_AG_F_REMARK_$id"]}</td>
					<td>{$values["ZLP_AG_F_PATCH_$id"]}</td>
				</tr>
				{/for}
			</tbody>
			{/if}
		</table>
	</div>
</div><!-- End row -->

<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="2">{$text->__('MOUNTING_FOIL')}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_MT_FOIL_WIDTH_FIX')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_MT_FOIL_WIDTH_FIX']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_FOIL_WIDTH_MIN')}</strong></td>
					<td>{$values['ZLP_MT_FOIL_WIDTH_MIN']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_FOIL_WIDTH_MAX')}</strong></td>
					<td>{$values['ZLP_MT_FOIL_WIDTH_MAX']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_FOIL_HEIGHT_FIX')}</strong></td>
					<td>{$values['ZLP_MT_FOIL_HEIGHT_FIX']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_FOIL_HEIGHT_MIN')}</strong></td>
					<td>{$values['ZLP_MT_FOIL_HEIGHT_MIN']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_FOIL_HEIGHT_MAX')}</strong></td>
					<td>{$values['ZLP_MT_FOIL_HEIGHT_MAX']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_FOIL_CUT_FROM')}</strong></td>
					<td>{$values['ZLP_MT_FOIL_CUT_FROM']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_GALLEY_PROOF_MOUNTING')}</strong></td>
					<td>{$values['ZLP_MT_GALLEY_PROOF_MOUNTING']}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="2">{$text->__('MOUNTING_SLAT')}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_MT_CLIP_SYSTEM')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_MT_CLIP_SYSTEM']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_SLAT_TYPE_FRONT')}</strong></td>
					<td>{$values['ZLP_MT_SLAT_TYPE_FRONT']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_SLAT_TYPE_BACK')}</strong></td>
					<td>{$values['ZLP_MT_SLAT_TYPE_BACK']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_FOIL_ARRANGEMENT')}</strong></td>
					<td>{$values['ZLP_MT_FOIL_ARRANGEMENT']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_CENTERLINE')}</strong></td>
					<td>{$values['ZLP_MT_CENTERLINE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_SLAT_FIXATION')}</strong></td>
					<td>{$values['ZLP_MT_SLAT_FIXATION']}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th>{$text->__('ZLP_MT_REMARKS_02')}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{$values['ZLP_MT_REMARKS_02']|nl2br}</td>
				</tr>
			</tbody>
		</table>
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="2">{$text->__('TEXTILE_TAPE')}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_MT_TEXTILE_TAPE_COLOR')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_MT_TEXTILE_TAPE_COLOR']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_TEXTILE_TAPE_WIDTH')}</strong></td>
					<td>{$values['ZLP_MT_TEXTILE_TAPE_WIDTH']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_TEXTILE_TAPE_STAND')}</strong></td>
					<td>{$values['ZLP_MT_TEXTILE_TAPE_STAND']}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div> <!-- End row -->

<div class="row">
	<div class="col-sm-4">
		
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="2">{$text->__('MOUNTING_FOIL_CONFIGURATION')}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_MT_DIST_SLAT_TO_CUT')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_MT_DIST_SLAT_TO_CUT']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_DIST_SLAT_TO_CUT')}</strong></td>
					<td>{$values['ZLP_MT_DIST_FOIL_TO_CUT']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_DIST_LEFT_RIGHT_TO_CUT')}</strong></td>
					<td>{$values['ZLP_MT_DIST_LEFT_RIGHT_TO_CUT']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_DIST_SLAT_TO_PRINT')}</strong></td>
					<td>{$values['ZLP_MT_DIST_SLAT_TO_PRINT']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_GLUEING_EDGE')}</strong></td>
					<td>{$values['ZLP_MT_GLUEING_EDGE']}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4">
		
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="2">{$text->__('PULL_BANDS')}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('PULL_BANDS')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_GES_F_ALIGNMENT_BAR']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_PULL_BANDS_WIDTH')}</strong></td>
					<td>{$values['ZLP_MT_PULL_BANDS_WIDTH']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_PULL_BANDS_THICKNESS')}</strong></td>
					<td>{$values['ZLP_MT_PULL_BANDS_THICKNESS']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GES_F_ALIGNMENT_BAR_POS')}</strong></td>
					<td>{$values['ZLP_GES_F_ALIGNMENT_BAR_POS']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_PULL_BANDS_TYPE')}</strong></td>
					<td>{$values['ZLP_MT_PULL_BANDS_TYPE']}</td>
				</tr>
			</tbody>
		</table>
		
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="2">{$text->__('ZLP_GES_F_PRINT_CTRL_ELEMENTS')}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{$values['ZLP_GES_F_PRINT_CTRL_ELEMENTS']|nl2br}</td>
				</tr>
				{if $values['ZLP_MT_PULL_BANDS_PRINTCTRL_02'] != ''}
				<tr>
					<td>{$values['ZLP_MT_PULL_BANDS_PRINTCTRL_02']}</td>
				</tr>
				{/if}
				{if $values['ZLP_MT_PULL_BANDS_PRINTCTRL_03'] != ''}
				<tr>
					<td>{$values['ZLP_MT_PULL_BANDS_PRINTCTRL_03']}</td>
				</tr>
				{/if}
			</tbody>
		</table>
	</div>
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="2">{$text->__('EYES_RIVETS')}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_MT_EYES')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_MT_EYES']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_EYES_DISTANCE')}</strong></td>
					<td>{$values['ZLP_MT_EYES_DISTANCE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_EYES_SIZE')}</strong></td>
					<td>{$values['ZLP_MT_EYES_SIZE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_EYES_DIST_FROM_FOIL')}</strong></td>
					<td>{$values['ZLP_MT_EYES_DIST_FROM_FOIL']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_EYES_FOR_ARCHIVING')}</strong></td>
					<td>{$values['ZLP_MT_EYES_FOR_ARCHIVING']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_EYES_DISTANCE_ARCHIVE')}</strong></td>
					<td>{$values['ZLP_MT_EYES_DISTANCE_ARCHIVE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_EYES_SIZE_ARCHIVE')}</strong></td>
					<td>{$values['ZLP_MT_EYES_SIZE_ARCHIVE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_EYES_DIST_FOIL_ARCHIVE')}</strong></td>
					<td>{$values['ZLP_MT_EYES_DIST_FOIL_ARCHIVE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_RIVET')}</strong></td>
					<td>{$values['ZLP_MT_RIVET']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_RIVET_DISTANCE')}</strong></td>
					<td>{$values['ZLP_MT_RIVET_DISTANCE']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_RIVET_DISTANCE_WIDTH')}</strong></td>
					<td>{$values['ZLP_MT_RIVET_DISTANCE_WIDTH']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_RIVET_DIST_FROM_FOIL')}</strong></td>
					<td>{$values['ZLP_MT_RIVET_DIST_FROM_FOIL']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_DRAW_ORIGIN')}</strong></td>
					<td>{$values['ZLP_MT_DRAW_ORIGIN']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_MT_PACKAGING_TYPE')}</strong></td>
					<td>{$values['ZLP_MT_PACKAGING_TYPE']}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div> <!-- End row -->