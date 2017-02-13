{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
<div id="rightTop">
    <p>任务管理</p>
    <ul class="subnav">
        <li><span>APP大转盘设置</span></li>
        <li><input type="button" class="formbtn" value="增加设置" id="add" /></li>
    </ul>
</div>

<div class="tdare">
    <table width="850" cellspacing="0" class="dataTable">
        <tr class="tatr1">
            <td>奖品名称</td>
            <td>奖品类别</td>
            <td>奖品数量</td>
            <td>概率系数</td>
            <td>每日限制</td>
            <td>总限制</td>
            <td width="200">短信内容</td>
            <td width="150"></td>
        </tr>
        {{foreach from=$listArray key=key item=item}}
        <tr class="tatr2" id="tra_{{$item.id}}">
            <td>{{$item.award_name}}</td>
            <td>{{if $item.type eq 'star'}}幸运星{{elseif $item.type eq 'virtual'}}券{{elseif $item.type eq 'real'}}实物{{elseif $item.type eq 'call'}}话费{{/if}}<e>{{$item.type}}</e></td>
            <td>{{$item.award_number}}</td>
            <td>1 / <e>{{$item.pro}}</e></td>
            <td>{{$item.every_day_limit}}</td>
            <td>{{$item.total_limit}}</td>
            <td>{{$item.msg}}</td>
            <td>
            <a href="javascript:editConfirm({{$item.id}})" title="编辑"><img src="/images/e.png" /></a>　
            <a href="javascript:dropConfirm({{$item.id}})" title="删除"><img src="/images/x.png" /></a>
            </td>
        </tr>
        {{foreachelse}}
        <tr class="no_data">
            <td colspan="8">暂无记录</td>
        </tr>
        {{/foreach}}
    </table>
</div>
<script type="text/javascript">
    function dropConfirm(id, ex) {
        $.dialog({
            title:'警告',
            content:'确认删除？该动作不可逆转！',
            ok: function() {
                var url = '/' + _M + '/' + _C + '/del';
                $.post(url, {id:id}, function(data){
                    var obj = eval('(' + data + ')');
                    if (obj.res == 100) {
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
                title: '转盘设置',
                content:
					  '<span id="errMsg" style="color:red"></span>' 
					+ '奖品类别：<br/><br/>'
                    + '<input type="radio" name="type" value="star" />幸运星 '
					+ '<input type="radio" name="type" value="virtual" /> 券 ' 
					+ '<input type="radio" name="type" value="real" /> 实物'
					+ '<input type="radio" name="type" value="call" /> 话费<br/><br/>'
                    + '奖品名称：<br/>'
                    + '<input type="text" name="award_name" id="award_name" /><br/><br/>'
                    + '奖品数量：<br/>'
                    + '<input type="text" name="award_num" id="award_num" /><br/><br/>'
					+ '概率系数：<br/>'
                    + '<input type="text" name="pro" id="pro" /><br/><br/>'
					+ '每日限制：<br/>'
                    + '<input type="text" name="day_limit" id="day_limit" /><br/><br/>'
                    + '总限制：<br/>'
                    + '<input type="text" name="total_limit" id="total_limit" /> <br/>'
					+ '短信内容：<br/>'
                    + '<textarea name="msg" id="msg"></textarea>',
                ok: function () {
					var errMsg = "";
					var type = $("input[name=type]:checked").val();
                    var awardName    = $('#award_name').val();
                    var awardNum = $('#award_num').val();
					var pro = $('#pro').val();
                    var dayList = $('#day_limit').val();
                    var totalList = $('#total_limit').val();
                    var msg = $('#msg').val();
					if(!type) {
                        errMsg += "请选择奖品类别<br/>";
                    }
					
					if (!awardName) {
						errMsg += "请输入奖品名称<br/>";
                    }
					
					if (!awardNum) {
						errMsg += "请输入奖品数量<br/>";
                    }
					
					if (!pro) {
						errMsg += "请输入概率系数<br/>";
                    }
					
					if (!dayList) {
						errMsg += "请输入每日限制<br/>";
                    }
					
					if (!totalList) {
						errMsg += "请输入总限制<br/>";
                    }
					if(errMsg != "") {
						$("span#errMsg").html(errMsg);
						return false;
					} else {
                        var url = '/' + _M + '/' + _C + '/add';
                        $.post(url, {type:type, awardName:awardName, awardNum:awardNum, pro:pro, dayList:dayList, totalList:totalList, msg:msg}, function(data){
                            var obj = eval('(' + data + ')');
                            if (obj.res == 100) {
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
	

	function editConfirm(id) {
			var type = $("#tra_" + id + " td").eq(1).find("e").html();
			var awardName = $("#tra_" + id + " td").eq(0).html();
			var awardNum = $("#tra_" + id + " td").eq(2).html();
			var pro = $("#tra_" + id + " td").eq(3).find("e").html();
			var dayLimit = $("#tra_" + id + " td").eq(4).html();
			var totalLimit = $("#tra_" + id + " td").eq(5).html();
			var msg = $("#tra_" + id + " td").eq(6).html();
			
            $.dialog({
                title: '转盘编辑',
                content:
					  '<span id="errMsg" style="color:red"></span>' 
					+ '奖品类别：<br/><br/>'
                    + '<input type="radio" name="type" value="star" ' + (type == "star" ? "checked=checked" : "") + ' />幸运星 '
					+ '<input type="radio" name="type" value="virtual" ' + (type == "virtual" ? "checked=checked" : "") + '/> 券 ' 
					+ '<input type="radio" name="type" value="real" ' + (type == "real" ? "checked=checked" : "") + '/> 实物'
					+ '<input type="radio" name="type" value="call" ' + (type == "call" ? "checked=checked" : "") + '/> 话费<br/><br/>'
                    + '奖品名称：<br/>'
                    + '<input type="text" name="award_name" id="award_name" value="'+awardName+'" /><br/><br/>'
                    + '奖品数量：<br/>'
                    + '<input type="text" name="award_num" id="award_num" value="'+awardNum+'" /><br/><br/>'
					+ '概率系数：<br/>'
                    + '<input type="text" name="pro" id="pro" value="'+pro+'" /><br/><br/>'
					+ '每日限制：<br/>'
                    + '<input type="text" name="day_limit" id="day_limit" value="'+dayLimit+'" /><br/><br/>'
                    + '总限制：<br/>'
                    + '<input type="text" name="total_limit" id="total_limit" value="'+totalLimit+'" /> <br/>'
					+ '短信内容：<br/>'
                    + '<textarea name="msg" id="msg">'+msg+'</textarea>',
                ok: function () {
					var errMsg = "";
					var type = $("input[name=type]:checked").val();
                    var awardName    = $('#award_name').val();
                    var awardNum = $('#award_num').val();
					var pro = $('#pro').val();
                    var dayList = $('#day_limit').val();
                    var totalList = $('#total_limit').val();
                    var msg = $('#msg').val();
					if(!type) {
                        errMsg += "请选择奖品类别<br/>";
                    }
					
					if (!awardName) {
						errMsg += "请输入奖品名称<br/>";
                    }
					
					if (!awardNum) {
						errMsg += "请输入奖品数量<br/>";
                    }
					
					if (!pro) {
						errMsg += "请输入概率系数<br/>";
                    }
					
					if (!dayList) {
						errMsg += "请输入每日限制<br/>";
                    }
					
					if (!totalList) {
						errMsg += "请输入总限制<br/>";
                    }
					if(errMsg != "") {
						$("span#errMsg").html(errMsg);
						return false;
					} else {
                        var url = '/' + _M + '/' + _C + '/add';
                        $.post(url, {id:id, type:type, awardName:awardName, awardNum:awardNum, pro:pro, dayList:dayList, totalList:totalList,msg:msg}, function(data){
                            var obj = eval('(' + data + ')');
                            if (obj.res == 100) {
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
	}	
</script>
{{include file='admin/footer.php'}}