{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/jqtreetable.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/inline_edit.js" ></script>
<link rel="stylesheet" type="text/css" href="/css/admin/jqtreetable.css"  />
<script type="text/javascript">
$(function()
{
    var map = {{$map}};
    if (map.length > 0)
    {
        var option = {openImg: "/css/admin/images/treetable/tv-collapsable.gif", shutImg: "/css/admin/images/treetable/tv-expandable.gif", leafImg: "/css/admin/images/treetable/tv-item.gif", lastOpenImg: "/css/admin/images/treetable/tv-collapsable-last.gif", lastShutImg: "/css/admin/images/treetable/tv-expandable-last.gif", lastLeafImg: "/css/admin/images/treetable/tv-item-last.gif", vertLineImg: "/css/admin/images/treetable/vertline.gif", blankImg: "/css/admin/images/treetable/blank.gif", collapse: false, column: 1, striped: false, highlight: true, state:false};
        $("#treet1").jqTreeTable(map, option);
    }
});
</script>
<script charset="utf-8" type="text/javascript" src="/js/inline_edit.js"></script>
<div id="rightTop">
    <p>推荐管理</p>
    <ul class="subnav">
        <li><span>导航列表</span></li>
        <li><a class="btn3" href="/admin/nav/add">新增导航</a></li>
    </ul>
</div>

<div class="mrightTop">
  <div class="fontl">
    	<form method="post" action="{{$_CONF.FORM_ACTION}}">
    	<div class="left">
           推荐位：
			<select name="pos_id" id="pos_id">
            <option value="">请选择分类</option>
          	{{foreach from=$posSortList key=key item=item}}
            <option value="{{$item.pos_id}}" {{if $item.pos_id eq $request.pos_id}}selected="selected"{{/if}}>{{$item.pos_name}}</option>
            {{/foreach}}
          </select>
          <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/nav/list">撤销检索</a>
	  </form>
  </div>
</div>

<div class="info2">
    <table class="distinction">
        <thead>
            <tr>
                <th><input id="checkall_1" type="checkbox" class="checkall" /></th>
                <th><span class="all_checkbox"><label for="checkall_1">全选</label></span>导航名称</th>
                <th>导航地址</th>
                <th>位名称</th>
                <th>位标记</th>
                <th>排序</th>
                <th width="200">操作</th>
            </tr>
        </thead>
        <tbody id="treet1">        
            {{foreach from=$data item=item}}
            <tr>
                <td class="align_center w30"><input type="checkbox" class="checkitem"  value="{{$item.nav_id}}" /></td>
                <td>{{$item.nav_name}}</td>
                <td>{{$item.nav_url}}</td>
                <td class="table-center">{{$item.pos_name}}</td>
                <td class="table-center">{{$item.identifier}}</td>
                <td class="align_center">
                {{if $item.nav_pid gt 0}}　　　　{{/if}}
                <span ectype="inline_edit" fieldname="sequence" fieldid="{{$item.nav_id}}" required="1" class="node_name editable">{{$item.sequence}}</span></td>
                <td class="align_left">
                    <a href="/admin/nav/edit/pid:{{$item.nav_pid}}/psid:{{$item.pos_id}}/id:{{$item.nav_id}}">编辑</a>　|　
                    <a href="javascript:if(confirm('您确定要删除该导航分类吗？'))window.location = '/admin/nav/del/id:{{$item.nav_id}}';">删除</a>                  
                    {{if $item.layer lt $max_layer and $item.parent_children_valid}}
                    　|　<a href="/admin/nav/add/pid:{{$item.nav_id}}/psid:{{$item.pos_id}}">新增下级</a>
                    {{/if}}
                </td>
            </tr>
            {{foreachelse}}
            <tr class="no_data">
                <td colspan="7">暂无记录</td>
            </tr>
            {{/foreach}}
        </tbody>
        <tfoot>
            <tr class="tr_pt10">
                <td class="align_center"><label for="checkall1">
                    <input id="checkall_2" type="checkbox" class="checkall">
                    </label></td>
                <td colspan="3" id="batchAction">
                	<span class="all_checkbox">
                    <label for="checkall_2">全选</label>
                    </span>&nbsp;&nbsp;
                    <input class="formbtn batchButton" type="button" value="删除" name="id" uri="del" presubmit="confirm('删除该分类将会同时删除该分类的所有下级分类，您确定要删除吗');" />
                </td>
            </tr>
        </tfoot>
    </table>
</div>
{{include file='admin/footer.php'}}