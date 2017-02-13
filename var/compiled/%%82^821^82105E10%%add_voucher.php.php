<?php /* Smarty version 2.6.27, created on 2016-02-22 16:29:39
         compiled from admin/add_voucher.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', '_isset', 'admin/add_voucher.php', 78, false),array('modifier', 'date_format', 'admin/add_voucher.php', 154, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<style type="text/css">
	.img img{max-width:160px;max-height:160px;}
	.choiceBox a{position:relative;}
	.choiceBox a img {position: absolute;top: -6px;right: -6px;width: 20px;cursor:pointer;}
</style>
<div id="rightTop">
  <p>券管理</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/ticket/voucher-list/page:<?php echo $this->_tpl_vars['page']; ?>
">现金券列表</a></li>
    <?php if (! $this->_tpl_vars['row']['ticket_id']): ?><li><a class="btn3" href="/admin/ticket/user-shop/type:v/uname:<?php echo $this->_tpl_vars['uname']; ?>
">店铺选择列表</a></li><?php endif; ?>
    <li><span><?php if ($this->_tpl_vars['row']['ticket_id']): ?>编辑现金券<?php else: ?>新建现金券<?php endif; ?></span></li>
  </ul>
</div>

<div class="info">
<form method="POST" action="<?php echo $this->_tpl_vars['_CONF']['FORM_ACTION']; ?>
" id="form" enctype="multipart/form-data">
<input type="hidden" name="sid" id="sid" value="<?php echo $this->_tpl_vars['sid']; ?>
" />
<input type="hidden" name="user_name" id="user_name" value="<?php echo $this->_tpl_vars['uname']; ?>
" />
<input type="hidden" name="tid" id="tid" value="<?php echo $this->_tpl_vars['tid']; ?>
" />
<input type="hidden" name="page" id="page" value="<?php echo $this->_tpl_vars['page']; ?>
" />
<input type="hidden" name="gids" id="gids" value="<?php echo $this->_tpl_vars['gids']; ?>
" />
<input type="hidden" name="sids" id="sids" value="" />
<div id="tabs">
	<ul>
		<li><a href="#tabs-1">WEB</a></li>
		<li><a href="#tabs-2">WAP</a></li>
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
                <th class="paddingT15">活动名称:</th>
                <td class="paddingT15 wordSpacing5">
                <select name="activity_id" id="activity_id" style="width:300px;">
                        <option value="">请选择活动</option>
                        <?php $_from = $this->_tpl_vars['activity']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
                        <option value="<?php echo $this->_tpl_vars['item']['activity_id']; ?>
" <?php if ($this->_tpl_vars['row']['activity_id'] == $this->_tpl_vars['item']['activity_id']): ?> selected="selected" <?php endif; ?>><?php echo $this->_tpl_vars['item']['activity_name']; ?>
</option>
                        <?php endforeach; endif; unset($_from); ?>
                    </select>  
                  <a href="javascript:add_a();" >创建活动</a>
                  <input type="button" class="formbtn2" value="刷新活动" id="refresh_act" />	
                  <label class="field_notice"></label>
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
                <a><span><?php echo $this->_tpl_vars['item']['shop_name']; ?>
</span><img class="shopDel" src="/images/delete.png" data-sid = "<?php echo $this->_tpl_vars['item']['shop_id']; ?>
"/></a>
                <?php endforeach; endif; unset($_from); ?>
                </span>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">是否特卖:</th>
                <td class="paddingT15 wordSpacing5">
                    <input type="radio" name="is_sale" id="sale_no" value="0" <?php if ($this->_tpl_vars['row']['is_sale'] == 0): ?>checked="checked"<?php endif; ?> /> 否
                    <input type="radio" name="is_sale" id="sale_yes" value="1" <?php if ($this->_tpl_vars['row']['is_sale'] == 1): ?>checked="checked"<?php endif; ?> /> 是
                    <input class="infoTableFile2" type="text" name="sale_code" id="sale_code" value="<?php echo $this->_tpl_vars['row']['sale_code']; ?>
" placeholder="场次编号" <?php if ($this->_tpl_vars['row']['is_sale'] == 0): ?>style="display:none"<?php endif; ?> />
                </td>    	
            </tr>
            <tr>
                <th class="paddingT15">券分类:</th>
                <td class="paddingT15 wordSpacing5">
                    <input type="radio" name="ticket_class" value="1" <?php if (((is_array($_tmp=$this->_tpl_vars['row']['ticket_class'])) ? $this->_run_mod_handler('_isset', true, $_tmp) : _isset($_tmp)) && $this->_tpl_vars['row']['ticket_class'] == 1): ?>checked="checked"<?php endif; ?> /> 商场
                    <input type="radio" name="ticket_class" value="2" <?php if (((is_array($_tmp=$this->_tpl_vars['row']['ticket_class'])) ? $this->_run_mod_handler('_isset', true, $_tmp) : _isset($_tmp)) && $this->_tpl_vars['row']['ticket_class'] == 2): ?>checked="checked"<?php endif; ?> /> 品牌
                    <input type="radio" name="ticket_class" value="3" <?php if (((is_array($_tmp=$this->_tpl_vars['row']['ticket_class'])) ? $this->_run_mod_handler('_isset', true, $_tmp) : _isset($_tmp)) && $this->_tpl_vars['row']['ticket_class'] == 3): ?>checked="checked"<?php endif; ?> /> 特卖
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
                <th class="paddingT15">现金券标题:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableFile" type="text" name="ticket_title" id="ticket_title" value="<?php echo $this->_tpl_vars['row']['ticket_title']; ?>
" />
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">现金券面值:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2" type="text" name="par_value" id="par_value" value="<?php echo $this->_tpl_vars['row']['par_value']; ?>
" />
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">现金券售价:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2" type="text" name="selling_price" id="selling_price" value="<?php echo $this->_tpl_vars['row']['selling_price']; ?>
" />
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">APP券售价:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2" type="text" name="app_price" id="app_price" value="<?php if ($this->_tpl_vars['row']['app_price'] > 0): ?><?php echo $this->_tpl_vars['row']['app_price']; ?>
<?php endif; ?>" />
                   <input type="checkbox" name="is_free" value="1" <?php if ($this->_tpl_vars['row']['is_free'] == 1): ?>checked="checked"<?php endif; ?> />APP端免单
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">现金券数量:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2" type="text" name="total" id="total" value="<?php echo $this->_tpl_vars['row']['total']; ?>
" />
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">限购:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2" type="text" name="limit_count" id="limit_count" value="<?php echo $this->_tpl_vars['row']['limit_count']; ?>
" /> 件 / 
                  <select name="limit_unit" id="limit_unit">
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
                  <input class="infoTableFile2" style="width:140px;" type="text" name="start_time" id="start_time" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm'})" value="<?php if ($this->_tpl_vars['row']['start_time']): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['row']['start_time'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M')); ?>
<?php endif; ?>" /> - 
                  <input class="infoTableFile2" style="width:140px;" type="text" name="end_time" id="end_time" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm',minDate:'#F{$dp.$D(\'start_time\',{H:1})}'})" value="<?php if ($this->_tpl_vars['row']['end_time']): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['row']['end_time'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M')); ?>
<?php endif; ?>"/>
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">使用有效期:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableFile2" style="width:140px;" type="text" name="valid_stime" id="valid_stime" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm'})" value="<?php if ($this->_tpl_vars['row']['valid_stime']): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['row']['valid_stime'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M')); ?>
<?php endif; ?>" /> - 
                  <input class="infoTableFile2" style="width:140px;" type="text" name="valid_etime" id="valid_etime" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm',minDate:'#F{$dp.$D(\'valid_stime\',{H:1})}'})" value="<?php if ($this->_tpl_vars['row']['valid_etime']): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['row']['valid_etime'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M')); ?>
<?php endif; ?>"/>
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">现金券简介:</th>
                <td class="paddingT15 wordSpacing5">
                  <textarea id="ticket_summary" name="ticket_summary"><?php echo $this->_tpl_vars['row']['ticket_summary']; ?>
</textarea>
                  <label class="field_notice">70字内(汉字算一个字符)</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">封面图片:</th>
                <td class="paddingT15 wordSpacing5">
                  <input type="file" class="file" name="file_img" id="file_img" />
                  <label class="field_notice">图片尺寸 640 * 300</label>
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
                    <div class="imgBox" id="postImageList" <?php if (! $this->_tpl_vars['row']['good_id']): ?>style="display:none"<?php endif; ?>>
                        <ul class="clearfix">
                        <?php $_from = $this->_tpl_vars['imgList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
                            <li>
                                <p class="img"><img src="<?php echo $this->_tpl_vars['_CONF']['SITE_URL']; ?>
/data/good/small/<?php echo $this->_tpl_vars['item']['img_url']; ?>
"></p>
                                <p><a data-aid="<?php echo $this->_tpl_vars['item']['good_img_id']; ?>
" class="del" href="javascript:;">删除</a>
                            </li>
                        <?php endforeach; endif; unset($_from); ?>                
                        </ul>
                    </div>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">使用说明:</th>
                <td class="paddingT15 wordSpacing5">
                  <textarea id="content" name="content" style="width:800px; height:400px;"><?php echo $this->_tpl_vars['row']['content']; ?>
</textarea>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">WAP详情:</th>
                <td class="paddingT15 wordSpacing5">
                  <textarea id="wap_content" name="wap_content" style="width:600px; height:300px;"><?php echo $this->_tpl_vars['row']['wap_content']; ?>
</textarea>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">上下架:</th>
                <td class="paddingT15 wordSpacing5" >
                    <input type="radio" name="is_auth" value="1" <?php if (((is_array($_tmp=$this->_tpl_vars['row']['is_auth'])) ? $this->_run_mod_handler('_isset', true, $_tmp) : _isset($_tmp)) && $this->_tpl_vars['row']['is_auth'] == 1): ?>checked="checked"<?php else: ?>checked="checked"<?php endif; ?> />上架
                    <input type="radio" name="is_auth" value="0" <?php if (((is_array($_tmp=$this->_tpl_vars['row']['is_auth'])) ? $this->_run_mod_handler('_isset', true, $_tmp) : _isset($_tmp)) && $this->_tpl_vars['row']['is_auth'] == 0): ?>checked="checked"<?php endif; ?> />下架
                    <label class="field_notice"></label>
                </td>
            </tr> 
            <tr>
                <th class="paddingT15">是否显示:</th>
                <td class="paddingT15 wordSpacing5" >
                    <input type="radio" name="is_show" value="0" <?php if (((is_array($_tmp=$this->_tpl_vars['row']['is_show'])) ? $this->_run_mod_handler('_isset', true, $_tmp) : _isset($_tmp)) && $this->_tpl_vars['row']['is_show'] == 0): ?>checked="checked"<?php else: ?>checked="checked"<?php endif; ?> />否
                    <input type="radio" name="is_show" value="1" <?php if (((is_array($_tmp=$this->_tpl_vars['row']['is_show'])) ? $this->_run_mod_handler('_isset', true, $_tmp) : _isset($_tmp)) && $this->_tpl_vars['row']['is_show'] == 1): ?>checked="checked"<?php endif; ?> />是
                    <label class="field_notice"></label>
                </td>
            </tr>       
            <tr>
                <th class="paddingT15">适用商品:</th>
                <td class="paddingT15 wordSpacing5"><a id="setShop" class="setShop" href="javascript:void(0)">点击设置适用商品</a></td>
            </tr>
                <tr>
                <th class="paddingT15"></th>
                <td class="paddingT15 wordSpacing5">
                 <p class="choiceBox" id="choiceBox"></p>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">允许分享:</th>
                <td class="paddingT15 wordSpacing5" >
                    <input type="radio" name="can_share" value="0" <?php if (((is_array($_tmp=$this->_tpl_vars['row']['can_share'])) ? $this->_run_mod_handler('_isset', true, $_tmp) : _isset($_tmp)) && $this->_tpl_vars['row']['can_share'] == 0): ?>checked="checked"<?php else: ?>checked="checked"<?php endif; ?> />否
                    <input type="radio" name="can_share" value="1" <?php if (((is_array($_tmp=$this->_tpl_vars['row']['can_share'])) ? $this->_run_mod_handler('_isset', true, $_tmp) : _isset($_tmp)) && $this->_tpl_vars['row']['can_share'] == 1): ?>checked="checked"<?php endif; ?> />是
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
/buy/ticketwap/<?php echo $this->_tpl_vars['item']['img_url']; ?>
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
    	<tbody>
        <tr>
            <th class="paddingT15"> </th>
            <td class="ptb20">
                <?php if ($this->_tpl_vars['tid']): ?>
                <input type="button" value="编辑" name="Submit" class="formbtn1" onClick="checkSubmit()">
                <input type="reset" value="重置" name="reset" class="formbtn2">
                <?php else: ?>
                <input type="button" value="提交" name="Submit" class="formbtn1" onClick="checkSubmit()">
                <?php endif; ?>
            </td>
        </tr>   
        </tbody> 
    </table>
</div>
</form>
</div>    


<div class="Popup" style="display:none"  id="selectPopup">
    <div class="selectPopup">
            <h2>设置券适用商品<a class="close">&times;</a></h2>
            <div class="select-con">
                <p class="selectAll"><input type="checkbox" class="check" id="selectAll" checked="false"><label>选择所有</label></p>
                <div class="selectTable">
                </div>
                <p class="center" id="selectBox"><input type="submit" value="提交设置" class="selectBtn"/></p>
            </div>
    </div>
          <div class="shade"></div>
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
<script type="text/javascript" src="/js/jquery.validate.min.js" charset="utf-8"></script>
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
<script type="text/javascript" src="/js/setshop.js?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
" charset="utf-8" ></script>
<script type="text/javascript" src="/js/JqueryDialog/jquery-ui-1.9.2.custom.min.js" charset="utf-8" ></script>
<script type="text/javascript" src="/js/admin/inline_edit.js" charset="utf-8"></script>
<script type="text/javascript">
var postImage, editor, uploadSwf, setGood;
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
		
	$("#choiceBoxShop").on('click','.shopDel',function(){
		var _this = $(this);
		$.dialog({
			title:'警告',
			content: '是否确认取消该店铺与券的关联？',
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
			
	editor = KindEditor.create('#content',{items : ['source', '|', 'undo', 'redo', '|', 'justifyleft', 'justifycenter', 'justifyright','justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent','|', 'fullscreen', '/','fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold','italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'emoticons', 'link', 'unlink', 'baidumap']});
	setGood = new SelectShop();	
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
	
    $('#form').validate({
        errorPlacement: function(error, element){
			if(element.is(':radio') || element.is(':checkbox')) {
				error.appendTo(element.parent());
			}else if(element.attr('name') == 'region_id' || element.attr('name') == 'circle_id' || element.attr('name') == 'shop_id') {
				$(element).parent().find('.field_notice').html(error);
			} else if (element.attr('name') == 'ticket_type' ) {
				$(element).parent().find('.field_notice').html(error);
			} else {
				$(element).next('.field_notice').hide();
				$(element).after(error); 
			}
        },
		success : function(label){
			label.addClass('right').text('OK!');
		},
		onkeyup : false, 
        rules : { 
            activity_id : {
            	required : true
            },
			sale_code : {
				required : function(){
					return $("#sale_yes").attr("checked") == "checked";
				}
			},
			ticket_title : {
				required : true
			},
			ticket_class : {
				required : true
			},
			par_value : {
				required : true,
				number : true
			},
			selling_price : {
				required : true,
				number : true
			},
			total : {
				required : true,
				digits : true			
			},
			limit_count : {
				required : true,
				digits : true			
			},
			start_time : {
				required : true
			},
			end_time : {
				required : true
			},
			valid_stime : {
				required : true
			},
			valid_etime : {
				required : true
			},
			ticket_summary : {
				required : true,
				maxlength : 70
			}
        },
        messages : {
            activity_id : {
        		required : '请选择活动名称'
        	},
			sale_code : {
				required : '请输入特卖场次'
			},
			ticket_title : {
				required : '请输入现金券标题'
			},
			ticket_class : {
				required : '请选择券分类'
			},
			selling_price : {
				required : '请输入现金券售价',
				number : '必须输入合法的数字(负数，小数)'
			},
			par_value : {
				required : '请输入现金券面值',
				number : '必须输入合法的数字(负数，小数)'
			},
			total : {
				required : '请输入现金券数量',
				digits : '必须输入整数'			
			},
			limit_count : {
				required : '请输入限购数量',
				digits : '必须输入整数'			
			},
			start_time : {
				required : '券销售开始时间'
			},
			end_time : {
				required : '券销售结束时间'
			},
			valid_stime : {
				required : '券使用开始时间'
			},
			valid_etime : {
				required : '券使用结束时间'
			},
			ticket_summary : {
				required : '请输入优惠券简介',
				maxlength : '70字内(汉字算一个字符)'
			}
        }
    });
	
	$('#refresh_act').click(function(){
		var _this = $('#activity_id');
		var user_name = $('#user_name').val();
		_this.empty();
		_this.append($("<option>").text('请选择活动').val(''));
		$.post('/admin/ticket/get-act', {user_name:user_name}, function(obj){
			var data = eval('(' + obj + ')');
			$.each(data, function(i, s){
				_this.append($("<option>").text(s.activity_name).val(s.activity_id));
			});
			alert('已刷新');
		});	
	});
	
	$("input[type=radio][name=is_sale]").change(function(){
		if(this.value == 1) {
			$("#sale_code").show();
		} else {
			$("#sale_code").hide();
		}
	});
});

$(function() {
	$( "#tabs" ).tabs();
	//$( "#tabs2" ).tabs();
});
  
function add_a() {
	$.dialog({
		title: '添加活动',
		content: '活动名称：<br/><br/>'
				 + '<input type="text" name="aname" id="aname" /><br/>',
		ok: function () {
			var aname = $('#aname').val();
			var user_name = $('#user_name').val();
			if(aname == '') {
				alert('活动名称不能为空');
				return false;
			} else {
				var url = '/' + _M + '/' + _C + '/add-activity';
				$.post(url, {aname:aname, user_name:user_name}, function(data){
					var obj = eval('(' + data + ')');
					if (obj.res == 1) {
						$.dialog({
							title : '结果',
							content : obj.msg, 
							okValue: '确定',
							ok: true
						});							
					}
				});
			}
		},
		cancel: true
	});
}

function checkSubmit()
{
	if($("#form").valid())
	{
		if (editor.html() == '') {
			$.dialog.alert('请输入现金券使用说明');
			return false;
		}		
		$('#gids').val(setGood.getCache().join(','));
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
	
	//$('#related_id').append($("<option>").text('请选择商圈').val(''));
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
	
	$('#choiceBoxShop').html(shopTextStr);
	$('#sids').val(shopIdStr);

	$('#selectPopupShop').hide();
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
                    url:'/admin/ticket/wap-img-del',
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