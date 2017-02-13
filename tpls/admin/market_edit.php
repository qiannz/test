{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/jquery.validate.min.js" ></script>
<link href="/css/JqueryDialog/jquery-ui-1.10.4.custom.css?2014062311" rel="stylesheet">
<script src="/js/JqueryDialog/jquery-ui-1.10.4.custom.js"></script>
<script type="text/javascript" src="/js/jquery.form.js?t={{$_CONF.WEB_VERSION}}"></script>

<script type="text/javascript">
    $(function(){
        $('#brand_form').validate({
            errorPlacement: function(error, element){
                $(element).next('.field_notice').hide();
                $(element).after(error);
            },
            success       : function(label){
                label.addClass('right').text('OK!');
            },
            rules : {
            	market_name : {
            		required : true
            	},
            	market_address : {
            		required : true
                },
                city : {
                	required : true
                }
            },
            messages : {
            	market_name : {
            		required : '请填写商场名称！'
                },
                market_address : {
                	required : '请填写商场地址！'
                },
                city : {
                	required : '请填写城市！'
                }
            }
        });
		
		$('#region_id').change(function(){
			var _this = $('#circle_id');
			_this.empty();
			_this.append($("<option>").text('请选择商圈').val(''));
			$.post('/admin/good/get-circle', {id:$(this).val()}, function(obj){
				var data = eval('(' + obj + ')');
				$.each(data, function(i, s){
					_this.append($("<option>").text(s.name).val(s.id));
				});
			});
		});
	
		{{if $makets.market_id}}
			$('#region_id').val('{{$makets.region_id}}');
			$('#circle_id').val('{{$makets.circle_id}}');
		{{/if}}

    });
    
    $(function(){
        $("#check_submit").click(
            function()
            {
            	$("#brand_form").submit();
            }
        );
        //上传图片dialog 弹出层调用
        $( "#dialog-head" ).click(function() {
            dialog('dialog','m_head');
        });

        $( "#dialog-logo" ).click(function() {
            dialog('dialog1','logo');
        });
    });

    function dialog(class_id,dialog_name){
        $('#'+class_id).dialog({
            autoOpen: true,
            width: 400,
            height:200,
            modal:true,
            buttons: [
                {
                    text: "上传",
                    click: function() {
                        $('#'+dialog_name).ajaxSubmit({
                            type:'post',
                            url:'/admin/market/upload',
                            success:function(data){
                                data = eval('(' + data + ')');
                                if(data.msg == 100){
                                    $('#'+dialog_name+'Html').attr("src",data.url);
                                    $('#'+dialog_name+'TD').show();
                                    $('#'+dialog_name+'Href').attr("href",data.url);
                                    $('#'+dialog_name+'Img').val(data.img_url);
                                    $('#'+dialog_name+'Error').html('');
                                }else if (data.msg == 101){
                                    $('#'+dialog_name+'Error').html('请选择上传图片');
                                }else if (data.msg == 102){
                                    $('#'+dialog_name+'Error').html('选择的图片尺寸不对');
                                }else if (data.msg == 103){
                                    $('#'+dialog_name+'Error').html('选择的图片格式不对或者太大了');
                                }
                            }
                        })
                        $( this ).dialog( "close" );
                    }
                },
                {
                    text: "取消",
                    click: function() {
                        $( this ).dialog( "close" );
                    }
                }
            ]
        });
    }
</script>

<style>
    #dialog-head, #dialog-logo {
        padding: .4em 1em .4em 20px;
        text-decoration: none;
        position: relative;
    }
    #dialog-logo span.ui-icon, #dialog-head span.ui-icon {
        margin: 0 5px 0 0;
        position: absolute;
        left: .2em;
        top: 50%;
        margin-top: -8px;
    }
    #m_head  {    font-size: 11px;
        height: 25px;
        left: 90px;
        top:19px;
        position: absolute;
        width: 250px;
        z-index: 2;
        line-height: 25px;;
    }

    #logo {    font-size: 11px;
        height: 25px;
        left: 90px;
        top:19px;
        position: absolute;
        width: 250px;
        z-index: 2;
        line-height: 25px;;
    }
    
    input.text { margin:5px 0 12px; width:150px; padding: .4em; vertical-align: middle}
    fieldset { padding:0; border:0; margin-top:25px; }
    h1 { font-size: 1.2em; margin: .6em 0; }
    div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
    div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
