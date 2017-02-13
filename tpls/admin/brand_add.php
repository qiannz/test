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
            	store_id : {
            		required : true
            	},
                brand_name_zh : {
                    remote   : {
                        url :'/admin/brand/check-zh',
                        type:'post',
                        data:{
                            brand_name_zh : function(){
                                return $('#brand_name_zh').val();
                            },
                            b_id : '{{$brands.brand_id}}'
                        }
                    }
                },
                brand_name_en : {
                    remote   : {
                        url :'/admin/brand/check-en',
                        type:'post',
                        data:{
                            brand_name_en : function(){
                                return $('#brand_name_en').val();
                            },
                            b_id : '{{$brands.brand_id}}'
                        }
                    }
                },
                firs_word : {
                	required : true
                }
            },
            messages : {
                store_id : {
                	required : '请选择分类'
                },
                brand_name_zh : {
                    remote : '该品牌已存在'
                },
                brand_name_en : {
                    remote : '该品牌已存在'
                },
                firs_word : {
                	required : '请输入品牌首字母'
                }
            }
        });

    	{{if $brands.brand_id}}
		$('#store_id').val({{$brands.store_id}});
		{{/if}}
    });

    function checkSubmit() {
        if($('#brand_name_zh').val() == '' && $('#brand_name_en').val() == '') {
            $('#ts').html('中文品牌或英文品牌必须要输一个');
            return false;
        }
        return true;
    }

    $(function(){
        $("#check_submit").click(
            function()
            {
                if($("#brand_form").valid())
                {
                    if(checkSubmit())
                    {
                        $(this).attr("disabled", true);
                        $("#brand_form").submit();
                    }
                }
            }
        );
        //上传图片dialog 弹出层调用
        $( "#dialog-logo" ).click(function() {
            dialog('dialog','logo');
        });

        $( "#dialog-map" ).click(function() {
            dialog('dialog1','map');
        });

        $( "#dialog-head" ).click(function() {
            dialog('dialog2','m_head');
        });

        $( "#dialog-icon" ).click(function() {
            dialog('dialog3','icon');
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
                            url:'/admin/brand/upload',
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
    #dialog-logo, #dialog-map, #dialog-head, #dialog-icon {
        padding: .4em 1em .4em 20px;
        text-decoration: none;
        position: relative;
    }
    #dialog-logo span.ui-icon, #dialog-map span.ui-icon, #dialog-head span.ui-icon, #dialog-icon span.ui-icon {
        margin: 0 5px 0 0;
        position: absolute;
        left: .2em;
        top: 50%;
        margin-top: -8px;
    }
    #logo  {    font-size: 11px;
        height: 25px;
        left: 90px;
        position: absolute;
        width: 250px;
        z-index: 2;
        line-height: 25px;;
    }
    #map  {    font-size: 11px;
        height: 25px;
        left: 90px;
        top:19px;
        position: absolute;
        width: 250px;
        z-index: 2;
        line-height: 25px;;
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
    #icon  {    font-size: 11px;
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
    <p>品牌管理</p>
    <ul class="subnav">
        <li><a class="btn1" href="/admin/brand/list{{if $page}}/page:{{$page}}{{/if}}">品牌管理</a></li>
        {{if $brands.brand_id}}
        <li><span>编辑品牌</span></li>
        {{else}}
        <li><span>新增品牌</span></li>
        {{/if}}
    </ul>
</div>


<div class="info">
    <form method="POST" id="brand_form">
        <input type="hidden" name="brand_id" value="{{$brands.brand_id}}">
        <input type="hidden" name="session_id" id="session_id" value="{{$session_id}}" />
        <input type="hidden" name="session_name" id="session_name" value="{{$session_name}}" />
        <table class="infoTable">
        	  <tr>
		        <th class="paddingT15">品牌分类:</th>
		        <td class="paddingT15 wordSpacing5">
		            <select name="store_id" id="store_id">
		            	<option value="">请选择分类</option>
		                {{foreach from=$storeArray key=key item=item}}
		                <option value="{{$key}}">{{$item}}</option>
		                {{/foreach}}
		            </select>	
		            <label class="field_notice"></label>
		        </td>
		      </tr>
            <tr>
                <th class="paddingT15" width="200"> 中文名称:</th>
                <td class="paddingT15 wordSpacing5" width="40%">
                    <input class="infoTableInput2" id="brand_name_zh" type="text" name="brand_name_zh" value="{{$brands.brand_name_zh}}" style="width:100px;" />
                    <label class="field_notice"></label>
                </td>
                <td></td>
            </tr>

            <tr>
                <th class="paddingT15"> 英文名称:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="brand_name_en" type="text" name="brand_name_en" value="{{$brands.brand_name_en}}" style="width:100px;" />
                    <label class="field_notice"></label>
                    <span style="color:red" id="ts"></span>
                </td>
                <td></td>
            </tr>
            
            <tr>
                <th class="paddingT15"> 首字母:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="firs_word" type="text" name="firs_word" value="{{$brands.firs_word}}" style="width:100px;" />
                    <label class="field_notice"></label>
                    <span style="color:red" id="ts"></span>
                </td>
                <td></td>
            </tr>
            
            
            <tr>
                <th class="paddingT15"> 前台展示:</th>
                <td class="paddingT15 wordSpacing5" >
                    <input  id="is_show" type="radio" name="is_show" value="1" {{if $brands.is_show eq 1}}checked{{/if}} />是
                    <input  id="is_show" type="radio" name="is_show" value="0" {{if $brands.is_show eq 0}}checked{{/if}} />否
                    <label class="field_notice"></label>
                </td>
                <td></td>
            </tr>

            <tr>
                <th class="paddingT15"> 品牌简介:</th>
                <td class="paddingT15 wordSpacing5" >
                    <textarea class="infoTableInput2" id="brand_profile" name="brand_profile" style="width:450px;height: 130px;">{{$brands.brand_profile}}</textarea>
                    <label class="field_notice"></label>
                    <span style="color:red" id="ts2"></span>
                </td>
                <td></td>
            </tr>

            <tr>
                <th class="paddingT15" width="100"> 品牌logo:</th>
                <td class="paddingT15 wordSpacing5" >
                    <input name="logoImg" id="logoImg" type="text" size="35" value="{{$brands.brand_logo}}" />
                    <span id="logoError" style="color: red;font-size: 12px;">品牌LOGO 图片格式 {{$logosize.width}} * {{$logosize.height}}</span>
                </td>
                <td><a href="#" id="dialog-logo" class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-newwin"></span>点击上传品牌LOGO</a></td>
            </tr>
            <tr>
            	<th class="paddingT15"></th>
                <td id="logoTD">
                {{if $brands.brand_logo}}
                <a href="{{$_CONF.IMG_URL}}/buy/brand/{{$brands.brand_logo}}" target="_BLANK" id="logoHref"><img src="{{$_CONF.IMG_URL}}/buy/brand/{{$brands.brand_logo}}" id="logoHtml" ></a>
               {{/if}}
                </td>
            </tr>
                        
            <tr>
                <th class="paddingT15"> 品牌图标（APP）:</th>
                <td class="paddingT15 wordSpacing5">
                    <input name="iconImg" id="iconImg" type="text" size="35" value="{{$brands.brand_icon}}"/>
                    <span id="iconError" style="color: red;font-size: 12px;">品牌LOGO图标（APP） 图片格式   {{$iconsize.width}} * {{$iconsize.height}}</span>
                </td>
                <td><p><a href="#" id="dialog-icon" class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-newwin"></span>点击上传品牌图标(APP)</a></p></td>
            </tr>
            <tr>
            	<th class="paddingT15"></th>
            	<td id="iconTD">
            	{{if $brands.brand_icon}}
                	<a href="{{$_CONF.IMG_URL}}/buy/brand/{{$brands.brand_icon}}" target="_BLANK" id="iconHref"><img src="{{$_CONF.IMG_URL}}/buy/brand/{{$brands.brand_icon}}" id="iconHtml" ></a>
                {{/if}}
            	</td>
            </tr>
            
            
            <tr>
                <th class="paddingT15"> 品牌形象图:</th>
                <td class="paddingT15 wordSpacing5">
                    <input name="mapImg" id="mapImg" type="text" size="35" value="{{$brands.brand_figure}}"/>
                    <span id="mapError" style="color: red;font-size: 12px;">品牌形象图 图片格式 {{$mapsize.width}} * {{$mapsize.height}}</span>
                </td>
                <td><p><a href="#" id="dialog-map" class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-newwin"></span>点击上传品牌形象图</a></p></td>
            </tr>
            <tr>
            	<th class="paddingT15"></th>
                <td id="mapTD">
                {{if $brands.brand_figure}}
                <a href="{{$_CONF.IMG_URL}}/buy/brand/{{$brands.brand_figure}}" target="_BLANK" id="mapHref"><img src="{{$_CONF.IMG_URL}}/buy/brand/{{$brands.brand_figure}}" id="mapHtml" ></a>
                {{/if}}
                </td>
            </tr>
            
            <tr>
                <th class="paddingT15"> 品牌头图（APP）:</th>
                <td class="paddingT15 wordSpacing5">
                    <input name="m_headImg" id="m_headImg" type="text" size="35" value="{{$brands.brand_head}}"/>
                    <span id="m_headError" style="color: red;font-size: 12px;">品牌头图 （APP）图片格式   {{$headsize.width}} * {{$headsize.height}}</span>
                </td>
                <td><p><a href="#" id="dialog-head" class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-newwin"></span>点击上传品牌头图</a></p></td>
            </tr>
            <tr>
            	<th class="paddingT15"></th>
                <td id="m_headTD">
                {{if $brands.brand_head}}
                	<a href="{{$_CONF.IMG_URL}}/buy/brand/{{$brands.brand_head}}" target="_BLANK" id="m_headHref"><img src="{{$_CONF.IMG_URL}}/buy/brand/{{$brands.brand_head}}" id="m_headHtml" ></a>
                {{/if}}
                </td>
            </tr>
            
            
            <tr>
                <th class="paddingT15"> 是否启用:</th>
                <td class="paddingT15 wordSpacing5" >
                    <input  id="is_enable" type="radio" name="is_enable" value="1" {{if $brands.is_enable eq 1}}checked="checked"{{/if}} />是
                    <input  id="is_enable" type="radio" name="is_enable" value="0" {{if $brands.is_enable eq 0}}checked="checked"{{/if}} />否
                    <label class="field_notice"></label>
                </td>
                <td></td>
            </tr>

            <tr>
                <th class="paddingT15"> </th>
                <td class="ptb20">
                    <input class="formbtn" type="button" id="check_submit" value="确定" />
                    <input class="formbtn" type="reset" name="Submit2" value="重置" /></td>
                <td></td>
            </tr>
        </table>
    </form>

    <!-- ui dialog-->
    <div id="dialog" title="上传品牌LOGO" style="display: none">
        <form id ="logo" method ="post" enctype ="multipart/form-data" >
            <fieldset>
                <label for="logo">品牌LOGO</label>
                <input type="file" name="uploadlogo" id="uploadlogo" class="text ui-widget-content ui-corner-all">
                <input type="hidden" name="type"   id="type"   value="uploadlogo">
                <input type="hidden" name="width"  id="width"  value="{{$logosize.width}}">
                <input type="hidden" name="height" id="height" value="{{$logosize.height}}">
            </fieldset>
        </form>
    </div>

    <div id="dialog1" title="上传品牌形象图" style="display: none">
        <form id ="map" method ="post" enctype ="multipart/form-data" >
            <fieldset>
                <label for="map">品牌形象图</label>
                <input type="file" name="uploadmap" id="uploadmap" class="text ui-widget-content ui-corner-all">
                <input type="hidden" name="type" id="type" value="uploadmap">
                <input type="hidden" name="width"  id="width"  value="{{$mapsize.width}}">
                <input type="hidden" name="height" id="height" value="{{$mapsize.height}}">
            </fieldset>
        </form>
    </div>
    
    <div id="dialog2" title="上传品牌头图(APP)" style="display: none">
        <form id ="m_head" method ="post" enctype ="multipart/form-data" >
            <fieldset>
                <label for="head">品牌头图</label>
                <input type="file" name="uploadhead" id="uploadhead" class="text ui-widget-content ui-corner-all">
                <input type="hidden" name="type" id="type" value="uploadhead">
                <input type="hidden" name="width"  id="width"  value="{{$headsize.width}}">
                <input type="hidden" name="height" id="height" value="{{$headsize.height}}">
            </fieldset>
        </form>
    </div>
    
   <div id="dialog3" title="上传品牌图标(APP)" style="display: none">
        <form id ="icon" method ="post" enctype ="multipart/form-data" >
            <fieldset>
                <label for="icon">品牌图标图</label>
                <input type="file" name="uploadicon" id="uploadicon" class="text ui-widget-content ui-corner-all">
                <input type="hidden" name="type" id="type" value="uploadicon">
                <input type="hidden" name="width"  id="width"  value="{{$iconsize.width}}">
                <input type="hidden" name="height" id="height" value="{{$iconsize.height}}">
            </fieldset>
        </form>
    </div>
</div>
{{include file='admin/footer.php'}}