<div class="sku_opert" style="width:400px">
	<form method="POST" id="sku_form">
		<input type="hidden" name="tid" id="tid" value="{{$row.ticket_id}}" />
		<input type="hidden" name="color" id="color" value="{{$sku.good_color}}" />
		<input type="hidden" name="size" id="size" value="{{$sku.good_size}}" />
		<input type="hidden" name="warehouse_num" id="warehouse_num" value="{{$sku.good_warehouse_num}}" />
		<input type="hidden" name="market_num" id="market_num" value="{{$sku.good_market_num}}" />
		<input type="hidden" name="sold_num" id="sold_num" value="{{$sku.good_sold_num}}" />
		<input type="hidden" name="opert" id="opert" value="{{$opert}}" />
		<table class="infoTable">
			<tr>
				<th class="paddingT15"> 品名:</th>
				<td class="paddingT15 wordSpacing5" width="60%">
					<span class="good_title">{{$row.ticket_title}}</span>
				</td>
				<td></td>
			</tr>
			<tr>
				<th class="paddingT15"> 颜色:</th>
				<td class="paddingT15 wordSpacing5" width="60%">
					<span class="good_color">{{$sku.color}}</span>
				</td>
				<td></td>
			</tr>  
			<tr>
				<th class="paddingT15"> 尺码:</th>
				<td class="paddingT15 wordSpacing5" width="60%">
					<span class="good_size">{{$sku.size}}</span>
				</td>
				<td></td>
			</tr>
			{{if $opert eq 1 OR $opert eq 3 }}
			<tr>
				<th class="paddingT15"> 仓库数量:</th>
				<td class="paddingT15 wordSpacing5" width="60%">
					<span class="good_warehouse_num">{{$sku.good_warehouse_num}}</span>
				</td>
				<td></td>
			</tr>
			{{/if}}
			{{if $opert eq 2 }}
			<tr>
				<th class="paddingT15"> 卖场剩余数量:</th>
				<td class="paddingT15 wordSpacing5" width="60%">
					<span class="good_market_num">{{$sku.good_market_num-$sku.good_sold_num}}</span>
				</td>
				<td></td>
			</tr>
			{{/if}}
			<tr>
				<th class="paddingT15"> {{if $opert eq 1}}入场数量{{elseif $opert eq 2}}回退后仓数量{{else}}回退工厂数量{{/if}}:</th>
				<td class="paddingT15 wordSpacing5" width="60%">
					<input class="infoTableInput2 opert_num" id="{{if $opert eq 1}}market_add_num{{elseif $opert eq 2}}rollback_to_warehouse_num{{else}}rollback_to_factory_num{{/if}}" type="text" name="{{if $opert eq 1}}market_add_num{{elseif $opert eq 2}}rollback_to_warehouse_num{{else}}rollback_to_factory_num{{/if}}" value="" />
					<label class="field_notice"></label>
				</td>
				<td></td>
			</tr>
		</table>
	</form>
</div>