</style>
<div id="rightTop">
    <p>数据配置</p>
    <ul class="subnav">
        <li><a class="btn1" href="/admin/market/list/page:{{$page}}">商场管理</a></li>
        {{if $makets.market_id}}
        <li><span>编辑商场</span></li>
        {{else}}
        <li><span>新增商场</span></li>
        {{/if}}
    </ul>
</div>


<div class="info">
    <form method="POST" id="brand_form">
        <input type="hidden" name="market_id" value="{{$makets.market_id}}">
        <input type="hidden" name="session_id" id="session_id" value="{{$session_id}}" />
        <input type="hidden" name="session_name" id="session_name" value="{{$session_name}}" />
        <input type="hidden" name="page" id="page" value="{{$page}}" />
        <table class="infoTable">
              <tr>
        <th class="paddingT15">所属行政区:</th>
        <td class="paddingT15 wordSpacing5">
			<select name="region_id" id="region_id">
            	<option value="">请选择所在区</option>
                {{foreach from=$regionArray key=key item=item}}
                <option value="{{$key}}">{{$item}}</option>
                {{/foreach}}
            </select>	
            <label class="field_notice"></label>
        </td>
      </tr> 
      <tr>
        <th class="paddingT15">所属商圈:</th>
        <td class="paddingT15 wordSpacing5">
            <select name="circle_id" id="circle_id">
            	<option value="">请选择商圈</option>
                {{foreach from=$circleArray key=key item=item}}
                <option value="{{$item.id}}">{{$item.name}}</option>
                {{/foreach}}
            </select>	
            <label class="field_notice"></label>
        </td>
      </tr>  
            <tr>
                <th class="paddingT15" width="10%"> 商场名称:</th>
                <td class="paddingT15 wordSpacing5" width="40%">
                    <input class="infoTableInput2" id="market_name" type="text" name="market_name" value="{{$makets.market_name}}" style="width:200px;" />
                    <label class="field_notice"></label>
                    <span style="color:red" id="ts"></span>
                </td>
                <td></td>
                <td></td>
            </tr>

            <tr>
                <th class="paddingT15"> 商场地址:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="market_address" type="text" name="market_address" value="{{$makets.market_address}}" style="width:200px;" />
                    <label class="field_notice"></label>
                    <span style="color:red" id="ts"></span>
                </td>
                <td></td>
                <td></td>
            </tr>
