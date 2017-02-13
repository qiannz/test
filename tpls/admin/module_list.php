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
<div id="rightTop">
    <p>模块管理</p>
    <ul class="subnav">
        <li><span>管理</span></li>
        <li><a class="btn1" href="/admin/module/add">新增</a></li>
    </ul>
</div>
<div class="tdare">
    <table class="distinction">
        <!-- {{if $acategories}} -->
        <thead>
            <tr>
                <th class="w30"><input id="checkall_1" type="checkbox" class="checkall" /></th>
                <th width="20%"><span class="all_checkbox">
                    <label for="checkall_1">全选</label>
                    </span>分类名称</th>
                <th>分类标记</th>
                <th width="20%">模块路径</th>
                <th>排序</th>
                <th class="handler">操作</th>
            </tr>
        </thead>
        <tbody id="treet1">
            <!-- {{/if}} -->
            <!--{{foreach from=$acategories item=acategory}}-->
            <tr>
                <td class="align_center w30"><input type="checkbox" class="checkitem" value="{{$acategory.mid}}" /></td>
                <td class="node"><span ectype="inline_edit" fieldname="m_name" fieldid="{{$acategory.mid}}" required="1" class="node_name editable">{{$acategory.m_name}}</span></td>
                <td>{{$acategory.mark}}</td>
                <td>{{if $acategory.layer gt 1}}/{{$_CONF.Default_Manager_Module_Path}}/{{$acategory.m_path}}{{/if}}</td>
                <td class="align_center">
                	{{if $acategory.pid neq 0}}
                    　　　　
                    {{/if}}
                    <span ectype="inline_edit" fieldname="sequence" fieldid="{{$acategory.mid}}" datatype="pint" maxvalue="99" class="editable">{{$acategory.sequence}}</span></td>
                <td class="handler"><span>
                    <a href="/admin/module/edit/id:{{$acategory.mid}}">编辑</a> | <a href="javascript:if(confirm('删除该分类将会同时删除该分类的所有下级分类，您确定要删除吗'))window.location = '/admin/module/drop/id:{{$acategory.mid}}';">删除</a>
                    <!-- {{if $acategory.layer lt $max_layer and $acategory.parent_children_valid}} -->
                    |
                    <a href="/admin/module/add/pid:{{$acategory.mid}}">新增下级</a>
                    <!--{{/if}}-->
                    </span> </td>
            </tr>
            <!--{{foreachelse}}-->
            <tr class="no_data">
                <td colspan="6">暂无模块</td>
            </tr>
            <!--{{/foreach}}-->
            <!-- {{if $acategories}} -->
        </tbody>
        <!-- {{/if}} -->
        <tfoot>
            <tr class="tr_pt10">
                <!-- {{if $acategories}} -->
                <td class="align_center"><label for="checkall1">
                    <input id="checkall_2" type="checkbox" class="checkall">
                    </label></td>
                <td colspan="3" id="batchAction"><span class="all_checkbox">
                    <label for="checkall_2">全选</label>
                    </span>&nbsp;&nbsp;
                    <input class="formbtn batchButton" type="button" value="删除" name="id" uri="drop" presubmit="confirm('删除该分类将会同时删除该分类的所有下级分类，您确定要删除吗');" />
                    <!--<input class="formbtn batchButton" type="button" value="lang.update_order" name="id" presubmit="updateOrder(this);" />-->
                </td>
                <!--{{/if}}-->
            </tr>
        </tfoot>
    </table>
</div>
{{include file='admin/footer.php'}}