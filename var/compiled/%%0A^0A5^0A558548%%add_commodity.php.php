<?php /* Smarty version 2.6.27, created on 2016-02-18 17:45:55
         compiled from admin/add_commodity.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', '_isset', 'admin/add_commodity.php', 82, false),array('modifier', 'date_format', 'admin/add_commodity.php', 170, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id="rightTop">
  <p>商城商品</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/commodity/list/page:<?php echo $this->_tpl_vars['page']; ?>
">商品管理</a></li>
    <?php if (! $this->_tpl_vars['row']['ticket_id']): ?><li><a class="btn3" href="/admin/commodity/user-shop/uname:<?php echo $this->_tpl_vars['uname']; ?>
">店铺选择</a></li><?php endif; ?>
    <li><span><?php if ($this->_tpl_vars['row']['ticket_id']): ?>编辑商品<?php else: ?>新建商品<?php endif; ?></span></li>
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
<input type="hidden" name="dataStr" id="dataStr" value="" />
<input type="hidden" name="dataRetStr" id="dataRetStr" value="" />
<input type="hidden" name="dataSkuStr" id="dataSkuStr" value='<?php echo $this->_tpl_vars['row']['SkuStr']; ?>
' />
<input type="hidden" id="MaxNumber" value="" />
<input type="hidden" id="MinNumber" value="" />
<input type="hidden" id="MaxAppNumber" value="" />
<input type="hidden" id="MinAppNumber" value="" />
<div id="tabs">
	<ul>
		<li><a href="#tabs-1">商品详情</a></li>
		<li><a href="#tabs-2">商品图片</a></li>
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
            <tr>
                <th class="paddingT15">关联店铺:</th>
                <td class="paddingT15 wordSpacing5">
                    <a href="javascript:associate()">点击选择关联店铺</a>
                </td>
            </tr>
            <tr>
                <th class="paddingT15"></th>
                <td class="paddingT15 wordSpacing5">
                <span id="choiceBoxShop" class="choiceBox">
                <?php $_from = $this->_tpl_vars['ticketRelationShopArray']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
                <a><?php echo $this->_tpl_vars['item']['shop_name']; ?>
</a>
                <?php endforeach; endif; unset($_from); ?>
                </span>
                </td>
            </tr>  
            <tr>
                <th class="paddingT15">商品名称:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableFile" type="text" name="ticket_title" id="ticket_title" value="<?php echo $this->_tpl_vars['row']['ticket_title']; ?>
" />
                  <label class="field_notice">100字以内</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">商品卖点:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableFile" type="text" name="selling_points" id="selling_points" value="<?php echo $this->_tpl_vars['row']['selling_points']; ?>
" />
                  <label class="field_notice"></label>
                </td>
            </tr> 
            <tr>
                <th class="paddingT15">商品分类:</th>
                <td class="paddingT15 wordSpacing5">
                    <select name="ticket_sort" id="ticket_sort" class="querySelect">
                        <option value="">请选择商品分类</option>    	
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
                <th class="paddingT15">商品小分类:</th>
                <td class="paddingT15 wordSpacing5">
                    <input type="radio" name="ticket_class" value="1" <?php if (((is_array($_tmp=$this->_tpl_vars['row']['ticket_class'])) ? $this->_run_mod_handler('_isset', true, $_tmp) : _isset($_tmp)) && $this->_tpl_vars['row']['ticket_class'] == 1): ?>checked="checked"<?php endif; ?> /> 店铺商品
                    <input type="radio" name="ticket_class" value="2" <?php if (((is_array($_tmp=$this->_tpl_vars['row']['ticket_class'])) ? $this->_run_mod_handler('_isset', true, $_tmp) : _isset($_tmp)) && $this->_tpl_vars['row']['ticket_class'] == 2): ?>checked="checked"<?php endif; ?> /> 特卖商品
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
                <th class="paddingT15">SKU商品分类:</th>
                <td class="paddingT15 wordSpacing5">
                    <div class="inputBox" style="position:relative; z-index:995">
                                <span class="change-shop"><?php if ($this->_tpl_vars['row']['category_name']): ?><?php echo $this->_tpl_vars['row']['category_name']; ?>
<?php else: ?>----选择----<?php endif; ?></span><span class="change-shop-pic"><s class="change-shop-pic-s"></s></span>
                                
                              <div class="allList" id="allBox">
                                    <div class="listBox">
                                            <ul>
                                                <?php $_from = $this->_tpl_vars['skuArray']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
                                                <li class="listBox-col">
                                                    <a data-cid="<?php echo $this->_tpl_vars['item']['CategoryId']; ?>
"><?php echo $this->_tpl_vars['item']['CategoryName']; ?>
</a>
                                                    <?php if ($this->_tpl_vars['item']['child']): ?>
                                                    <div class="shopBox">
                                                        <ul>
                                                            <?php $_from = $this->_tpl_vars['item']['child']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['ckey'] => $this->_tpl_vars['citem']):
?>
                                                            <li><a data-cid="<?php echo $this->_tpl_vars['citem']['CategoryId']; ?>
"><?php echo $this->_tpl_vars['citem']['CategoryName']; ?>
</a></li>
                                                            <?php endforeach; endif; unset($_from); ?>
                                                        </ul>
                                                    </div>
                                                    <?php endif; ?>
                                                </li>
                                                <?php endforeach; endif; unset($_from); ?>
                                            </ul>
                                    </div>
                               </div>
                            </div>
                </td>    	
            </tr> 
            <tr>
                <th class="paddingT15"></th>
                <td class="paddingT15 wordSpacing5">
                <div class="sku-dis">
                        <?php $_from = $this->_tpl_vars['skuListArray']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
                        <div class="inputBox">
                            <label class="sku-label" data-cid="<?php echo $this->_tpl_vars['item']['CategoryId']; ?>
" data-pid="<?php echo $this->_tpl_vars['item']['PropId']; ?>
"><?php echo $this->_tpl_vars['item']['PropName']; ?>
</label>
                            <div class="color-list">
                                <ul>
                                    <?php $_from = $this->_tpl_vars['item']['Values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['skey'] => $this->_tpl_vars['sitem']):
?>
                                    <li><input type="checkbox" class="color-checkbox" value="<?php echo $this->_tpl_vars['sitem']['ValueId']; ?>
" initvalue="<?php echo $this->_tpl_vars['sitem']['ValueName']; ?>
" data-alias="<?php echo $this->_tpl_vars['item']['AllowAlias']; ?>
">
                                    <span class="color-val"><?php echo $this->_tpl_vars['sitem']['ValueName']; ?>
</span><input type="text" class="color-input" /></li>
                                    <?php endforeach; endif; unset($_from); ?>
                                </ul>
                            </div>
                        </div>
                        <?php endforeach; endif; unset($_from); ?>
                </div>
                <div class="inputBox" id="ske-table-box">
                    <label class="label">SKU设置：</label>
                    <div class="right-list"></div>
                </div>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">销售有效日期:</th>
                <td class="paddingT15 wordSpacing5">
                	<input class="infoTableFile2" style="width:140px;" type="text" name="start_time" id="sdate" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" value="<?php if ($this->_tpl_vars['row']['start_time']): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['row']['start_time'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M:%S') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M:%S')); ?>
<?php endif; ?>" /> - 
 					<input class="infoTableFile2" style="width:140px;" type="text" name="end_time" id="edate" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'#F{$dp.$D(\'sdate\')}'})" value="<?php if ($this->_tpl_vars['row']['end_time']): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['row']['end_time'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M:%S') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M:%S')); ?>
<?php endif; ?>"/>
                  	<label class="field_notice">开始时间不填默认为当前时间，结束时间不填默认为2036-12-31 23:59:59</label>
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
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">现价:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2" type="text" name="s_value" id="s_value" value="<?php echo $this->_tpl_vars['row']['selling_price']; ?>
" />
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">APP售价:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2" type="text" name="a_price" id="a_price" value="<?php if ($this->_tpl_vars['row']['app_price'] > 0): ?><?php echo $this->_tpl_vars['row']['app_price']; ?>
<?php endif; ?>" />
                  <input type="checkbox" name="is_free" value="1" <?php if ($this->_tpl_vars['row']['is_free'] == 1): ?>checked="checked"<?php endif; ?> />APP端免单
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">商品简介:</th>
                <td class="paddingT15 wordSpacing5">
                  <textarea id="ticket_summary" name="ticket_summary"><?php echo $this->_tpl_vars['row']['ticket_summary']; ?>
</textarea>
                  <label class="field_notice">120个字符之内(汉字算一个字符)</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">上传图片</th>
                <td class="paddingT15 wordSpacing5">
                    <div class="list-box">
                        <div class="imgBtn">
                            上传图片
                            <input type="file" class="file" id="file">
                        </div>
                        <div id="loadBox"></div>
                        <span class="loadBar"><em style="width: 0%" id="loadBar"></em></span>
                        <span id="loadNum" class="exp"><em>等待上传</em></span>
                        <span class="exp">最多不超过10张，每张图片限1M</span>
                    </div>
                    <div class="imgBox" id="postImageList" style="display:none">
                        <ul class="clearfix">              
                        </ul>
                    </div>
                </td>
            </tr>   
            <tr>
                <th class="paddingT15">商品内容:</th>
                <td class="paddingT15 wordSpacing5"><textarea id="content"
                        name="content" style="width:800px; height:400px;" ><?php echo $this->_tpl_vars['row']['content']; ?>
</textarea>
                    <label class="field_notice"></label></td>
            </tr>            
            <tr>
                <th class="paddingT15">商品详情（APP）:</th>
                <td class="paddingT15 wordSpacing5">
                  <textarea id="wap_content" name="wap_content" style="width:600px; height:300px;"><?php echo $this->_tpl_vars['row']['wap_content']; ?>
</textarea>
                  <label class="field_notice"></label>
                </td>
            </tr>
			<tr>
                <th class="paddingT15">是否包邮:</th>
                <td class="paddingT15 wordSpacing5" >
                    
                    <input type="radio" name="free_shipping" value="0" <?php if (((is_array($_tmp=$this->_tpl_vars['row']['free_shipping'])) ? $this->_run_mod_handler('_isset', true, $_tmp) : _isset($_tmp)) && $this->_tpl_vars['row']['free_shipping'] == 0): ?>checked="checked"<?php endif; ?> />否
                    <input type="radio" name="free_shipping" value="1" <?php if (((is_array($_tmp=$this->_tpl_vars['row']['free_shipping'])) ? $this->_run_mod_handler('_isset', true, $_tmp) : _isset($_tmp)) && $this->_tpl_vars['row']['free_shipping'] == 1): ?>checked="checked"<?php endif; ?> />是
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
/buy/commodity/<?php echo $this->_tpl_vars['item']['img_url']; ?>
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
<script type="text/javascript" src="/js/sku20141120.js?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
"></script>
<script type="text/javascript" src="/js/JqueryDialog/jquery-ui-1.9.2.custom.min.js" charset="utf-8" ></script>
<script type="text/javascript" src="/js/admin/inline_edit.js" charset="utf-8"></script>
<script type="text/javascript">
var postImage, editor, uploadSwf;
var validArray = ['ticket_title', 'ticket_sort', 'ticket_class', 'p_value', 's_value', 'a_price', 'ticket_summary'];
$(function(){	
	var couponImage=function(){
		this.elem=$("#postImageList ul");
		this.imgCache={};
		this.init();
	}
	couponImage.prototype={
		init:function(){
			var _this=this;
			_this.elem.delegate(".del","click",function(){
				var _aid=$(this).attr("data-aid");
				_this.dele($(this),_aid);
			})
			_this.elem.delegate(".add","click",function(){
				var _aid=$(this).attr("data-aid");
				_this.addHtml(_aid);
			})
			$("#allAdd").bind("click",function(){
				_this.elem.find(".add").each(function(){
					var _aid=$(this).attr("data-aid");
					_this.addHtml(_aid);
				})
			})
		},
		getHtml:function(jsonData){
			var _data=(jsonData.data)[0];
			var _html="";
			_html+="<li>";
			_html+='<p class="img">';
			_html+='<img src="'+_data.img_url+'/w/160/h/160">';
			_html+='</p>'
			_html+='<p><a href="javascript:;" class="del" data-aid="'+_data.aid+'">删除</a> <a class="add" href="javascript:;" data-aid="'+_data.aid+'">插入</a></p>';
			_html+='</li>'
			this.imgCache[_data.aid]=_data.img_url;
			this.elem.append(_html).parent().css("display","block");
		},
		dele:function(elem,aid){
			var _this=this;
			var _elem=elem;
			$.ajax({
				url:"/admin/ticket/del-img",
				data:{"aid":aid},
				dataType:"json",
				success:function(data){
					if(data.status="ok"){
						var stats = uploadSwf.getStats();
						stats.successful_uploads--;
						uploadSwf.setStats(stats);
	   
						var _len=_this.elem.find("li").length;
						_elem.closest("li").remove();
						delete _this.imgCache[aid];
						if(_len<2){
							_this.elem.parent().css("display","none");
						}
					}
				}
			})
		},
		addHtml:function(aid){
			var _this=this;
			var _html="";
			_html+="<p>"
			_html+='<img src="'+_this.imgCache[aid]+'/w/740" alt="">'
			_html+="</p>"
			editor.insertHtml(_html);
		},
		addImgCache:function( aid , imgsrc ){
			this.imgCache[aid]=imgsrc;
		}
	}
			
	editor = KindEditor.create('#content',{items : ['source', '|', 'undo', 'redo', '|', 'justifyleft', 'justifycenter', 'justifyright','justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent','|', 'fullscreen', '/','fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold','italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'emoticons', 'link', 'unlink', 'baidumap']});
	postImage=new couponImage();
	uploadSwf = new SWFUpload({
        upload_url: "/admin/ticket/upload?folder=ticket",
        file_size_limit : "1024 KB",
        file_types : "*.jpg;*.gif;*.png",
        file_types_description : "All Files",
        file_upload_limit : "10",
        file_queue_limit : "10",
        file_post_name : "uploadFile",
		post_params: {"user_name" : "<?php echo $this->_tpl_vars['uname']; ?>
", "ticket_id" : "<?php echo $this->_tpl_vars['row']['ticket_id']; ?>
", "shop_id" : "<?php echo $this->_tpl_vars['sid']; ?>
"},
        file_dialog_start_handler : fileDialogStart,
        file_queued_handler : fileQueued,
        file_queue_error_handler : fileQueueError,
        file_dialog_complete_handler : fileDialogComplete,
        upload_start_handler : uploadStart,
        upload_progress_handler : uploadProgress,
        upload_error_handler : uploadError,
        upload_success_handler : uploadSuccess,
        upload_complete_handler : uploadComplete,

        // Button Settings
        button_image_url : "/images/upload.png",
        button_placeholder_id : "file",
        button_width: 137,
        button_height: 26,
        button_cursor: SWFUpload.CURSOR.HAND,
        button_window_mode : SWFUpload.WINDOW_MODE.TRANSPARENT,
        // Flash Settings
        flash_url : "/js/swfupload/swfupload.swf",
        // Debug Settings
        debug: false
    });
	
	for(var i=0; i<validArray.length; i++) {
		$('#' + validArray[i]).bind('blur',function(){
			validInput($(this).attr("id"));
		});
	}
	
	changeShop({
		btn:'.change-shop-pic',
		txt:'.allList',
		val:'.change-shop'
	});
	
	$('.shopBox a').click(function(){
		var cid = $(this).attr('data-cid');
		var _html = '';
		$(".sku-dis").html('加载中。。。');
		$(".right-list").html('');
		$.ajax({
		   type:"POST",
		   url:"/admin/buygood/get-sku-list",
		   data:{cid:cid},
		   dataType:"json",
		   success:function(data){
			   
			   var len = data.length;
			   for(var i=0; i < len; i++) {
					_html += '<div class="inputBox">';
					_html += '<label class="sku-label" data-cid="'+data[i]['CategoryId']+'" data-pid="'+data[i]['PropId']+'">'+data[i]['PropName']+'：</label>';
					_html += '<div class="color-list">';
					_html += '<ul>';
					var vlen = data[i].Values.length;
					for(var j=0; j < vlen; j++) {
						_html += '<li><input type="checkbox" class="color-checkbox" value="'+data[i]['Values'][j]['ValueId']+'" initvalue="'+data[i]['Values'][j]['ValueName']+'" data-alias="'+data[i]['AllowAlias']+'">';
						_html += '<span class="color-val">'+data[i]['Values'][j]['ValueName']+'</span><input type="text" class="color-input" /></li>';
					}
					_html += '</ul>';
					_html += '</div>';
					_html += '</div>';
			   }
			   if(_html != '') {
			   	  $(".sku-dis").html(_html);
				  sku();
			   } else {
				  $(".sku-dis").html('');
			   }
		   }
		});
	});
				   

	if($("#tid").val() > 0) {
		sku(<?php echo $this->_tpl_vars['row']['SkuStr']; ?>
);
	}
});

$(function() {
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
	/*
	if($("#tid").val() == 0) {
		if($("input[type=file]").val().length == 0) {
			alert('至少上传一张商品图片');
			return false;
		}
	}
	*/
	
	
	if(len == validArray.length) {
		getSkuToStr();
		$("#content").val(editor.html());
		$('.formbtn1').attr("value", "提交中...").attr("disabled", true);
		$("#form").submit();
	}
	
}

