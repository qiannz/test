<?php /* Smarty version 2.6.27, created on 2016-02-22 16:32:31
         compiled from ticket/right.php */ ?>
<div class="nyRight">
	<?php if ($this->_tpl_vars['shopInfo']): ?>
    <!--百度地图-->
    <div class="shopMessage">
        <h3><a href="/home/shop/show/sid/<?php echo $this->_tpl_vars['shopInfo']['shop_id']; ?>
" target="_blank"><?php echo $this->_tpl_vars['shopInfo']['shop_name']; ?>
</a></h3>
        <?php if ($this->_tpl_vars['shopInfo']['brand_name']): ?><p>品牌：
        <a href="<?php if ($this->_tpl_vars['shopInfo']['brand_detail'] && $this->_tpl_vars['shopInfo']['brand_detail']['is_enable'] == 1): ?>/home/brand/show/bid/<?php echo $this->_tpl_vars['shopInfo']['brand_id']; ?>
<?php else: ?>/home/good/list/sid/0_<?php echo $this->_tpl_vars['shopInfo']['brand_id']; ?>
_0_0_0_0<?php endif; ?>" target="_blank"><?php echo $this->_tpl_vars['shopInfo']['brand_name']; ?>
</a></p>
        <?php endif; ?>
        <?php if ($this->_tpl_vars['shopInfo']['circle_name']): ?><p>商圈：<?php echo $this->_tpl_vars['shopInfo']['circle_name']; ?>
</p><?php endif; ?>
        <p>详细地址：<?php echo $this->_tpl_vars['shopInfo']['shop_address']; ?>
</p>
        <div id="allmap" class="baiduMap"></div>
    </div>
    <?php endif; ?>
    <!--其他商品-->
    <?php if ($this->_tpl_vars['goodShowHotList']): ?>
    <div class="other">    	
        <div class="otherTit">
          <h3>热门商品</h3>
        </div>
        <div class="otherList">
                <ul>
                	<?php $_from = $this->_tpl_vars['goodShowHotList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
                    <li>
                        <a href="<?php echo $this->_tpl_vars['item']['www_url']; ?>
" target="_blank"><img src="<?php echo $this->_tpl_vars['item']['img_url']; ?>
" width="160" height="210"></a>
                        <a href="<?php echo $this->_tpl_vars['item']['www_url']; ?>
" target="_blank"><?php echo $this->_tpl_vars['item']['title']; ?>
</a>
                    </li>
                    <?php endforeach; endif; unset($_from); ?>
                </ul>
        </div>
    </div>
    <?php endif; ?>
</div>