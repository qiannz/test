{{include file='admin/header.php'}}
<div id="rightTop">
  <p>权限分配</p>
  <ul class="subnav">
    <li><span>组列表</span></li>
  </ul>
</div>

<div class="tdare">
  <table width="400" cellspacing="0" class="dataTable">
    <!--{{if $groups}}-->
    <tr class="tatr1">
      <td class="handler">组名称</td>
      <td class="handler">操作</td>
    </tr>
    <!--{{/if}}-->
    <!--{{foreach from=$groups item=group}}-->
    <tr class="tatr2">
      <td class="handler">{{$group.g_name}}</td>
      <td class="handler"><a href="/admin/purview/allot/gid:{{$group.gid}}">分配</a></td>
    </tr>
    <!--{{foreachelse}}-->
    <tr class="no_data">
      <td colspan="3">暂无组</td>
    </tr>
    <!--{{/foreach}}-->
  </table>
</div>
{{include file='admin/footer.php'}}