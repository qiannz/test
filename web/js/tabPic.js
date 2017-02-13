//图片切换
function TabPic(json){
	if(!(this instanceof TabPic))
		return new TabPic(json);
	this.bigImg = getTagName(json.bigImg,'img')[0];
	this.bigImgPre = getId(json.bigImgPre);
	this.bigImgNext = getId(json.bigImgNext);
	this.smallImgPre =  getId(json.pre);
	this.smallImgNext = getId(json.next);
	this.smallPicBox = getTagName(json.pisList,'ul')[0];
	this.smallPicLi = getTagName(json.pisList,'li');
	this.smallPicLiWidth = this.smallPicLi[0].offsetWidth+10;
	this.preLink = json.preLink;
	this.nextLink = json.nextLink;
	this.Len = this.smallPicLi.length;
 	this.scrollBg = getId(json.Scroll);
	this.scrollBtn = getTagName(json.Scroll,'span')[0];
	this.scrollBtnLeft = getPoint(this.scrollBg,'offsetLeft');
	this.scrollMax = this.scrollBg.offsetWidth - this.scrollBtn.offsetWidth;
	this.iNum = 0;
	this.scrollWidth = 0;
	this.iPicNum = 0;
	this.b = (/msie/ig).test(window.navigator.userAgent);
	this.init();
}

TabPic.prototype = {
	init:function(){
		var _this = this,re=/small/ig;
		this.bigImg.src = this.smallPicLi[0].children[0].src.replace(re,'740');
		this.smallPicBox.style.width = this.smallPicLiWidth*this.Len+'px';
		this.Scroll();
		this.BigPre();
		this.BigNext();
		this.SmallTab();
	},
	//滚动条
	Scroll:function(){
		var _this = this;
		this.scrollBtn.onmousedown = function(ev){
			var oEvent = ev||event,
				disX = oEvent.clientX -getPoint(this,'offsetLeft');
				if(_this.b)
				{
					_this.MouseDown(this,disX);
					this.setCapture();
					
				}else{
					_this.MouseDown(document,disX);
					return false;
				}
		}
		
	},
	MouseDown:function(obj,disX){
			var _this = this;
			
				obj.onmousemove = function(ev){
					var oEvent = ev||event, l = oEvent.clientX-getPoint(_this.scrollBg,'offsetLeft')-disX;
					_this.Mousemove(l);
				}
				this.Mouseup(obj)
	},
	Mousemove:function(l){
		 if(l<=0)
		 {
			 l = 0;
		 }else if(l>=this.scrollMax)
		 {
			 l=this.scrollMax;
		 }
		 this.iNum = l/(this.scrollMax);
		 this.scrollBtn.style.left = l + 'px';
		 if(this.Len>6){
		 this.smallPicBox.style.left = -1*this.iNum*(this.smallPicBox.offsetWidth-this.scrollBg.offsetWidth)+'px';
		 }
		 
	},
	Mouseup:function(obj){
		var _this =this;
		obj.onmouseup = function(){
			obj.onmousemove = null;
			obj.onmouseup = null;
			if(_this.b)
			{
				_this.scrollBtn.releaseCapture();
			}
		}
	},
	//大图上一页
	BigPre:function(){
			var _this = this;
			this.smallImgPre.onclick = this.bigImgPre.onclick = function(){
					_this.iPicNum--;
					_this.LinkText(_this.iPicNum);
				if(_this.iPicNum<=0)
				{
					_this.iPicNum=0;
				}
				_this.TabClassName(_this.iPicNum);
				if(_this.Len>6)
				{
					_this.Move(_this.iPicNum)
				}
			
			}
		},
	//大图下一页
	BigNext:function(){
			var _this = this;
			this.smallImgNext.onclick =this.bigImgNext.onclick = function(){
				_this.iPicNum++;
				_this.LinkText(_this.iPicNum);
				if(_this.iPicNum>=_this.Len-1)
				{
					_this.iPicNum=_this.Len-1;
				}
					_this.TabClassName(_this.iPicNum);
				if(_this.Len>6)
				{
					_this.Move(_this.iPicNum);
					
				}
				
			}
	},
	//图片class
	TabClassName:function(n){
		var re=/small/ig;
		for(var j=0;j<this.Len;j++)
		{
			this.smallPicLi[j].className = '';
		}
		this.smallPicLi[n].className = 'selbg';
		this.bigImg.src = this.smallPicLi[n].children[0].src.replace(re,'740');
	},
	Move:function(n){
		if(n>=2 && n<(this.Len-3))
		{
			fnAnimate(this.smallPicBox,'left',-1*this.smallPicLiWidth*(n-2),5);
		}else if(n<=2){
			fnAnimate(this.smallPicBox,'left',0,5);
		}else if(n <= (this.Len-2) || n == (this.Len-1)){
			fnAnimate(this.smallPicBox,'left',-1*this.smallPicLiWidth*(this.Len-6),5);
		}
		if(n<=0)
		{
			n=-1;
		}
		this.scrollBtn.style.left = ((this.smallPicLiWidth*(n+1))/this.smallPicBox.offsetWidth)*this.scrollMax+'px';
	},
	//小图切换
	SmallTab:function(){
		//小图路径
		var _this =  this,re=/small/ig;
		for(var i = 0;i<this.Len;i++)
		{	
			this.smallPicLi[i].index = i;
			this.smallPicLi[i].onclick = function(){
				_this.iPicNum = this.index
				_this.TabClassName(_this.iPicNum);
				if(_this.Len>6)
				{
					_this.Move(this.index)
				}
				_this.bigImg.src = this.children[0].src.replace(re,'740');
			}
		}
	},
	//判断上一篇，下一篇;
	LinkText:function(n){
		//上一篇
		if(n<0)
		{
			this.PreText();
		}
		//下一篇
		if(n==this.Len){
			
			this.NextText();
		}
		
	},
	//上一篇
	PreText:function(){
		if(this.preLink){
			this.smallImgPre.href = this.bigImgPre.href = this.preLink;
		}
	},
	//下一篇
	NextText:function(){
		if(this.nextLink){
			this.smallImgNext.href = this.bigImgNext.href = this.nextLink;
		}
	}
}