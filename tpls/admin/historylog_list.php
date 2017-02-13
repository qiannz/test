{{include file='admin/header.php'}}
<script type="text/javascript">
$(function(){
	$('#pmodule').change(function(){
		var _this = $('#cmodule');
		_this.empty();
		$.post('/admin/historylog/get-cmodule', {id:$(this).val()}, function(obj){
			var data = eval('(' + obj + ')');
			$.each(data, function(i, s){
				_this.append($("<option>").text(s.cmodule).val(s.cmodule));
			});
		});
	});
});
</script>
<div id="rightTop">
  <p>日志管理</p>
  <ul class="subnav">
    <li><span>日志列表</span></li>
  </ul>
</div>
<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="{{$_CONF.FORM_ACTION}}">
       <div class="left">
            一级模块：
			<select name="pmodule" id="pmodule">
            	<option value="">所有</option>
            	{{foreach from=$pmodule key=key item=item}}
            	<option value="{{$item.pmodule}}" {{if $smarty.request.pmodule eq $item.pmodule}} selected="selected"{{/if}}>{{$item.pmodule}}</option>
            	{{/foreach}}
        	</select>&nbsp;&nbsp;&nbsp;
            二级模块：
			<select name="cmodule" id="cmodule">
            	<option value="">所有</option>
				{{foreach from=$cmodule key=key item=item}}
            	<option value="{{$item.cmodule}}" {{if $smarty.request.cmodule eq $item.cmodule}} selected="selected"{{/if}}>{{$item.cmodule}}</option>
            	{{/foreach}}
        	</select> &nbsp;&nbsp;&nbsp;
        	操作动作：
			<select class="querySelect" name="activity">{{html_options options=$activity_fields selected=$smarty.request.activity}}</select>  &nbsp;&nbsp;&nbsp;
        	类别：
			<select class="querySelect" name="field_name">
            	{{foreach from=$query_fields key=key item=item}}
            	<option value="{{$key}}" {{if $smarty.request.field_name eq $key || $key eq $type}} selected="selected"{{/if}}>{{$item}}</option>
            	{{/foreach}}
			</select>  
        	<input class="queryInput" style="width:160px;" type="text" name="field_value" value="{{if $name}}{{$name}}{{else}}{{$smarty.request.field_value}}{{/if}}" />
            <input type="submit" class="formbtn" value="查询" />
      </div>
      
      <a class="left formbtn1" href="/admin/historylog/list">撤销检索</a>
      
    </form>
  </div>
  <div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <table width="100%" cellspacing="0" class="dataTable">
    {{if $logs}}
    <tr class="tatr1">
      <td width="50">ID</td>
      <td width="50%">操作内容</td>
      <td>一级模块</td>
      <td>二级模块</td>
      <td>操作</td>
      <td>操作人</td>
      <td>创建时间</td>
    </tr>
    {{/if}}
    {{foreach from=$logs item=item}}
    <tr class="tatr2">
      <td>{{$item.log_id}}</td>
      <td>{{$item.operat_info}}</td>
      <td>{{$item.pmodule}}</td>
      <td>{{$item.cmodule}}</td>
      <td>{{$item.activity_name}}</td>
      <td>{{$item.admin_user_name}}</td>
      <td>{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
    </tr>
    {{foreachelse}}
    <tr class="no_data">
      <td colspan="6">暂无日志记录</td>
    </tr>
  {{/foreach}}
  </table>
  <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
</div>
{{include file='admin/footer.php'}}