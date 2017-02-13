{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
<div id="rightTop">
    <p>任务管理</p>
    <ul class="subnav">
        <li><span>店员刮奖设置</span></li>
        <li><input type="button" class="formbtn" value="增加设置" id="add" /></li>
    </ul>
</div>
<div class="mrightTop">
    <div class="fontl">

        <div class="left">
            <span>预计当日奖池总金额为<b style="color:red"> {{$sum}} </b>元</span>
        </div>
    </div>
</div>
<div class="tdare">
    <table width="400" cellspacing="0" class="dataTable">
        <tr class="tatr1">
            <td>中奖概率</td>
            <td>奖金</td>
            <td>数量</td>
            <td>备注</td>
            <td></td>
        </tr>
        {{foreach from=$data key=key item=item}}
        <tr class="tatr2">
            <td>1 / {{$item.config_value.pro}}</td>
            <td>{{$item.config_value.award}}</td>
            <td>{{$item.config_value.amount}}</td>
            <td>{{$item.config_value.ex}}</td>
            <td><span style="width: 100px"><a href="javascript:drop_confirm({{$item.config_id}}, '{{$item.config_value.ex}}')" title="删除"><img src="/images/x.png" /></a></span></td>
        </tr>
        {{foreachelse}}
        <tr class="no_data">
            <td colspan="2">暂无记录</td>
        </tr>
        {{/foreach}}
    </table>
</div>
<script type="text/javascript">
    function drop_confirm(id, ex) {
        $.dialog({
            title:'警告',
            content:'确认删除？该动作不可逆转！',
            ok: function() {
                var url = '/' + _M + '/' + _C + '/del';
                $.post(url, {id:id, ex:ex}, function(data){
                    var obj = eval('(' + data + ')');
                    if (obj.res == 1) {
                        $.dialog({
                            title : '结果',
                            content : obj.msg,
                            ok : function () {location.href =  '/' + _M + '/' + _C + '/list';},
                            cancel : false
                        });
                    }
                });
            },
            cancel: true
        });
    }

    $(function(){
        $('#add').click(function(){
            $.dialog({
                title: '刮奖设置',
                content: '概率基数：<br/>'
                    + '<input type="text" name="pro" id="pro" /><br/><br/>'
                    + '奖金：<br/>'
                    + '<input type="text" name="award" id="award" /><br/><br/>'
                    + '数量：<br/>'
                    + '<input type="text" name="amount" id="amount" /><br/><br/>'
                    + '备注：<br/>'
                    + '<input type="text" name="ex" id="ex"/> <br/>',
                ok: function () {
                    var pro    = $('#pro').val();
                    var award  = $('#award').val();
                    var amount = $('#amount').val();
                    var ex     = $('#ex').val();
                    if(pro == '') {
                        alert('概率基数不能为空');
                        return false;
                    } else if (award == '') {
                        alert('奖金不能为空');
                        return false;
                    }
                    else if (amount == '') {
                        alert('数量不能为空');
                        return false;
                    }
                    else if (ex == '') {
                        alert('备注不能为空');
                        return false;
                    }else {
                        var url = '/' + _M + '/' + _C + '/add';
                        $.post(url, {pro:pro, award:award, amount:amount,ex:ex }, function(data){
                            var obj = eval('(' + data + ')');
                            if (obj.res == 1) {
                                $.dialog({
                                    title : '结果',
                                    content : obj.msg,
                                    ok : function () {location.href =  '/' + _M + '/' + _C + '/list';},
                                    cancel : false
                                });
                            }
                        });
                    }
                },
                cancel : true
            });
        });
    });

    $(".dataTable tr.tatr2").mouseover(function(){
        $(this).addClass("over");
    })

    $(".dataTable tr.tatr2").mouseout(function(){
        $(this).removeClass("over");
    })
</script>
{{include file='admin/footer.php'}}