//获取ID
function getId(id)
{
	return document.getElementById(id);
}
function getTagName(id,tagName)
{
	return document.getElementById(id).getElementsByTagName(tagName);
}
function setStylePx(obj,json)
{
	for(var n in json)
	{
		obj.style[n]= json[n]+'px';
	}
}
//get parent
function getParent(obj,ele){
	var obj = obj.parentNode;
	while(obj.tagName.toLocaleLowerCase() !== ele)
	{
		obj = obj.parentNode;
	}
	return obj;
}
//get Class
function getClass(oParent,sClass)
{
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
//set css
function setCss(obj,json)
{
	for(var attr in json)
	{
		obj.style[attr] = json[attr];
	}
}
//获取节点位置
function getPoint(obj,attr)
{ 
	var t = obj[attr];
	while (obj.tagName.toLocaleLowerCase() != 'body' ) 
	{
		obj = obj.offsetParent;
		t += obj[attr]; 
	}
	return t;
}
/*获取样式属性值*/
function getStyle(obj,attr)
{
	if(obj.currentStyle)
	{
		return obj.currentStyle[attr];
	}
	else
	{
		return getComputedStyle(obj,false)[attr]
	}
}
/*运动函数*/
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
//所有商品
function FnHover(id,txt){
		if(!(this instanceof FnHover))
			return new FnHover(id,txt);
		this.id = getId(id);
		this.txt = getId(txt);
		this.fnHover();
		this.oS = this.id.getElementsByTagName('s')[0];
}
FnHover.prototype = {
	fnHover:function(){
				var _this = this;
				this.txt.onmouseover =this.id.onmouseover = function(){
					_this.oS.className = 'hover';
					_this.txt.style.display = 'block';
					fnAnimate(_this.txt,'opacity',100,5);
					//解决闪烁
					this.onmousemove = function(){
						_this.txt.style.filter ='alpha(opacity=100)';
						_this.txt.style.opacity = 1;
					}
				}
				this.txt.onmouseout =this.id.onmouseout = function(){
					_this.oS.className = '';
					fnAnimate(_this.txt,'opacity',0,2,function(){
					_this.txt.style.display = 'none';
					})
				}
			}
}
 //三级
function ShowDis(obj){
	if(!(this instanceof ShowDis))
		return new ShowDis(obj);
	this.obj = obj;
	this.id = obj.children[1];
}
ShowDis.prototype = {
	show:function(){
			this.id.style.display = 'block';
			this.obj.style.zIndex = 99;
		},
	hide:function(){
			this.id.style.display = 'none';
			this.obj.style.zIndex = 1;
		}
}
//焦点图
function Autotab(json)
		{
			if(!(this instanceof Autotab))
			 	return new Autotab(json);
			this.Btn = json.btnList;
			this.oDiv = json.bigPic;
			this.smallPic = json.smallPic;
			this.speed = json.speed;
			this.iNow = 0;	
			this.init();
			this.Event();
			this.auto();
		}
Autotab.prototype = {
		init:function()
		{
			this.Btn[0].className = this.smallPic[0].className = 'on';
			this.oDiv[0].style.opacity=100;
			this.oDiv[0].style.filter ='alpha(opacity=100)';
		},
		Event:function()
		{
			var _this =this
			for(var i=0;i<this.Btn.length;i++)
				{	
					this.Btn[i].index=this.smallPic[i].index =i;
					this.smallPic[i].onmouseover = this.Btn[i].onmouseover = function()
					{
						_this.iNow = this.index;
						_this.show(this.index);
						clearInterval(_this.timer);
					}
					this.oDiv[i].onmouseover = function()
					{
						clearInterval(_this.timer);
					}
					this.smallPic[i].onmouseout = this.Btn[i].onmouseout = this.oDiv[i].onmouseout =function()
					{
						_this.auto();
					}
				}
		},
		show:function(num)
		{
			var _this = this;
			for(var i=0;i<this.Btn.length;i++)
				{
					this.Btn[i].className = '';
					this.smallPic[i].className = ''; 
					this.oDiv[i].style.zIndex = 1;
					fnAnimate(_this.oDiv[i],'opacity',0,10);
					
				}
			this.Btn[num].className = this.smallPic[num].className ='on';
			this.oDiv[num].style.zIndex = 2;
			fnAnimate(this.oDiv[num],'opacity',100,10);
		},
		auto:function()
		{
			var _this =this;
			this.timer = setInterval(function(){
					_this.iNow++;
					if(_this.iNow>_this.Btn.length-1)
					{
						_this.iNow = 0;
					}
				_this.show(_this.iNow);
				},_this.speed)
		}
	}
//品牌
function Broaden(json){
	if(!(this instanceof Broaden))
		return new Broaden(json);
	this.ele =document.getElementById(json.id).children;
	this.Len = this.ele.length;
	this.Hover = json.HoverWidth;
	this.iWidth = this.ele[0].offsetWidth;
	this.init();
}
Broaden.prototype = {
	init:function(){
		setStylePx(this.ele[0],{'width':this.Hover});
		this.MouseEvent();
	},
	MouseEvent:function(){
		var _this = this;
		for(var i=0;i<this.Len;i++)
		{
			this.ele[i].onmouseover = function(){
				for(var j = 0;j<_this.Len;j++)
				{
					fnAnimate(_this.ele[j],'width',_this.iWidth,8);
				}
				fnAnimate(this,'width',_this.Hover,8)
			}
		}
	}
}

//显示更多
function FnShow(btn,txt)
{
	var btn = getId(btn),txt = getId(txt)
	btn.onclick = function(){
		if(txt.offsetHeight == 20){
				setCss(txt,{height:'auto'});
		}else{
			setCss(txt,{height:'20px'});
		}	
	}
	
}
//显示弹层
function ShowPopup(id){
	var obj = getId(id);
	obj.style.display = 'block';
}
//关闭弹层
function fnClose(id){
	var oParent = document.getElementById('popup'),
		oInputList = document.getElementById('inputList'),
		sHmtl = oInputList.innerHTML;
		oClose =  document.getElementById(id);
		oClose.onclick = function(){
			oParent.style.display = 'none';
			oInputList.innerHTML = sHmtl;
		}
}
function fnInput(boolean){
	var oInputList = document.getElementById('inputList'),
		sHtml = oInputList.innerHTML;
	if(boolean)
	{
		oInputList.innerHTML = '<div class="loginTxt"><img src="images/loading-pic.gif" width="50" height="50"></div>';	
		setTimeout(function(){
			document.getElementById('popup').style.display = 'none';
			oInputList.innerHTML = sHtml;
		},1500)
	}else{
		oInputList.innerHTML = '<div class="loginTxt">提交失败，请关闭窗口</div>';
	}
}

//设置封面
/*function FnSetCover(obj){
		var Ele = getClass('postImageList','setCover'),
			Len = Ele.length,
			_this,
			ImgList =getTagName('postImageList','li');
		for(var i=0;i<Len;i++)
		{
			ImgList[i].children[0].className = 'picBox';
		}
		_this = getParent(obj,'li');
		_this.children[0].className = 'sel';
}*/
//删除父级
/*function FnDelParent(obj){
		var ul  = getParent(obj,'ul'),
			li =  getParent(obj,'li');
				ul.removeChild(li);
}*/
//批量删除
/*function FnAllDel(){
		var Ul = getTagName('postImageList','ul')[0];
			InputList = getTagName('postImageList','input');
			LiList = getTagName('postImageList','li'),
			array = [],
			Len = InputList.length;
			for(var i = 0;i<Len;i++)
			{
				if(InputList[i].checked){
					array.push(InputList[i]);
				}
			}
			for(var i =0;i<array.length;i++)
			{
				var li =  getParent(array[i],'li');
					Ul.removeChild(li);
			}
	}
*/
//创建店铺
function creatShop(){
	$('#selectShow').css({display:'none'});
	$('#selectHide').css({display:'block'});
}

//返回
function reBtn(){
	$('#selectShow').css({display:'block'});
	$('#selectHide').css({display:'none'});
}
//展开
function showNav(id){
	var _id =  document.getElementById(id);
		_list = _id.getElementsByTagName('a'),
		_Len = _list.length;
		for(var i=0;i<_Len;i++)
		{
			if(_list[i].className == 'selbg' && _list[i].offsetTop>0)
			{
				_id.style.height = 'auto';
			}
		}
	
}

//左上menu tab切换
var menutab = function(a,b){
	var _ele = {
		btnlist:$('.' + a +' li'),
		txtlist:$('.' + b + ' > ul')
	}
	_ele.txtlist.eq(0).css({'display':'block','opacity':1});
	_ele.btnlist.mouseover(function(){
		_ele.txtlist.css({'display':'none'}).eq($(this).index()).css('display','block');
	})
} 