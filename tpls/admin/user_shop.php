{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css" />
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js"></script>
<script type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js"></script>
<div id="rightTop">
  <p>用户管理</p>
  <ul class="subnav">
    <li><a class="btn4" href="/admin/user/list/page:{{$page}}">用户列表</a></li>
    <li><span>关联店铺</span></li>
  </ul>
</div>
<div class="info">
    
    <input type="hidden" name="uid" id="uid" value="{{$row.user_id}}" />
    <input type="hidden" name="page" id="page" value="{{$page}}" />
    <input type="hidden" name="utype" id="utype" value="{{$utype}}" />
    <table width="800" cellspacing="0" class="dataTable">
    {{if $step eq 1}}
    <tbody>
      <tr>
        <th class="paddingT15"> 用户名:</th>
        <td class="paddingT15 wordSpacing5">{{$row.user_name}}</td>
      </tr>
      <tr>
        <th class="paddingT15"> 用户分类:</th>
        <td class="paddingT15 wordSpacing5">
          <p>
            <label><input type="radio" value="1" name="user_type" {{if $row.user_type eq '1'}}checked="checked"{{/if}} /> 普通用户</label>
            <label><input type="radio" value="2" name="user_type" {{if $row.user_type eq '2'}}checked="checked"{{/if}} /> 认证商户</label>
            <label><input type="radio" value="3" name="user_type" {{if $row.user_type eq '3'}}checked="checked"{{/if}} /> 营业员</label>
          </p>       
        </td>
      </tr>
      <tr>
        <th></th>
        <td class="ptb20"><input type="button" value="下一步" name="Submit" class="formbtn" onClick="nextStep()"></td>
      </tr>
    </tbody>
	<script type="text/javascript">
    function nextStep() {
        var utype = $("input[name=user_type]:checked").val();
        var uid = $('#uid').val();
        var step = $('#step').val();
        var page = $('#page').val();
        window.location = '/admin/user/user-shop/uid:' + uid + '/utype:' +  utype + '/step:2/page:' + page
    }
    </script>   
    {{elseif $step eq 2}}
    <tbody>
      <tr>
        <th class="paddingT15"> 用户名:</th>
        <td class="paddingT15 wordSpacing5">{{$row.user_name}}</td>
      </tr>
      <tr>
        <th class="paddingT15"> 用户分类:</th>
        <td class="paddingT15 wordSpacing5">{{if $utype eq 1}}普通用户{{elseif $utype eq 2}}认证商户{{elseif $utype eq 3}}营业员{{/if}}</td>
      </tr>
      <tr>
        <th class="paddingT15"> 关联店铺:</th>
        <td class="paddingT15 wordSpacing5">
          <p>
            <label><input type="radio" value="1" name="shop_type" checked="checked" /> 商圈</label>
            <label><input type="radio" value="2" name="shop_type" /> 商场</label>
            <label><input type="radio" value="3" name="shop_type" /> 品牌</label>
          </p>
        </td>
      </tr>
      <tr>
        <th class="paddingT15"></th>
        <td class="paddingT15 wordSpacing5">
        	<select name="region_id" id="region_id">
            	<option value="">请选择所在区</option>
                {{foreach from=$regionArray key=key item=item}}
                <option value="{{$key}}">{{$item}}</option>
                {{/foreach}}
            </select>
            <select name="related_id" id="related_id"></select>
            店铺: <input type="text" name="search_name" id="search_name" style="width:150px;" />
            <input type="button" class="formbtn" value="搜索店铺" onclick="search_to()" />
        </td>
      </tr>
      <tr>
          <th class="paddingT15"></th>
          <td class="paddingT15 wordSpacing5">
          	<table class="infoTable">
            	<tr>
                    <th width="300">可选择店铺</th>
                    <th width="200"></th>
                    <th width="300">已关联店铺</th>
                </tr>
                <tr>
                    <td>
                        <select style="height:300px; width:100%" multiple="multiple" name="moveFrom" id="moveFrom"></select>
                    </td>
                    <td>
                      <table border="0" cellspacing="1" cellpadding="0" width="98%">
                            <tr><td style="text-align:center; height:60px; line-height:60px;">
                            <input type="button" value="添　加" id="add"/></td></tr>
                            <tr><td style="text-align:center; height:60px; line-height:60px;">
                            <input type="button" value="删　除" id="delete"/></td></tr>
                      </table>
                    </td>
                    <td>
                    <select style="height:300px; width:100%" multiple="multiple" name="moveTo" id="moveTo">
                    {{foreach from=$userRelationShopArray key=key item=item}}
                    <option value="{{$item.shop_id}}">{{$item.shop_name}}</option>
                    {{/foreach}}
                    </select>
                    </td>
            	</tr>                
            </table>          
          </td>
      </tr> 
      <tr>
        <th></th>
        <td class="ptb20"><input type="submit" value="确定" name="Submit" class="formbtn" onClick="moveTrue()"></td>
      </tr>
    </tbody>
    <script type="text/javascript">
	
		function resetRelated(obj, stype) {
			if(stype == 1) {
				obj.append($("<option>").text('请选择商圈').val(''));
			} else if(stype == 2) {
				obj.append($("<option>").text('请选择商场').val(''));
			} else if(stype == 3) {
				obj.append($("<option>").text('请选择品牌').val(''));
			}			
		}
		
		$("input[name=shop_type]").click(function(){
			var _this;
			$('#region_id').val('');
			_this = $('#related_id');
			_this.empty();			
			resetRelated(_this, this.value);
		});
		
		$(function(){
			$('#region_id').val('');
			var stype = $("input[name=shop_type]:checked").val();
			resetRelated($('#related_id'), stype);
						
			//$('#related_id').append($("<option>").text('请选择商圈').val(''));
			attachAddButtonEvent('add', 'moveFrom', "moveTo", '请选择被关联的对象!');
			attachDeleteButtonEvent('delete', 'moveFrom', "moveTo", "请选择要删除的关联对象");
			
			$('#region_id').change(function(){
					var stype = $("input[name=shop_type]:checked").val();
					var _this = $('#related_id');
					_this.attr("disabled", false);
					_this.empty();
					resetRelated(_this, stype);
					if(this.value) {
						$.post('/admin/user/get-sel-list', {region_id:$(this).val(), stype : stype}, function(obj){
							var data = eval('(' + obj + ')');
							$.each(data, function(i, s){
								_this.append($("<option>").text(s.name).val(s.id));
							});
						});	
					}
			});			
		});
		
		
		function search_to() {
			var stype = $("input[name=shop_type]:checked").val();
			var related_id = $('#related_id').val();
			var region_id = $('#region_id').val();
			var sname = $('#search_name').val();
			var _this = $('#moveFrom');
			_this.empty();
			$.ajax({
				url:"/admin/user/get-shop-list",
				dataType:"json",
				data:{"stype":stype,"related_id":related_id, "region_id":region_id, "sname":sname},
				success:function(data){
					$.each(data, function(i, s){
						_this.append($("<option>").text(s.name).val(s.id));
					});											
				},
				error:function(){
				}
			})
		}
		
		/*attachAddButtonEvent：给add按钮添加事件*/
		 function attachAddButtonEvent(addButtonId, candidateListId, selectedListId, msg) {
			$(function() {
				$("#" + addButtonId).click(function() {
					if ($("#" + candidateListId + " option:selected").length > 0)
					{
						$("#" + candidateListId + " option:selected").each(function() {
							$("#" + selectedListId).append("<option value='" + $(this).val() + "'>" + $(this).text() + "</option");
							//$(this).remove();
						})
					}
					else
					{
						alert(msg);
					}
				})
			})
		}
		/*attachDeleteButtonEvent：给delet按钮添加事件*/
		function attachDeleteButtonEvent(deleteButtonId, candidateListId, selectedListId, msg) {
			$(function() {
				$("#" + deleteButtonId).click(function() {
					if ($("#" + selectedListId + " option:selected").length > 0)
					{
						$("#" + selectedListId + " option:selected").each(function() {
							//$("#" + candidateListId).append("<option value='" + $(this).val() + "'>" + $(this).text() + "</option");
							$(this).remove();
						})
					}
					else
					{
						alert(msg);
					}
				})
			})
		}
		
		function moveTrue(){	
			var shopIdStr = "";
			var page = $('#page').val();
			var uid = $("#uid").val();
			var utype = $("#utype").val();
			
			$("#moveTo option").each(function(){
				shopIdStr += $(this).val() + ",";
			});
			$.post('/admin/user/user-shop-save',{uid:uid, utype:utype, sid:shopIdStr, page:page},function(data){				
				if(data == 'ok') {
					$.dialog({
							title:'提示',
							content:'用户店铺关联成功',
							okValue : '确定',
							ok:function(){
								window.location = '/admin/user/user-shop/uid:' + uid + '/utype:' +  utype + '/step:2/page:' + page;
							},
							cancelValue : '取消'
						}
					);
					 				
				}
			});
		}				
    </script>
    {{/if}}
    </table>
</form>
</div>

{{include file='admin/footer.php'}}