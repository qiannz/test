{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/inline_edit.js"></script>
<div id="rightTop">
  <p>用户管理</p>
  <ul class="subnav">
    <li><span>留言管理</span></li>
  </ul>
</div>

<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="{{$_CONF.FORM_ACTION}}">
       <div class="left">
           提问：
           <input class="queryInput" type="text" name="question" value="{{$request.question}}" />
           用户：
           <input class="queryInput" type="text" name="author" value="{{$request.author}}" />
           分类：<select name="type">
           			<option value="">全部</option>
                    <option value="voucher" {{if $request.type eq 'voucher'}}selected="selected"{{/if}}>现金券</option>
                    <option value="buygood" {{if $request.type eq 'buygood'}}selected="selected"{{/if}}>团购商品</option>
                    <option value="commodity" {{if $request.type eq 'commodity'}}selected="selected"{{/if}}>商城商品</option>
		        </select>
          <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/message/list">撤销检索</a>
    </form>
  </div>
  <div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <table width="100%" cellspacing="0" class="dataTable">
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td>提问</td>      
      <td>用户</td>
      <td>分类</td>
      <td width="300">标题</td>
      <td>回复用户</td>
      <td>回复时间</td>
      <td>创建时间</td>
      <td>操作</td>
    </tr>
    {{foreach from=$data item=item}}
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="{{$item.tid}}" /></td>
      <td><a href="/admin/message/show/tid:{{$item.tid}}/page:{{$page}}">{{$item.question}}</a></td>
      <td>{{$item.user_name}}</td>
      <td>{{if $item.type eq 'voucher'}}现金券{{elseif $item.type eq 'buygood'}}团购商品{{elseif $item.type eq 'commodity'}}商城商品{{/if}}</td>
      <td>{{$item.title}}<br/>{{$item.ticket_uuid}}</td>
      <td>{{$item.repler}}</td>
      <td>{{if $item.reply_time}} {{$item.reply_time|date_format:'%Y-%m-%d %H:%M:%S'}} {{/if}}</td>
      <td>{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
      <td>      	
      	<a href="javascript:drop_confirm('确定删除', '/admin/message/del-thread-all/id:{{$item.tid}}/page:{{$page}}')">删除</a>
      </td>  
    </tr>
    {{foreachelse}}
    <tr class="no_data">
      <td colspan="9">暂无记录</td>
    </tr>
  {{/foreach}}
  </table>
<div id="dataFuncs">
    <div id="batchAction" class="left paddingT15"> &nbsp;&nbsp;
        <input class="formbtn batchButton" type="button" value="删除" uri="del-thread-all" presubmit="confirm('你确定要删除？');" />                
    </div>
    <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
    <div class="clear"></div>
  </div>  
</div>
{{include file='admin/footer.php'}}