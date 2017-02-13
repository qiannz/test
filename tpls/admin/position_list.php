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
        <li><span>推荐位列表</span></li>
        <li><a class="btn3" href="/admin/position/add">新增推荐位</a></li>
    </ul>
</div>
<div class="info2">
    <table class="distinction">
        <thead>
            <tr>
                <th><input id="checkall_1" type="checkbox" class="checkall" /></th>
                <th><span class="all_checkbox"><label for="checkall_1">全选</label></span>推荐位名称</th>
                <th class="table-left">推荐位标记</th>
                <th class="table-left">链接地址</th>
                <th>图片宽度</th>
                <th>图片高度</th>
                <th>排序</th>
                <th width="200">操作</th>
            </tr>
        </thead>
        <tbody id="treet1">        
            {{foreach from=$data item=item}}
            <tr>
                <td class="align_center w30"><input type="checkbox" class="checkitem"  value="{{$item.pos_id}}" /></td>
                <td >{{$item.pos_name}}</td>
                <td class="table-left">{{$item.identifier}}</td>
                <td class="table-left">{{$item.pos_url}}</td>
                <td class="align_center">{{if $item.width}}{{$item.width}}{{/if}} </td>
                <td class="align_center">{{if $item.height}}{{$item.height}}{{/if}}</td>
                <td class="align_center">
                {{if $item.pos_pid gt 0}}　　　　{{/if}}
                <span ectype="inline_edit" fieldname="sequence" fieldid="{{$item.pos_id}}" required="1" class="node_name editable">{{$item.sequence}}</span></td>
                <td class="align_left">
                    <a href="/admin/position/edit/pid:{{$item.pos_pid}}/id:{{$item.pos_id}}">编辑</a>　|　
                    <a href="javascript:if(confirm('您确定要删除该版块吗'))window.location = '/admin/position/del/id:{{$item.pos_id}}';">删除</a>                  
                    {{if $item.layer lt $max_layer and $item.parent_children_valid}}
                    　|　<a href="/admin/position/add/pid:{{$item.pos_id}}">新增下级</a>
                    {{/if}}
                </td>
            </tr>
            {{foreachelse}}
            <tr class="no_data">
                <td colspan="8">暂无记录</td>
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