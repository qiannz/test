{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/jquery.validate.min.js" ></script>
<script type="text/javascript">
$(function(){
    jQuery.validator.addMethod("stringCheck", function(value, element) {
        return this.optional(element) || /^[a-zA-Z]{1}([a-zA-Z0-9]|[._]){4,19}$/.test(value);
        }, "请输入符合规则的验证码");

    $('#prize').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },
        success       : function(label){
            label.addClass('right').text('OK!');
        },
        rules : {
        	prize_name : {
                required : true,
                remote   : {
                    url :'/admin/gift/verifica-prize',
                    type:'post',
                    dateType:"json",
                    data:{
                    	prize_name : function(){ return $('#prize_name').val();},
                        p_id : '{{$prizeRow.id}}'
                    }
                } 
            },
        	prize_content : {
                required : true
            }
        },
        messages : {
        	prize_name : {
                required : '请输入奖品名称',
                remote : '奖品已经存在'
            },
        	prize_content : {
                required : '请输入奖品描述'
            }
        }
    });
});
</script>
<div id="rightTop">
    <p>新手包</p>
    <ul class="subnav">
        <li><a class="btn1" href="/admin/gift/list">领奖列表</a></li>
        <li><a class="btn1" href="/admin/gift/gift-list">验证码管理</a></li>
         <li><a class="btn1" href="/admin/gift/gift-list">验证码管理</a></li>
        <li><a class="btn1" href="/admin/gift/prize-list">奖品列表</a></li>
		{{if $prizeRow.id}}
			<li><span>编辑奖品</span></li>
		{{else}}
			<li><span>新增奖品</span></li>
		{{/if}}
    </ul>
</div>
<div class="info">
    <form method="POST" id="prize" enctype="multipart/form-data">
    <input type="hidden" name="id" value="{{$prizeRow.id}}">
        <table class="infoTable">
            <tr>
                <th class="paddingT15"> 奖品名称:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="prize_name" type="text" name="prize_name" value="{{$prizeRow.prize_name}}" style="width:160px;" />
                    <label class="field_notice"></label>
                </td>
            </tr>
            
            <tr>
                <th class="paddingT15"> 奖品描述:</th>
                <td class="paddingT15 wordSpacing5">
					<textarea id="prize_content" name="prize_content">{{$prizeRow.prize_content}}</textarea>                    
                    <label class="field_notice"></label>
                </td>
            </tr>

            <tr>
                <th class="paddingT15"> </th>
                <td class="ptb20">
                    <input class="formbtn" type="submit" value="保存" />
            </tr>
        </table>
    </form>
</div>
{{include file='admin/footer.php'}}