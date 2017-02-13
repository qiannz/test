{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/jquery.validate.min.js" ></script>
<script type="text/javascript">
$(function(){
	jQuery.validator.addMethod("isCode", function(value, element) {  
		var unique = /^[a-z]{3,20}$/;
		return this.optional(element) || (unique.test(value));
	}, "类别标记只能由3-20位小写英文字母组成");
	
    $('#category_form').validate({
		
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },
        success:function(label){
            label.addClass('right').text('OK!');
        },
        onkeyup:false,
        rules:{
        	sort_name : {
                required:true,
                remote:{
                    url :'/admin/sort/check-category',
                    type:'post',
                    data:{
                    	sort_name : function(){
                            return $('#sort_name').val();
                        },
                        id : '{{$category.sort_id}}'
                    }
                }
            },
            sort_unique : {
                required:true,
				isCode:true,
                remote:{
                    url :'/admin/sort/check-unique',
                    type:'post',
                    data:{
                    	sort_unique : function(){
                            return $('#sort_unique').val();
                        },
                        id : '{{$category.sort_id}}'
                    }
                }
            }
        },
        messages : {
        	sort_name : {
                required : '类别名称不能为空 ',
                remote : '该类别名称已经存在了，请您换一个'
            },
			sort_unique : {
                required : '类别标记不能为空 ',
				//isCode : '类别标记只能由3-20位小写英文字母组成',
                remote : '该类别标记已经存在了，请您换一个'
            }
        }
    });
});
</script>
<div id="rightTop">
  <p>全站分类</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/sort/list">分类管理</a></li>    
    <li><a class="btn1" href="/admin/sort/sort-add">分类新增</a></li>
    <li><a class="btn1" href="/admin/sort/category-list">类别管理</a></li>
    <li>
    {{if $category.id}}
    	<a class="btn1" href="/admin/sort/category-add">类别添加</a>
        {{else}}
         <span>类别添加</span>
        {{/if}}    
    </li>
  </ul>
</div>
<div class="info">
  <form method="post" id="category_form">
    <input type="hidden" name="id" value="{{$category.sort_id}}" />
    <table class="infoTable">
    
      <tr>
        <th class="paddingT15"> 类别名称:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" id="sort_name" type="text" name="sort_name" value="{{$category.sort_name}}" />
          <label class="field_notice"></label>
          </td>
      </tr>
      <tr>
        <th class="paddingT15"> 类别标记:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" id="sort_unique" type="text" name="sort_unique" value="{{$category.sort_unique}}" />
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