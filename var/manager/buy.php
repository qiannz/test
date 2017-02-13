<?php
 return array (
  'dashboard' => 
  array (
    'text' => '常用操作',
    'subtext' => '常用操作',
    'default' => 'welcome',
    'children' => 
    array (
      'welcome' => 
      array (
        'text' => '欢迎页面',
        'url' => '/admin/index/welcome',
      ),
      'gift' => 
      array (
        'text' => '新手包',
        'url' => '/admin/gift/list',
      ),
    ),
  ),
  'shop' => 
  array (
    'text' => '店铺管理',
    'subtext' => '店铺管理',
    'default' => 'shop_list',
    'children' => 
    array (
      'shop_list' => 
      array (
        'text' => '店铺列表',
        'url' => '/admin/shop/list',
      ),
    ),
  ),
  'user' => 
  array (
    'text' => '用户管理',
    'subtext' => '用户管理',
    'default' => 'user_list',
    'children' => 
    array (
      'user_list' => 
      array (
        'text' => '用户列表',
        'url' => '/admin/user/list',
      ),
      'user_good' => 
      array (
        'text' => '用户商品列表',
        'url' => '/admin/usergood/list',
      ),
      'user_good_collect' => 
      array (
        'text' => '用户商品收藏',
        'url' => '/admin/usergoodcollect/list',
      ),
      'user_good_like' => 
      array (
        'text' => '用户商品喜欢',
        'url' => '/admin/usergoodlike/list',
      ),
      'message_list' => 
      array (
        'text' => '留言管理',
        'url' => '/admin/message/list',
      ),
      'notice_list' => 
      array (
        'text' => '公告管理',
        'url' => '/admin/notice/list',
      ),
      'discount_list' => 
      array (
        'text' => '折扣管理',
        'url' => '/admin/discount/list',
      ),
      'special_list' => 
      array (
        'text' => '专题管理',
        'url' => '/admin/special/list',
      ),
      'privateletter_list' => 
      array (
        'text' => '私信管理',
        'url' => '/admin/privateletter/list',
      ),
    ),
  ),
  'ticket' => 
  array (
    'text' => '商品管理',
    'subtext' => '商品管理',
    'default' => 'voucher_list',
    'children' => 
    array (
      'voucher_list' => 
      array (
        'text' => '现金券',
        'url' => '/admin/ticket/voucher-list',
      ),
      'selfpay_list' => 
      array (
        'text' => '自定义买单',
        'url' => '/admin/ticket/selfpay-list',
      ),
      'commodity_list' => 
      array (
        'text' => '商城商品',
        'url' => '/admin/commodity/list',
      ),
      'crowdfunding_list' => 
      array (
        'text' => '一元众筹',
        'url' => '/admin/crowdfunding/list',
      ),
      'spike_list' => 
      array (
        'text' => '快来抢',
        'url' => '/admin/spike/list',
      ),
    ),
  ),
  'warehousing' => 
  array (
    'text' => '仓储管理',
    'subtext' => '仓储管理',
    'default' => 'batch_list',
    'children' => 
    array (
      'batch_list' => 
      array (
        'text' => '入库记录',
        'url' => '/admin/batch/list',
      ),
      'batchgood_list' => 
      array (
        'text' => '入库商品',
        'url' => '/admin/batchgood/list',
      ),
    ),
  ),
  'recommend' => 
  array (
    'text' => '推荐管理',
    'subtext' => '推荐管理',
    'default' => 'recommend_list',
    'children' => 
    array (
      'recommend_list' => 
      array (
        'text' => '推荐列表',
        'url' => '/admin/recommend/list',
      ),
      'position_list' => 
      array (
        'text' => '推荐位设置',
        'url' => '/admin/position/list',
      ),
      'nav_list' => 
      array (
        'text' => '导航列表',
        'url' => '/admin/nav/list',
      ),
      'app_link' => 
      array (
        'text' => '链接设置',
        'url' => '/admin/link/list',
      ),
    ),
  ),
  'site' => 
  array (
    'text' => '数据配置',
    'subtext' => '数据配置',
    'default' => 'store_list',
    'children' => 
    array (
      'store_list' => 
      array (
        'text' => '商品分类',
        'url' => '/admin/store/list',
      ),
      'brand_list' => 
      array (
        'text' => '品牌管理',
        'url' => '/admin/brand/list',
      ),
      'circle_list' => 
      array (
        'text' => '商圈管理',
        'url' => '/admin/circle/list',
      ),
      'market_list' => 
      array (
        'text' => '商场管理',
        'url' => '/admin/market/list',
      ),
      'app_list' => 
      array (
        'text' => 'APP设置',
        'url' => '/admin/app/list',
      ),
      'img_list' => 
      array (
        'text' => '商品图片',
        'url' => '/admin/img/list',
      ),
      'pack_list' => 
      array (
        'text' => '套餐设置',
        'url' => '/admin/pack/list',
      ),
      'sort_list' => 
      array (
        'text' => '全站分类',
        'url' => '/admin/sort/list',
      ),
      'keyword_list' => 
      array (
        'text' => '搜索管理',
        'url' => '/admin/keyword/list',
      ),
      'config_list' => 
      array (
        'text' => '全局配置',
        'url' => '/admin/config/list',
      ),
      'region_list' => 
      array (
        'text' => '行政区管理',
        'url' => '/admin/region/list',
      ),
      'appversion' => 
      array (
        'text' => 'APP版本设置',
        'url' => '/admin/appversion/list',
      ),
      'slogan' => 
      array (
        'text' => '宣传标语',
        'url' => '/admin/slogan/list',
      ),
    ),
  ),
  'task' => 
  array (
    'text' => '任务管理',
    'subtext' => '任务管理',
    'default' => 'details_list',
    'children' => 
    array (
      'details_list' => 
      array (
        'text' => '用户奖励明细',
        'url' => '/admin/details/list',
      ),
      'clerk_rebate' => 
      array (
        'text' => '店员返利',
        'url' => '/admin/rebate/list',
      ),
      'withdrawal_list' => 
      array (
        'text' => '提现记录',
        'url' => '/admin/withdrawal/list',
      ),
      'loans_list' => 
      array (
        'text' => '放款记录',
        'url' => '/admin/loans/list',
      ),
      'order_list' => 
      array (
        'text' => '订单管理',
        'url' => '/admin/order/list',
      ),
      'deals_list' => 
      array (
        'text' => '特卖管理',
        'url' => '/admin/deals/list',
      ),
    ),
  ),
  'wbmanage' => 
  array (
    'text' => '微商管理',
    'subtext' => '微商管理',
    'default' => 'wbmember_list',
    'children' => 
    array (
      'wbmember_list' => 
      array (
        'text' => '会员管理',
        'url' => '/admin/wbmember/list',
      ),
      'wbmemberadd_list' => 
      array (
        'text' => '会员添加',
        'url' => '/admin/wbmemberadd/list',
      ),
      'wbdiscount_list' => 
      array (
        'text' => '折扣管理',
        'url' => '/admin/wbdiscount/list',
      ),
      'wborder_list' => 
      array (
        'text' => '订单登记',
        'url' => '/admin/wborder/list',
      ),
    ),
  ),
  'history' => 
  array (
    'text' => '历史模块',
    'subtext' => '历史模块',
    'default' => 'day_list',
    'children' => 
    array (
      'day_list' => 
      array (
        'text' => '天天向上',
        'url' => '/admin/day/list',
      ),
      'tenday_list' => 
      array (
        'text' => '十全大补',
        'url' => '/admin/tenday/list',
      ),
      'dubai_tour_list' => 
      array (
        'text' => '畅游迪拜',
        'url' => '/admin/dubaitour/list',
      ),
      'client_list' => 
      array (
        'text' => '街友最划算',
        'url' => '/admin/client/list',
      ),
      'clerk_list' => 
      array (
        'text' => '店员最划算',
        'url' => '/admin/clerk/list',
      ),
      'scratchset_list' => 
      array (
        'text' => '街友刮奖设置',
        'url' => '/admin/scratchset/list',
      ),
      'clerkset_list' => 
      array (
        'text' => '店员刮奖设置',
        'url' => '/admin/clerkset/list',
      ),
      'good_list' => 
      array (
        'text' => '商品列表',
        'url' => '/admin/good/list',
      ),
      'good_shop_list' => 
      array (
        'text' => '店铺商品列表',
        'url' => '/admin/goodshop/list',
      ),
      'merchant_list' => 
      array (
        'text' => '商户入驻',
        'url' => '/admin/merchant/list',
      ),
      'ticket_list' => 
      array (
        'text' => '优惠券',
        'url' => '/admin/ticket/coupon-list',
      ),
      'appwheel_list' => 
      array (
        'text' => 'APP大转盘',
        'url' => '/admin/appwheel/list',
      ),
      'appse_list' => 
      array (
        'text' => 'APP大转盘设置',
        'url' => '/admin/appset/list',
      ),
      'buygood' => 
      array (
        'text' => '团购商品',
        'url' => '/admin/buygood/list',
      ),
    ),
  ),
  'log' => 
  array (
    'text' => '日志管理',
    'subtext' => '日志管理',
    'default' => 'log_list',
    'children' => 
    array (
      'log_list' => 
      array (
        'text' => '日志列表',
        'url' => '/admin/log/list',
      ),
      'historylog_list' => 
      array (
        'text' => '历史日志列表',
        'url' => '/admin/historylog/list',
      ),
    ),
  ),
  'settings' => 
  array (
    'text' => '系统设置',
    'subtext' => '系统设置',
    'default' => 'module_list',
    'children' => 
    array (
      'module_list' => 
      array (
        'text' => '模块管理',
        'url' => '/admin/module/list',
      ),
      'manager_list' => 
      array (
        'text' => '管理员管理',
        'url' => '/admin/manager/list',
      ),
      'group_list' => 
      array (
        'text' => '组管理',
        'url' => '/admin/group/list',
      ),
      'purview_list' => 
      array (
        'text' => '权限设置',
        'url' => '/admin/purview/list',
      ),
    ),
  ),
);