<!--            
            <tr>
                <th class="paddingT15"> 区域:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="city" type="text" name="area" value="{{$makets.area}}" style="width:100px;" />
                    <label class="field_notice"></label>
                    <span style="color:red" id="ts"></span>
                </td>
                <td></td>
                <td></td>
            </tr>-->
            
           <tr>
                <th class="paddingT15"> 电话:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="tel" type="text" name="tel" value="{{$makets.tel}}" style="width:300px;" />
                    <label class="field_notice"></label>
                    <span style="color:red" id="ts"></span>
                </td>
                <td></td>
                <td></td>
            </tr>
   
           <tr>
                <th class="paddingT15"> 公交信息:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="trafficInfo" type="text" name="trafficInfo" value="{{$makets.trafficInfo}}" style="width:600px;" />
                    <label class="field_notice"></label>
                    <span style="color:red" id="ts"></span>
                </td>
                <td></td>
                <td></td>
            </tr>   
   
           <tr>
                <th class="paddingT15"> 商场LOGO（APP）:</th>
                <td class="paddingT15 wordSpacing5">
                    <input name="logoImg" id="logoImg" type="text" size="35" value="{{$makets.logo_img}}"/>
                    <span id="logoError" style="color: red;font-size: 12px;">商场LOGO图片格式   {{$logosize.width}} * {{$logosize.height}}</span>
                </td>
                <td><p><a href="#" id="dialog-logo" class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-newwin"></span>点击上传商场LOGO</a></p></td>
            </tr> 
            <tr>
            	<th class="paddingT15"></th>
                <td id="logoTD">
                {{if $makets.logo_img}}
                <a href="{{$_CONF.IMG_URL}}/buy/market/{{$makets.logo_img}}" target="_BLANK" id="headHref"><img src="{{$_CONF.IMG_URL}}/buy/market/{{$makets.logo_img}}" id="headHtml"></a>
                {{/if}}
                </td>
            </tr>
            
            <tr>
                <th class="paddingT15"> 商场头图（APP）:</th>
                <td class="paddingT15 wordSpacing5">
                    <input name="m_headImg" id="m_headImg" type="text" size="35" value="{{$makets.head_img}}"/>
                    <span id="m_headError" style="color: red;font-size: 12px;">商场头图图片格式   {{$headsize.width}} * {{$headsize.height}}</span>
                </td>
                <td><p><a href="#" id="dialog-head" class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-newwin"></span>点击上传商场头图</a></p></td>
            </tr>
            <tr>
            	<th class="paddingT15"></th>
                <td id="m_headTD">
                {{if $makets.head_img}}
                <a href="{{$_CONF.IMG_URL}}/buy/market/{{$makets.head_img}}" target="_BLANK" id="m_headHref"><img src="{{$_CONF.IMG_URL}}/buy/market/{{$makets.head_img}}" id="m_headHtml"></a>
                {{/if}}
                </td>
            </tr>
           
           
            <tr>
                <th class="paddingT15"> APP推荐:</th>
                <td class="paddingT15 wordSpacing5">
                    <input type="radio" name="is_show" value="0" {{if $makets.is_show eq 0}}checked="checked"{{/if}} /> 否
                    <input type="radio" name="is_show" value="1" {{if $makets.is_show eq 1}}checked="checked"{{/if}} /> 是
                </td>
                <td></td>
                <td></td>
            </tr>
           
            <tr>
                <th class="paddingT15"> </th>
                <td class="ptb20">
                    <input class="formbtn" type="button" id="check_submit" value="确定" />
                    <input class="formbtn" type="reset" name="Submit2" value="重置" /></td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </form>

    <!-- ui dialog-->    
    <div id="dialog" title="上传商场头图" style="display: none">
        <form id ="m_head" method ="post" enctype ="multipart/form-data" >
            <fieldset>
                <label for="head">商场头图</label>
                <input type="file" name="uploadhead" id="uploadhead" class="text ui-widget-content ui-corner-all">
                <input type="hidden" name="type" id="type" value="uploadhead">
                <input type="hidden" name="width"  id="width"  value="{{$headsize.width}}">
                <input type="hidden" name="height" id="height" value="{{$headsize.height}}">
            </fieldset>
        </form>
    </div>
    
   <div id="dialog1" title="上传商场LOGO" style="display: none">
        <form id ="logo" method ="post" enctype ="multipart/form-data" >
            <fieldset>
                <label for="logo">商场LOGO</label>
                <input type="file" name="uploadlogo" id="uploadlogo" class="text ui-widget-content ui-corner-all">
                <input type="hidden" name="type" id="type" value="uploadlogo">
                <input type="hidden" name="width"  id="width"  value="{{$logosize.width}}">
                <input type="hidden" name="height" id="height" value="{{$logosize.height}}">
            </fieldset>
        </form>
    </div>
</div>
{{include file='admin/footer.php'}}