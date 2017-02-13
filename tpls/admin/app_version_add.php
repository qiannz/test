{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/jquery.validate.min.js" ></script>
<script type="text/javascript" src="/js/DatePicker/WdatePicker.js" charset="utf-8"></script>
<script type="text/javascript">
    $(function(){
        $('#active_form').validate({
            errorPlacement: function(error, element){
                $(element).next('.field_notice').hide();
                $(element).after(error);
            },
            success       : function(label){
                label.addClass('right').text('OK!');
            },
            rules : {
            	phone_type : {
                    required : true
                },
                version : {
                    required : true,
                    remote   : {
                        url :'/admin/appversion/version-check',
                        type:'post',
                        data:{
                        	version : function(){
                                return $('#version').val();
                            },
                            phone_type : function(){
                                return $('#phone_type').val();
                            },
							id : '{{$data.id}}'
                        }
                    }
				},
				channel : {
                    required :  function() { return $('#phone_type').val() == 'android';},
					remote   : {
                        url :'/admin/appversion/version-check-android',
                        type:'post',
                        data:{
                        	version : function(){
                                return $('#version').val();
                            },
                            phone_type : function(){
                                return $('#phone_type').val();
                            },
							channel : function(){
                                return $('#channel1').val();
                            },
							id : '{{$data.id}}'
                        }
                    }
                }			
            },
            messages : {
            	phone_type : {
                    required : '请选择手机类型'
                },
                version : {
                    required : '请输入手机版本',
                    remote :   '该版本号已存在'
                }
				,
                channel : {
                    required : '请输入渠道',
					remote :   '该版本号已存在'
                }
            }
        });
        
        $("select").change(function(){
    		if(this.value == 'android') {
    			$("#channel").show();
    		} else {
    			$("#channel").hide();
    		}
    	});
    });
</script>
<div id="rightTop">
    <p>数据配置</p>
    <ul class="subnav">
        <li><a class="btn4" href="/admin/appversion/list">APP版本列表</a></li>
        <li>
	        {{if $data.id}}
	    	<a class="btn4" href="/admin/appversion/add">APP版本添加</a>
	        {{else}}
	         <span>新增APP版本</span>
	        {{/if}}   
        </li>
    </ul>
</div>
<div class="info">
    <form method="post" id="active_form">
        <input type="hidden" name="id" value="{{$data.id}}" />
        <table class="infoTable">
            <tr>
                <th class="paddingT15"> 手机类型:</th>
                <td class="paddingT15 wordSpacing5">
<!--                     <input  id="phone_type" type="radio" name="phone_type" value="ios" {{if $data.type eq ios}}checked{{/if}} />IOS -->
<!--                     <input  id="phone_type" type="radio" name="phone_type" value="android" {{if $data.type eq android}}checked{{/if}}  />ANDROID -->
                   <select name="phone_type" id="phone_type" class="querySelect">
		            	<option value="">请选择</option>
		                <option value="ios" {{if $data.type eq ios}}selected="selected"{{/if}}>IOS</option>
		                <option value="android" {{if $data.type eq android}}selected="selected"{{/if}}>ANDROID</option>
		            </select>
                    
                    <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15"> APP版本号:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" style='width:60px;' id="version" type="text" name="version" value="{{$data.version}}" />
                    <label class="field_notice"></label>
                </td>
            </tr>
            
            <tr>
                <th class="paddingT15"> 强制更新:</th>
                <td class="paddingT15 wordSpacing5">
                    <input type="radio" name="is_update" value="1" {{if $data.is_update eq 1}}checked{{/if}} />是
                    <input type="radio" name="is_update" value="0" {{if $data.is_update eq 0}}checked{{/if}}  />否
                    <label class="field_notice"></label>
                </td>
            </tr>
            
            <tr>
                <th class="paddingT15"> 备注:</th>
                <td class="paddingT15 wordSpacing5">
                    <textarea name="content">{{$data.content}}</textarea>
                    <label class="field_notice"></label>
                </td>
            </tr>

            <tr id="channel" {{if $data.type eq ios}}style="display:none"{{/if}}>
                <th class="paddingT15"> 频道来源（仅限安卓）:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" type="text" name="channel" id="channel1" value="{{$data.channel}}" />
                    <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15"> 渠道地址:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="url" style='width:350px;' type="text" name="url" value="{{$data.url}}" />
                    <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15"> 钱包功能:</th>
                <td class="paddingT15 wordSpacing5">
                    <input type="radio" name="allow_wallet_show" value="0" {{if $data.allow_wallet_show eq 0}}checked{{/if}} />全部禁用
                    <input type="radio" name="allow_wallet_show" value="1" {{if $data.allow_wallet_show eq 1}}checked{{/if}} />全部启用
                    <input type="radio" name="allow_wallet_show" value="2" {{if $data.allow_wallet_show eq 2}}checked{{/if}} />仅限天使
                    <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th></th>
                <td class="ptb20"><input class="formbtn" type="submit" name="Submit" value="提交" />
                    <input class="formbtn" type="reset" name="Reset" value="重置" />        </td>
            </tr>
        </table>
    </form>
</div>
{{include file='admin/footer.php'}}