<?php /* Smarty version 2.6.27, created on 2016-02-18 17:46:16
         compiled from admin/add_crowdfunding.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'admin/add_crowdfunding.php', 99, false),array('modifier', '_isset', 'admin/add_crowdfunding.php', 180, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<style type="text/css">
	.choiceBox a{position:relative;}
	.choiceBox a img {position: absolute;top: -6px;right: -6px;width: 20px;cursor:pointer;}
</style>
<div id="rightTop">
  <p>券管理</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/crowdfunding/list/page:<?php echo $this->_tpl_vars['page']; ?>
">一元众筹</a></li>
    <?php if (! $this->_tpl_vars['row']['ticket_id']): ?><li><a class="btn3" href="/admin/crowdfunding/user-shop/uname:<?php echo $this->_tpl_vars['uname']; ?>
">店铺选择</a></li><?php endif; ?>
    <li><span><?php if ($this->_tpl_vars['row']['ticket_id']): ?>编辑众筹<?php else: ?>新建众筹<?php endif; ?></span></li>
  </ul>
</div>
<div class="info">
<form method="POST" action="<?php echo $this->_tpl_vars['_CONF']['FORM_ACTION']; ?>
" id="form" enctype="multipart/form-data">
<input type="hidden" name="sid" id="sid" value="<?php echo $this->_tpl_vars['sid']; ?>
" />
<input type="hidden" name="user_name" id="user_name" value="<?php echo $this->_tpl_vars['uname']; ?>
" />
<input type="hidden" name="tid" id="tid" value="<?php echo $this->_tpl_vars['row']['ticket_id']; ?>
" />
<input type="hidden" name="page" id="page" value="<?php echo $this->_tpl_vars['page']; ?>
" />
<input type="hidden" name="sids" id="sids" value="" />
<div id="tabs">
	<ul>
		<li><a href="#tabs-1">众筹详情</a></li>
		<li><a href="#tabs-2">众筹图片</a></li>
	</ul>
	<div id="tabs-1">
        <table class="infoTable">
            <tr>
                <th class="paddingT15">所属店铺:</th>
                <td class="paddingT15 wordSpacing5">
                 <?php echo $this->_tpl_vars['row']['region_name']; ?>
 <?php echo $this->_tpl_vars['row']['circle_name']; ?>
 <?php echo $this->_tpl_vars['row']['shop_name']; ?>

                </td>
            </tr>
<!--        <tr>
                <th class="paddingT15">关联店铺:</th>
                <td class="paddingT15 wordSpacing5">
                    <a href="javascript:chooseRelationShop()">点击选择关联店铺</a>
                </td>
            </tr>
            <tr>
                <th class="paddingT15"></th>
                <td class="paddingT15 wordSpacing5">
                <span id="choiceBoxShop" class="choiceBox">
                <?php $_from = $this->_tpl_vars['ticketRelationShopArray']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
                <a><span><?php echo $this->_tpl_vars['item']['shop_name']; ?>
</span><img class="shopDel" src="/images/delete.png" data-sid = "<?php echo $this->_tpl_vars['item']['shop_id']; ?>
"/></a>
                <?php endforeach; endif; unset($_from); ?>
                </span>
                </td>
            </tr>--> 
            <tr>
                <th class="paddingT15">众筹标题:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableFile" type="text" name="ticket_title" id="ticket_title" value="<?php echo $this->_tpl_vars['row']['ticket_title']; ?>
" />
                  <label class="field_notice">30字以内</label>
                </td>
            </tr>  
            <tr>
                <th class="paddingT15">众筹分类:</th>
                <td class="paddingT15 wordSpacing5">
                    <select name="ticket_sort" id="ticket_sort" class="querySelect">
                        <option value="">请选择众筹分类</option>    	
                        <?php $_from = $this->_tpl_vars['storeArray']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
                        <option value="<?php echo $this->_tpl_vars['key']; ?>
" <?php if ($this->_tpl_vars['row']['ticket_sort'] == $this->_tpl_vars['key']): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['item']; ?>
</option>
                        <?php endforeach; endif; unset($_from); ?>
                    </select>
                    <label class="field_notice"></label>
                </td>    	
            </tr>
            <tr>
                <th class="paddingT15">销售平台:</th>
                <td class="paddingT15 wordSpacing5">
                    <input type="checkbox" class="querySelect" name="CanWeb" id="CanWeb" <?php if ($this->_tpl_vars['row']['CanWeb'] == 1): ?> checked="checked" <?php endif; ?> value="1" />WEB
                    <input type="checkbox" class="querySelect" name="CanWap" id="CanWap" <?php if ($this->_tpl_vars['row']['CanWap'] == 1): ?> checked="checked" <?php endif; ?> value="1" />WAP
                    <input type="checkbox" class="querySelect" name="CanApp" id="CanApp" <?php if ($this->_tpl_vars['row']['CanApp'] == 1): ?> checked="checked" <?php endif; ?> value="1" />APP
                </td>    	
            </tr>    
            <tr>
                <th class="paddingT15">限购:</th>
                <td class="paddingT15 wordSpacing5">
                  <input type="checkbox" class="querySelect" name="UserNameLimit" id="UserNameLimit" <?php if ($this->_tpl_vars['row']['UserNameLimit'] == 1): ?> checked="checked" <?php endif; ?> value="1" />每用户
                  <input type="checkbox" class="querySelect" name="MobileLimit" id="MobileLimit" <?php if ($this->_tpl_vars['row']['MobileLimit'] == 1): ?> checked="checked" <?php endif; ?> value="1" />每手机
                  <input class="infoTableInput2" type="text" name="climit" id="climit" value="<?php echo $this->_tpl_vars['row']['limit_count']; ?>
" /> 件 / 
                  <select name="unit" id="unit">
                        <option value="Activity" <?php if ($this->_tpl_vars['row']['limit_unit'] == 'Activity'): ?> selected="selected" <?php endif; ?>>场</option>
                        <option value="Hour" <?php if ($this->_tpl_vars['row']['limit_unit'] == 'Hour'): ?> selected="selected" <?php endif; ?> >小时</option>
                        <option value="Day" <?php if ($this->_tpl_vars['row']['limit_unit'] == 'Day'): ?> selected="selected" <?php endif; ?> >天</option>
                        <option value="Week" <?php if ($this->_tpl_vars['row']['limit_unit'] == 'Week'): ?> selected="selected" <?php endif; ?> >周</option>
                        <option value="Weekly" <?php if ($this->_tpl_vars['row']['limit_unit'] == 'Weekly'): ?> selected="selected" <?php endif; ?> >自然周</option>
                        <option value="Month" <?php if ($this->_tpl_vars['row']['limit_unit'] == 'Month'): ?> selected="selected" <?php endif; ?>>月</option>
                        <option value="Monthly" <?php if ($this->_tpl_vars['row']['limit_unit'] == 'Monthly'): ?> selected="selected" <?php endif; ?>>自然月</option>
                        <option value="Minutes" <?php if ($this->_tpl_vars['row']['limit_unit'] == 'Minutes'): ?> selected="selected" <?php endif; ?>>分钟</option>
                    </select>  
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">销售有效期:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableFile2" style="width:140px;" type="text" name="sdate" id="sdate" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm'})" value="<?php if ($this->_tpl_vars['row']['start_time']): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['row']['start_time'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M')); ?>
<?php endif; ?>" /> - 
                  <input class="infoTableFile2" style="width:140px;" type="text" name="edate" id="edate" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm',minDate:'#F{$dp.$D(\'sdate\',{d:7})}'})" value="<?php if ($this->_tpl_vars['row']['end_time']): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['row']['end_time'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M')); ?>
<?php endif; ?>"/>
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">使用有效期:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableFile2" style="width:140px;" type="text" name="stime" id="stime" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm'})" value="<?php if ($this->_tpl_vars['row']['valid_stime']): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['row']['valid_stime'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M')); ?>
<?php endif; ?>" /> - 
                  <input class="infoTableFile2" style="width:140px;" type="text" name="etime" id="etime" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm',minDate:'#F{$dp.$D(\'stime\',{d:1})}'})" value="<?php if ($this->_tpl_vars['row']['valid_etime']): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['row']['valid_etime'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M')); ?>
<?php endif; ?>"/>
                  <label class="field_notice"></label>
                </td>
            </tr>     
            <tr>
                <th class="paddingT15">抽奖时间:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableFile2" style="width:140px;" type="text" name="lottery_time" id="lottery_time" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm'})" value="<?php if ($this->_tpl_vars['row']['lottery_time']): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['row']['lottery_time'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M')); ?>
<?php endif; ?>" />
                  <label class="field_notice"></label>
                </td>
            </tr>  
            <tr>
                <th class="paddingT15">支付超时时间:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2" type="text" name="expiration_minute" id="expiration_minute" value="<?php if ($this->_tpl_vars['row']['expiration_minute']): ?><?php echo $this->_tpl_vars['row']['expiration_minute']; ?>
<?php else: ?>10<?php endif; ?>" />
                  <label class="field_notice">单位：分钟</label>
                </td>
            </tr>     
            <tr>
                <th class="paddingT15">数量:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2" type="text" name="total" id="total" value="<?php echo $this->_tpl_vars['row']['total']; ?>
" />
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">原价:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2" type="text" name="p_value" id="p_value" value="<?php echo $this->_tpl_vars['row']['par_value']; ?>
" />
                  <label class="field_notice">元</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">众筹价:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2" type="text" name="s_value" id="s_value" value="<?php if ($this->_tpl_vars['row']['selling_price']): ?><?php echo $this->_tpl_vars['row']['selling_price']; ?>
<?php else: ?>1<?php endif; ?>" />
                  <label class="field_notice">元</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">众筹简介:</th>
                <td class="paddingT15 wordSpacing5">
                  <textarea id="ticket_summary" name="ticket_summary"><?php echo $this->_tpl_vars['row']['ticket_summary']; ?>
</textarea>
                  <label class="field_notice">120个字符之内(汉字算一个字符)</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">封面图片:</th>
                <td class="paddingT15 wordSpacing5">
                  <input type="file" class="file" name="file_img" id="file_img" />
                  <label class="field_notice">图片尺寸 750 * 350</label>
                </td>
            </tr>
            <?php if ($this->_tpl_vars['row']['cover_img']): ?>
            <tr>
                <th class="paddingT15"></th>
                <td class="paddingT15 wordSpacing5">
                  <img src="<?php echo $this->_tpl_vars['_CONF']['IMG_URL']; ?>
/buy/cover/<?php echo $this->_tpl_vars['row']['cover_img']; ?>
" />
                </td>
            </tr>
            <?php endif; ?>            
            <tr>
                <th class="paddingT15">众筹详情:</th>
                <td class="paddingT15 wordSpacing5">
                  <textarea id="wap_content" name="wap_content" style="width:600px; height:300px;"><?php echo $this->_tpl_vars['row']['wap_content']; ?>
</textarea>
                  <label class="field_notice"></label>
                </td>
            </tr>            
            <tr>
                <th class="paddingT15">上下架:</th>
                <td class="paddingT15 wordSpacing5" >
                    
                    <input type="radio" name="is_auth" value="0" <?php if (((is_array($_tmp=$this->_tpl_vars['row']['is_auth'])) ? $this->_run_mod_handler('_isset', true, $_tmp) : _isset($_tmp)) && $this->_tpl_vars['row']['is_auth'] == 0): ?>checked="checked"<?php endif; ?> />下架
                    <input type="radio" name="is_auth" value="1" <?php if (((is_array($_tmp=$this->_tpl_vars['row']['is_auth'])) ? $this->_run_mod_handler('_isset', true, $_tmp) : _isset($_tmp)) && $this->_tpl_vars['row']['is_auth'] == 1): ?>checked="checked"<?php endif; ?> />上架
                    <label class="field_notice"></label>
                </td>
            </tr> 
            <tr>
                <th class="paddingT15">是否显示:</th>
                <td class="paddingT15 wordSpacing5" >
                    <input type="radio" name="is_show" value="0" <?php if (((is_array($_tmp=$this->_tpl_vars['row']['is_show'])) ? $this->_run_mod_handler('_isset', true, $_tmp) : _isset($_tmp)) && $this->_tpl_vars['row']['is_show'] == 0): ?>checked="checked"<?php endif; ?> />否
                    <input type="radio" name="is_show" value="1" <?php if (((is_array($_tmp=$this->_tpl_vars['row']['is_show'])) ? $this->_run_mod_handler('_isset', true, $_tmp) : _isset($_tmp)) && $this->_tpl_vars['row']['is_show'] == 1): ?>checked="checked"<?php endif; ?> />是
                    <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">关注数:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2" type="text" name="love_number" id="love_number" value="<?php if ($this->_tpl_vars['row']['love_number']): ?><?php echo $this->_tpl_vars['row']['love_number']; ?>
<?php else: ?>0<?php endif; ?>" />
                  <label class="field_notice">关注数伪造偏移量</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">中奖手机:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2" type="text" name="no_winning" id="no_winning" value="<?php echo $this->_tpl_vars['row']['no_winning']; ?>
" />
                  <label class="field_notice">默认中奖手机</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">中奖用户名:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2" type="text" name="winning_user_name" id="winning_user_name" value="<?php echo $this->_tpl_vars['row']['winning_user_name']; ?>
" />
                  <label class="field_notice">默认用户名</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">中奖倍率:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2" type="text" name="count_times" id="count_times" value="<?php echo $this->_tpl_vars['row']['count_times']; ?>
" />
                  <label class="field_notice">一份发几个中奖码</label>
                </td>
            </tr>
            <tr>
            	<th class="paddingT15">购买优惠:</th>
                <td class="paddingT15 wordSpacing5">
                  <select name="buy_discount" id="buy_discount">
                    <option value="0">无</option>
				  	<?php $_from = $this->_tpl_vars['buyDiscount']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
                    <option value="<?php echo $this->_tpl_vars['key']; ?>
" <?php if ($this->_tpl_vars['key'] == $this->_tpl_vars['row']['buy_discount']): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['item']; ?>
</option>
                    <?php endforeach; endif; unset($_from); ?>	                  
                  </select>
                  <label class="field_notice"></label>
                </td>
            </tr>
        </table>    
    </div>
    <div id="tabs-2">
    <table class="infoTable" id="gallery-table">
        <tbody>
            <tr>
                <td class="paddingT15"><a onclick="addImg(this)" href="javascript:;">[+]</a> 上传图片</td>
                <td class="paddingT15 wordSpacing5"><input type="file" class="file" name="img_url[]" /></td>
            </tr>
        </tbody>
    </table>
    <?php if ($this->_tpl_vars['wapImgData']): ?>
    <table class="infoTable">
        <tbody>
          <tr>
              <td width="150" class="paddingT15 wordSpacing5">排序</td>
              <td class="paddingT15 wordSpacing5">图片</td>
              <td class="paddingT15 wordSpacing5">创建时间</td>
              <td class="paddingT15 wordSpacing5">操作</td>
          </tr>
          <?php $_from = $this->_tpl_vars['wapImgData']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?> 
          <tr id="imgCol_<?php echo $this->_tpl_vars['item']['id']; ?>
">
              <td class="paddingT15 wordSpacing5"><span ectype="inline_edit" fieldname="sequence" fieldid="<?php echo $this->_tpl_vars['item']['id']; ?>
" required="1" class="node_name editable" action="img-ajax-col"><?php echo $this->_tpl_vars['item']['sequence']; ?>
</span></td>
              <td class="paddingT15 wordSpacing5"><img src="<?php echo $this->_tpl_vars['_CONF']['IMG_URL']; ?>
/buy/crowdfunding/<?php echo $this->_tpl_vars['item']['img_url']; ?>
" class="makesmall" max_width="400" /></td>
              <td class="paddingT15 wordSpacing5"><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['created'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M:%S') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M:%S')); ?>
</td>
              <td class="paddingT15 wordSpacing5"><a href="javascript:delConfirm(<?php echo $this->_tpl_vars['item']['id']; ?>
, '确认删除','你确定要删除这个图片吗？')">删除</a></td>
          </tr>
          <?php endforeach; endif; unset($_from); ?>
   	 	</tbody>
	</table>
    <?php endif; ?>
    </div>
    <table class="infoTable">
        <tr>
            <th class="paddingT15"> </th>
            <td class="ptb20">
                <?php if ($this->_tpl_vars['row']['ticket_id']): ?>
                <input type="button" value="编辑" name="Submit" class="formbtn1" onClick="checkSubmit()">
                <input type="reset" value="重置" name="reset" class="formbtn2">
                <?php else: ?>
                <input type="button" value="提交" name="Submit" class="formbtn1" onClick="checkSubmit()">
                <?php endif; ?>
            </td>
        </tr>    
    </table>
</div>
</form>
</div>

<div class="selectPopupShop" style="display:none" id="selectPopupShop">
<a class="selectPopupShop-button" onclick="document.getElementById('selectPopupShop').style.display = 'none'">关闭</a>
<table width="800" cellspacing="0" class="dataTable">
	<tbody>
      <tr>
        <td class="paddingT15 wordSpacing5">
          <p>
            <label><input type="radio" value="1" name="shop_type" checked="checked" /> 商圈</label>
            <label><input type="radio" value="2" name="shop_type" /> 商场</label>
            <label><input type="radio" value="3" name="shop_type" /> 品牌</label>
          </p>
        </td>
      </tr>    
      <tr>
        <td class="paddingT15 wordSpacing5">
        	<select name="region_id" id="region_id">
            	<option value="">请选择所在区</option>
                <?php $_from = $this->_tpl_vars['regionArray']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
                <option value="<?php echo $this->_tpl_vars['key']; ?>
"><?php echo $this->_tpl_vars['item']; ?>
</option>
                <?php endforeach; endif; unset($_from); ?>
            </select>
            <select name="related_id" id="related_id"></select>
            店铺: <input type="text" name="search_name" id="search_name" style="width:150px;" />
            <input type="button" class="formbtn" value="搜索店铺" onclick="search_to()" />
        </td>
      </tr>
      <tr>
          <td class="paddingT15 wordSpacing5">
          	<table class="infoTable">
            	<tr>
                    <th width="300">可选择店铺</th>
                    <th width="200"></th>
                    <th width="300">已选择店铺</th>
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
                    <?php $_from = $this->_tpl_vars['ticketRelationShopArray']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
                    <option value="<?php echo $this->_tpl_vars['item']['shop_id']; ?>
"><?php echo $this->_tpl_vars['item']['shop_name']; ?>
</option>
                    <?php endforeach; endif; unset($_from); ?>
                    </select>
                    </td>
            	</tr>                
            </table>          
          </td>
      </tr> 
      <tr>
        <td class="ptb20"><input type="button" value="确定" name="Submit" class="formbtn" onClick="shopTrue()"></td>
      </tr>
	</tbody> 
</table>
</div>

<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<link rel="stylesheet" type="text/css" href="/css/admin/upload.css" />
<link rel="stylesheet" type="text/css" href="/css/admin/popup.css" />
<link rel="stylesheet" type="text/css" href="/css/JqueryDialog/jquery-ui-1.9.2.custom.min.css" />
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" charset="utf-8" ></script>
<script type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" charset="utf-8" ></script>
<script type="text/javascript" src="/js/DatePicker/WdatePicker.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/admin/addImage.js?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
" charset="utf-8" ></script>
<script type="text/javascript" src="/js/admin/swfupload/swfupload.js?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
" charset="utf-8" ></script>
<script type="text/javascript" src="/js/admin/swfupload/swfupload.queue.js?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
" charset="utf-8" ></script>
<script type="text/javascript" src="/js/admin/swfupload/handlers.js?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
" charset="utf-8" ></script>
<script type="text/javascript" src="/js/kindeditor/kindeditor-min.js?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
" charset="utf-8" ></script>
<script type="text/javascript" src="/js/kindeditor/zh_CN.js?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
" charset="utf-8" ></script>
<script type="text/javascript" src="/js/select.js?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
"></script>
<script type="text/javascript" src="/js/JqueryDialog/jquery-ui-1.9.2.custom.min.js" charset="utf-8" ></script>
<script type="text/javascript" src="/js/admin/inline_edit.js" charset="utf-8"></script>
<script type="text/javascript">
var validArray = ['ticket_title', 'ticket_sort', 'p_value', 's_value', 'sdate', 'edate', 'ticket_summary'];
$(function(){	
	for(var i=0; i<validArray.length; i++) {
		$('#' + validArray[i]).bind('blur',function(){
			validInput($(this).attr("id"));
		});
	}	

	$("#choiceBoxShop").on('click','.shopDel',function(){
		var _this = $(this);
		$.dialog({
			title:'警告',
			content: '是否确认取消该店铺与众筹的关联？',
			ok: function() {
				$.ajax({
					url:"/admin/ticket/shop-del",
					dataType:"json",
					data:{"tid" : $("#tid").val(), "sid": _this.attr("data-sid")},
					success:function(data){
						if(data.status == 'ok') {
							_this.parent("a").remove();
						}
					},
					error:function(){
					}
				});	
			},
			cancel: true
		});
	});	
	
	$( "#tabs" ).tabs();
});

$('.formbtn1,.formbtn2').attr("disabled", false);
function checkSubmit()
{
	var len = 0;
	for(var i=0; i<validArray.length; i++) {
		if(validInput(validArray[i])) {
			len++;
		}
	}	
	
	if(len == validArray.length) {
		$('.formbtn1').attr("value", "提交中...").attr("disabled", true);
		$("#form").submit();
	}
	
}

function chooseRelationShop() {
	$('#selectPopupShop').show();
}

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
	
	attachAddButtonEvent('add', 'moveFrom', "moveTo", '请选择可选店铺!');
	attachDeleteButtonEvent('delete', 'moveFrom', "moveTo", "请选择要删除的店铺");
	
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

function shopTrue(){	
	var shopIdStr = "";
	var shopTextStr = "";
	
	$("#moveTo option").each(function(){
		shopIdStr += $(this).val() + ",";
		shopTextStr += '<a>' + $(this).text() + "</a>";
	});
	
	$('#sids').val(shopIdStr);
	$("#choiceBoxShop").html(shopTextStr);
	$('#selectPopupShop').hide();
}

function validInput(id) {
	var _msg = '';
	switch(id) {
		case 'activity_id':
				if($('#' + id).val().length == 0) {
					_msg = '选择活动名称';	
				}
			break;
		case 'ticket_class':
				if($('#' + id).val().length == 0) {
					_msg = '请选择商品分类';	
				}
			break;
		case 'ticket_title':
				if($('#' + id).val().length == 0) {
					_msg = '请输入商品名称';	
				} else if($('#' + id).val().length > 30) {
					_msg = '商品名称最多30个字符，汉字算一个字符';
				}
			break;
		case 'p_value':
				if($('#' + id).val().length == 0) {
					_msg = '请输入商品原价';	
				} else if(!/^[1-9][0-9]*(\.[0-9]{1,2})?$/.test($('#' + id).val())) {
					_msg = '商品原价错误';
				}
			break;			
		case 's_value':
				if($('#' + id).val().length == 0) {
					_msg = '请输入商品现价';	
				} else if(!/^[1-9]?[0-9]*(\.[0-9]{1,2})?$/.test($('#' + id).val())) {
					_msg = '商品现价错误';
				} else if(Number($("#MaxNumber").val()) > 0 && Number($("#MinNumber").val()) > 0) {
					if(Number($('#' + id).val()) < Number($("#MinNumber").val()) || Number($('#' + id).val()) > Number($("#MaxNumber").val())) {
						_msg = '商品现价只能在：' + $("#MinNumber").val() + '-' + $("#MaxNumber").val() + '之间';
					}
				}
			break;
		case 'a_price':
				if(Number($('#' + id).val()) > 0 && !/^[1-9]?[0-9]*(\.[0-9]{1,2})?$/.test($('#' + id).val())) {
					_msg = 'APP售价错误';
				} else if(Number($("#MaxAppNumber").val()) > 0 && Number($("#MinAppNumber").val()) > 0) {
					if(Number($('#' + id).val()) < Number($("#MinAppNumber").val()) || Number($('#' + id).val()) > Number($("#MaxAppNumber").val())) {
						_msg = 'APP售价只能在：' + $("#MinAppNumber").val() + '-' + $("#MaxAppNumber").val() + '之间';
					}
				}
			break;	
		case 'total':
				if($('#' + id).val().length == 0) {
					_msg = '请输入商品数量';	
				} else if(!/[1-9][0-9]*$/.test($('#' + id).val())) {
					_msg = '商品数量为正整数';
				}			
			break;
		case 'sdate':
				if($('#' + id).val().length == 0) {
					_msg = '请输入销售有效期';	
				}
			break;
		case 'edate':
				if($('#' + id).val().length == 0) {
					_msg = '请输入销售有效期';	
				}
			break;
		case 'stime':
				if($('#' + id).val().length == 0) {
					_msg = '请输入使用有效期';	
				}
			break;
		case 'etime':
				if($('#' + id).val().length == 0) {
					_msg = '请输入使用有效期';	
				}
			break;
		case 'ticket_summary':
				if($('#' + id).val().length == 0) {
					_msg = '请输入商品简介';	
				} else if($('#' + id).val().length > 120) {
					_msg = '商品简介最多120个字符，汉字算一个字符';
				}
			break;
		case 'wap_content':
				if($('#' + id).val().length == 0) {
					_msg = '请输入商品详情（APP）';	
				}
			break;
		case 'file_img_large':
				if($("#tid").val().length == 0) {
					if($('#' + id).val().length == 0) {
						_msg = '上传商品大图';	
					} else {
						var file = $('#' + id).val();
						if(!/.(gif|jpg|jpeg|png|gif|jpg|png)$/i.test(file)){
							_msg = '图片类型必须是.gif,jpeg,jpg,png中的一种';
						}					
					}
				}
			break;	
		case 'file_img_small':
				if($("#tid").val().length == 0) {
					if($('#' + id).val().length == 0) {
						_msg = '上传商品小图';	
					} else {
						var file = $('#' + id).val();
						if(!/.(gif|jpg|jpeg|png|gif|jpg|png)$/i.test(file)){
							_msg = '图片类型必须是.gif,jpeg,jpg,png中的一种';
						}					
					}
				}
			break;	
		case 'postTextarea':
			if(editor.html().length == 0) {
				_msg = '请输入商品使用说明';	
			}
			break;
	}
	if(_msg == '') {
		$('#' + id).closest("td").children("label").attr('class', 'field_notice').html('');
	} else {
		$('#' + id).closest("td").children("label").attr('class', 'error').html(_msg);
		return false;
	}
	return true;	
}

var Browser = new Object();
Browser.isIE = window.ActiveXObject ? true : false;

function addImg(obj)
{
    var _html = $("#gallery-table tr:first").html();
    _html = "<tr>" + _html.replace(/(.*)(addImg)(.*)(\[)(\+)/i, "$1removeImg$3$4-") + "</tr>";
    $("#gallery-table tbody").append(_html);

}

function removeImg(obj)
{
    var row = rowindex(obj.parentNode.parentNode);
    var tbl = document.getElementById('gallery-table');

    tbl.deleteRow(row);
}

function rowindex(tr)
{
    if (Browser.isIE)
    {
        return tr.rowIndex;
    }
    else
    {
        table = tr.parentNode.parentNode;
        for (i = 0; i < table.rows.length; i ++ )
        {
            if (table.rows[i] == tr)
            {
                return i;
            }
        }
    }
}

//单项删除确认框
function delConfirm(id,title,message){
        message = message?message:'你确定要删除这条数据吗？';
        $.dialog({
            title: title,
            okValue:'确认',
            cancelValue:'取消',
            width: 230,
            height: 100,
            fixed: true,
            content: message,
            ok: function () {
                $.ajax({
                    type:'GET',
                    url:'/admin/commodity/wap-img-del',
                    dataType:'json',
                    data:'id=' + id,
                    success : function(data) {
                        if(data.status == 'ok') {
                            $("#imgCol_" + id).remove();
                        }
                    }
                })
            },
            cancel: function () {
                return true;
            }
        });
} 
</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>