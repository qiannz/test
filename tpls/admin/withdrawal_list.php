{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/DatePicker/WdatePicker.js" ></script>
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
<div id="rightTop">
    <p>任务管理</p>
    <ul class="subnav">
        <li><span>提现记录</span></li>
    </ul>
</div>

<div class="mrightTop">
    <div class="fontl">
        <form method="post" action="{{$_CONF.FORM_ACTION}}">
            <div class="left">
                开始日期：
                <input class="queryInput" type="text" style="width:140px;" name="start_time" onFocus="WdatePicker({isShowClear:false,readOnly:false,dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'now()'})" value="{{$request.start_time}}" />
                &nbsp;
                结束日期：
                <input class="queryInput" type="text" style="width:140px;" name="end_time" onFocus="WdatePicker({isShowClear:false,readOnly:false,dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'now()'})" value="{{$request.end_time}}" />
                用户名：
                <input class="queryInput" type="text" name="user_name" value="{{$request.user_name}}" />
                <input type="submit" class="formbtn" value="查询" />
            </div>
            <a class="left formbtn1" href="/admin/withdrawal/list">撤销检索</a>
        </form>
    </div>
    <div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>


<div class="mrightTop">
    <div class="fontl">
        <div class="left">
            <span>本时间段内共有<b style="color:red"> {{$statistics.num}} </b>个用户已提交提现申请，总金额<b style="color:red"> {{ if $statistics.total_amount}}{{$statistics.total_amount}}{{else}} 0 {{/if}}</b>元</span>
        </div>
    </div>
</div>



<div class="tdare">

        <table width="100%" cellspacing="0" class="dataTable">
        <tr class="tatr1">
            <td width="10%">用户名</td>
            <td width="10%">用户身份</td>
            <td width="10%">真实姓名</td>
            <td width="10%">申请日期</td>
            <td width="10%">申请提现金额</td>
            <td width="7%">提现方式</td>
            <td width="30%">账号/卡号</td>
            <td width="5%">操作</td>
        </tr>

        {{foreach from=$withdrawalList item=item}}
        <tr class="tatr2">
            <td id="test">{{$item.user_name}}</td>
            <input type="hidden" value="{{$item.user_id}}"/>
            <td>{{if $item.user_type == 1}}普通用户 {{elseif $item.user_type == 2}}认证商户 {{elseif $item.user_type == 3}}营业员{{/if}}</td>
            <td>{{if $item.paypal_name}}{{$item.paypal_name}}{{else if $item.bank_name}}{{$item.bank_name}}{{/if}}</td>
            <td>{{$item.app_time|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
            <td>{{$item.amount}}</td>
            <td>{{if $item.paypal_account}}支付宝转账{{else}}银行转账{{/if}}</td>
            <td>{{if $item.paypal_account}}【支付宝账号】:{{$item.paypal_account}}{{else if $item.bank_name && $item.bank_number}}【开户银行】:{{$item.bank_name}}  【卡号】:{{$item.bank_number}}{{/if}}</td>
            <td><a href="javascript:void (0)" onclick="query('{{$item.money_id}}','{{$item.user_id}}','{{$item.paypal_name}}','{{$item.paypal_account}}','{{$item.amount}}','{{$item.bank_name}}','{{$item.bank_number}}')">放款</a></td>
        </tr>
        {{foreachelse}}
        <tr class="no_data">
            <td colspan="6">暂无记录</td>
        </tr>
        {{/foreach}}
    </table>

    <div id="dataFuncs">
        <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
        <div class="clear"></div>
    </div>

</div>
{{include file='admin/footer.php'}}

<script>
        function query(id,user_id,paypal_name,paypal_account,amount,bank_name,bank_number){
            if(!paypal_account){
                var content = '真实姓名：('+paypal_name+')<br/>'+'提现方式：银行转账'+'<br/>'+'开户银行：'+bank_name+'<br/>'+'银行卡号：'+bank_number+'<br/>'+'转入金额：'+amount+'元<br/>';
            }else if(paypal_account){
                var content =  '真实姓名：('+paypal_name+')<br/>'+'提现方式：支付宝转账'+'<br/>'+'支付宝账号：'+paypal_account+'<br/>'+'转入金额：'+amount+'元<br/>';
            }
            $.dialog({
                title: '放款确认',
                content:content ,
                button: [
                    {
                        value: '确认转账',
                        callback: function () {
                            var url = '/' + _M + '/' + _C + '/change-status';
                            $.post(url, {id:id,user_id:user_id,operate:1,amount:amount}, function(data){
                                var obj = eval('(' + data + ')');
                                if (obj.res == 1) {
                                    $.dialog({
                                        title : '通知',
                                        content : obj.msg,
                                        okValue:'确定',
                                        ok:function(){location.href = location.href}
                                    });
                                }else{
                                    $.dialog({
                                        title : '通知',
                                        content : obj.msg,
                                        okValue:'确定',
                                        ok:function(){location.href = location.href}
                                    });
                                }
                            });
                        },
						disabled: false,
                        focus: true
                    },
                    {
                        value: '取消转账',
                        callback: function () {
                            var html = '<select onchange="reason()" id="reason"><option value="1">账号不存在</option><option value="2">账号和姓名不符</option><option value="3">其他原因</option> </select><br/>' +
                                       '<br/><span id="select_span" style="display: none">其他原因：<input type="text" size="30" id="reason_text"></span>' +
                                       '<br/><br/><span id="error_msg"  style="display: none">错误通知：<b style="color:red">请填写其他原因，不能为空</b></span>';
                            $.dialog({
                                title: '取消放款',
                                content: '取消理由：'+html,
                                okValue:'确认取消转账',
                                ok:function(){
                                    var url = '/' + _M + '/' + _C + '/change-status';
                                    var selected_value =  $("#reason").find("option:selected").val();
                                    if(selected_value == 3){
                                        var selected_text = $("#reason_text").val();
                                    }else{
                                        var selected_text =  $("#reason").find("option:selected").text();
                                    }
                                    if(!selected_text){
                                        $('#error_msg').show();
                                        return false;
                                    }
                                    $.post(url, {id:id,selected_value:selected_text,operate:2}, function(data){
                                        var obj = eval('(' + data + ')');
                                        if (obj.res == 1) {
                                            $.dialog({
                                                title : '通知',
                                                content : obj.msg,
                                                okValue:'确定',
                                                ok:function(){location.href = location.href}
                                            });
                                        }else{
                                            $.dialog({
                                                title : '通知',
                                                content : obj.msg,
                                                okValue:'确定',
                                                ok:function(){location.href = location.href}
                                            });
                                        }
                                    });
                                },
                                cancelValue:'取消',
                                cancel:true
                            });

                        }
                    },
                    {
                        value: '关闭'
                    }
                ]
            })
        }



        function reason(){
            var reason_value =  $("#reason").find("option:selected").val();
            if(reason_value == 3){
                $("#select_span").show();

            }else{
                $("#select_span").hide();
                $('#error_msg').hide();
            }
        }








</script>