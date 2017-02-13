(function(window){
		var Mp = {};
			window.Mp = Mp;
			/**************************************************************************************/
			//获取ID
			Mp.getId = function(id){
				return document.getElementById(id);
			}
			//获取tagName
			Mp.getTagName = function(id,tagName){
				return document.getElementById(id).getElementsByTagName(tagName);
			}
			//set Css
			Mp.setCss = function(obj,json){
					for(var attr in json)
					{
						obj.style[attr] = json[attr];
					}
			}
			//get Class
			Mp.getClass = function(oParent,sClass){
				var oParent = document.getElementById(oParent),
					aEle = oParent.getElementsByTagName('*'),
					aRlut = [],
					i = 0;
				for(i=0;i<aEle.length;i++)
				{
					if(aEle[i].className == sClass)
					{
						aRlut.push(aEle[i])
					}
				}
				return aRlut;
			}
			//获取节点位置
			Mp.getPoint = function(obj,attr){
				var t = obj[attr];
				while (obj.tagName.toLocaleLowerCase() != 'body' ) 
				{
					obj = obj.offsetParent;
					t += obj[attr]; 
				}
				return t;
			}
			/*获取样式属性值*/
			Mp.getStyle = function(obj,attr){
				if(obj.currentStyle)
				{
					return obj.currentStyle[attr];
				}
				else
				{
					return getComputedStyle(obj,false)[attr]
				}
			}
			/*事件监听*/
			Mp.getEvent = function(obj,sEvent,fn){
				return obj.attachEvent?obj.attachEvent('on'+sEvent,fn):obj.addEventListener(sEvent,fn,false);
			}
			/*运动函数*/
			Mp.Animate = function(obj,attr,iTarget,speed,fn){
			var timer = null;	
			clearInterval(obj.timer)
			obj.timer = setInterval(function(){
				var iCur = 0;
				if(attr == 'opacity')
				{
					iCur =parseInt(parseFloat(Mp.getStyle(obj,attr))*100);
				}
				else
				{
					iCur = parseInt(Mp.getStyle(obj,attr));
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
		/**************************************************************************************/
		//焦点图
		Mp.Focus = function(json){
			if(!(this instanceof Mp.Focus))
				return new Mp.Focus(json);
			this.PicBox = Mp.getId(json.ele);
			this.PicList = Mp.getTagName(json.ele,'li');
			this.PreBtn = Mp.getId(json.pre);
			this.NextBtn = Mp.getId(json.next);
			this.Len = this.PicList.length;
			
			if(this.PicList == undefined) {
				return;
			}
			if(this.Len<2){
				this.PicList[0].className = 'selbg';				
			 	return;				
			}
			this.iNum = 1;
			this.timer = null;
			this.msec = json.msec;
			this.speed = 7;//分数
			this.iLeftNum =this.PicList[0].offsetWidth;
			this.init();		
		}
		Mp.Focus.prototype = {
			init:function(){
					var _this = this;
					this.PicList[0].className ='selbg';
					this.PreBtn.onmouseover=this.NextBtn.onmouseover=this.PicBox.onmouseover = function(){
						_this.PreBtn.style.display = _this.NextBtn.style.display = 'block';
					}
						this.PicBox.onmouseout = function(){
						_this.PreBtn.style.display = _this.NextBtn.style.display =  'none';
					}
					//重组
					var _firstLi = document.createElement('li'), 
						_lastLi = document.createElement('li');
					_firstLi.innerHTML = this.PicList[this.Len-1].innerHTML;
					_lastLi.innerHTML = this.PicList[0].innerHTML;
					this.Len = this.Len + 2;
					this.PicBox.insertBefore(_firstLi,this.PicList[0]);
					this.PicBox.appendChild(_lastLi);
					this.PicBox.style.left = -1*this.iLeftNum+'px';
				 //事件
				 Mp.getEvent(_this.NextBtn,'click',function(){
						clearInterval(_this.timer);
					 	_this.next();
						_this.auto();
						
					})
				 Mp.getEvent(_this.PreBtn,'click',function(){
					 	clearInterval(_this.timer);
						_this.pre();
						_this.auto();
					})
				//自动
				this.auto()
			},
			move:function(n){
					var _n =-1*n*this.iLeftNum;
					Mp.Animate(this.PicBox,'left',_n,this.speed);
					for(var i=0;i<this.Len;i++){
						this.PicList[i].className = '';
					}
					this.PicList[Math.abs(n)].className = 'selbg';
			},
			next:function(){
					this.iNum++;
					if(this.iNum>this.Len-2)
					{
					
						this.iNum = 1;
						this.PicBox.style.left = 0;
						
					}
					this.move(this.iNum);
			},
			pre:function(){
					this.iNum--;
					if(this.iNum<1)
					{
						this.iNum = this.Len-2;
						this.PicBox.style.left =-1*(this.iNum+1)*this.iLeftNum+'px';
					
					}
					 this.move(this.iNum);
			},
			auto:function(){
				var _this = this;
				clearInterval(this.timer);
				this.timer = setInterval(function(){
					_this.next()
				},_this.msec)
			}
		
		
		}
		/*right-list*/
		Mp.LinkHover = function(json){
			if(!(this instanceof Mp.LinkHover))
				return new Mp.LinkHover(json);
				this.id = $('#'+json.id);
				this.hoverPic = this.id.find('.hover-link');
				this.listPic = this.id.find('li');
				this.Len = this.listPic.length;	
				this.init();	
		}
		Mp.LinkHover.prototype = {
			init:function(){
				var that = this;
				this.id.hover(function(){
					that.hoverPic.css('display','block');
					},function(){
					that.hoverPic.css('display','none');	
				})
				this.listPic.each(function(){
					$(this).mouseover(function(){
						var _x = $(this).offset().top-that.id.offset().top-2,
							_y = $(this).offset().left-that.id.offset().left-2;	
						that.move(_x,_y);
					})
				})
			},
			move:function(x,y){
				var that = this;
				if(Math.random()>0.5){
					this.hoverPic.stop().animate({'top':x},300,function(){
						that.hoverPic.stop().animate({'left':y},150)
					})
				}else{
					this.hoverPic.stop().animate({'left':y},300,function(){
						that.hoverPic.stop().animate({'top':x},150)
					})
				}
				
				
			}
		}
		
	})(window)



