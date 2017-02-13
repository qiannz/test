{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
<div id="rightTop">
  <p>用户管理</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/discount/list">折扣管理</a></li>
    <li><span>咨询管理</span></li>
  </ul>
</div>

<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="{{$_CONF.FORM_ACTION}}">
       <input type="hidden" name="did" id="did" value="{{$did}}" />
       <div class="left">
           提问内容：
           <input class="queryInput" type="text" name="question" value="{{$request.question}}" />
           提问用户：
           <input class="queryInput" type="text" name="author" value="{{$request.author}}" />
           <input class="querySelect" type="checkbox" name="isd" value="1" {{if $request.isd eq 1}}checked="checked"{{/if}} /> 删除　
          <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/discount/consultation/did:{{$did}}">撤销检索</a>
    </form>
  </div>
  <div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <table width="100%" cellspacing="0" class="dataTable">
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td>提问内容</td>      
      <td>提问用户</td>
      <td>标题</td>
      <td>回复用户</td>
      <td>回复时间</td>
      <td>创建时间</td>
      {{if $request.isd eq 0}}
      <td>操作</td>
      {{/if}}
    </tr>
    {{foreach from=$data item=item}}
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="{{$item.tid}}" /></td>
      <td><a href="/admin/discount/consultation-show/did:{{$did}}/tid:{{$item.tid}}">{{$item.question}}</a></td>
      <td>{{$item.user_name}}</td>
      <td>{{$item.question}}</td>
      <td>{{$item.repler}}</td>
      <td>{{$item.reply_time|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
      <td>{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
      {{if $request.isd eq 0}}
      <td>    	
      	<a href="javascript:drop_confirm('del-consultation', {{$item.tid}})">删除</a>
      </td>  
      {{/if}}
    </tr>
    {{foreachelse}}
    <tr class="no_data">
      <td colspan="{{if $request.isd eq 0}}8{{else}}7{{/if}}">暂无记录</td>
    </tr>
  {{/foreach}}
  </table>
<div id="dataFuncs">
    <div id="batchAction" class="left paddingT15"> &nbsp;&nbsp;
        {{if $request.isd eq 1}}
        <input class="formbtn" type="button" value="取消删除" onClick="drop_confirm('un-del-consultation')" />
        {{else}}
        <input class="formbtn" type="button" value="删除" onClick="drop_confirm('del-all-consultation')" />
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
		case 'del-consultation': content = '确认删除？'; break;
		case 'del-all-consultation': 
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
		case 'un-del-consultation': content = '确认取消删除它们吗？'; 
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
			location.href  = url + '/did:' + $("#did").val() + '/id:' + id + '/page:' +  $('#page').val();	
		},
		cancel: true
	});
}
</script>
{{include file='admin/footer.php'}}