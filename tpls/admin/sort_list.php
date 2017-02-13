{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/inline_edit.js"></script>
<div id="rightTop">
  <p>全站分类</p>
  <ul class="subnav">
    <li><span>分类管理</span></li>
    <li><a class="btn1" href="/admin/sort/sort-add">分类新增</a></li>
    <li><a class="btn1" href="/admin/sort/category-list">类别管理</a></li>
    <li><a class="btn1" href="/admin/sort/category-add">类别添加</a></li>
  </ul>
</div>

<div class="tdare">
  <input type="hidden" name="page" id="page" value="{$page}" />
  <table width="800" cellspacing="0" class="dataTable">
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td>分类名称</td>
      <td width="250">分类标识</td>
      <td>所属类别</td>
      <td>排序</td>
      <td class="handler">
      <select name="tid" id="tid" onChange="jumpTo()">
      <option value="0">全部</option>
      {{foreach from=$categories key=key item=category}}
      	<option value="{{$category.sort_id}}"{{if $tid eq $category.sort_id}} selected="selected"{{/if}}>{{$category.sort_name}}</option>
      {{/foreach}}
      </select>
      <script language="javascript">
      	function jumpTo(){
			location.href = '/admin/sort/list/tid:' + $('#tid').val()+ '/page:1';
		}
      </script>
      </td>
    </tr>
    {{foreach from=$sorts item=sort}}
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="{{$sort.sort_detail_id}}" /></td>
      <td>{{$sort.sort_detail_name}}</td>
      <td>{{$sort.sort_detail_mark}}</td>
      <td>{{$sort.sort_name}}</td>
      <td><span ectype="inline_edit" fieldname="sequence" fieldid="{{$sort.sort_detail_id}}" required="1" class="node_name editable">{{$sort.sequence}}</span></td>
      <td>
      <span style="width: 100px">
      <a href="/admin/sort/sort-edit/id:{{$sort.sort_detail_id}}/page:{{$page}}">编辑</a>
       | 
      <a href="javascript:drop_confirm('你确定要删除它吗？', '/admin/sort/sort-del/id:{{$sort.sort_detail_id}}/page:{{$page}}');">删除</a>
      </span>
      </td>
    </tr>
    {{foreachelse}}
    <tr class="no_data">
      <td colspan="6">暂无分类</td>
    </tr>
    {{/foreach}}
  </table>
  {{if $sorts}}
  <div id="dataFuncs">
    <div id="batchAction" class="left paddingT15"> &nbsp;&nbsp;
      <input class="formbtn batchButton" type="button" value="删除" name="id" uri="sort-del" presubmit="confirm('你确定要删除它吗？');" />
    </div>
    <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
  </div>
  {{/if}}
</div>
{{include file='admin/footer.php'}}