{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/validate.min.js" ></script>
<script type="text/javascript">
$(function(){
    $('#position_form').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error); 
        },
        rules : { 
        	pos_name : {
                required : true  
            }, 
            identifier : {
            	required : true
            },
            width : {
            	required : true,
            	number   : true
            },
            height : {
            	number   : true
            }
        },
        messages : {
        	pos_name :{
                required     : '请输入推荐位名称'
            },
            identifier : {
            	required : '请输入推荐位标识'
            },
            width :{
                required     : '请输入宽度',
                number       : '宽度必须为数字'
            },
            height :{
                number       : '高度必须为数字'
            }
        }
    });
});
</script>
<div id="rightTop">
  <p>推荐位管理</p>
  <ul class="subnav">
  	<li><span>新增推荐位</span></li>
    <li><a class="btn1" href="/admin/position/list">推荐位列表</a></li>
  </ul>
</div>
<div class="info">
<form method="POST" id="position_form">
<input type="hidden" name="pos_id" value="{{$pRow.pos_id}}">
<table class="infoTable">
      <tr>
        <th class="paddingT15"> 推荐位名称:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" id="pos_name" type="text" name="pos_name" value="{{$pRow.pos_name}}" style="width:100px;" />
          <label class="field_notice"></label>
          </td>
      </tr>
    
    <tr>
        <th class="paddingT15"> 推荐位标识:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" id="identifier" type="text" name="identifier" value="{{$pRow.identifier}}" style="width:100px;" />
          <label class="field_notice"></label>
          </td>
      </tr>

    <tr>
        <th class="paddingT15"> 宽度:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" id="width" type="text" name="width" value="{{$pRow.width}}" style="width:30px;" />
          <label class="field_notice"></label>
          </td>
      </tr>
      <tr>
        <th class="paddingT15"> 高度:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" id="height" type="text" name="height" value="{{$pRow.height}}" style="width:30px;" />
          <label class="field_notice"></label>
          </td>
      </tr>
      
    <tr>
        <th class="paddingT15"> </th>
        <td class="ptb20">
    	  <input class="formbtn" type="submit" value="确定" />
          <input class="formbtn" type="reset" name="Submit2" value="重置" /></td>
    </tr>
</table>
</form>
</div>
{{include file='admin/footer.php'}}