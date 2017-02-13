{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/DatePicker/WdatePicker.js" ></script>
<div id="rightTop">
  <p>新手包</p>
  <ul class="subnav">
    <li><span>领奖列表</span></li>
    <li><a class="btn1" href="/admin/gift/gift-list">验证码管理</a></li>
    <li><a class="btn1" href="/admin/gift/add">新增验证码</a></li>
    <li><a class="btn1" href="/admin/gift/prize-list">奖品列表</a></li>
    <li><a class="btn1" href="/admin/gift/prize-add">新增奖品</a></li>
  </ul>
</div>
<div class="mrightTop">
  	<div class="fontl">
       <form method="post" action="{{$_CONF.FORM_ACTION}}">
       <div class="left">
            验证码：
            <input class="queryInput" type="text" name="captcha" {{if $gift_id}}value="{{$data.0.captcha}}"{{else}} value="{{$request.captcha}}"{{/if}} />
            手机号码：
            <input class="queryInput" type="text" name="mobile" value="{{$request.mobile}}" />
            发放状态：
			<select name="is_award" class="querySelect">
            	<option value="">全部</option>
                <option value="1" {{if $request.is_award eq 1}}selected="selected"{{/if}}>未发放</option>
                <option value="2" {{if $request.is_award eq 2}}selected="selected"{{/if}}>已发放</option>
            </select>
           手机类型：
			<select name="type" class="querySelect">
            	<option value="">全部</option>
                <option value="ios" {{if $request.type eq 'ios'}}selected="selected"{{/if}}>IOS</option>
                <option value="android" {{if $request.type eq 'android'}}selected="selected"{{/if}}>ANDROID</option>
            </select>           
           奖品类型：
           <select name="award_type" class="querySelect">
               <option value="">全部</option>
               <option value="1" {{if $request.award_type eq 1}}selected="selected"{{/if}}>自主选择</option>
               <option value="2" {{if $request.award_type eq 2}}selected="selected"{{/if}}>10元现金券</option>
               <option value="3" {{if $request.award_type eq 3}}selected="selected"{{/if}}>5元话费</option>
           </select>
			绑定时间：
			 <input class="queryInput" type="text" name="start_time" onFocus="WdatePicker({isShowClear:false,readOnly:false,dateFmt:'yyyy-MM-dd',Date:'now()'})" value="{{$request.start_time}}" />　~　 
             <input class="queryInput" type="text" name="end_time"   onFocus="WdatePicker({isShowClear:false,readOnly:false,dateFmt:'yyyy-MM-dd',Date:'now()'})" value="{{$request.end_time}}" />            
            <input type="submit" class="formbtn" value="查询" />      　
      </div>
     <a class="left formbtn1" href="/admin/gift/list">撤销检索</a>
     
<!--     <a class="left formbtn1" href="/admin/gift/export/captcha:{{$request.captcha}}/mobile:{{$request.mobile}}/is_award:{{$request.is_award}}/type:{{$request.award_type}}/stime:{{$request.end_time}}/etime:{{$request.end_time}}">导出</a>          
-->
	</div>
    </form>
	<div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <input type="hidden" name="gift_id" id="gift_id" value="{{$gift_id}}" />
  <table width="100%" cellspacing="0" class="dataTable">
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td>手机号码</td>
      <td>手机归属地</td>
      <td width="300">tokenkey</td>
	  <td>验证码</td>
	  <td>发放状态</td>
      <td>奖品内容</td>
	  <td>绑定时间</td>
      <td>操作</td>
    </tr>
    {{foreach from=$data key=key item=item}}
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="{{$item.gift_record_id}}" {{if $item.is_award eq 1}} disabled="disabled" {{/if}}/></td>
      <td>{{$item.mobile}}</td>
      <td>{{$item.area.MobileArea}}（{{$item.area.MobileType}}）</td>
      <td>{{$item.tokenkey}}</td>
      <td>{{$item.captcha}}</td>
      <td>{{if $item.is_award eq 0}}未发奖{{else}}已发奖{{/if}}</td>
      <td>{{$item.gift_value}}</td>
      <td>{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
      <td>{{if $item.is_award eq 0}}<a href="javascript:paymentOfPrizes({{$item.gift_record_id}})">发放奖品</a>{{else}}<a href="javascript:void(0); "></a>{{/if}}</td>
    </tr>
   {{foreachelse}}
    <tr class="no_data">
      <td colspan="9">暂无数据</td>
    </tr>
    {{/foreach}}
  </table>
  <div id="dataFuncs">
    <div class="left paddingT15">
        <input class="formbtn" type="button" value="批量发放" onclick="batchAudit()"/>
    </div>
    <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
    <div class="clear"></div>
  </div>
</div>
<script type="text/javascript">
function paymentOfPrizes(record_id){
    $.dialog({
        title: '提示',
        content: '确认发放奖品？',
        okValue: '确定',
        ok: function () {
            $.ajax({
                url:'/admin/gift/payment-prizes',
                type:'post',
                dataType:'json',
                data:{record_id:record_id},
                success:function(json){
                    if(json.status == 100){
                        $.dialog({
                            title: '提示',
                            content: '奖品发放成功',
                            okValue: '确定',
                            ok: function () {
                                location.href = location;
                            }
                        })
                    }
                }
            })
        },
        cancelValue:'取消',
        cancel: function () {}
    })
}

function batchAudit(){
    if($('.checkitem:checked').length == 0){
        alert('请选择确认发奖对象');
        return false;
    }

    var items = '';
    $('.checkitem:checked:enabled').each(function(){
        items += this.value + ',';
    });
    items = items.substr(0, (items.length - 1));
    var url = '/' + _M + '/' + _C + '/batch-audit';
    if(!items){
        alert('请选择确认发奖对象');
        return false;
    }
    $.dialog({
        title:'警告',
        content: '确认批量发放奖品？',
        okValue: '确定',
        ok: function () {
            $.ajax({
                url:url,
                type:'post',
                dataType:'json',
                data:{id:items,page:$('#page').val()},
                success:function(json){
                    if(json.msg == 100){
                        $.dialog({
                            title: '提示',
                            content: '奖品发放成功',
                            okValue: '确定',
                            ok: function () {
                                location.href = location;
                            }
                        })
                    }
                }
            })
        },
        cancelValue:'取消',
        cancel:function(){}
    });

}

</script>
{{include file='admin/footer.php'}}