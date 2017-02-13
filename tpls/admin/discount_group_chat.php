{{include file='admin/header.php'}}
<style type="text/css">
.qs{width:100px; height:24px; line-height:24px; font-weight:bold; color:#F00; text-align:center; font-size:24px;}
.as{width:100px; height:24px; line-height:24px; font-weight:bold; color:#0F0; text-align:center; font-size:24px;}
.im{text-align:center;}
</style>
<div id="rightTop">
  <p>用户管理</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/{{$type}}/list/page:{{$page}}">{{if $type eq "special"}}专题管理{{else}}折扣管理{{/if}}</a></li>
    <li><span>群聊</span></li>
  </ul>
</div>

<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <table width="60%" cellspacing="0" class="dataTable">
    {{foreach from=$data key=key item=item}}
    <tr class="tatr2">
      <td width="100">
      <p class="im"><img src="{{$item.avatar}}"></p>      
      </td>
      <td>{{$item.question}}<br /><br />{{$item.user_name}} / {{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
      <td width="200">
        <a href="javascript:drop_confirm('确定删除', '/admin/discount/del-group-chat-post/did:{{$did}}/gcid:{{$item.id}}/type:{{$type}}')">删除</a>
      </td>
    </tr>
    {{/foreach}}
  </table>
</div>
{{include file='admin/footer.php'}}