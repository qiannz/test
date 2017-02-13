<?php
// 全站 Meta, SEO
$GLOBALS['metaConfig'] = array(
	//首页
	'Home|Index|index' => array('名品街，城市购物导航', '名品街,O2O,上海逛街,上海实体店', '名品街提供上海地区最全的各品牌实体店铺商品信息，让您足不出户即可逛遍各品牌商场实体店，全面了解新品款式、价格等信息，更可获得多重优惠！名品街提供上海女装、上海女鞋、上海包包配饰、上海男装、上海婴童等各类别各大品牌店铺，是上海地区最权威的百货消费领域O2O平台。'),
	
	//商品列表页
	'Home|Good|list' => array(
			'{region}{circle}{brand}{store}产品大全-名品街-名品导购网', 
			'{region}{circle}{brand}{store},{region}{circle}{brand}{store}优惠促销,{region}{circle}{brand}{store}产品大全', 
			'{region}{circle}{brand}{store}产品大全为您提供最新{region}{circle}{brand}{store}产品，包括产品名称、产品价格、产品图片、优惠券等信息。'
		),	
		
	//优惠券列表页
	'Home|Ticket|list' => array(
			'优惠券-名品街-名品导购网',
			'优惠券,上海优惠,上海优惠券',
			'名品街优惠券频道提供上海百货消费最全最给力的优惠券，包括商场优惠券、品牌优惠券、专柜优惠券、小店优惠券等，逛街首先优惠券就在名品导购网名品街优惠券频道。'
		),
		
	//店铺详情页
	'Home|Shop|show' => array(
			'{shop}-名品街-名品导购网',
			'{shop}',
			'{shop}，位于{region}{circle}，主营{brand}品牌{store}类别产品，最新最全的店铺商品展示和优惠信息就在名品导购网名品街{shop}。'
		),
		
	//商品详情页	
	'Home|Good|show' => array(
			'{good}-{shop}-名品街-名品导购网',
			'{good},{shop},{brand}',
			'{good},{shop}最新最全的商品展示和优惠信息就在名品导购网名品街{shop}。'
		),

	//商品WAP详情页
	'Home|Good|wap' => array(
			'{good}-{shop}-名品街-名品导购网',
			'{good},{shop},{brand}',
			'{good},{shop}最新最全的商品展示和优惠信息就在名品导购网名品街{shop}。'
	),
		
	//优惠券详情页
	'Home|Ticket|show' => array(
			'{ticket}-{shop}-名品街-名品导购网',
			'{ticket},{shop},{brand}',
			'{ticket}，{shop}最新最全的商品展示和优惠信息就在名品导购网名品街{shop}。'
		),
		
	//优惠券详情页
	'Home|Ticket|wap' => array(
			'{ticket}-{shop}-名品街-名品导购网',
			'{ticket},{shop},{brand}',
			'{ticket}，{shop}最新最全的商品展示和优惠信息就在名品导购网名品街{shop}。'
		),
		
	//优惠券详情页
	'Home|Ticket|wapShow' => array(
			'{ticket}-{shop}-名品街-名品导购网',
			'{ticket},{shop},{brand}',
			'{ticket}，{shop}最新最全的商品展示和优惠信息就在名品导购网名品街{shop}。'
		),

	//优惠券适用商品页
	'Home|Good|more' => array(
			'{ticket}适用商品-{shop}-名品街-名品导购网',
			'{ticket}适用商品',
			'{ticket}适用商品页，提供所有在{ticket}适用范围内的商品列表。'
		),
	//品牌首页
	'Home|Brand|list' => array(
			'品牌-名品街-名品导购网'
	),
	//品牌详情页show
	'Home|Brand|show' => array(
			'品牌名{brand}-名品街-名品导购网'
	),
	//品牌大全页
	'Home|Brand|all' => array(
			'上海品牌大全-名品街-名品导购网'
	),
	//商场首页
	'Home|Market|list' => array(
			'上海商场-名品街-名品导购网'
	),
	//商场详情页VIEW
	'Home|Market|show' => array(
			'{market}-名品街-名品导购网'
	),
	//搜索结果页
	'Home|Search|list' => array('{keyword}搜索结果-名品街-名品导购网'),
	
	//上传商品页
	'Home|Good|add' => array('上传商品-名品街-名品导购网'),
	
	//我的商圈页
	'Home|Circle|show' => array('我的商圈-我的名品街-名品导购网'),
	
	//商户后台-店铺商品管理
	'Home|Suser|myGood' => array('店铺商品管理-名品街商户后台-名品街-名品导购网'),
	
	//商户后台-商品编辑
	'Home|Suser|goodEdit' => array('商品编辑-名品街商户后台-名品街-名品导购网'),
	
	//商户后台-上传商品
	'Home|Suser|add' => array('商品上传-名品街商户后台-名品街-名品导购网'),
	
	//商户后台-编辑店铺
	'Home|Suser|shopEdit' => array('编辑店铺-名品街商户后台-名品街-名品导购网'),
	
	//商户后台-券管理
	'Home|Suser|couponList' => array('券管理-名品街商户后台-名品街-名品导购网'),
	
	//商户后台-券编辑
	'Home|Suser|couponEdit' => array('券编辑-名品街商户后台-名品街-名品导购网'),
	
	//商户后台-发券
	'Home|Suser|addCoupon' => array('发券-名品街商户后台-名品街-名品导购网'),
	
	//商户后台-券验证
	'Home|Suser|valid' => array('券验证-名品街商户后台-名品街-名品导购网'),
	
	
);