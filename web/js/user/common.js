//获取ID
function getid(id)
{
	return document.getElementById(id);
}
function getTagName(id,tagName)
{
	return document.getElementById(id).getElementsByTagName(tagName);
}
function setStyle(obj,json)
{
	for(var n in json)
	{
		obj.style[n]= json[n];
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
//事件绑定
function getEvent(obj,sEvent,fn)
{
	return obj.attachEvent?obj.attachEvent('on'+sEvent,fn):obj.addEventListener(sEvent,fn,false);
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