{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/inline_edit.js" ></script>
<div id="rightTop">
  <p>管理员管理</p>
  <ul class="subnav">
    <li><span>管理</span></li>
    <li><a class="btn1" href="add">添加</a></li>
  </ul>
</div>

<div class="mrightTop">
  <div class="fontl">
    <form method="post">
       <div class="left">
          <select class="querySelect" name="field_name">{{html_options options=$query_fields selected=$smarty.request.field_name}}</select>
          <input class="queryInput" type="text" name="field_value" value="{{$smarty.request.field_value}}" />
          排序:
          <select class="querySelect" name="sort">{{html_options options=$sort_options selected=$smarty.request.sort}}</select>
          <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/manager/list">撤销检索</a>
    </form>
  </div>
  <div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
  <table width="100%" cellspacing="0" class="dataTable">
    <!--{{if $users}}-->
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td>会员名</td>
      <td>最后登录时间</td>
      <td>最后登录IP</td>
      <td>用户组</td>
      <td>组管理员</td>
      <td>状态</td>
      <td>操作</td>
    </tr>
    <!--{{/if}}-->
    <!--{{foreach from=$users item=user}}-->
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="{{$user.a_id}}" {{if $user.role_id eq 1}}disabled="disabled"{{/if}} /></td>
      <td>{{$user.userid}}</td>
      <td><!--{{if $user.logintime}}-->{{$user.logintime|date_format:'%Y-%m-%d %H:%M:%S'}}<!--{{/if}}--></td>
      <td>{{$user.loginip}}</td>
      <td>
      {{if $user.role_id eq 1}}
      	创始人
      {{else}}
      	{{insert name="groupName" gid = $user.gid}}
      {{/if}}
      </td>
      <td>{{if $user.role_id eq 2}} {{if $user.group_admin eq 0}}否{{elseif $user.group_admin eq 1}}是{{/if}} {{/if}}</td>
      <td>{{if $user.is_disabled eq 0}}可用{{else}}禁用{{/if}}</td>
      <td class="handler">
      <span style="width: 100px">
      	 {{if $user.role_id eq 1}}
         	 <a href="/admin/manager/edit/id:{{$user.id}}">编辑</a>
         {{elseif $user.role_id eq 2}}
             <a href="/admin/manager/edit/id:{{$user.id}}">编辑</a> |
             <a href="/admin/manager/disabled/id:{{$user.id}}">{{if $user.is_disabled eq 0}}禁用{{else}}启用{{/if}}</a>
             <!--<a href="javascript:drop_confirm('你确定要删除它吗？', '/admin/manager/drop/id:{{$user.id}}');">删除</a>-->
         {{/if}}
      </span>
      </td>
    </tr>
    <!--{{foreachelse}}-->
    <tr class="no_data">
      <td colspan="8">暂无管理员</td>
    </tr>
    <!--{{/foreach}}-->
  </table>
  <!--{{if $users}}-->
  <div id="dataFuncs">
    <div id="batchAction" class="left paddingT15"> &nbsp;&nbsp;
      <input class="formbtn batchButton" type="button" value="删除" name="id" uri="drop" presubmit="confirm('你确定要删除它吗？');" />
    </div>
    <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
    <div class="clear"></div>
  </div>
  <!--{{/if}}-->
</div>
{{include file='admin/footer.php'}}