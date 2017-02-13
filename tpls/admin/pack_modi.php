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
        	pack_name : {
				required : true
            },
            pack_logo : {
				required : true,
                remote   : {
                    url :'/admin/pack/logo-check',
                    type:'post',
                    data:{
                    	pack_logo : function(){
                            return $('#pack_logo').val();
                        },
                        pack_id : '{{$packRows.pack_id}}'
                    }
                }
            },
            good_num : {
				required : true,
				number : true
            },
            ticket_num : {
				required : true,
				number : true
            },
			pack_explan  : {
				required : true
			}
        },
        messages : {
        	pack_name : {
            	required : '请输入套餐名称'
            },
        	pack_logo : {
            	required : '请输入套餐标识', 
            	remote : '该套餐标记已存在'
            },
        	good_num : {
            	required : '请输入商品数量限制',
				number : '商品数量限制必须为数字'
            },
        	ticket_num : {
            	required : '请输入券限制数量',
				number : '券限制数量必须为数字'
            },
			pack_explan : {
            	required : '请输券说明'
            }
        }
    });

});
</script>
<div id="rightTop">
  <p>套餐设置</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/pack/list">套餐列表</a></li>    
    {{if $packRows.pack_id}}
    <li><a class="btn1" href="/admin/pack/add">新增套餐</a></li>    
  	<li><span>编辑套餐</span></li>
    {{else}}
  	<li><span>新增套餐</span></li>
    {{/if}}	
  </ul>
</div>
<div class="info">
  <form method="post" id="active_form">
    <input type="hidden" name="pack_id" value="{{$packRows.pack_id}}" />
    <table class="infoTable">
      <tr>
        <th class="paddingT15"> 套餐名称:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" id="pack_name" type="text" name="pack_name" value="{{$packRows.pack_name}}" />
          <label class="field_notice"></label>
          </td>
      </tr>
      <tr>
        <th class="paddingT15"> 套餐标识:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" id="pack_logo" type="text" name="pack_logo" value="{{$packRows.pack_logo}}" />
          <label class="field_notice"></label>
          </td>
      </tr>      
      <tr>
        <th class="paddingT15"> 商品数量限制:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" style="width:30px" id="good_num" type="text" name="good_num" value="{{$packRows.good_num}}" />
          <label class="field_notice"></label>
          </td>
      </tr> 
      <tr>
        <th class="paddingT15"> 券限制数量:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" style="width:30px" id="ticket_num" type="text" name="ticket_num" value="{{$packRows.ticket_num}}" />
          <label class="field_notice"></label>
          </td>
      </tr> 
      <tr>
        <th class="paddingT15"> 券说明:</th>
        <td class="paddingT15 wordSpacing5">
          <textarea id="pack_explan" name="pack_explan">{{$packRows.pack_explan}}</textarea>
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