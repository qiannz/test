{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/DatePicker/WdatePicker.js" ></script>
<div id="rightTop">
  <p>任务管理</p>
  <ul class="subnav">
    <li><span>店员返利</span></li>
  </ul>
</div>
<form method="post" action="{{$_CONF.FORM_ACTION}}">
<div class="mrightTop">
  <div class="fontl">
       <div class="left">	
            返利时间：
            <input class="queryInput" type="text" style="width:70px" name="start_time" onFocus="WdatePicker({isShowClear:false,readOnly:false,dateFmt:'yyyy-MM-dd',maxDate:'now()'})" value="{{if $request.start_time && $request.end_time}}{{$request.start_time}}{{else}}{{/if}}"/>-
            <input class="queryInput" type="text" style="width:70px" name="end_time"   onFocus="WdatePicker({isShowClear:false,readOnly:false,dateFmt:'yyyy-MM-dd',maxDate:'now()'})" value="{{if $request.start_time && $request.end_time}}{{$request.end_time}}{{else}}{{/if}}"/>
            用户名：
            <input class="queryInput" type="text" name="username"  value="{{$request.username}}"/>
            店铺名称：
            <input class="queryInput" type="text" name="shopname"  value="{{$request.shopname}}"/>
            订单号：
            <input class="queryInput" type="text" name="order_no"  value="{{$request.order_no}}"/>
            验证码：
            <input class="queryInput" type="text" name="captcha"  value="{{$request.captcha}}"/>           
      </div>
    </div>
</div>
<div class="mrightTop">
  <div class="fontl">
       <div class="left">
            返利金额：
            <input class="queryInput" type="text" style="width:50px" name="award_start" value="{{if $request.award_start && $request.award_end}}{{$request.award_start}}{{else}}{{/if}}"/>-
            <input class="queryInput" type="text" style="width:50px" name="award_end" value="{{if $request.award_start && $request.award_end}}{{$request.award_end}}{{else}}{{/if}}"/>
            返利次数：
            <input class="queryInput" type="text" style="width:50px" name="rebateNum_start"  value="{{$request.rebateNum_start}}"/>-
            <input class="queryInput" type="text" style="width:50px" name="rebateNum_end" value="{{$request.rebateNum_end}}"/>
            <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/rebate/list">撤销检索</a>
    
    </div>
	<div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
</form>
<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <table width="100%" cellspacing="0" class="dataTable">
    {{if $rebateList.0.num }}
    <tr class="tatr1">
      <td>用户名</td>
      <td>店铺名称</td>
	  <td>返利次数</td>
	  <td>返利时间</td>
      <td>操作</td>
    </tr>
      {{else}}
      <tr class="tatr1">
          <td>用户名</td>
          <td>店铺名称</td>
          <td>返利金额</td>
          <td>订单号</td>
          <td>验证码</td>
          <td>返利时间</td>
          <td>操作</td>
      </tr>
      {{/if}}
    {{foreach from=$rebateList key=key item=item}}
      {{if $item.num}}
    <tr class="tatr2">
      <td>{{$item.user_name}}</td>
      <td>{{$item.shop_name}}</td>
      <td>{{$item.num}}</td>
      <td>{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
      <td><a href="/admin/rebate/detail/user_id:{{$item.user_id}}/start_time:{{$request.start_time}}/end_time:{{$request.end_time}}/username:{{$request.username}}/shopname:{{$request.shopname}}/award_start:{{$request.award_start}}/award_end:{{$request.award_end}}">查看明细</a></td>
    </tr>
      {{else}}
      <tr class="tatr2">
          <td>{{$item.user_name}}</td>
          <td>{{$item.shop_name}}</td>
          <td>{{$item.award}}</td>
          <td>{{$item.order_no}}</td>
          <td>{{$item.captcha}}</td>
          <td>{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
          <td><a href="/admin/rebate/detail/id:{{$item.id}}">查看明细</a></td>
      </tr>
      {{/if}}
    {{/foreach}}
  </table>
    <div id="dataFuncs">
    <div class="left paddingT15"> &nbsp;&nbsp;

    </div>
    <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
    <div class="clear"></div>
  </div>
</div>

{{include file='admin/footer.php'}}