{{include file='admin/header.php'}}
<div id="rightTop">
  <p>用户管理</p>
  <ul class="subnav">
    <li><a class="btn3" href="/admin/usergood/list/uid:{{$request.uid}}/uname:{{$request.uname}}">用户商品列表</a></li>
  </ul>
</div>
<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="{{$_CONF.FORM_ACTION}}">
       <div class="left">
            录入人：
            <input class="queryInput" type="text" name="uname" value="{{$request.uname}}" />
            <input class="querySelect" type="radio" name="isd" value="1" {{if $request.isd eq 1}}checked="checked"{{/if}} /> 删除　
            <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/usergood/list">撤销检索</a>
    </form>
  </div>
<div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <table width="100%" cellspacing="0" class="dataTable">
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td width="20%">商品标题 [<span style="color:red">原价/折扣价</span>]</td>
      <td>店铺名称</td>
	  <td>用户名</td>
	  <td>录入时间</td>
	  <td width="100">状态</td>
	  <td class="table-center">喜欢 / 收藏</td>
    </tr>
    {{foreach from=$data key=key item=item}}
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="{{$item.good_id}}" /></td>
      <td>{{$item.good_name}} [<span style="color:red">{{$item.org_price}} / {{$item.dis_price}}</span>]</td>
      <td>{{$item.shop_name}}</td>
      <td>{{$item.user_name}}</td>
      <td>{{if $item.created}}{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}{{/if}}</td>
      <td>
      		{{if $item.good_status eq '-1'}}审核不通过
      		{{elseif $item.good_status eq '0'}}未审核
			{{elseif $item.good_status eq '1'}}已审核
            {{/if}}
      </td>
      <td class="table-center">{{$item.concerned_number}} / {{$item.favorite_number}} </td>
    </tr>
   {{foreachelse}}
    <tr class="no_data">
      <td colspan="7">暂无数据</td>
    </tr>
    {{/foreach}}
  </table>
  <div id="dataFuncs">   
    <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
    <div class="clear"></div>
  </div>
</div>
{{include file='admin/footer.php'}}