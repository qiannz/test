{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/jquery.validate.min.js" ></script>
<script type="text/javascript">
$(function(){
    jQuery.validator.addMethod("stringCheck", function(value, element) {
        return this.optional(element) || /^[a-zA-Z]{1}([a-zA-Z0-9]|[._]){4,19}$/.test(value);
        }, "请输入符合规则的验证码");

    $('#gift').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },
        success       : function(label){
            label.addClass('right').text('OK!');
        },
        rules : {
            captcha : {
                required:true,
                remote   : {
                    url :'/admin/gift/verifica',
                    type:'post',
                    dateType:"json",
                    data:{
                        captcha : function(){ return $('#captcha').val();}
                    }
                },
                stringCheck:true
            },
            remark : {
                required : true
            }
        },
        messages : {
            captcha : {
                required : '请输入验证码',
                remote:"验证码已经存在" ,
                stringCheck:'请输入符合规则的验证码'
            },
            remark : {
                required : '请输入备注'
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
        <li><span>验证码管理</span></li>
        <li><a class="btn1" href="/admin/gift/prize-list">奖品列表</a></li>
		<li><a class="btn1" href="/admin/gift/prize-add">新增奖品</a></li>
    </ul>
</div>
<div class="info">
    <form method="POST" id="gift" enctype="multipart/form-data">
        <table class="infoTable">
            <tr>
                <th class="paddingT15"> 验证码:</th>
                <td class="paddingT15 wordSpacing5">
                   {{if $giftRow.gift_id}}
                   	{{$giftRow.captcha}}
                   {{else}}
                   	<input class="infoTableInput2" id="captcha" type="text" name="captcha" value="" style="width:160px;" />
                   {{/if}}
                    <label class="field_notice"></label>
                </td>
            </tr>

            <tr>
                <th class="paddingT15"> 备注:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="remark" type="text" name="remark" value="{{$giftRow.remark}}" style="width:160px;" />
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