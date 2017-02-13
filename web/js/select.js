/*
	商品分类
*/
function changeShop(data){
		if(!(this instanceof changeShop))
			return new changeShop(data);
		this.btn = $(data.btn);
		this.txt = $(data.txt);
		this.val = $(data.val);
		this.timer = null;
		this.init();
}
changeShop.prototype = {
	init:function(){
		var that = this;
		this.btn.click(function(event){
			event.stopPropagation();
			that.showDis();
		})
		
		this.val.click(function(event){
			event.stopPropagation();
			that.showDis();
			
		});
		
		this.txt.find('a').click(function(){
		
	
		})
		
		$('body').click(function(){
			$('.allList').hide();
		});
		this.txt.find('a').mouseover(function(){
			that.showChildDis($(this));
		})
		
		this.txt.find('a').mouseout(function(){
			that.hideChildDis($(this));
		})
		
	},
	showDis:function(){
		this.txt.show();
	},
	hideDis:function(){
		this.txt.hide();
		$('.shopBox').hide();
		$('.shopBox').parent().css('z-index',1);
	},
	showChildDis:function(obj){
		var that = this;
		$('.shopBox').hide();
		$('.shopBox').parent().css('z-index',1);
		if(obj.next()){
			obj.next().show();
			obj.next().parent().css('z-index',90);
		}
		obj.next().mouseover(function(){
			
			obj.next().parent().css('z-index',90);
			obj.next().show();
			clearTimeout(that.timer);
			
		});
		
		obj.next().mouseout(function(){
			
				obj.next().hide();
			
		});
		
		this.txt.find('a').click(function(event){
			 event.stopPropagation();
			var _val = $(this).text();
			that.val.text(_val);
			that.val.next().val(_val);
			$('.allList').hide();
			$('.shopBox').hide();
		});
		
		
		
	},
	hideChildDis:function(obj){
		if(obj.next()){
			this.timer = setTimeout(function(){
				obj.next().hide();
				obj.next().parent().css('z-index',1);
			},100)
			
		}
	}
	
}