function associate() {
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

function getSkuToStr() {
	var dataStr = '';
	var data_cid;
	$("div .sku-dis .inputBox").each(function(index, element) {
		var data_pid;
        data_cid = $(this).children("label").attr('data-cid');
		data_pid = $(this).children("label").attr('data-pid');
		var skuStr = "";
		$.each($(this).find("li"), function(){
			var ckStr = "";
			if($(this).find(".color-checkbox").attr("checked") == "checked") {
				ckStr = $(this).find(".color-checkbox").val() + ',' + $(this).find(".color-val").html();
			}
			if(ckStr != "") {
				skuStr += data_pid + ';' + ckStr + '^^';
			}
		});
		
		if(skuStr != "") {
			skuStr = skuStr.substr(0, skuStr.length - 2);
			dataStr +=  skuStr + '&&';
		}
    });
	if(dataStr != "") {
		dataStr = data_cid + ',' + $("span.change-shop").html() + '&&' + dataStr.substr(0, dataStr.length - 2);
		$("#dataStr").val(dataStr);
	}
}

function validInput(id) {
	var _msg = '';
	var _input_type = 0;
	switch(id) {
		case 'activity_id':
				if($('#' + id).val().length == 0) {
					_msg = '选择活动名称';	
				}
			break;
		case 'ticket_sort':
				if($('#' + id).val().length == 0) {
					_msg = '请选择商品分类';	
				}
			break;
		case 'ticket_class':
				if( !$('input[name=' + id + ']:checked').val()) {
					_msg = '请选择商品小分类';	
					_input_type = 1;
				}
			break;
		case 'ticket_title':
				if($('#' + id).val().length == 0) {
					_msg = '请输入商品名称';	
				} else if($('#' + id).val().length > 100) {
					_msg = '商品名称最多100个字符，汉字算一个字符';
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
	if(_input_type == 1) {
		if(_msg == '') {
			$('input[name=' + id + ']').closest("td").children("label").attr('class', 'field_notice').html('');
		} else {
			$('input[name=' + id + ']').closest("td").children("label").attr('class', 'error').html(_msg);
			return false;
		}		
	} else {
		if(_msg == '') {
			$('#' + id).closest("td").children("label").attr('class', 'field_notice').html('');
		} else {
			$('#' + id).closest("td").children("label").attr('class', 'error').html(_msg);
			return false;
		}
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