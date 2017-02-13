{{include file='admin/header.php'}}
<div id="rightTop">
  <p>组管理</p>
  <ul class="subnav">
    <li><span>管理</span></li>
    <li><a class="btn1" href="add">添加</a></li>
  </ul>
</div>

<div class="tdare">
  <table width="400" cellspacing="0" class="dataTable">
    <!--{{if $groups}}-->
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td>组名称</td>
      <td class="handler">操作</td>
    </tr>
    <!--{{/if}}-->
    <!--{{foreach from=$groups item=group}}-->
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="{{$group.gid}}" /></td>
      <td>{{$group.g_name}}</td>
      <td>
      <span style="width: 100px">
      <a href="/admin/group/edit/id:{{$group.gid}}">编辑</a>　|　
      <a href="javascript:drop_confirm('你确定要删除它吗？', '/admin/group/drop/id:{{$group.gid}}');">删除</a>
      </span>
      </td>
    </tr>
    <!--{{foreachelse}}-->
    <tr class="no_data">
      <td colspan="3">暂无组</td>
    </tr>
    <!--{{/foreach}}-->
  </table>
  <!--{{if $groups}}-->
  <div id="dataFuncs">
    <div id="batchAction" class="left paddingT15"> &nbsp;&nbsp;
      <input class="formbtn batchButton" type="button" value="删除" name="id" uri="drop" presubmit="confirm('你确定要删除它吗？');" />
    </div>
    <div class="clear"></div>
  </div>
  <!--{{/if}}-->
</div>
{{include file='admin/footer.php'}}