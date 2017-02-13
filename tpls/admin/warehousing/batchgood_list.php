{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/DatePicker/WdatePicker.js" ></script>
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
<script type="text/javascript">
	function popSkuManageDialog( ticket_id, shop_id ){ 
		var d = $.dialog({id: "T"+ticket_id, 
			title: 'SKU信息管理'
		});
		$.ajax({ 
			url: '/admin/batchgood/get-good-sku?tid='+ticket_id+'&sid='+shop_id, 
			success:function (data) 
			{ 	
				d.content(data); 
			}, 
			cache: false 
		});
	}
</script>
<div id="rightTop">
  <p>仓储管理</p>
  <ul class="subnav">
    <li><span>后仓商品</span></li>
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
      <a class="left formbtn1" href="/admin/batchgood/list">撤销检索</a>
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
      <td>总数量</td>
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
      <td>{{$item.total}}</td>
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
      	<a href="javascript:popSkuManageDialog({{$item.ticket_id}},{{$item.shop_id}})">管理</a> |
      	<a href="/admin/batchgood/add-edit/tid:{{$item.ticket_id}}/type:1/sid:{{$item.shop_id}}/uname:{{$item.user_name}}/page:{{$page}}">编辑</a> | 
        <a href="javascript:jumpToLog('ticket', {{$item.ticket_id}})">记录</a>
      </td>
    </tr>
   {{foreachelse}}
    <tr class="no_data">
      <td colspan="10">暂无记录</td>
    </tr>
    {{/foreach}}
  </table>
  <div id="dataFuncs">
    <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
    <div class="clear"></div>
  </div>
</div>
{{include file='admin/footer.php'}}