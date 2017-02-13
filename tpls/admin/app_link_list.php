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
        <li><span>链接设置</span></li>
        <li><a class="btn3" href="/admin/link/add">链接新增</a></li>
    </ul>
</div>

<div class="info2">
    <table class="distinction">
        <thead>
            <tr>
                <th><input id="checkall_1" type="checkbox" class="checkall" /></th>
                <th><span class="all_checkbox"><label for="checkall_1">全选</label></span>名称</th>
                <th>标记</th>
                <th>排序</th>
                <th width="200">操作</th>
            </tr>
        </thead>
        <tbody id="treet1">        
            {{foreach from=$data item=item}}
            <tr>
                <td class="align_center w30"><input type="checkbox" class="checkitem"  value="{{$item.id}}" /></td>
                <td>{{$item.name}}</td>
                <td>{{$item.mark}}</td>
                <td class="align_center">
                {{if $item.pid gt 0}}　　　　{{/if}}
                <span ectype="inline_edit" fieldname="sequence" fieldid="{{$item.id}}" required="1" class="node_name editable">{{$item.sequence}}</span></td>
                <td class="align_left">
                    <a href="/admin/link/edit/pid:{{$item.pid}}/id:{{$item.id}}">编辑</a>　|　
                    <a href="javascript:if(confirm('您确定要删除该链接设置吗？'))window.location = '/admin/link/del/id:{{$item.id}}';">删除</a>                  
                    {{if $item.layer lt $max_layer and $item.parent_children_valid}}
                    　|　<a href="/admin/link/add/pid:{{$item.id}}">新增下级</a>
                    {{/if}}
                </td>
            </tr>
            {{foreachelse}}
            <tr class="no_data">
                <td colspan="5">暂无记录</td>
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