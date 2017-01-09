	{if $errorMsg != ''}
	<div class="page-header alert alert-danger">
		<h4><span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;&nbsp;{$text->__('ERROR_OCCURRED')}:</h4>
		<p>{$errorMsg} (#{$errorCode})</p>
	</div>
	{else}
	{strip}
	
	<div class="page-header">
		<div class="row">
			<div class="col-xs-8 col-sm-8">
            	<h3>{$serviceOrderHead['SHORT_TEXT']}</h3>
            	<h4>{$serviceOrderName}</h4>
            	<h5>{$salesOrderHead['PURCH_NO']}{if $salesOrderHead['PURCH_NO'] !='' && $salesOrderHead['po_date'] != ''} - {/if}{$salesOrderHead['po_date']}</h5>
            </div>
            <div class="col-xs-4 col-sm-4">
            	<h3 class="text-right">{$serviceOrderHead['ORDERID']|intval}<br/><img src="data:image/png;base64,{$serviceOrderBarcode}" /></h3>
            	<h5 class="text-right">{$text->__($salesOrderItem['USAGE'])}{if $values['ZLP_AG_REF_JOB'] != ''} ({$values['ZLP_AG_REF_JOB']|trim}){/if}</h5>
            	{if $values['ZLP_ACCOUNTING_TXT'] != ''}<h5 class="text-right">{$values['ZLP_ACCOUNTING_TXT']}</h5>{/if}
			</div>
		</div>
	</div>
	
	<div id="orderhead" class="row">
		<div class="col-sm-4">
			<table class="table table-striped table-hover table-condensed">
				<thead>
					<tr>
						<th colspan="2" class="header"><span class="fa fa-info-circle"></span>&nbsp;&nbsp;{$text->__('ORDER_DETAILS')}</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="col-xs-5 col-sm-6 col-md-4"><strong>{$text->__('SALES_ORDER')}</strong></td>
						<td class="col-xs-7 col-sm-6 col-md-8">{$serviceOrderHead['SALES_ORD']|intval} <img src="data:image/png;base64,{$salesOrderBarcode}" class="pull-right" /></td>
					</tr>
					<tr>
						<td><strong>{$text->__('SKU_MATERIAL_NUMBER')}</strong></td>
						<td>{$sku['MATNR']}</td>
					</tr>
					<tr>
						<td><strong>{$text->__('CUSTOMER_MATERIAL_NUMBER')}</strong></td>
						<td>{$custmatinfo['KDMAT']}</td>
					</tr>
					<tr>
						<td><strong>{$text->__('CUSTOMER_DESCRIPTION')}</strong></td>
						<td>{$custmatinfo['POSTX']}</td>
					</tr>
					<tr>
						<td><strong>{$text->__('PRINT_METHOD')}</strong></td>
						<td>{$values['ZLP_PRINT_METHOD']}</td>
					</tr>
					<tr>
						<td><strong>{$text->__('PRINT_MACHINE')}</strong></td>
						<td>{$values['ZLP_MTH_PRINTER_SPEC_NAME']}</td>
					</tr>
					<tr>
						<td><strong>{$text->__('DUE_DATE')}</strong></td>
						<td>{$serviceOrderHead['due_date']}</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-sm-4">
			<table class="table table-striped table-hover table-condensed">
				<thead>
					<tr>
						<th colspan="2" class="header"><span class="glyphicon glyphicon-phone-alt"></span>&nbsp;&nbsp;{$text->__('CONTACTS')}</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="col-xs-5 col-sm-6 col-md-4"><strong>{$text->__('CONTACTS_AG')}</strong></td>
						<td class="col-xs-7 col-sm-6 col-md-8">{if $contacts['AG']['NAME_LIST'] != ''}{$contacts['AG']['NAME_LIST']}{else}{$contacts['AG']['NAME']}{/if}{if $contacts['AG']['ADRESSDATA'] != ''}<br /><small>{$contacts['AG']['ADRESSDATA']|regex_replace:"/\s+/":" "}</small>{/if}</td>
					</tr>
					<tr>
						<td><strong>{$text->__('CONTACTS_AP')}</strong></td>
						<td>{if $contacts['AP']['NAME_LIST'] != ''}{$contacts['AP']['NAME_LIST']}{else}{$contacts['AP']['NAME']}{/if}</td>
					</tr>
					<tr>
						<td><strong>{$text->__('CONTACTS_WE')}</strong></td>
						<td>{if $contacts['WE']['NAME_LIST'] != ''}{$contacts['WE']['NAME_LIST']}{else}{$contacts['WE']['NAME']}{/if}{if $contacts['WE']['ADRESSDATA'] != ''}<br /><small>{$contacts['WE']['ADRESSDATA']|regex_replace:"/\s+/":" "}</small>{/if}</td>
					</tr>
					<tr>
						<td><strong>{$text->__('CONTACTS_ZM')}</strong></td>
						<td>{if $contacts['ZM']['NAME_LIST'] != ''}{$contacts['ZM']['NAME_LIST']}{else}{$contacts['ZM']['NAME']}{/if}</td>
					</tr>
					<tr>
						<td><strong>{$text->__('CONTACTS_VE')}</strong></td>
						<td>{if $contacts['VE']['NAME_LIST'] != ''}{$contacts['VE']['NAME_LIST']}{else}{$contacts['VE']['NAME']}{/if}</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-sm-4">
			<table class="table table-striped table-hover table-condensed">
				<thead>
					<tr>
						<th colspan="2" class="header"><span class="fa fa-user"></span>&nbsp;&nbsp;{$text->__('PARTNERS')}</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="col-xs-5 col-sm-6 col-md-4"><strong>{$text->__('PARTNERS_ZN')}</strong></td>
						<td class="col-xs-7 col-sm-6 col-md-8">{if $partners['ZN']['NAME_LIST'] != ''}{$partners['ZN']['NAME_LIST']}{else}{$partners['ZN']['NAME']}{/if}{if $partners['ZN']['ADRESSDATA'] != ''}<br /><small>{$partners['ZN']['ADRESSDATA']|regex_replace:"/\s+/":" "}</small>{/if}</td>
					</tr>
					<tr>
						<td><strong>{$text->__('PARTNERS_ZO')}</strong></td>
						<td>{if $partners['ZO']['NAME_LIST'] != ''}{$partners['ZO']['NAME_LIST']}{else}{$partners['ZO']['NAME']}{/if}{if $partners['ZN']['ADRESSDATA'] != ''}<br /><small>{$partners['ZO']['ADRESSDATA']|regex_replace:"/\s+/":" "}</small>{/if}</td>
					</tr>
					<tr>
						<td><strong>{$text->__('PARTNERS_ZR')}</strong></td>
						<td>{if $partners['ZR']['NAME_LIST'] != ''}{$partners['ZR']['NAME_LIST']}{else}{$partners['ZR']['NAME']}{/if}{if $partners['ZN']['ADRESSDATA'] != ''}<br /><small>{$partners['ZR']['ADRESSDATA']|regex_replace:"/\s+/":" "}</small>{/if}</td>
					</tr>
					<tr>
						<td><strong>{$text->__('PARTNERS_ZS')}</strong></td>
						<td>{if $partners['ZS']['NAME_LIST'] != ''}{$partners['ZS']['NAME_LIST']}{else}{$partners['ZS']['NAME']}{/if}{if $partners['ZS']['ADRESSDATA'] != ''}<br /><small>{$partners['ZS']['ADRESSDATA']|regex_replace:"/\s+/":" "}</small>{/if}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	
	{if $colorCount > 0}
		{include file='color_rotation.tpl'}
	{/if}
	
	{if $barcodeCount > 0}
		{include file='barcodes.tpl'}
	{/if}
	
	<div class="row">
		<div class="col-xs-6 col-sm-8"><h4 class="section" id="operationHeader"><span class="fa fa-tasks"></span>&nbsp;&nbsp;{$text->__('OPERATIONS')}</h4></div>
		<div class="col-xs-6 col-sm-4 text-right"><h6><a href="#" class="toggleCnf"><span class="toggleCnfIcon glyphicon glyphicon-chevron-right"></span>&nbsp;<span class="toggleCnfCount"></span>{$text->__('CONFIRMED_OPERATIONS')}</a></h6></div>
	</div>
	<div class="row">
		<div class="col-sm-12">	
			<div class="panel-group" id="accordion">
			{foreach $operations as $opIdx=>$operation}
				{if preg_match('/(^|\b)CNF/', $operation['SYSTEM_STATUS_TEXT']) && ($operation['STANDARD_TEXT_KEY'] != '10001' && $operation['STANDARD_TEXT_KEY'] != '10002') }
					{if $operation['STANDARD_TEXT_KEY'] == 'CORRECT' || $operation['STANDARD_TEXT_KEY'] == 'APPROVE'}
						{assign var="operationClass" value="hidden-print"}
					{else}
						{assign var="operationClass" value="text-muted"}
					{/if}
				{else}
					{assign var="operationClass" value=""}
				{/if}
				<div class="panel panel-default {$operationClass}">
					<div class="panel-heading row">
						<div class="col-sm-5">
							<h4 class="panel-title">
								<span class="panelhandle glyphicon glyphicon-chevron-right {$operationClass}"></span>
								<a data-toggle="collapse" href="#{$opIdx|cat:'phase'|md5}" class="handle">
									<span class="{$operationClass}">{$operation['ACTIVITY']} {$operation['DESCRIPTION']}</span>
								</a>
							</h4>
						</div>
						<div class="col-sm-2"><small class="text-muted">{$operation['WORK_CNTR']} ({$operation['PLANT']})</small></div>
						<div class="col-sm-1"><small class="text-muted">{$operation['PERS_NO']}</small></div>
						<div class="col-sm-2"><small class="text-muted">{if $operation['PERS_NAME'] != ','}{$operation['PERS_NAME']}{/if}</small></div>
						<div class="col-sm-2"><small class="text-muted">{$operation['end_ts']}</small></div>
					</div>
					<div id="{$opIdx|cat:'phase'|md5}" class="panel-collapse collapse">
						<div class="panel-body">
							{if $operation['STANDARD_TEXT_KEY'] == 10001}
								{include file='op-genspec_corrugated.tpl'}
							{/if}
							
							{if $operation['STANDARD_TEXT_KEY'] == 10002}
								{include file='op-genspec_flexibles.tpl'}
							{/if}
							
							{if $operation['STANDARD_TEXT_KEY'] == 10201}
								{include file='op-diecut_corrugated.tpl'}
							{/if}
							
							{if $operation['STANDARD_TEXT_KEY'] == 10202}
								{include file='op-diecut_flexibles.tpl'}
							{/if}
							
							{if $operation['STANDARD_TEXT_KEY'] == 10100}
								{include file='op-data_input.tpl'}
							{/if}
							
							{if $operation['STANDARD_TEXT_KEY'] == 30401}
								{include file='op-assembly_corrugated.tpl'}
							{/if}
							
							{if $operation['STANDARD_TEXT_KEY'] == 30402}
								{include file='op-assembly_flexibles.tpl'}
							{/if}
							
							{if $operation['STANDARD_TEXT_KEY'] == 30601}
								{include file='op-steprepeat_corrugated.tpl'}
							{/if}
							
							{if $operation['STANDARD_TEXT_KEY'] == 30501}
								{include file='op-barcode1.tpl'}
							{/if}
							{if $operation['STANDARD_TEXT_KEY'] == 30502}
								{include file='op-barcode2.tpl'}
							{/if}
							{if $operation['STANDARD_TEXT_KEY'] == 30503}
								{include file='op-barcode3.tpl'}
							{/if}
							{if $operation['STANDARD_TEXT_KEY'] == 30504}
								{include file='op-barcode4.tpl'}
							{/if}
							{if $operation['STANDARD_TEXT_KEY'] == 30505}
								{include file='op-barcode5.tpl'}
							{/if}
							{if $operation['STANDARD_TEXT_KEY'] == 30506}
								{include file='op-barcode6.tpl'}
							{/if}
							
							{if $operation['STANDARD_TEXT_KEY'] == 30300}
								{include file='op-colour_retouching.tpl'}
							{/if}
							
							{if $operation['STANDARD_TEXT_KEY'] == 50100}
								{include file='op-data_delivery.tpl'}
							{/if}
							
							{if $operation['STANDARD_TEXT_KEY'] == 50110}
								{include file='op-data_output.tpl'}
							{/if}
							
							{if $operation['STANDARD_TEXT_KEY'] == 50300}
								{include file='op-flexo_plate.tpl'}
							{/if}
							
							{if $operation['STANDARD_TEXT_KEY'] == 50200}
								{include file='op-offset_plate.tpl'}
							{/if}
							
							{if $operation['STANDARD_TEXT_KEY'] == 40101}
								{include file='op-proof1.tpl'}
							{/if}
							{if $operation['STANDARD_TEXT_KEY'] == 40102}
								{include file='op-proof2.tpl'}
							{/if}
							{if $operation['STANDARD_TEXT_KEY'] == 40103}
								{include file='op-proof3.tpl'}
							{/if}
							{if $operation['STANDARD_TEXT_KEY'] == 40104}
								{include file='op-proof4.tpl'}
							{/if}
							{if $operation['STANDARD_TEXT_KEY'] == 40105}
								{include file='op-proof5.tpl'}
							{/if}
							{if $operation['STANDARD_TEXT_KEY'] == 40106}
								{include file='op-proof6.tpl'}
							{/if}
							
							{if $operation['STANDARD_TEXT_KEY'] == 50500}
								{include file='op-mounting.tpl'}
							{/if}
							
							{if $operation['STANDARD_TEXT_KEY'] == 30602}
								{include file='op-steprepeat_flexibles.tpl'}
							{/if}
							
							{if $operation['STANDARD_TEXT_KEY'] == 50400}
								{include file='op-gravure_cylinder.tpl'}
							{/if}
							
							{if $operation['STANDARD_TEXT_KEY'] == 'CORRECT'}
								{include file='op-correction.tpl'}
							{/if}
							
							{if $operation['STANDARD_TEXT_KEY'] == 'APPROVE'}
								{include file='op-approval.tpl'}
							{/if}
							
							{if $operation['STANDARD_TEXT_KEY'] == 10101}
								{include file='op-typesetting.tpl'}
							{/if}
							
							{if $operation['STANDARD_TEXT_KEY'] == 30100}
								{include file='op-production_art.tpl'}
							{/if}
							
							{if $operation['STANDARD_TEXT_KEY'] == 30200}
								{include file='op-artistic_retouching.tpl'}
							{/if}
							
							{if $operation['STANDARD_TEXT_KEY'] == 20100}
								{include file='op-creative.tpl'}
							{/if}
							
							{if $operation['STANDARD_TEXT_KEY'] == 20200}
								{include file='op-photography.tpl'}
							{/if}
							
							{if $operation['STANDARD_TEXT_KEY'] == 60100}
								{include file='op-mockup.tpl'}
							{/if}
							
							{if $operation['STANDARD_TEXT_KEY'] == 70100}
								{include file='op-technical_services.tpl'}
							{/if}
							
							{if $components[$operation['ACTIVITY']]}
								{include file='op-components.tpl'}
							{/if}	
						</div>
					</div>
				</div>
			{/foreach}
			</div>
		</div>
	</div> <!-- End row -->
	
	{/strip}
	{/if}