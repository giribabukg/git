<!-- data output -->
<div class="row">
	<div class="col-sm-6">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_DO_PRINTING_TYPE')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_DO_PRINTING_TYPE']}</td>
				</tr>
				
				<tr>
					<td><strong>{$text->__('ZLP_DO_DGC_01')}</strong></td>
					<td>{$values['ZLP_DO_DGC']}</td>
				</tr>
				<!--
				<tr>
					<td><strong>{$text->__('ZLP_DO_PLATE_SLEEVE_01')}</strong></td>
					<td>{$values['ZLP_DO_PLATE_SLEEVE_01']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_DO_DATATYPE_01')}</strong></td>
					<td>{$values['ZLP_DO_DATATYPE_01']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_DO_RESOLUTION_01')}</strong></td>
					<td>{$values['ZLP_DO_RESOLUTION_01']}</td>
				</tr>
				-->
			</tbody>
		</table>
	</div>
	<div class="col-sm-6">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th>{$text->__('ZLP_DD_NOTES')}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{$values['ZLP_DD_NOTES']|nl2br}</td>
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
					<th>{$text->__('ZLP_DO_PLATE_SLEEVE_01')}</th>
					<th>{$text->__('ZLP_DO_DATATYPE_01')}</th>
					<th>{$text->__('ZLP_DO_RESOLUTION_01')}</th>
					<th>{$text->__('ZLP_AG_F_DISTORTION_L_01')}</th>
					<th>{$text->__('ZLP_AG_F_DISTORTION_C_01')}</th>
					<th>{$text->__('ZLP_GES_F_END_TO_END_01')}</th>
					<th>{$text->__('ZLP_GES_F_STAG_CUT_01')}</th>
					<th>{$text->__('ZLP_DO_PROD_PLANT_01')}</th>
				</tr>
			</thead>
			{if $colorCount > 0}
			<tbody>
				{for $cid=1 to $colorCount}
				{$id = {$cid|str_pad:2:0:$smarty.const.STR_PAD_LEFT}}
				<tr>
					<td rowspan="2" class="swatch" style='background-color:#{$values["ColorValue_$id"]};'>{$values["ZLP_AG_F_COLOR_$id"]}</td>
					<td>{$values["ZLP_DO_PLATE_SLEEVE_$id"]}</td>
					<td>{$values["ZLP_DO_DATATYPE_$id"]}</td>
					<td>{$values["ZLP_DO_RESOLUTION_$id"]}</td>
					<td>{$values["ZLP_AG_F_DISTORTION_L_$id"]}</td>
					<td>{$values["ZLP_AG_F_DISTORTION_C_$id"]}</td>
					<td>{$values["ZLP_GES_F_END_TO_END_$id"]}</td>
					<td>{$values["ZLP_GES_F_STAG_CUT_$id"]}</td>
					<td>{$values["ZLP_DO_PROD_PLANT_$id"]}</td>
				</tr>
				<tr>
					<td colspan="4">{$text->__('ZLP_DO_DGC_01')}: {$values["ZLP_DO_DGC_$id"]}</td>
					<td colspan="4">{$text->__('ZLP_DO_LENFILE_NAME_01')}: {$values["ZLP_DO_LENFILE_NAME_$id"]}</td>
				</tr>
				{/for}
			</tbody>
			{/if}
		</table>
	</div>
</div><!-- End row -->