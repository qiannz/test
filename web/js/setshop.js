function getid(id)
{
	return document.getElementById(id);
}

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
//事件绑定
function getEvent(obj,sEvent,fn)
{
	return obj.attachEvent?obj.attachEvent('on'+sEvent,fn):obj.addEventListener(sEvent,fn,false);
}
function setStyle(obj,json)
{
	for(var n in json)
	{
		obj.style[n]= json[n];
	}
}
var SelectShop=function (){
		if(!(this instanceof SelectShop))
		return new SelectShop();
		this.Popup = getid('selectPopup');
		this.setShopBtn = getid('setShop');
		this.allSelectBtn = getid('selectAll');
		this.closePopup = getClass('selectPopup','close')[0];
		this.selectTable = getClass('selectPopup','selectTable')[0];
		this.choiceBox = getid('choiceBox');
		this.submitBtn = getClass('selectBox','selectBtn')[0];
		this.choiceBox = getid('choiceBox');
		this.gidCache = [];
		this.init();		
}

SelectShop.prototype = {
	init:function(){
		var _this = this;
		this.loadingPage();
		this.allSelectBtn.checked = false;
		//显示弹窗
		getEvent(this.setShopBtn,'click',function(){
			_this.showPopup();
		});
		//关闭弹窗
		getEvent(this.closePopup,'click',function(){
			_this.hidePopup();
		})
		//全选
		getEvent(this.allSelectBtn,'click',function(){
			var inputList = _this.selectTable.getElementsByTagName('input'),
				_Len = inputList.length;
			for(var i=0;i<_Len;i++)
			{
				_this.allSelect(inputList[i]);
			}
		})	
		//提交
		getEvent(this.submitBtn,'click',function(){
			var inputList = _this.selectTable.getElementsByTagName('input')
				_this.choiceBox.innerHTML = '',
				_Len = inputList.length;
				_this.gidCache = [];
				for(var i=0;i<_Len;i++)
				{
					if(inputList[i].checked)
					{
						_this.ajax(inputList[i]);
					}
				}
		})
	},
	showChoice:function(html){
		var a = document.createElement('a');
			a.innerHTML = html;
		this.choiceBox.appendChild(a);
		
	},
	loadingPage:function(){
		//loading
		var _this = this;
		var sid = $('#sid').val();
		var gids = $('#gids').val();
		var gidsArray = gids.split(',');
		var url = '';
		if( typeof(_M) == "undefined" && typeof(_C) == "undefined") {
			url = '/home/suser/get-good';
		} else {
			url = '/admin/ticket/get-good';
		}
		$.ajax({
			type: "get",
			url: url,
			data: {sid:sid},
			dataType: "json",
			success: function (data) {
				var _Len = data.length,
				_Table = document.createElement('table');
				for(var i=0;i<_Len;i++)
				{
					var _Tr = document.createElement('tr');
					var _Td1 = document.createElement('td');
					_Td1.className = 'w1'
					if($.inArray(data[i].good_id, gidsArray) == -1){
						_Td1.innerHTML = '<input type="checkbox" class="check" data-aid="'+data[i].good_id + '" />';
					} else { 
						_Td1.innerHTML = '<input type="checkbox" class="check" data-aid="'+data[i].good_id + '" checked="checked" />';
					}
					_Tr.appendChild(_Td1);
					var _Td2 = document.createElement('td');
					_Td2.className = 'w2'
					_Td2.innerHTML = '<td class="w2">'+data[i].good_name+'<font>/'+data[i].dis_price+'</font></td>';
					_Tr.appendChild(_Td2);
					_Table.appendChild(_Tr);
				}
				_this.selectTable.appendChild(_Table);
			}
		})	
	},
	ajax:function(ele){
		var _ele = ele,
			 aid =_ele.attributes['data-aid'].value,
			_this = this;
			_this.gidCache.push(aid);
			sHtml = _ele.parentNode.parentNode.children[1].innerHTML;
			_this.showChoice(sHtml);
			setStyle(this.Popup,{'display':'none'});
	},
	allSelect:function(ele){
		var _ele = ele,
			 aid =_ele.attributes['data-aid'].value,
			_this = this; 
			(_this.allSelectBtn.checked)?_ele.checked = true:_ele.checked = false;
	},
	showPopup:function(){
		setStyle(this.Popup,{'display':'block'})
	},
	hidePopup:function(){
		setStyle(this.Popup,{'display':'none'})
	},
	getCache:function(){
		return this.gidCache;
	}
}