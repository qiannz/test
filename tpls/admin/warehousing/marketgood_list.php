{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/DatePicker/WdatePicker.js" ></script>
<div id="rightTop">
  <p>仓储管理</p>
  <ul class="subnav">
    <li><span>卖场商品</span></li>
  </ul>
</div>
<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="{{$_CONF.FORM_ACTION}}">
       <div class="left">
    商品名称：
    		<input class="queryInput" type="text" name="gname" value="{{$request.gname}}" />
            批次：
            <input class="queryInput" type="text" name="batch" value="{{$request.batch}}" />
            品牌：
            <input class="queryInput" type="text" name="bname" value="{{$request.bname}}" />
    入库时间：
            <input class="queryInput" type="text" name="stime" id="stime" onFocus="WdatePicker({isShowClear:false,readOnly:false,dateFmt:'yyyy-MM-dd HH'})"  value="{{$request.stime}}"  />　- 
            <input class="queryInput" type="text" name="etime" id="etime" onFocus="WdatePicker({isShowClear:false,readOnly:false,dateFmt:'yyyy-MM-dd HH',minDate:'#F{$dp.$D(\'stime\',{H:1})}'})"  value="{{$request.etime}}"  />
            <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/marketgood/list">撤销检索</a>
    </form>
  </div>
<div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <table width="100%" cellspacing="0" class="dataTable">
    <tr class="tatr1">
      <td>商品ID</td>
      <td width="250">品名</td>
      <td>款号</td>
      <td>批次</td>
      <td>店铺</td>
      <td>品牌</td>
      <td>颜色</td>
      <td>尺码</td>
      <td>总数量</td>
      <td>仓库数量</td>
      <td>回退厂家数量</td>
      <td>卖场数量</td>
      <td>已售数量</td>
      <td>上下架</td>
	  <td>显示状态</td>
	  <td>入库时间</td>
      <td>操作</td>
    </tr>
    {{foreach from=$data key=key item=item}}
    <tr class="tatr2">
      <td>{{$item.ticket_id}} {{$item.ticket_uuid}}</td>
      <td class="title">{{$item.ticket_title}}</td>
      <td>{{$item.good_number}}</td>
      <td>{{$item.good_batch}}</td>
      <td>{{$item.shop_name}}</td>
      <td>{{$item.brand_name}}</td>
      <td class="color">{{$item.color}}</td>
      <td class="size">{{$item.size}}</td>
      <td>{{$item.total}}</td>
	  <td class="warehouse_num">{{$item.good_warehouse_num}}</td>
	  <td class="rollback_num">{{$item.good_rollback_num}}</td>
	  <td class="market_num">{{$item.good_market_num}}</td>
	  <td class="sold_num">{{$item.good_sold_num}}</td>
	  <td>
      		{{if $item.is_auth eq '0'}}下架
      		{{elseif $item.is_auth eq '1'}}上架
            {{/if}}
      </td>
      <td>
      		{{if $item.is_show eq '0'}}不显示
      		{{elseif $item.is_show eq '1'}}显示
            {{/if}}
      </td>
      <td>{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
      <td>
      	<a href="/admin/batchgood/add-edit/tid:{{$item.ticket_id}}/type:2/sid:{{$item.shop_id}}/uname:{{$item.user_name}}/page:{{$page}}">编辑</a> | 
        <a class="add_factory_btn" data-tid="{{$item.ticket_id}}" data-color="{{$item.good_color}}" data-size="{{$item.good_size}}" href="#">补货</a> |
        <a class="rollback_btn" data-tid="{{$item.ticket_id}}" data-color="{{$item.good_color}}" data-size="{{$item.good_size}}"  href="#">回退后仓</a> |
        <a href="javascript:jumpToLog('ticket', {{$item.ticket_id}})">记录</a>
      </td>
    </tr>
   {{foreachelse}}
    <tr class="no_data">
      <td colspan="17">暂无记录</td>
    </tr>
    {{/foreach}}
  </table>
  <div id="dataFuncs">
    <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
    <div class="clear"></div>
  </div>
</div>
<div class="factory" style="display:none">
<form method="POST" id="factory_form">
	<input type="hidden" name="factory_tid" id="factory_tid" value="" />
	<input type="hidden" name="factory_color" id="factory_color" value="" />
	<input type="hidden" name="factory_size" id="factory_size" value="" />
	<input type="hidden" name="factory_warehouse_num" id="factory_warehouse_num" value="" />
    <input type="hidden" name="factory_page" id="factory_page" value="{{$page}}" />
    <table class="infoTable">
    	<tr>
            <th class="paddingT15"> 品名:</th>
            <td class="paddingT15 wordSpacing5" width="60%">
                <span class="factory_good_title"></span>
            </td>
            <td></td>
        </tr>
        <tr>
            <th class="paddingT15"> 颜色:</th>
            <td class="paddingT15 wordSpacing5" width="60%">
                <span class="factory_good_color"></span>
            </td>
            <td></td>
        </tr>
        <tr>
            <th class="paddingT15"> 尺码:</th>
            <td class="paddingT15 wordSpacing5" width="60%">
                <span class="factory_good_size"></span>
            </td>
            <td></td>
        </tr>
        <tr>
            <th class="paddingT15"> 仓库数量:</th>
            <td class="paddingT15 wordSpacing5" width="60%">
                <span class="factory_good_warehouse_num"></span>
            </td>
            <td></td>
        </tr>
        <tr>
            <th class="paddingT15"> 补货数量:</th>
            <td class="paddingT15 wordSpacing5" width="60%">
                <input class="infoTableInput2" id="market_num" type="text" name="market_num" value="" />
                <label class="field_notice"></label>
            </td>
            <td></td>
        </tr>
       <tr>
            <th class="paddingT15"> </th>
            <td class="ptb20"></td>
        </tr>
    </table>
</form>
</div>

<div class="rollback" style="display:none">
<form method="POST" id="rollback_form">
	<input type="hidden" name="rollback_tid" id="rollback_tid" value="" />
	<input type="hidden" name="rollback_color" id="rollback_color" value="" />
	<input type="hidden" name="rollback_size" id="rollback_size" value="" />
	<input type="hidden" name="rollback_good_market_num" id="rollback_good_market_num" value="" />
    <input type="hidden" name="page" id="page" value="{{$page}}" />
    <table class="infoTable">
    	<tr>
            <th class="paddingT15"> 品名:</th>
            <td class="paddingT15 wordSpacing5" width="60%">
                <span class="rollback_good_title"></span>
            </td>
            <td></td>
        </tr>
        <tr>
            <th class="paddingT15"> 颜色:</th>
            <td class="paddingT15 wordSpacing5" width="60%">
                <span class="rollback_good_color"></span>
            </td>
            <td></td>
        </tr>
        <tr>
            <th class="paddingT15"> 尺码:</th>
            <td class="paddingT15 wordSpacing5" width="60%">
                <span class="rollback_good_size"></span>
            </td>
            <td></td>
        </tr>
        <tr>
            <th class="paddingT15"> 商场数量:</th>
            <td class="paddingT15 wordSpacing5" width="60%">
                <span class="rollback_good_market_num"></span>
            </td>
            <td></td>
        </tr>
        <tr>
            <th class="paddingT15"> 回退后仓数量:</th>
            <td class="paddingT15 wordSpacing5" width="60%">
                <input class="infoTableInput2" id="rollback_num" type="text" name="rollback_num" value="" />
                <label class="field_notice"></label>
            </td>
            <td></td>
        </tr>
       <tr>
            <th class="paddingT15"> </th>
            <td class="ptb20"></td>
        </tr>
    </table>
</form>
</div>
<link rel="stylesheet" type="text/css" href="/css/JqueryDialog/jquery-ui-1.10.4.custom.css" />
<script type="text/javascript" src="/js/JqueryDialog/jquery-ui-1.10.4.custom.js"></script>
<script type="text/javascript">
var validArray = ['market_num' , 'rollback_num'];
function validInput(id) {
	var _msg = '';
	var _is_radio = 0;
	switch(id) {
		case 'market_num':
				if($('#' + id).val().length == 0) {
					_msg = '请选择输入入场数量';	
				}else if( !/[1-9]+/.test($('#' + id).val()) ){
					_msg = '入场数量必须为整数';
				}else if( parseInt($('#' + id).val()) > parseInt($("#factory_warehouse_num").val()) ){
					_msg = '入场数量必须小于仓库数量';
				}
			break;
		case 'rollback_num':
			if($('#' + id).val().length == 0) {
				_msg = '请选择输入回退厂家数量';	
			}else if( !/[1-9]+/.test($('#' + id).val()) ){
				_msg = '回退厂家数量必须为整数';
			}else if( parseInt($('#' + id).val()) > parseInt($("#rollback_market_num").val()) ){
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
	$(function(){
		$(".add_factory_btn").click(function(){
			$("#market_num").val('');
			$(".factory label.error").remove();
			validArray = ['market_num'];
			var tr = $(this).parents("tr");
			$('span.factory_good_title').html(tr.children('td.title')[0].innerHTML);
			$('span.factory_good_color').html(tr.children('td.color')[0].innerHTML);
			$('span.factory_good_size').html(tr.children('td.size')[0].innerHTML);
			$('span.factory_good_warehouse_num').html(tr.children('td.warehouse_num')[0].innerHTML);
			$("#factory_tid").val($(this).data("tid"));
			$("#factory_color").val($(this).data("color"));
			$("#factory_size").val($(this).data("size"));
			$("#factory_warehouse_num").val(tr.children('td.warehouse_num')[0].innerHTML);
			$('.factory').dialog({
				title : '补货',
				width : 600,
				height : 310,
				buttons: {
					'确定': function() {
						var len = 0;
						for(var i=0; i<validArray.length; i++) {
							if(validInput(validArray[i])) {
								len++;
							}
						}
						var _tid = $("#factory_tid").val();
						var _good_color = $("#factory_color").val();
						var _good_size = $("#factory_size").val();
						var _page = $("#factory_page").val();
						var _num = $("#market_num").val();
						if(len == validArray.length) {
							$.ajax({
								type:'POST',
								url:'/admin/batchgood/into-market',
								data:{tid : _tid, color:_good_color, size:_good_size, num:_num},
								dataType:'json',
								success:function(data){
									if(data.res == 100){
										var extra = data.extra;
										tr.children('td.warehouse_num')[0].innerHTML = extra.good_warehouse_num;
										tr.children('td.rollback_num')[0].innerHTML  = extra.good_rollback_num;
										tr.children('td.market_num')[0].innerHTML    = extra.good_market_num;
										tr.children('td.sold_num')[0].innerHTML      = extra.good_sold_num;
										$('.factory').dialog('close');
									}else{
										alert(data.msg);
									}
								}
							});
						}
					}
				}	
			});
		});	

		$(".rollback_btn").click(function(){
			$("#rollback_num").val('');
			$(".rollback label.error").remove();
			validArray = ['rollback_num'];
			var tr = $(this).parents("tr");
			$('span.rollback_good_title').html(tr.children('td.title')[0].innerHTML);
			$('span.rollback_good_color').html(tr.children('td.color')[0].innerHTML);
			$('span.rollback_good_size').html(tr.children('td.size')[0].innerHTML);
			$('span.rollback_good_market_num').html(tr.children('td.market_num')[0].innerHTML);
			$("#rollback_tid").val($(this).data("tid"));
			$("#rollback_color").val($(this).data("color"));
			$("#rollback_size").val($(this).data("size"));
			$("#rollback_good_market_num").val(tr.children('td.market_num')[0].innerHTML);
			$('.rollback').dialog({
				title : '回退后仓',
				width : 600,
				height : 310,
				buttons: {
					'确定': function() {
						var len = 0;
						for(var i=0; i<validArray.length; i++) {
							if(validInput(validArray[i])) {
								len++;
							}
						}
						var _tid = $("#rollback_tid").val();
						var _good_color = $("#rollback_color").val();
						var _good_size = $("#rollback_size").val();
						var _page = $("#rollback_page").val();
						var _num = $("#rollback_num").val();
						if(len == validArray.length) {
							$.ajax({
								type:'POST',
								url:'/admin/batchgood/rollback-to-warehouse',
								data:{tid : _tid, color:_good_color, size:_good_size, num:_num},
								dataType:'json',
								success:function(data){
									if(data.res == 100){
										var extra = data.extra;
										tr.children('td.warehouse_num')[0].innerHTML = extra.good_warehouse_num;
										tr.children('td.rollback_num')[0].innerHTML  = extra.good_rollback_num;
										tr.children('td.market_num')[0].innerHTML    = extra.good_market_num;
										tr.children('td.sold_num')[0].innerHTML      = extra.good_sold_num;
										$('.rollback').dialog('close');
									}else{
										alert(data.msg);
									}
								}
							});
						}
					}
				}	
			});
		});
	});
</script>
{{include file='admin/footer.php'}}