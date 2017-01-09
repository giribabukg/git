<!-- components -->
<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-hover table-condensed table-component">
			<thead>
				<tr>
					<th>Component</th>
					<th>Description</th>
					<th>Req. Quantity</th>
					<th>Quantity Withdrawn</th>
					<th>Unit</th>
					<th>ICt</th>
					<th>SLoc</th>
					<th>Plant</th>
				</tr>
			</thead>
			<tbody>
				{foreach $components[$operation['ACTIVITY']] as $component}
				<tr>
					<td>{$component['MATERIAL']|intval}</td>
					<td>{$component['ITEM_TEXT']}</td>
					<td>{$component['ENTRY_QNT']}</td>
					<td>{$component['WITHD_QUAN']}</td>
					<td>{$component['ENTRY_UOM']}</td>
					<td>{$component['ITEM_CAT']}</td>
					<td>{$component['STGE_LOC']}</td>
					<td>{$component['PLANT']}</td>
				</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
</div>