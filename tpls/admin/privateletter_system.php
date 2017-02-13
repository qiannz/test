{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/admin/inline_edit.js" ></script>
<div id="rightTop">
  <p>私信管理</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/privateletter/list">管理</a></li>
    <li><a class="btn1" href="/admin/privateletter/add">添加</a></li>
    <li><span>待发送通知</span></li>
  </ul>
</div>

<div class="mrightTop">
  <div class="fontl">
    <form method="post">
       <div class="left">
          通知内容：
          <input class="queryInput" type="text" name="content" value="{{$smarty.post.content|escape}}" />
          <input type="submit" class="formbtn" value="查询" />
      </div>
    </form>
  </div>
  <div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
  <table width="100%" cellspacing="0" class="dataTable">
    <!--{if $systems}-->
    <tr class="tatr1">
      <td width="150">接收者</td>
      <td width="30%">通知内容</td>
      <td>type</td>
      <td>opentype</td>
      <td>from_id</td>
      <td>消息类型</td>
      <td>友盟推送</td>
      <td>发送状态</td>
      <td>添加时间</td>
    </tr>
    <!--{/if}-->
    {{foreach from=$systems item=system}}
    <tr class="tatr2">
      <td>{{if $system.user_id}}用户名：{{$system.user_name}}<br /> 用户ID：{{$system.user_id}}{{else}}全部用户{{/if}}</td>
      <td><span ectype="inline_edit" fieldname="content" fieldid="{{$system.message_id}}" required="1" class="node_name editable">{{$system.message|escape}}</span></td>
      <td>{{$system.type}}</td>
      <td>{{$system.opentype}}</td>
      <td>{{if $system.from_id}}{{$system.from_id}}{{/if}}</td>
      <td>{{if $system.notice_type eq 1}}活动{{elseif $system.notice_type eq 2}}私信{{elseif $system.notice_type eq 3}}通知{{/if}}</td>
      <td>{{if $system.is_push eq 1}}是{{else}}否{{/if}}</td>
      <td>{{if $system.is_handle eq 1}}已发送{{else}}未发送{{/if}}</td>
      <td>{{$system.created|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
    </tr>
    {{foreachelse}}
    <tr class="no_data">
      <td colspan="9">暂无私信</td>
    </tr>
    {{/foreach}}
  </table>
  {{if $systems}}
  <div id="dataFuncs">
    <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
    <div class="clear"></div>
  </div>
  {{/if}}
</div>
{{include file='admin/footer.php'}}