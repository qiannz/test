{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css" />
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js"></script>
<script type="text/javascript" src="/js/inline_edit.js"></script>
<div id="rightTop">
  <p>推荐管理</p>
  <ul class="subnav">
    <li><span>推荐列表</span></li>
    <li><a class="btn1" href="/admin/recommend/add">新增推荐</a></li>
  </ul>
</div>

<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="{{$_CONF.FORM_ACTION}}">
       <div class="left">
           标题：
           <input class="queryInput" type="text" name="title" value="{{$smarty.request.title}}" style="width:160px;" />
           推荐位：
			<select name="pos_id">
            	<option value="">所有</option>
            	{{foreach from=$position key=key item=item}}
            		<optgroup label="{{$item.pos_name}}"/>
                    {{foreach from=$item.child key=skey item=sitem}}
            		<option value="{{$sitem.pos_id}}" {{if $smarty.request.pos_id eq $sitem.pos_id}} selected="selected"{{/if}}>{{$sitem.pos_name}}</option>
                    {{/foreach}}
            	{{/foreach}}
        </select> 
          <input type="submit" class="formbtn" value="查询" />
      </div>
      
      <a class="left formbtn1" href="/admin/recommend/list">撤销检索</a>
      
    </form>
  </div>
  <div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <table width="100%" cellspacing="0" class="dataTable">
    {{if $recommend}}
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td>标题</td>
      <td>简介</td>
      <td>推荐位</td>
      <td>推荐位标识</td>
      <td>链接地址</td>
      <td>排序</td>
      <td>创建时间</td>
      <td>修改时间</td>
      <td>操作</td>
    </tr>
    {{/if}}
    {{foreach from=$recommend item=item}}
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="{{$item.recommend_id}}" /></td>
      <td>{{$item.title}}</td>
      <td>{{$item.summary}}</td>
      <td>{{$item.pos_name}}</td>
      <td>{{$item.identifier}}</td>
      <td>{{$item.www_url}}</td>
      <td><span ectype="inline_edit" fieldname="sequence" fieldid="{{$item.recommend_id}}" required="1" class="node_name editable">{{$item.sequence}}</span></td>
      <td>{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
      <td>{{if $item.updated neq 0}}{{$item.updated|date_format:'%Y-%m-%d %H:%M:%S'}}{{/if}}</td>
      <td>      	
      	<a href="/admin/recommend/edit/id:{{$item.recommend_id}}">编辑</a> | 
      	<a href="javascript:drop_confim({{$item.recommend_id}})">删除</a>
      </td>  
    </tr>
    {{foreachelse}}
    <tr class="no_data">
      <td colspan="6">暂无推荐记录</td>
    </tr>
  {{/foreach}}
  </table>
  <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
</div>
<script type="text/javascript">
function drop_confim(rid) {
	$.dialog({
		title: '提示',
		content: '确定删除？' ,
		okValue: '确定',
		ok: function () {
			location.href = '/admin/recommend/del/id:' + rid;
		},
		cancelValue: '取消',
		cancel : true
	});	
}
</script>
{{include file='admin/footer.php'}}