{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/DatePicker/WdatePicker.js" ></script>
<style>
<!--
.btndays {background: url(/css/admin/images/btn1.gif); display: block; width: 69px; height: 20px; line-height: 20px; color: #fff; text-align: center; text-decoration: none}
-->
</style>
<div id="rightTop">
  <p>用户管理</p>
  <ul class="subnav">
    <li><span>专题管理</span></li>
    <li><a class="btn1" href="/admin/special/add-edit">新建专题</a></li>
  </ul>
</div>
<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="{{$_CONF.FORM_ACTION}}">
       <div class="left">
          	 专题标题：
            <input class="queryInput" type="text" name="title" value="{{$request.title}}" />
            <input class="querySelect" type="checkbox" name="isd" value="1" {{if $request.isd eq 1}}checked="checked"{{/if}} /> 删除　
            <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/special/list">撤销检索</a>
    </form>
  </div>
<div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <table width="100%" cellspacing="0" class="dataTable">
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td>ID</td>
      <td>标题</td>
<!--       <td>URL地址</td> -->
	  <td>创建时间</td>
      {{if $request.isd eq 0}}
      <td width="250px">操作</td>
      {{/if}}
    </tr>
    {{foreach from=$data key=key item=item}}
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="{{$item.special_id}}" /></td>
      <td>{{$item.special_id}}</td>
      <td>{{$item.title}}</td>
<!--       <td>{{$item.www_url}}</td> -->
      <td>{{if $item.created}}{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}{{/if}}</td>
      {{if $request.isd eq 0}}
      <td>
      		<a href="/admin/special/add-edit/sid:{{$item.special_id}}">编辑</a> | 
      		<a href="javascript:drop_confirm('del', {{$item.special_id}})">删除</a> |
            <a href="/admin/special/recommend/sid:{{$item.special_id}}">推荐</a> |
            <a href="/admin/discount/group-chat/did:{{$item.special_id}}/page:{{$page}}/type:special">群聊</a> |
            <a href="javascript:jumpToLog('special', '{{$item.special_id}}')">记录</a> 
      </td>
      {{/if}}
    </tr>
   {{foreachelse}}
    <tr class="no_data">
      <td colspan="{{if $request.isd eq 0}}5{{else}}4{{/if}}">暂无数据</td>
    </tr>
    {{/foreach}}
  </table>
    <div id="dataFuncs">
    <div class="left paddingT15"> &nbsp;&nbsp;
    	{{if $request.isd eq 1}}
        <input class="formbtn" type="button" value="取消删除" onClick="drop_confirm('un-del')" />
        {{else}}
        <input class="formbtn" type="button" value="删除" onClick="drop_confirm('del-all')" />
        {{/if}}
    </div>
    <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
    <div class="clear"></div>
  </div>
</div>
<script type="text/javascript">
function drop_confirm(act, id) {
	var content;
	switch(act) {
		case 'del': content = '确认删除？'; break;
		case 'del-all': 
            if($('.checkitem:checked').length == 0){
                alert('请选择删除对象');
				return false;
            }
           
            var items = '';
            $('.checkitem:checked:enabled').each(function(){
                items += this.value + ',';
            });
            items = items.substr(0, (items.length - 1));
			id = items;	
			content = '确认删除他们？'; 
			break;
		case 'un-del': content = '确认取消删除它们吗？'; 
            if($('.checkitem:checked').length == 0){
                alert('请选择恢复删除对象');
				return false;
            }
           
            var items = '';
            $('.checkitem:checked:enabled').each(function(){
                items += this.value + ',';
            });
            items = items.substr(0, (items.length - 1));
			id = items;				
			break;
	}
	
	$.dialog({
		title:'警告',
		content: content,
		ok: function() {
			var url = '/' + _M + '/' + _C + '/' + act;
			location.href  = url + '/id:' + id + '/page:' +  $('#page').val();	
		},
		cancel: true
	});
}

function jumpToGoodShopList(sid, rid, cid)
{
	window.parent.pickTab('good');
	window.parent.openItem('good_shop_list');
	location.href="/admin/goodshop/list/shop_id:" + sid + "/region_id:" + rid + "/circle_id:" + cid;
}
</script>
{{include file='admin/footer.php'}}