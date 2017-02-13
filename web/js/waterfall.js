/*
 * Created by jimmychen.
 * Date: 13-08-05
 */
 /*获取ID*/
function Waterfall(json)
{
		if(!(this instanceof Waterfall))
			return new Waterfall(json);
		this.oWaterfall = getId(json.id);
		this.aLiWidth = json.listwidth;
		this.Len = json.colnum;
		this.iMargin = json.m;
		this.widthteam = [];
		this.aLi = [];
		this.bAjax = this.bStop = true;
		this.iNow = this.sumTop = this.i = this.j = this.WaterfallHeight =0;
		this.oP = null;
		this.pageNum = 1;
		this.init();
		this.Scroll();
		this.cStop = true;
}	

Waterfall.prototype = {
	init:function()
	{
			for(var i=0;i<this.Len;i++){this.widthteam.push((this.aLiWidth+this.iMargin)*i)}
	},
	del:function(){
		if(this.oP)
			{
				this.oWaterfall.removeChild(this.oP);
				delete this.oP;
			}
	},
	ajax:function()
	{
		var _this = this;
		$.ajax({
		url:"/home/good/ajax",
		dataType:"json",
		async:true,
		data:{seid:seid,bdid:bdid,rnid:rnid,ceid:ceid,mkid:mkid,spid:spid,"page":_this.pageNum, "order":order},
		cache: false,
		success:function(data){
			$('#total_num').html(data['totalNum']);
			if(data['data'].length == 0 && _this.pageNum == 1) {
				_this.oP.innerHTML = '抱歉，当前刷选商品为空！';
				_this.cStop = false;
			}else{
				_this.del();
			}
			if(data['totalPage'] < _this.pageNum) {
				return;
			}						
			for(var i=0;i<data['data'].length;i++)
			{
				_this.sumTop = 0;
				Li = document.createElement('li');
				var _html = '';
				_html+= '<div class="pic">';
				_html+= '<a href="/home/good/show/gid/'+ data['data'][i].good_id +'/order/'+ data['order'] +'" target="_blank"><img src="' + data['data'][i].img_url + '" width="'+data['data'][i].width+'" height="'+data['data'][i].height+'"></a>';
				_html+= '</div>';
				_html+= '<div class="txt">';
				_html+= ' <p class="l1"><span class="name"><a href="/home/shop/show/sid/'+data['data'][i].shop_id+'/order/'+ data['order'] +'" target="_blank">'+data['data'][i].shop_name+'</a></span><span class="price">¥<font>'+data['data'][i].dis_price+'</font></span></p>';
				_html+= ' <p class="l2"><a href="/home/good/show/gid/'+data['data'][i].good_id+'/order/'+ data['order'] +'" target="_blank">'+data['data'][i].good_name+'</a></p>';
				_html+= '</div>';
				_html+= '<div class="vote"><a class="vote-l" href="javascript:Concern('+ data['data'][i].good_id  +', \'sort_concern\')" id="sort_concern_'+data['data'][i].good_id+'"><s class="vote-l-icon"></s><q>'+data['data'][i].concerned_number+'</q></a>';
				_html+= '<a class="vote-r" href="javascript:Favorite('+ data['data'][i].good_id  +', \'sort_favorite\')" id="sort_favorite_'+data['data'][i].good_id+'"><s class="vote-r-icon"></s><q>'+data['data'][i].favorite_number+'</q></a>';
				_html+= '</div>';
				Li.innerHTML = _html;
				_this.aLi.push(Li);
				_this.oWaterfall.appendChild(_this.aLi[_this.i]);
				//计算top值
				_this.aLi[_this.i].style.top = (_this.i>_this.Len-1)?(_this.aLi[_this.i-_this.Len].offsetTop+_this.aLi[_this.i-_this.Len].offsetHeight+_this.iMargin+'px'):0;
				//计算left值
				_this.aLi[_this.i].style.left = _this.widthteam[_this.j%_this.Len]+'px';
				_this.sumTop = _this.aLi[_this.i].offsetTop;
				//计算父元素的高度
				if((_this.aLi[_this.i].offsetTop+_this.aLi[_this.i].offsetHeight)>_this.WaterfallHeight)
				{
					_this.WaterfallHeight=_this.aLi[_this.i].offsetTop+_this.aLi[_this.i].offsetHeight;
				}
				if(i==data['data'].length-1)
				{
					_this.pageNum++;
				}
				_this.i++;
				_this.j++;
				}
				_this.oWaterfall.parentNode.style.height = _this.WaterfallHeight+_this.iMargin*2+'px';
				_this.bAjax = true;
			}
		})
	},
	error:function(){
	},
	Scroll:function()
	{
		 var _this = this;
		 this.oP = document.createElement('p');
		 this.oP.className = 'loading';
		 this.oP.innerHTML = '加载中，请稍等';
		 this.oWaterfall.appendChild(this.oP);
		 window.onload = window.onscroll = function(){
				var iClientHeight = document.documentElement.clientHeight;
				var iScroll = document.documentElement.scrollTop || document.body.scrollTop || 0;
				if(_this.sumTop<(iClientHeight+iScroll) && _this.bAjax && _this.cStop)
				{	
					_this.bAjax = false;		
					_this.ajax();
				}
		}
	}
}

