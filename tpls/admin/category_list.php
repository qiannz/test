{{include file='admin/header.php'}}
<div id="rightTop">
  <p>全站分类</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/sort/list">分类管理</a></li>
    <li><a class="btn1" href="/admin/sort/sort-add">分类新增</a></li>
    <li><span>类别管理</span></a></li>
    <li><a class="btn1" href="/admin/sort/category-add">类别添加</a></li>
  </ul>
</div>

<div class="tdare">
  <table width="600" cellspacing="0" class="dataTable">
    {{if $categories}}
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td>类别ID</td>
      <td>类别名称</td>
      <td>类别标记</td>
      <td class="handler">操作</td>
    </tr>
    {{/if}}
    {{foreach from=$categories item=category}}
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="{{$category.sort_id}}" /></td>
      <td>{{$category.sort_id}}</td>
      <td>{{$category.sort_name}}</td>
      <td>{{$category.sort_unique}}</td>
      <td>
      <span style="width: 100px">
      <a href="/admin/sort/category-edit/id:{{$category.sort_id}}">编辑</a>
       | 
      <a href="javascript:drop_confirm('删除该类别的同时将删除所属它的所有分类，你确定要删除它吗？', '/admin/sort/category-del/id:{{$category.sort_id}}');">删除</a>
      </span>
      </td>
    </tr>
    {{foreachelse}}
    <tr class="no_data">
      <td colspan="5">暂无分类</td>
    </tr>
    {{/foreach}}
  </table>
  {{if $categorys}}
  <div id="dataFuncs">
    <div id="batchAction" class="left paddingT15"> &nbsp;&nbsp;
      <input class="formbtn batchButton" type="button" value="删除" name="id" uri="category-del" presubmit="confirm('你确定要删除它吗？');" />
    </div>
    <div class="clear"></div>
  </div>
  {{/if}}
</div>
{{include file='admin/footer.php'}}