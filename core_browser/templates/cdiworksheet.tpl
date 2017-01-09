	{if $errorMsg != ''}
	<div class="page-header alert alert-danger">
		<h4><span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;&nbsp;{$text->__('ERROR_OCCURRED')}:</h4>
		<p>{$errorMsg} (#{$errorCode})</p>
	</div>
	{else}
	{strip}
	
	<div class="page-header">
		<div class="row">
			<div class="col-xs-8">
            	<h3><strong>{$text->__('CDI_WORKSHEET_FOR_ORDER')}: {$serviceOrderHead['ORDERID']|intval}</strong></h3>
            	<h5>{$text->__($salesOrderItem['USAGE'])}</h5>
            </div>
            <div class="col-xs-4">
            	<p class="text-right"><img src="{$smarty.const.WWW_TOP}/templates/images/schawk_logo.png" width="180" /></p>
			</div>
		</div>
	</div>
	
	<div class="help-block"><br/></div>
	
	<div id="orderhead">
		<div class="row">
			<div class="col-xs-6">
				<table class="table table-condensed">
					<tbody>
						<tr>
							<td class="col-xs-3 col-sm-4"><strong>{$text->__('CONTACTS_AG')}</strong></td>
							<td class="col-xs-9 col-sm-8">{if $contacts['AG']['NAME_LIST'] != ''}{$contacts['AG']['NAME_LIST']}{else}{$contacts['AG']['NAME']}{/if}{if $contacts['AG']['ADRESSDATA'] != ''}<br /><small>{$contacts['AG']['ADRESSDATA']|regex_replace:"/\s+/":" "}</small>{/if}</td>
						</tr>
						<tr>
							<td><strong>{$text->__('PARTNERS_ZN')}</strong></td>
							<td>{if $partners['ZN']['NAME_LIST'] != ''}{$partners['ZN']['NAME_LIST']}{else}{$partners['ZN']['NAME']}{/if}{if $partners['ZN']['ADRESSDATA'] != ''}<br /><small>{$partners['ZN']['ADRESSDATA']|regex_replace:"/\s+/":" "}</small>{/if}</td>
						</tr>
						<tr>
							<td><strong>{$text->__('DESCRIPTION')}</strong></td>
							<td>{$serviceOrderHead['SHORT_TEXT']}</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="col-xs-6">
				<table class="table table-condensed">
					<tbody>
						<tr>
							<td class="col-xs-3 col-sm-4"><strong>{$text->__('FINISH_DATE')}</strong></td>
							<td colspan="2" class="col-xs-9 col-sm-8">{$operationKey['50300'][0]['end_ts']}</td>
						</tr>
						<tr>
							<td><strong>{$text->__('PERSONAL_NAME')}</strong></td>
							<td class="col-xs-5">{if $operationKey['50110'][0]['PERS_NAME'] != ','}{$operationKey['50110'][0]['PERS_NAME']}{/if}</td>
							<td class="col-xs-4 underlined">&nbsp;</td>
						</tr>
						<tr>
							<td><strong>{$text->__('CONTACTS_ZM')}</strong></td>
							<td class="col-xs-5">{if $contacts['ZM']['NAME_LIST'] != ''}{$contacts['ZM']['NAME_LIST']}{else}{$contacts['ZM']['NAME']}{/if}</td>
							<td class="col-xs-4 underlined">&nbsp;</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		
		<div class="row">
			<div class="col-xs-12">
				<table class="table table-borderless table-small">
					<tbody>
						<tr>
							<td>{$text->__('ZLP_FP_NO_DIGITAL_PLATES')}:&nbsp;&nbsp;{$values['ZLP_FP_NO_DIGITAL_PLATES']}</td>
							<td>{$text->__('ZLP_FP_NO_ANALOG_PLATES')}:&nbsp;&nbsp;{$values['ZLP_FP_NO_ANALOG_PLATES']}</td>
							<td>{$text->__('ZLP_FP_FILM_EXPOSURE')}:&nbsp;&nbsp;{$values['ZLP_FP_FILM_EXPOSURE']}</td>
							<td class="text-right">{if $values['ZLP_AG_F_PRINT_METHOD'] == ''}{$text->__('FACE_PRINT')}{else}{$text->__($values['ZLP_AG_F_PRINT_METHOD'])}{/if}</td>
						</tr>
					</tbody>
				</table>
			
			</div>
		</div>
		
		<div class="row" id="cdiworksheet">
			<div class="col-xs-12">	
				<table class="table table-bordered  table-condensed table-small">
					<thead>
						<tr>
							<th>{$text->__('ZLP_AG_F_COLOR_01')}</th>
							<th>{$text->__('ZLP_DO_LENFILE_NAME_01')}<br />{$text->__('ZLP_DO_RESOLUTION_01')}<br />{$text->__('ZLP_FP_REMARKS_01')}</th>
							<th><br/>{$text->__('CDI_ZLP_MT_RELIEF_DEPTH')}</th>
							<th><br/>{$text->__('ZLP_FP_ROTATION_01')}</th>
							<th><br/>{$text->__('ZLP_FP_OUTPUT_01')}</th>
							<th>{$text->__('CDI_ZLP_FP_NO_OF_PLATES_01')}<br/>{$text->__('ZLP_FP_FLAT_TOP_01')}</th>
							<th>{$text->__('CDI_ZLP_AG_F_PLATE_TYPE_01')}<br/>{$text->__('CDI_ZLP_FP_DIGITAL_ANALOGUE_01')}</th>
							<th>{$text->__('CDI_ZLP_AG_F_PLATE_THICKNESS_01')}<br/>{$text->__('CDI_ZLP_GES_F_STAG_CUT_01')}</th>
							<th>{$text->__('PRODUCTION_NO')}</th>		
						</tr>
					</thead>
					{if $colorCount > 0}
					<tbody>
						{for $cid=1 to $colorCount}
						{$id = {$cid|str_pad:2:0:$smarty.const.STR_PAD_LEFT}}
						{if {$values["ZLP_FP_NO_OF_PLATES_$id"]} != '' && {$values["ZLP_FP_NO_OF_PLATES_$id"]} > 0}
						<tr>
							<td rowspan="3" style="height: 75px;">{$values["ZLP_AG_F_COLOR_$id"]}</td>
							<td colspan="4" style="height: 25px;">{$values["ZLP_DO_LENFILE_NAME_$id"]}</td>
							<td>{$values["ZLP_FP_NO_OF_PLATES_$id"]}</td>
							<td>{$values["ZLP_AG_F_PLATE_TYPE_$id"]}</td>
							<td>{$values["ZLP_AG_F_PLATE_THICKNESS_$id"]}</td>
							<td rowspan="3" class="col-xs-3 noborderright"></td>
						</tr>
						<tr>
							<td style="height: 25px;">{$values["ZLP_DO_RESOLUTION_$id"]}</td>
							<td>{$values["ZLP_MT_RELIEF_DEPTH_$id"]}</td>
							<td>{$values["ZLP_FP_ROTATION_$id"]}</td>
							<td>{$values["ZLP_FP_OUTPUT_$id"]}</td>
							<td>{$values["ZLP_FP_FLAT_TOP_$id"]}</td>
							<td>{$values["ZLP_FP_DIGITAL_ANALOGUE_$id"]}</td>
							<td>{$values["ZLP_GES_F_STAG_CUT_$id"]}</td>
						</tr>
						<tr>
							<td colspan="7" style="height: 25px;">{if $values["ZLP_FP_REMARKS_$id"] == '' && $values["ZLP_AG_F_REMARK_$id"] == ''}&nbsp;{else}{$values["ZLP_FP_REMARKS_$id"]} {$values["ZLP_AG_F_REMARK_$id"]}{/if}</td>
						</tr>
						{/if}
						{/for}
					</tbody>
					{/if}
				</table>
			</div>
		</div> <!-- End row -->
	
		<div class="row">
			<div class="col-xs-12">
				<table class="table table-borderless">
					<tbody>
						<tr>
							<td class="col-xs-1">&nbsp;</td>
							<td class="col-xs-3">&nbsp;</td>
							<td class="col-xs-8">&nbsp;</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		
		<div class="row">
			<div class="col-xs-12">
				<table class="table table-borderless">
					<tbody>
						<tr>
							<td class="col-xs-1">{$text->__('BATCH_NO')}:</td>
							<td class="col-xs-2 underlined">&nbsp;</td>
							<td class="col-xs-1">&nbsp;</td>
							<td class="col-xs-1">{$text->__('BOX_NO')}:</td>
							<td class="col-xs-2 underlined">&nbsp;</td>
							<td class="col-xs-1">&nbsp;</td>
							<td class="col-xs-1">{$text->__('TYPE')}:</td>
							<td class="col-xs-2 underlined">&nbsp;</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		
		<div class="row">
			<div class="col-xs-12">
				<table class="table table-borderless">
					<tbody>
						<tr>
							<td class="col-xs-1">{$text->__('BATCH_NO')}:</td>
							<td class="col-xs-2 underlined">&nbsp;</td>
							<td class="col-xs-1">&nbsp;</td>
							<td class="col-xs-1">{$text->__('BOX_NO')}:</td>
							<td class="col-xs-2 underlined">&nbsp;</td>
							<td class="col-xs-1">&nbsp;</td>
							<td class="col-xs-1">{$text->__('TYPE')}:</td>
							<td class="col-xs-2 underlined">&nbsp;</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		
		<div class="row">
			<div class="col-xs-12">
				<table class="table table-borderless">
					<tbody>
						<tr>
							<td class="col-xs-1">{$text->__('BATCH_NO')}:</td>
							<td class="col-xs-2 underlined">&nbsp;</td>
							<td class="col-xs-1">&nbsp;</td>
							<td class="col-xs-1">{$text->__('BOX_NO')}:</td>
							<td class="col-xs-2 underlined">&nbsp;</td>
							<td class="col-xs-1">&nbsp;</td>
							<td class="col-xs-1">{$text->__('TYPE')}:</td>
							<td class="col-xs-2 underlined">&nbsp;</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	
		<div class="row">
			<div class="col-xs-12">
				<table class="table table-borderless">
					<tbody>
						<tr>
							<td class="col-xs-2">{$text->__('CDI_IMAGING_OPERATOR')}:</td>
							<td class="col-xs-6 underlined">&nbsp;</td>
							<td class="col-xs-1">&nbsp;</td>
							<td class="col-xs-1">{$text->__('DATE')}:</td>
							<td class="col-xs-2 underlined">&nbsp;</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	
		<div class="row">
			<div class="col-xs-12">
				<table class="table table-borderless">
					<tbody>
						<tr>
							<td class="col-xs-2">{$text->__('EXPOSURE_OPERATOR')}:</td>
							<td class="col-xs-6 underlined">&nbsp;</td>
							<td class="col-xs-1">&nbsp;</td>
							<td class="col-xs-1">{$text->__('DATE')}:</td>
							<td class="col-xs-2 underlined">&nbsp;</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	
		<div class="row">
			<div class="col-xs-12">
				<table class="table table-borderless">
					<tbody>
						<tr>
							<td class="col-xs-2">{$text->__('QUALITY_CONTROL')}:</td>
							<td class="col-xs-6 underlined">&nbsp;</td>
							<td class="col-xs-1">&nbsp;</td>
							<td class="col-xs-1">{$text->__('DATE')}:</td>
							<td class="col-xs-2 underlined">&nbsp;</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	
		<div class="row">
			<div class="col-xs-12">
				<table class="table table-borderless">
					<tbody>
						<tr>
							<td class="col-xs-2">{$text->__('ZLP_FP_REMARKS_01')}:</td>
							<td class="col-xs-10 underlined">&nbsp;</td>
						</tr>
						<tr>
							<td></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td></td>
							<td class="underlined">&nbsp;</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	{/strip}
	{/if}