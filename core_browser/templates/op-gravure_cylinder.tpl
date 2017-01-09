<!-- gravure cylinder -->
<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th>{$text->__('ZLP_AG_F_COLOR_01')}</th>
					<th>{$text->__('ZLP_GC_USAGE_01')}</th>
					<th>{$text->__('ZLP_GC_PROCESS_01')}</th>
					<th>{$text->__('ZLP_AG_F_CLICHE_NUMBER_01')}</th>
					<th>{$text->__('ZLP_AG_NEW_01')}</th>
					<th>{$text->__('ZLP_GC_SCREEN_DEPTH_01')}</th>
					<th>{$text->__('ZLP_AG_F_REMARK_01')}</th>
					<th>{$text->__('ZLP_GC_NOMINALSIZE_01')}</th>
					<th>{$text->__('ZLP_GC_CODE_01')}</th>
					<th>{$text->__('ZLP_GC_PROOF_01')}</th>
				</tr>
			</thead>
			{if $colorCount > 0}
			<tbody>
				{for $cid=1 to $colorCount}
				{$id = {$cid|str_pad:2:0:$smarty.const.STR_PAD_LEFT}}
				<tr>
					<td class="swatch" style='background-color:#{$values["ColorValue_$id"]};'>{$values["ZLP_AG_F_COLOR_$id"]}</td>
					<td>{$values["ZLP_GC_USAGE_$id"]}</td>
					<td>{$values["ZLP_GC_PROCESS_$id"]}</td>
					<td>{$values["ZLP_AG_F_CLICHE_NUMBER_$id"]}</td>
					<td>{$values["ZLP_AG_NEW_$id"]}</td>
					<td>{$values["ZLP_GC_SCREEN_DEPTH_$id"]}</td>
					<td>{$values["ZLP_AG_F_REMARK_$id"]}</td>
					<td>{$values["ZLP_GC_NOMINALSIZE_$id"]}</td>
					<td>{$values["ZLP_GC_CODE_$id"]}</td>
					<td>{$values["ZLP_GC_PROOF_$id"]}</td>
				</tr>
				{/for}
			</tbody>
			{/if}
		</table>
	</div>
</div>
<div class="row">
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_GC_STAGGERING')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_GC_STAGGERING']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GC_AMT_NEW')}</strong></td>
					<td>{$values['ZLP_GC_AMT_NEW']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GC_AMT_STOCK')}</strong></td>
					<td>{$values['ZLP_GC_AMT_STOCK']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GC_AMT_OLD')}</strong></td>
					<td>{$values['ZLP_GC_AMT_OLD']}</td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('ZLP_GC_OLD_CYL')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$values['ZLP_GC_OLD_CYL']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GC_OLD_CYL_DATE')}</strong></td>
					<td>{$values['ZLP_GC_OLD_CYL_DATE']|floatval|strtotime|date_format:$smarty.const.DATE_FORMAT}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GC_MATERIAL')}</strong></td>
					<td>{$values['ZLP_GC_MATERIAL']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('ZLP_GC_MATERIAL_DATE')}</strong></td>
					<td>{$values['ZLP_GC_MATERIAL_DATE']|floatval|strtotime|date_format:$smarty.const.DATE_FORMAT}</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="col-sm-4">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th><strong>{$text->__('ZLP_GC_NOTES')}</strong></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{$values['ZLP_GC_NOTES']|nl2br}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>