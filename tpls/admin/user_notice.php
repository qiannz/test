{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css" />
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js"></script>
<div id="rightTop">
  <p>用户管理</p>
  <ul class="subnav">
    <li><a class="btn4" href="/admin/user/list">用户列表</a></li>
    <li><span>个人消息</span></li>
  </ul>
</div>

<div class="mrightTop">
  <div class="fontl">

  </div>
  <div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <table width="100%" cellspacing="0" class="dataTable">
    <!--{{if $data}}-->
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td>通知内容</td>
      <td>通知类型</td>
      <td>发起人</td>
      <td>来源ID</td>
      <td>订单号</td>
      <td>商户</td>
      <td>已读</td>
      <td>创建时间</td>
    </tr>
    <!--{{/if}}-->
    <!--{{foreach from=$data key=key item=item}}-->
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="{{$item.user_id}}" /></td>
      <td>{{$item.message}}</td>
      <td>{{$item.type}}</td>
      <td>{{if $item.charter_user_id}}<img src="{{$item.charter_member_avator}}" /><br/>用户名：{{$item.charter_member}}<br/>用户ID：{{$item.charter_user_id}}{{else}}名品街{{/if}}</td>
      <td>{{$item.from_id}}</td>
      <td>{{$item.order_no}}</td>
      <td>{{if $item.is_auth eq 0}}不是{{else}}是{{/if}}</td>
      <td>{{if $item.is_read eq 0}}未{{else}}已{{/if}}</td>
      <td>{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
    </tr>
    <!--{{foreachelse}}-->
    <tr class="no_data">
      <td colspan="9">暂无记录</td>
    </tr>
    <!--{{/foreach}}-->
  </table>
  <!--{{if $data}}-->
  <div id="dataFuncs">
    <div id="batchAction" class="left paddingT15"> &nbsp;&nbsp;
      
    </div>
    <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
    <div class="clear"></div>
  </div>
  <!--{{/if}}-->
</div>
{{include file='admin/footer.php'}}