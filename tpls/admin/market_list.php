{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/inline_edit.js"></script>
<div id="rightTop">
  <p>数据配置</p>
  <ul class="subnav">
    <li><span>商场管理</span></li>
    <li><a class="btn1" href="/admin/market/add-edit">新建商场</a></li>
    {{if $_ad_city eq 'sh'}}
    <li><input type="button" class="formbtn1" value="同步商场" onClick="marketSync()" /></li>
    {{/if}}
  </ul>
</div>

<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="{{$_CONF.FORM_ACTION}}">
       <div class="left">
          商场名称：
           <input class="queryInput" type="text" name="market" value="{{$request.market}}" />
          APP推荐：
           <input class="querySelect" type="radio" name="ist" value="1" {{if $request.ist eq 1}}checked="checked"{{/if}} />
          <input type="submit" class="formbtn" value="查询" />
      </div>      
      <a class="left formbtn1" href="/admin/market/list">撤销检索</a>
      
    </form>
  </div>
  <div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <table width="100%" cellspacing="0" class="dataTable">
    {{if $data}}
    <tr class="tatr1">
      <td>商场名称</td>
      <td>商场地址</td>
      <td>经度</td>
      <td>纬度</td>
      <td>区域</td>
      <td>APP推荐</td>
      <td>排序</td>
      <td></td>
    </tr>
    {{/if}}
    {{foreach from=$data item=item}}
    <tr class="tatr2">
      <td>{{$item.market_name}}</td>
      <td>{{$item.market_address}}</td>
      <td>{{$item.lng}}</td>
      <td>{{$item.lat}}</td>
      <td>{{$item.region_name}}</td>
      <td>{{if $item.is_show eq 1}}是{{else}}否{{/if}}</td>
      <td><span ectype="inline_edit" fieldname="sequence" fieldid="{{$item.market_id}}" required="1" class="node_name editable">{{$item.sequence}}</span></td>
      <td>
      <a href="/admin/market/add-edit/id:{{$item.market_id}}/page:{{$page}}">编辑</a> | 
       <a href="/admin/market/recommend/id:{{$item.market_id}}/page:{{$page}}">推荐</a> |   
       <a href="javascript:drop_confirm('你确定要删除它吗？', '/admin/market/delete/id:{{$item.market_id}}/page:{{$page}}');">删除</a>
        
       </td>
    </tr>
    {{foreachelse}}
    <tr class="no_data">
      <td colspan="8">暂无活动记录</td>
    </tr>
  {{/foreach}}
  </table>
  <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
</div>
<script type="text/javascript">
function marketSync() {
	$('.formbtn1').attr('disabled',true).attr('value', '同步中...');
	$.post('/admin/market/market-sync', {}, function(json){
		var obj = eval ("(" + json + ")" );
		if(obj.res == 100) {
			$('.formbtn1').attr('disabled',false).attr('value', '同步商场');
			$.dialog.alert('同步成功');
		} else {
			$.dialog.alert('同步失败');
		}
	});
}
</script>
{{include file='admin/footer.php'}}