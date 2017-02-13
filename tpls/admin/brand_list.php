{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/inline_edit.js"></script>
<div id="rightTop">
  <p>品牌管理</p>
  <ul class="subnav">
    <li><span>品牌管理</span></li>
    <li><a class="btn1" href="/admin/brand/add">新增品牌</a></li>
  </ul>
</div>

<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="{{$_CONF.FORM_ACTION}}">
       <div class="left">
   	分类：
   				    <select name="store_id" id="store_id">
		            	<option value="">全部</option>
		                {{foreach from=$storeArray key=key item=item}}
		                	<option value="{{$key}}"  {{if $smarty.request.store_id eq $key }}selected="selected"{{/if}}>{{$item}}</option>
		                {{/foreach}}
		            </select>&nbsp;&nbsp;
   	   
           品牌名：
           <input class="queryInput" type="text" name="brand_name" value="{{$smarty.request.brand_name}}" />
           前台展示：
           <input type="checkbox" name="is_show" value="1" {{if $smarty.request.is_show eq 1}} checked {{/if}} />&nbsp;&nbsp;
           是否启用：
           <input type="checkbox" name="is_enable" value="1" {{if $smarty.request.is_enable eq 1}} checked {{/if}} />&nbsp;&nbsp;
          <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/brand/list">撤销检索</a>
    </form>
  </div>
  <div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <table width="100%" cellspacing="0" class="dataTable">
    {{if $brands}}
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td>分类</td>
      <td>中文名称</td>
      <td>英文名称</td>
      <td>首字母</td>
      <td>前台展示</td>
      <td>是否启用</td>
      <td>排序</td>
      <td>操作</td>
    </tr>
    {{/if}}
    {{foreach from=$brands item=item}}
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="{{$item.brand_id}}" /></td>
      <td>{{$item.store_name}}</td>
      <td>{{$item.brand_name_zh}}</td>
      <td>{{$item.brand_name_en}}</td>
      <td>{{$item.firs_word}}</td>
      <td>{{if $item.is_show eq 1}}是{{else}}否{{/if}}</td>
      <td>{{if $item.is_enable eq 1}}是{{else}}否{{/if}}</td>
      <td><span ectype="inline_edit" fieldname="sequence" fieldid="{{$item.brand_id}}" required="1" class="node_name editable">{{$item.sequence}}</span></td>
      <td>      	
      	<a href="/admin/brand/edit/id:{{$item.brand_id}}/page:{{$page}}">编辑</a> | 
        <a href="/admin/brand/recommend/id:{{$item.brand_id}}/page:{{$page}}">推荐</a> | 
        <a href="/admin/brand/color-size/bid:{{$item.brand_id}}/page:{{$page}}">颜色尺码</a> | 
      	<a href="/admin/brand/del/id:{{$item.brand_id}}">删除</a>
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