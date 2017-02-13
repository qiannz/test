/*获取ID*/
function getid(id)
{
	return document.getElementById(id)
}
function getTagName(id,tagName)
{
	return document.getElementById(id).getElementsByTagName(tagName);
}
/*获取CLASS*/
function getClass(oParent,sClass)
{
	var aEle = document.getElementsByTagName('*')
	var aRlut = [];
	var i = 0;
	for(i=0;i<aEle.length;i++)
	{
		if(aEle[i].className == sClass)
		{
			aRlut.push(aEle[i])
		}
	}
	return aRlut;
}
//右侧滑动
function ShowDis(id)
{	
	if(!(this instanceof ShowDis))
	 	return new ShowDis(id);
	this.oUpBox = document.getElementById(id);
	this.move();
}
ShowDis.prototype = {
	move:function()
			{
				this.scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
				this.t=this.scrollTop+(parseInt((document.documentElement.clientHeight-this.oUpBox.offsetHeight)/2));
				this.oUpBox.style.top = this.t+'px';
			}
	}

//tab切换
function TabTxt(json)
		{
			if(!(this instanceof TabTxt))
				return new TabTxt(json);
			this.tabTitle = getTagName(json.btn,'li');
			this.tabCon = getTagName(json.txt,'li');
			this.parentBtn = getid(json.btn);
			this.parentTxt = getid(json.txt);
			this.iNow = 0;
			this.timer = null;
			this.init();
			this.Event();
			this.returnFrist();
		}
		TabTxt.prototype = {
			init:function()
			{
				this.tabCon[1].style.display = 'block';
				(this.tabTitle)&&(this.tabTitle[1].className = 'selBg');
			},
			tab:function(obj)
			{
				for(var i=0;i<this.tabCon.length;i++)
				{
					this.tabCon[i].style.display = 'none';
					(this.tabTitle)&&(this.tabTitle[i].className = '');
				}
				this.tabCon[this.iNow].style.display = 'block';
				obj&&(obj.className = 'selBg');
			},
			Event:function()
			{
				var _this =this;
				for(var i=0;i<this.tabCon.length;i++)
				{
					this.tabTitle[i].index = i;
					this.tabTitle[i].onmouseover = function()
					{
						_this.iNow = this.index;
						_this.tab(this);
					} 
				}
			},
			returnFrist:function()
			{
				var _this =this;
				this.parentTxt.onmouseout= this.parentBtn.onmouseout= function(ev)
				{
					var e = ev || event;
					oTarget = e.target || e.srcElement;
					if(oTarget.id == 'tabtitle' || oTarget.id == 'tabTxt')
					{
						for(var i=0;i<_this.tabCon.length;i++)
						{
							_this.tabCon[i].style.display = 'none';
							(_this.tabTitle)&&(_this.tabTitle[i].className = '');
						}
						_this.init();
					}
				}
			}
		}	
//翻页
function fnGetPiao()
{
	for(var i=0;i<69;i++)
	{
		show(""+(i+1),"getpiao_"+(i+1));
	}
}
function PageList(json)
		{
			if(!(this instanceof PageList))
				return new PageList(json)
			this.PicUl = getid(json.addpic);
			this.aLi = getTagName(json.addpic,'li');
			this.ListNum = getClass(document,json.listnum);
			this.iPageNum = json.pagnum+1;
			this.iSum = json.sum;
			this.iNow = parseInt(json.sum/json.pagnum);
			this.j = 0;
			this.aBtn = [];
			this.init();
			this.oEvent();
		}
		PageList.prototype = {
			init:function(){
				for(var j=0;j<this.ListNum.length;j++)
				{
					for(var i=0;i<this.iNow+1;i++)
					{
						var Li = document.createElement('li');
						Li.innerHTML=i+1;
						this.ListNum[j].children[1].appendChild(Li);
					}
					this.aBtn.push(this.ListNum[j].children[1]);
					this.ListNum[j].children[1].children[0].className = 'selBg';
				}
				fnGetPiao();
				this.addPic(0);
			},
			addPic:function(num){
					var Len = this.iPageNum*num;
					var shtml = '';
					this.PicUl.innerHTML = '';
					for(var i=1;i<this.iPageNum;i++)
					{
						if((i+Len)<this.iSum)
						{
							var _html = '';
							_html += '<li>';
							_html +='<img src="images/girlpic/'+(i+Len)+'.jpg" width="180" height="240" onClick="showBigPic('+(i+Len)+')">';
							_html +='<p  class="num">编号:'+(i+Len)+'</p>';
							_html +='<p ><span>当前<font id="getpiao_'+(i+Len)+'"></font>票</span><a href="javascript:add(\''+(i+Len)+'\',\'getpiao_'+(i+Len)+'\');"></a></p>';
							_html +='</li>';
							shtml += _html
						}
					}
					this.PicUl.innerHTML+=shtml;
				for(var j=0;j<this.aBtn[0].children.length;j++)
						this.aBtn[0].children[j].className = this.aBtn[1].children[j].className= '';
					this.aBtn[0].children[num].className = this.aBtn[1].children[num].className = 'selBg';
				fnGetPiao();
				},
				Tab:function(obj){
						var _this =this;
						for(var j=0;j<obj.children.length;j++)
						{
							obj.children[j].index = j;
							obj.children[j].onclick = function()
							{
								_this.addPic(this.index);
								_this.j = this.index;
							}
						}
				},
				oEvent:function(){
					var _this = this;
					var Len = this.aBtn[0].children.length;
					for(var i=0;i<this.aBtn.length;i++)
					{
						this.Tab(this.aBtn[i])
					}
					for(var i=0;i<this.aBtn.length;i++)
					{
						this.ListNum[i].children[0].onclick = function()
						{
							_this.j--;
							if(_this.j<0)
								_this.j=0;
							
							_this.addPic(_this.j);
						}
						this.ListNum[i].children[2].onclick = function()
						{
							_this.j++;
							if(_this.j>_this.iNow)
								_this.j=_this.iNow;
							_this.addPic(_this.j);
						}
					}
				}
			}
// 搜索
function Search(json)
{
	if(!(this instanceof Search))
		return new Search(json)
	this.btn = getid(json.btn);
	this.text = getid(json.text);
	this.sum = json.sum;
	this.fnsearch();
}
Search.prototype = {
	fnsearch:function()
	{
		var _this = this;
		this.btn.onclick = function()
		{
			if(_this.text.value<_this.sum && _this.text.value!='' && _this.text.value>0)
			{
				showBigPic(parseInt(_this.text.value),10);
			}else{
				alert('您输入的编号不存在')
			}
		}
	}
}
//弹窗大图
	function showBigPic(num)
	{
			var _html = '';
			_html +='<div class="showBigPic">';
			_html +='<div class="center"><img src="images/girlpic/'+num+'.jpg" width="390" height="520"></div>';	
			_html +=' <p>编号:'+num+'</p>';
			_html +='<p><span>当前<font id="getpiao2_'+num+'"></font>票</span><a class="vote" href="javascript:add(\''+num+'\',\'getpiao2_'+num+'\');"></a><a class="close" onclick="getid(\'showBigPic\').style.display = \'none\';fnGetPiao();"></a></p>';
			_html +='</div>';
			_html +='<div id="shade"></div>';
			for(var i=0;i<69;i++)
			{
				show(""+(i+1),"getpiao2_"+(i+1));
			}
			getid('showBigPic').innerHTML = _html;
			getid('showBigPic').style.display = 'block';
	}