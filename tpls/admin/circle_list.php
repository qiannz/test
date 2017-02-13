{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/inline_edit.js"></script>
<div id="rightTop">
  <p>数据配置</p>
  <ul class="subnav">
    <li><span>商圈管理</span></li>
    <li><a class="btn1" href="/admin/circle/add">新增商圈</a></li>
  </ul>
</div>

<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="{{$_CONF.FORM_ACTION}}">
       <div class="left">
   	行政区：
   				    <select name="region_id" id="region_id">
		            	<option value="">全部</option>
		                {{foreach from=$region key=key item=item}}
		                	<option value="{{$key}}"  {{if $smarty.request.region_id eq $key }}selected="selected"{{/if}}>{{$item}}</option>
		                {{/foreach}}
		            </select>&nbsp;&nbsp;
   	   
           商圈名：
           <input class="queryInput" type="text" name="circle_name" value="{{$smarty.request.circle_name}}" />
           WEB：
           <input type="checkbox" name="is_show" value="1" {{if $smarty.request.is_show eq 1}} checked {{/if}} />&nbsp;&nbsp;
           APP：
           <input type="checkbox" name="is_hot" value="1" {{if $smarty.request.is_hot eq 1}} checked {{/if}} />&nbsp;&nbsp;
         
          <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/circle/list">撤销检索</a>
    </form>
  </div>
  <div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <table width="100%" cellspacing="0" class="dataTable">
    {{if $circle}}
    <tr class="tatr1">
      <td>商圈名</td>
      <td>所属行政区</td>
      <td>前台展示</td>
      <td>是否热门</td>
      <td>排序</td>
      <td>创建时间</td>
      <td>操作</td>
    </tr>
    {{/if}}
    {{foreach from=$circle item=item}}
    <tr class="tatr2">
      <td>{{$item.circle_name}}</td>
      <td>{{$item.r_name}}</td>
      <td>{{if $item.is_show eq 1}}是{{else}}否{{/if}}</td>
      <td>{{if $item.is_hot eq 1}}是{{else}}否{{/if}}</td>
      <td><span ectype="inline_edit" fieldname="sequence" fieldid="{{$item.circle_id}}" required="1" class="node_name editable">{{$item.sequence}}</span></td>
      <td>{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
      <td>      	
      	<a href="/admin/circle/edit/id:{{$item.circle_id}}/page:{{$page}}">编辑</a> | 
        <a href="/admin/circle/recommend/id:{{$item.circle_id}}/page:{{$page}}">推荐</a> | 
      	<a href="javascript:drop_confirm('你确定要删除它吗？', '/admin/circle/del/id:{{$item.circle_id}}/name:{{$item.circle_name}}');">删除</a> 
      </td>  
    </tr>
    {{foreachelse}}
    <tr class="no_data">
      <td colspan="6">暂无品牌记录</td>
    </tr>
  {{/foreach}}
  </table>
  <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
</div>
<script type="text/javascript">
$(".dataTable tr.tatr2").mouseover(function(){  
	$(this).addClass("over");
})

$(".dataTable tr.tatr2").mouseout(function(){
	$(this).removeClass("over");
})
</script>
{{include file='admin/footer.php'}}