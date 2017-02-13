<?php /* Smarty version 2.6.27, created on 2016-02-19 09:40:58
         compiled from test.php */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div class="info">
<form method="POST" action="<?php echo $this->_tpl_vars['_CONF']['FORM_ACTION']; ?>
" id="form" enctype="multipart/form-data">
 <table class="infoTable">
    <tr>
        <th class="paddingT15">所属店铺:</th>
        <td class="paddingT15 wordSpacing5">
        </td>
    </tr>
 	<tr>
        <th class="paddingT15"> </th>
        <td class="ptb20">

            <input type="button" value="提交" name="Submit" class="formbtn1" onClick="checkSubmit()">
        </td>
    </tr>    
  </table>
</form>
</div>


<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" charset="utf-8" ></script>
<script type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" charset="utf-8" ></script>
<script type="text/javascript">
var dialog = $.dialog({id: 'N3690'}); 

$.ajax({ 
	url: 'home/market/show/mid/624', 
	success:function (data) 
	{ 
		//dialog.content(data); 
	}, 
	cache: false 
});
</script>