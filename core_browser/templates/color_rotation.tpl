<!-- colour rotation -->
<div id="colorrotation" class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th colspan="10" class="header"><span class="fa fa-tint"></span>&nbsp;&nbsp;{$text->__('COLOR_ROTATION')}</th>
				</tr>
				<tr>
					<th>{$text->__('ZLP_AG_F_COLOR_01')}</th>
					<th>{$text->__('ZLP_AG_F_LINE_SCREEN_01')}</th>
					<th>{$text->__('ZLP_AG_F_CONT_SCR_CT_01')}</th>
					<th>{$text->__('ZLP_AG_F_TECH_SCR_CT_01')}</th>
					<th>{$text->__('ZLP_AG_F_DOT_TYPE_01')}</th>
					<th>{$text->__('ZLP_AG_F_ANGLE_01')}</th>
					<th>{$text->__('ZLP_AG_F_DISTORTION_L_01')}</th>
					<th>{$text->__('ZLP_AG_F_DISTORTION_C_01')}</th>
					<th>{$text->__('ZLP_AG_NEW_01')}</th>
					<th class="text-center">{$text->__('ZLP_AG_F_CLICHE_NUMBER_01')}</th>					
				</tr>
				<tr>
					<th>&nbsp;</th>
					<th>{$text->__('CDI_ZLP_AG_F_PLATE_TYPE_01')}</th>
					<th>{$text->__('CDI_ZLP_AG_F_PLATE_THICKNESS_01')}</th>
					<th>{$text->__('ZLP_FP_DIGI_CAP_01')}</th>
					<th>{$text->__('ZLP_FP_NO_OF_PLATES_01')}</th>
					<th colspan="5">{$text->__('ZLP_AG_F_REMARK_01')}</th>
				</tr>
			</thead>
			{if $colorCount > 0}
			<tbody>
				{for $cid=1 to $colorCount}
				{$id = {$cid|str_pad:2:0:$smarty.const.STR_PAD_LEFT}}
				<tr>
					<td rowspan="2" class="swatch" style='background-color:#{$values["ColorValue_$id"]};'>{$values["ZLP_AG_F_COLOR_$id"]}</td>
					<td>{$values["ZLP_AG_F_LINE_SCREEN_$id"]}</td>
					<td>{$values["ZLP_AG_F_CONT_SCR_CT_$id"]}</td>
					<td>{$values["ZLP_AG_F_TECH_SCR_CT_$id"]}</td>
					<td>{$values["ZLP_AG_F_DOT_TYPE_$id"]}</td>
					<td>{$values["ZLP_AG_F_ANGLE_$id"]}</td>
					<td>{$values["ZLP_AG_F_DISTORTION_L_$id"]}</td>
					<td>{$values["ZLP_AG_F_DISTORTION_C_$id"]}</td>
					<td>{$values["ZLP_AG_NEW_$id"]}</td>
					<td class="text-center">{$values["ZLP_AG_F_CLICHE_NUMBER_$id"]}</td>					
				</tr>
				<tr>
					<td>{$values["ZLP_AG_F_PLATE_TYPE_$id"]}</td>
					<td>{$values["ZLP_AG_F_PLATE_THICKNESS_$id"]}</td>
					<td>{$values["ZLP_FP_DIGI_CAP_$id"]}</td>
					<td>{$values["ZLP_FP_NO_OF_PLATES_$id"]}</td>
					<td colspan="5"><em>{$values["ZLP_AG_F_REMARK_$id"]}</em></td>
				</tr>
				{/for}
			</tbody>
			{/if}
		</table>
	</div>
</div>