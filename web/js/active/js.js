(function(window,fn){
		if(window){
			fn();
		}	
	})(window,function(){
	
		var mp = function(ele){
				return new mp.fn.init(ele);
			}
		//方法
		mp.fn = mp.prototype ={
			//焦点图
				Focus:function(){
						this.focusDis = this[0].getElementsByTagName('li');
						this.Len = this.focusDis.length;
						this.FocusEvent();
				},
			//切换
				Tab:function(){
					this.tabTitle =  this[0].getElementsByTagName('span');
					this.tabCon = this[1].getElementsByTagName('li');
					this.Len = this.tabTitle.length;
					this.TabEvent();
				//	this.TabAuto();
				},
			//goTop
			GoTop:function(){
				this[0].onclick = function(){
					document.documentElement.scrollTop&&(document.documentElement.scrollTop = 0);
					document.body.scrollTop&&(document.body.scrollTop = 0);
					}
				},
			//sidetab
			SideTab:function(){
				this.sideTit =  this[2].getElementsByTagName('a');
				this.Tab();
				this.sideTabEvent();
				}
			}
			
		//SIDE切换
		mp.prototype.sideTabEvent = function(){
			var _this = this;
			for(var i=0;i<this.Len;i++){
				this.sideTit[i].index = i;
				this.sideTit[i].onclick = function(){
					_this.iNow = this.index;
					_this.TabTxt(_this.iNow);
					//clearInterval(_this.timer);
				}
				
			//	this.sideTit[i].onmouseup = function(){
			//		_this.TabAuto();	
			//	}
			}
		}
		//焦点图	
		mp.prototype.FocusEvent = function(){
			var _this = this
			for(var i=0;i<this.Len;i++){
				this.focusDis[i].onmouseover = function(){
		
					for(var j = 0;j<_this.Len;j++){				
						fnAnimate(_this.focusDis[j],'width',72,9);
						_this.focusDis[j].className = '';
					}
				fnAnimate(this,'width',630,3);
				this.className = 'sel';
				}
			}
		}
		//q切换
		mp.prototype.TabEvent = function(){
			var _this = this;
			for(var i=0;i<this.Len;i++){
				this.tabTitle[i].index = i;
				this.tabTitle[i].onclick = function(){
					_this.iNow = this.index;
					_this.TabTxt(_this.iNow);
				//	clearInterval(_this.timer);
				}
				//this.tabTitle[i].onmouseup = function(){
				//	_this.TabAuto();	
			//	}
			}
		}
		mp.prototype.TabAuto =function(){
			var _this = this;
			this.timer = setInterval(function(){
				_this.iNow++;
				if(_this.iNow>_this.Len-1){
					_this.iNow = 0;				
				}
				_this.TabTxt(_this.iNow);
			},8000)
			
		}
		
		mp.prototype.TabTxt = function(n){
		
			for(var j=0;j<this.Len;j++){
						setCss(this.tabCon[j],{'display':'none'});
						this.tabTitle[j].className = ''
					}				
					setCss(this.tabCon[n],{'display':'block'});
					this.tabTitle[n].className = 'on';
		
		}
	
		//参数
		mp.fn.init = function(ele){
				var _push = Array.prototype.push;
				for(var n in ele)
				{
					_push.call(this,document.getElementById(ele[n])) 
				}				
				this.iNow = 0;
				this.Len = 0;
			}
		mp.fn.init.prototype = mp.prototype;
		_mp = window.mp;
		window.mp = mp;
		
		//方法
		function getPoint(obj,attr){ 
			var t = obj[attr];
			while (obj.tagName.toLocaleLowerCase() != 'body' ) 
			{
				obj = obj.offsetParent;
				t += obj[attr]; 
			}
			return t;
		}
		function fnAnimate(obj,attr,iTarget,speed,fn)
			{
				var timer = null;	
				clearInterval(obj.timer)
				obj.timer = setInterval(function(){
					var iCur = 0;
					if(attr == 'opacity')
					{
						iCur =parseInt(parseFloat(getStyle(obj,attr))*100);
					}
					else
					{
						iCur = parseInt(getStyle(obj,attr));
					}
						
						var iSpeed = (iTarget - iCur)/speed;
						iSpeed = iSpeed>0?Math.ceil(iSpeed):Math.floor(iSpeed);
					if(iCur == iTarget)
					{
						clearInterval(obj.timer)
						if(fn)
						{
							fn();
						}
					} 
					else
					{
						if(attr == 'opacity')
						{
							obj.style.filter ='alpha(opacity='+(iCur + iSpeed)+')';
							obj.style.opacity = (iCur+iSpeed)/100;
						}
						else
						{	
							obj.style[attr] = iCur+iSpeed+'px';
							
						}
					}
					},30)
			}
			function getStyle(obj,attr){
					if(obj.currentStyle)
					{
						return obj.currentStyle[attr];
					}
					else
					{
						return getComputedStyle(obj,false)[attr]
					}
				}
			function setCss(obj,data){
				for(var i in data){
					obj.style[i] = data[i]
				}
			}
	});