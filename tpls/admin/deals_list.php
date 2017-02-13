{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css" />
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js"></script>
<script charset="utf-8" type="text/javascript" src="/js/inline_edit.js"></script>
<div id="rightTop">
  <p>商品管理</p>
  <ul class="subnav">
    <li><span>特卖管理</span></li>
    <li><a class="btn4" href="/admin/deals/add-edit">新增特卖</a></li>
  </ul>
</div>

<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="{{$_CONF.FORM_ACTION}}">
       <div class="left">
          	特卖名称：	
           <input class="queryInput" type="text" name="name" value="{{$request.name}}" />　
           <input type="submit" class="formbtn" value="查询" />
      </div>
      
      <a class="left formbtn1" href="/admin/deals/list">撤销检索</a>
      
    </form>
  </div>
  <div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <table width="100%" cellspacing="0" class="dataTable">
    <tr class="tatr1">
      <td width="300">券ID</td>
      <td>特卖名称</td>
      <td>折扣信息</td>
      <td>是否有券</td>
      <td>排序</td>
      <td>开始时间</td>
      <td>结束时间</td>
      <td width="150">操作</td>
    </tr>
    <!--{{foreach from=$data key=key item=item}}-->
    <tr class="tatr2">
      <td>{{$item.voucher_id}}</td>
      <td>{{$item.deals_name}}</td>
      <td>{{$item.discount}}</td>
      <td>{{if $item.had_ticket eq 1}}有{{else}}无{{/if}}</td>
      <td><span ectype="inline_edit" fieldname="sequence" fieldid="{{$item.deals_id}}" required="1" class="node_name editable">{{$item.sequence}}</span></td>
      <td>{{$item.start_time|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
      <td>{{$item.end_time|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
      <td>
      	 <a href="/admin/deals/add-edit/id:{{$item.deals_id}}/page:{{$page}}">编辑</a>　 | 
         <a href="/admin/deals/del/id:{{$item.deals_id}}/page:{{$page}}">删除</a>　
      </td>
    </tr>
    <!--{{foreachelse}}-->
    <tr class="no_data">
      <td colspan="7">暂无记录</td>
    </tr>
    <!--{{/foreach}}-->
  </table>
  <div id="dataFuncs">
    <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
    <div class="clear"></div>
  </div>
</div>
{{include file='admin/footer.php'}}