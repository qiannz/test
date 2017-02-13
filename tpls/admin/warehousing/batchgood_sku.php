<html>
	<head>
		<style type="text/css">
		table.sku{ 
		    border-bottom:1px solid grey; 
			border-right:1px solid grey; 
			border-left-width:0px;
		    border-top-width:0px; 
		} 
		table.sku tr td, table.sku tr th {
		    border-left: 1px solid grey;
		    border-top: 1px solid grey;
			border-right-width:0px;
		    border-bottom-width:0px;
		    padding: 5px 0;
		    text-align: center;
		}
		</style>
	</head>
	<body>
		<table width="700" cellspacing="0" class="sku">
			<tr class="tatr1">
				<th>颜色</th>
				<th>尺寸</th>
				<th>仓库数量</th>
				<th>卖场数量</th>
				<th>已售数量</th>
				<th>回退厂家数量</th>
				<th width="150">操作</th>
			</tr>
			{{foreach from=$sku key=key item=item}}
			<tr class="tatr2 {{$ticket_id}}_{{$item.good_color}}_{{$item.good_size}}">
				<td>{{$item.color}}</td>
				<td>{{$item.size}}</td>
				<td class="warehouse_num">{{$item.good_warehouse_num}}</td>
				<td class="market_num">{{$item.good_market_num}}</td>
				<td class="sold_num">{{$item.good_sold_num}}</td>
				<td class="rollback_num">{{$item.good_rollback_num}}</td>
				<td>
					<a class="add_market_btn" href="javascript:goodNumManage({{$item.ticket_id}},'{{$item.good_color}}','{{$item.good_size}}',1);">入场</a> | 
					<a class="rollback_to_warehouse_btn" href="javascript:goodNumManage({{$item.ticket_id}},'{{$item.good_color}}','{{$item.good_size}}',2);">回退后仓</a> |
			        <a class="rollback_to_factory_btn" href="javascript:goodNumManage({{$item.ticket_id}},'{{$item.good_color}}','{{$item.good_size}}',3);">回退厂家</a>
				</td>
			</tr>
			{{foreachelse}}
			<tr class="no_data">
			   <td colspan="7">暂无记录</td>
			</tr>
			{{/foreach}}
		</table>
		<script type="text/javascript">
			var validArray = ['market_add_num','rollback_to_warehouse_num','rollback_to_factory_num'];
			function validInput(id) {
				var _msg = '';
				var _is_radio = 0;
				switch(id) {
					case 'market_add_num':
							console.log('#'+id);
							console.log($('#' + id).val());
							if($('#' + id).val().length == 0) {
								_msg = '请选择输入入场数量';	
							}else if( !/[1-9]+/.test($('#' + id).val()) ){
								_msg = '入场数量必须为大于0整数';
							}else if( parseInt($('#' + id).val()) > parseInt($("#warehouse_num").val()) ){
								_msg = '入场数量必须小于仓库数量';
							}
						break;
					case 'rollback_to_warehouse_num':
						if($('#' + id).val().length == 0) {
							_msg = '请选择输入回退后仓数量';	
						}else if( !/[1-9]+/.test($('#' + id).val()) ){
							_msg = '回退后仓数量必须为大于0整数';
						}else if( parseInt($('#' + id).val()) > parseInt($("#market_num").val()) - parseInt($("#sold_num").val()) ){
							_msg = '回退后仓数量必须小于卖场剩余数量';
						}
						break;
					case 'rollback_to_factory_num':
						if($('#' + id).val().length == 0) {
							_msg = '请选择输入回退厂家数量';	
						}else if( !/[1-9]+/.test($('#' + id).val()) ){
							_msg = '回退厂家数量必须为大于0整数';
						}else if( parseInt($('#' + id).val()) > parseInt($("#warehouse_num").val()) ){
							_msg = '回退厂家数量必须小于仓库数量';
						}
						break;
				}
				if(_msg == '') {
					$('input[name=' + id + ']').closest("td").children("label").attr('class', 'field_notice').html('');
				} else {
					$('input[name=' + id + ']').closest("td").children("label").attr('class', 'error').html(_msg);
					return false;
				}
				return true;
			}
			//商品数量管理 opert 1:入场 ; 2:退回后仓; 3:退回工厂
			function goodNumManage( ticket_id, good_color, good_size, opert ){
				var d = $.dialog({
					id: "T"+ticket_id+good_color+good_size+opert, 
					width: 400,
					title: 'SKU信息更新',
					okVal: '确定',
					ok: function(){
						var trObj = $("."+ticket_id+"_"+good_color+"_"+good_size)
				        var opert = $(".sku_opert #opert").val();
				        var _tid = $(".sku_opert #tid").val();
				        var _good_color = $(".sku_opert #color").val();
				        var _good_size = $(".sku_opert #size").val();
				        var warehouse_num = $(".sku_opert #warehouse_num").val();
				        var market_num = $(".sku_opert #market_num").val();
				        var sold_num = $(".sku_opert #sold_num").val();
				        var _num = $(".sku_opert .opert_num").val();
				        switch(opert)
				        {
				        case '1'://入场
				        	validArray = ['market_add_num'];
				        	var len = 0;
							for(var i=0; i<validArray.length; i++) {
								if(validInput(validArray[i])) {
									len++;
								}
							}
							if(len == validArray.length) {
								$.ajax({
									type:'POST',
									url:'/admin/batchgood/into-market',
									data:{tid : _tid, color:_good_color, size:_good_size, num:_num},
									dataType:'json',
									success:function(data){
										if(data.res == 100){
											var extra = data.extra;
											trObj.children('td.warehouse_num')[0].innerHTML = extra.good_warehouse_num;
											trObj.children('td.rollback_num')[0].innerHTML  = extra.good_rollback_num;
											trObj.children('td.market_num')[0].innerHTML    = extra.good_market_num;
											trObj.children('td.sold_num')[0].innerHTML      = extra.good_sold_num;
											d.close();
										}else{
											alert(data.msg);
										}
									}
								});
							}  
				          break;
				        case '2'://回退后仓
				        	validArray = ['rollback_to_warehouse_num'];
				        	var len = 0;
							for(var i=0; i<validArray.length; i++) {
								if(validInput(validArray[i])) {
									len++;
								}
							}
				        	if(len == validArray.length) {
								$.ajax({
									type:'POST',
									url:'/admin/batchgood/rollback-to-warehouse',
									data:{tid : _tid, color:_good_color, size:_good_size, num:_num},
									dataType:'json',
									success:function(data){
										if(data.res == 100){
											var extra = data.extra;
											trObj.children('td.warehouse_num')[0].innerHTML = extra.good_warehouse_num;
											trObj.children('td.rollback_num')[0].innerHTML  = extra.good_rollback_num;
											trObj.children('td.market_num')[0].innerHTML    = extra.good_market_num;
											trObj.children('td.sold_num')[0].innerHTML      = extra.good_sold_num;
											d.close();
										}else{
											alert(data.msg);
										}
									}
								});
							}
				          break;
				        case '3'://回退厂家
				        	validArray = ['rollback_to_factory_num'];
				        	var len = 0;
							for(var i=0; i<validArray.length; i++) {
								if(validInput(validArray[i])) {
									len++;
								}
							}
				        	if(len == validArray.length) {
								$.ajax({
									type:'POST',
									url:'/admin/batchgood/rollback-to-factory',
									data:{tid : _tid, color:_good_color, size:_good_size, num:_num},
									dataType:'json',
									success:function(data){
										if(data.res == 100){
											var extra = data.extra;
											trObj.children('td.warehouse_num')[0].innerHTML = extra.good_warehouse_num;
											trObj.children('td.rollback_num')[0].innerHTML  = extra.good_rollback_num;
											trObj.children('td.market_num')[0].innerHTML    = extra.good_market_num;
											trObj.children('td.sold_num')[0].innerHTML      = extra.good_sold_num;
											d.close();
										}else{
											alert(data.msg);
										}
									}
								});
							}
					        break;
				        }
				        return false;
				    }
				});
				$.ajax({ 
					url: '/admin/batchgood/get-good-sku-detail', 
					type: 'POST',
					data: {'tid':ticket_id,'sid':"{{$shop_id}}",'color':good_color,'size':good_size,'opert':opert},
					success:function (data) 
					{ 	
						d.content(data); 
					}, 
					cache: false 
				});
			}
		</script>
	</body>
</html>