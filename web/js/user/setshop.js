function SelectShop(){
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
					_this.choiceBox.innerHTML = null,
					_Len = inputList.length;
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
			$.ajax({
				type: "get",
				url: "txt.json",
				dataType: "json",
				success: function (data) {
					var _Len = data.length,
						_Table = document.createElement('table');
					for(var i=0;i<_Len;i++)
					{
						var _Tr = document.createElement('tr');
						_Tr.innerHTML = '<td class="w1" ><input type="checkbox" class="check" data-aid='+data[i].aid+'></td><td class="w2">'+data[i].name+'<font>/'+data[i].price+'</font></td>';
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
				sHtml = _ele.parentNode.parentNode.children[1].innerHTML;
		/*	$.ajax({
			url:"../upload.php",
			data:{"aid":aid},
			dataType:"json",
			success:function(data){
				if(data.status=="ok"){*/
					_this.showChoice(sHtml);
					setStyle(this.Popup,{'display':'none'});
				/*	}
				}
			})*/
		}
		,
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
		}
	}