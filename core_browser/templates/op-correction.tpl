<!-- correction -->
<div class="row">
	<div class="col-sm-6">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('CORRECTION_ON')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$operation['USR00']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('CORRECTION_PER')}</strong></td>
					<td>{$operation['USR02']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('CORRECTION_DATE')}</strong></td>
					<td>{$operation['USR08']|strtotime|date_format:$smarty.const.DATE_FORMAT}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('CORRECTION_NO')}</strong></td>
					<td>{$operation['USR04']|intval}</td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<div class="col-sm-6">
		<table class="table table-striped table-hover table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-8 col-md-6 col-lg-5"><strong>{$text->__('CORRECTION_FROM')}</strong></td>
					<td class="col-sm-4 col-md-6 col-lg-7">{$operation['USR01']}</td>
				</tr>
				<tr>
					<td><strong>{$text->__('CORRECTION_COMPLETED')}</strong></td>
					<td>{$operation['USR10']}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

{if $values['ZLP_CORR_APPR_MEMO'] != ''}
<div class="row">
	<div class="col-sm-12">
		<table class="table table-condensed">
			<tbody>
				<tr>
					<td class="col-sm-12">
						{$values['ZLP_CORR_APPR_MEMO']|nl2br}
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
{/if}