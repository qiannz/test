{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/admin/inline_edit.js" ></script>
<div id="rightTop">
  <p>私信管理</p>
  <ul class="subnav">
    <li><span>管理</span></li>
    <li><a class="btn1" href="/admin/privateletter/add">添加</a></li>
    <li><a class="btn1" href="/admin/privateletter/system">待发送通知</a></li>
  </ul>
</div>

<div class="mrightTop">
  <div class="fontl">
    <form method="post">
       <div class="left">
   	接收者：
          <input class="queryInput" type="text" name="field_value" value="{{$request.field_value|escape}}" /> 
          私信内容：
          <input class="queryInput" type="text" name="content" value="{{$request.content|escape}}" />
          私信类型：
          <select class="querySelect" name="type">
          {{foreach from=$message_type key=key item=item}}
          <option value="{{$key}}" {{if $key eq $request.type}}selected="selected"{{/if}}>{{$item}}</option>
          {{/foreach}}
          </select>
          <input type="submit" class="formbtn" value="查询" />
      </div>
       <a class="left formbtn1" href="/admin/privateletter/list">撤销检索</a>
    </form>
  </div>
  <div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
  <table width="100%" cellspacing="0" class="dataTable">
    <!--{if $messages}-->
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td width="150">接收者</td>
      <td width="150" >私信类型</td>
      <td width="150">opentype</td>
      <td width="100">from_id</td>
      <td width="150">发送者</td>
      <td width="100">友盟推送</td>
      <td>私信内容</td>
      <td>创建时间</td>
    </tr>
    <!--{/if}-->
    {{foreach from=$messages item=message}}
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="{{$message.id}}" /></td>
      <td>用户名：{{$message.to_user_name}}<br /> 用户ID：{{$message.user_id}}</td>
      <td>{{$message_type[$message.type]}}</td>
      <td>{{$message.opentype}}</td>
      <td>{{if $message.from_id}}{{$message.from_id}}{{/if}}</td>
      <td>{{if $message.charter_user_id}}用户名：{{$message.send_user_name}}<br />用户ID：{{$message.charter_user_id}}{{/if}}</td>
      <td>{{if $message.is_push eq 1}}是{{else}}否{{/if}}</td>
      <td>{{$message.message}}</td>
      <td>{{$message.created|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
    </tr>
    {{foreachelse}}
    <tr class="no_data">
      <td colspan="9">暂无私信</td>
    </tr>
    {{/foreach}}
  </table>
  {{if $messages}}
  <div id="dataFuncs">
	<div id="batchAction" class="left paddingT15"> &nbsp;&nbsp;
      <input class="formbtn batchButton" type="button" value="删除" name="id" uri="drop" presubmit="confirm('你确定要删除它吗？');" />
    </div>
    <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
    <div class="clear"></div>
  </div>
  {{/if}}
</div>
{{include file='admin/footer.php'}}