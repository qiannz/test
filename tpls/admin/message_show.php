{{include file='admin/header.php'}}
<style type="text/css">
.qs{width:100px; height:24px; line-height:24px; font-weight:bold; color:#F00; text-align:center; font-size:24px;}
.as{width:100px; height:24px; line-height:24px; font-weight:bold; color:#0F0; text-align:center; font-size:24px;}
.im{text-align:center;}
</style>
<div id="rightTop">
  <p>用户管理</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/message/list/page:{{$page}}">留言管理</a></li>
    <li><span>留言明细</span></li>
  </ul>
</div>

<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <table width="100%" cellspacing="0" class="dataTable">
    {{foreach from=$data key=key item=item}}
    <tr class="tatr2">
      <td width="100">
      <p class="im"><img src="{{$item.avator}}"></p>
      {{if $item.position eq 'L'}}
      	<p class="qs">Q</p>
      {{else}}
      	<p class="as">A</p>
      {{/if}}      
      </td>
      <td>{{$item.question}}<br /><br />{{$item.user_name}} / {{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
      <td width="200">{{if $item.first eq 0}}<a href="javascript:drop_confirm('确定删除', '/admin/message/del-post/tid:{{$item.tid}}/id:{{$item.pid}}/page:{{$page}}')">删除</a>{{/if}}</td>
    </tr>
    {{/foreach}}
  </table>
</div>
{{include file='admin/footer.php'}}