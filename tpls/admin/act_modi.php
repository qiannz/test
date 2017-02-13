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
        	act_name : {
				required : true
            },
            act_mart : {
				required : true,
                remote   : {
                    url :'/admin/active/mart-check',
                    type:'post',
                    data:{
                    	act_mart : function(){
                            return $('#act_mart').val();
                        },
                        act_id : '{{$actRow.act_id}}'
                    }
                }
            },
            act_content : {
				required : true
            },
            share_num : {
				required : true
            },
			win_num  : {
				required : true
			},
            start_time : {
				required : true
            },
            end_time : {
				required : true
            }
        },
        messages : {
        	act_name : {
            	required : '请输入活动名称'
            },
        	act_mart : {
            	required : '请输入活动标记', 
            	remote : '该活动标记已存在'
            },
        	act_content : {
            	required : '请输入活动简介'
            },
        	share_num : {
            	required : '请输入活动分享数'
            },
			win_num : {
            	required : '请输入活动中奖数'
            },
        	start_time : {
            	required : '请输入活动开始时间'
            },
        	end_time : {
            	required : '请输入活动结束时间'
            }
        }
    });

});
</script>
<div id="rightTop">
  <p>活动管理</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/active/list">活动列表</a></li>    
    {{if $actRow.act_id}}
    <li><a class="btn1" href="/admin/active/add">新增活动</a></li>    
  	<li><span>编辑活动</span></li>
    {{else}}
  	<li><span>新增活动</span></li>
    {{/if}}	
  </ul>
</div>
<div class="info">
  <form method="post" id="active_form">
    <input type="hidden" name="act_id" value="{{$actRow.act_id}}" />
    <table class="infoTable">
    
      <tr>
        <th class="paddingT15"> 活动名称:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" id="act_name" type="text" name="act_name" value="{{$actRow.act_name}}" />
          <label class="field_notice"></label>
          </td>
      </tr>
      <tr>
        <th class="paddingT15"> 活动标记:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" id="act_mart" type="text" name="act_mart" value="{{$actRow.act_mart}}" />
          <label class="field_notice"></label>
          </td>
      </tr>      
      <tr>
        <th class="paddingT15"> 活动简介:</th>
        <td class="paddingT15 wordSpacing5">
          <textarea id="act_content" name="act_content">{{$actRow.act_content}}</textarea>
          <label class="field_notice"></label>
          </td>
      </tr> 
      <tr>
        <th class="paddingT15"> 活动分享数:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" style="width:30px" id="share_num" type="text" name="share_num" value="{{$actRow.share_num}}" />
          <label class="field_notice"></label>
          </td>
      </tr> 
      <tr>
        <th class="paddingT15"> 活动中奖数:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" style="width:30px" id="win_num" type="text" name="win_num" value="{{$actRow.win_num}}" />
          <label class="field_notice"></label>
          </td>
      </tr> 
      <tr>
        <th class="paddingT15">开始时间:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableFile2" style="width:140px;" type="text" name="start_time" id="start_time" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm',minDate:'now()'})" value="{{if $actRow.start_time}}{{$actRow.start_time|date_format:'%Y-%m-%d %H:%I'}}{{/if}}" />  
          <label class="field_notice"></label>
        </td>
     </tr> 
      <tr>
        <th class="paddingT15">结束时间:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableFile2" style="width:140px;" type="text" name="end_time" id="end_time" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm',minDate:'now()'})" value="{{if $actRow.end_time}}{{$actRow.end_time|date_format:'%Y-%m-%d %H:%I'}}{{/if}}" />  
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