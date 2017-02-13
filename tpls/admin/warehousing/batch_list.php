{{include file='admin/header.php'}}
<div id="rightTop">
  <p>仓储管理</p>
  <ul class="subnav">
    <li><span>入库记录</span></li>
    <li><a class="btn4" href="/admin/batch/choose-shop">新增入库</a></li>
  </ul>
</div>
<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="{{$_CONF.FORM_ACTION}}">
       <div class="left">
            批次：
            <input class="queryInput" type="text" name="batch" value="{{$request.batch}}" />
            品牌：
            <input class="queryInput" type="text" name="bname" value="{{$request.bname}}" />
            入库时间：
            <input class="queryInput" type="text" name="stime" id="stime" onFocus="WdatePicker({isShowClear:false,readOnly:false,dateFmt:'yyyy-MM-dd HH'})"  value="{{$request.stime}}"  />　-　
            <input class="queryInput" type="text" name="etime" id="etime" onFocus="WdatePicker({isShowClear:false,readOnly:false,dateFmt:'yyyy-MM-dd HH',minDate:'#F{$dp.$D(\'stime\',{H:1})}'})"  value="{{$request.etime}}"  />
            <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/batch/list">撤销检索</a>
    </form>
  </div>
<div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <table width="100%" cellspacing="0" class="dataTable">
    <tr class="tatr1">
      <td>批次</td>
      <td>店铺名称</td>
      <td>品牌</td>
	  <td>入库数量</td>
      <td>入库时间</td>
      <td>入库人</td>
	  <td>存放位置</td>
      <td>状态</td>
      <td>审核人</td>
	  <td>审核时间</td>
      <td>操作</td>
    </tr>
    {{foreach from=$data key=key item=item}}
    <tr class="tatr2">
      <td>{{$item.good_batch}}</td>
      <td>{{$item.shop_name}}</td>
      <td>{{$item.brand_name}}</td>
      <td>{{$item.quantity}}</td>
      <td>{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
      <td>{{$item.creator_user_name}}</td>
      <td>{{$item.location}}</td>
	  <td>{{if $item.status}}{{else}}未审核{{/if}}</td>
      <td>{{if $item.inspector}}{{$item.inspector}}{{/if}}</td>    
      <td>{{if $item.checked}}{{$item.checked|date_format:'%Y-%m-%d %H:%M:%S'}}{{/if}}</td>
      <td>
      		{{if $item.status eq 1}}
            
            {{else}}
            <a href="javascript:audit({{$item.batch_id}})">审核</a>
            {{/if}}
      </td>
    </tr>
   {{foreachelse}}
    <tr class="no_data">
      <td colspan="11">暂无记录</td>
    </tr>
    {{/foreach}}
  </table>
  <div id="dataFuncs">
    <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
    <div class="clear"></div>
  </div>
</div>

<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/DatePicker/WdatePicker.js" ></script>
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" charset="utf-8" ></script>
<script type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" charset="utf-8" ></script>
<script type="text/javascript">
function audit(id) {
	alert(1);
}
</script>
{{include file='admin/footer.php'}}