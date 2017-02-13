var sku = function(objarr){
	/*
	SKU
	点击checked 为true 添加至多维数组
	用多维数组 创建表格
	多维数组第一组 为表格 行数和行数据
*/
	var data = {
		nowColLen:3,
		arrDate:[],
		arrTitle:[],
		tdHtml:'',
		bstop:true,
		init:function(){},
		delArray:function(arr,txt,n){},
		drawTable:function(arr){},
		createTd:function(){},
		searchInfo:function(txt){},
		showInput:function(obj){},
		createThead:function(){},
		spanRow:function(colNum,trNum){},
		addTdData:function(){},
		addInput:function(n){},
		saveData:function(arr){},
		delUserData:function(){n},
		createData:function(arr){},
		addUserData:function(len){},
		getUserNumber:function(){},
		getUserPrize:function(){},
		getUserAppPrize:function(){},
		checkbox:function(arr){},
		checkEvent:function(num){}
	};


	// 初始化
	data.init = function(obj){
		$(obj).each(function(){
		
			var _val = $(this).next().attr('initvalue');
			$(this).next().val(_val);
			$(this).text(_val);
		});
		$('.color-checkbox').attr('checked',false);
		
		//数组长度等于栏目数
		data.arrDate.length = $('.color-list').length+data.nowColLen;
		$.each(data.arrDate,function(i,val){
			data.arrDate[i] = [];
		})
		
		$('#MaxNumber').val(0);
		$('#MinNumber').val(0);
		$('#sumNumber').val(0);
		
		$('.color-input').each(function(){
			$(this).val($(this).prev().prev().attr('initvalue'));
		})
		
		
		$('.right-list').css('visibility','hidden');
		$('#ske-table-box').css('display','none');
	}
	
	//点击显示input	
	data.showInput = function(obj){
		$(obj).click(function(){
			if($(this).attr('checked') == 'checked' && $(this).attr('data-alias') == 1){
				$(obj).next().next().stop().animate({'opacity':0},100);
				$(this).next().next().stop().animate({'opacity':1},300);
			}
		});
		$(obj).next().next().change(function(){
			var re = /\s/g;
				if(re.test($(this).val()) || $(this).val() == ''){
			
					$(this).prev().text($(this).prev().prev().attr('initvalue'));
				}else{
					$(this).prev().text($(this).val());
				}
			});
		$(obj).next().next().blur(function(){
			var re = /\s/g;
			$(obj).next().next().stop().animate({'opacity':0},100);
			
			if(re.test($(this).val()) || $(this).val() == ''){
					$(this).prev().text($(this).prev().prev().attr('initvalue'));
				}else{
					$(this).prev().text($(this).val());
				}
		});
		
		
	}
	//创建表格
	data.createThead = function(){
		var _table = $('<table border="0" cellspacing="0" cellpadding="0" class="sku-table" width="750"><thead><tr><td>价格</td><td>app价格</td><td>数量</td></tr></thead><tbody id="tbody"></tbody></table>'), 
			_title = $('.sku-label'),
			_thtml = '';
			_arr = [];
			_title.each(function(){
				var _re = re = /\.{0,}\：/,
					_str = $(this).text();
				data.arrTitle.push(_str.replace(_re,''));
			})
			
 			$.each(data.arrTitle,function(i,val){
				_arr.push(data.arrTitle[data.arrTitle.length - 1- i]);
			})
			
			$.each(_arr,function(i,val){
				var _td = $('<td></td>');
				_td.text(val);
			
				_table.find('thead td:first').before(_td);
			})
			$('.right-list').append(_table);
	}
	
	//添加信息
	data.searchInfo =  function(txt,num){
		  var t = '';
		  $('.color-list').eq(num).find('.color-checkbox').each(function(){
			  if($(this).attr('initvalue') == txt){
  
				  if($(this).attr('initvalue') == $(this).next().text()){
					  t = $(this).attr('initvalue');
				  }else{
					  t =  $(this).next().text();
				  }
				  
			  }
		  })
		  return t;
	}
	
	//创建td
	data.createTd = function(){
		var _len = data.arrDate.length ;
		for(var i = 0;i<_len;i++){
			if(i >= _len -1){
				data.tdHtml+='<td class="number" uid="number"></td>';
			}else if(i >= _len -2){
				data.tdHtml+='<td class="number" uid="app"></td>';
			}else if(i>=$('.color-list').length){
				data.tdHtml+='<td class="number" uid="prize"></td>';
			}else{
				data.tdHtml+='<td></td>';
			}
		}
	}
	//表格行数信息
	// 余下td 添加input
	data.addInput = function(n){
		var html = '<p><input type="text"  class="sku-prize-input" initvalue="0" ></p>';
		var re  = /\d/g;
		$('.number').html('');
		
		for(var i =0;i<n;i++){
			$('.number').html(html);
		}
		//获取总数量
		data.getUserNumber();
		//获取 价格
		data.getUserPrize();
		//获取APP 价格
		data.getUserAppPrize();
		$('.sku-prize-input').keyup(function(){
			data.saveData();
			retData(data.arrDate,data.bstop);
				
		})
	}
	//删除用户数据
	data.delUserData = function(n){
		for(var i=0;i<data.nowColLen;i++){
			data.arrDate[data.arrDate.length-1-i].splice(n,1);	
		}
	}
	
	// 删除数组里元素
	data.delArray = function(arr,txt,n){
		$.each(arr,function(i,val){
				if(txt == val){
					arr.splice(i,1);
					data.delUserData(i);
					return;
				}
		})

	}

	//保存数据
	data.saveData = function(){
		
		var fn = function(len){
				var _arr = data.arrDate[len];
				
				$('.sku-table tr').each(function(i){
					var icount  = i;
					var _val = $(this).find('td').eq(len).find('input').val();
					

					if(!_val){
						_val = '';
					}
						_arr.push(_val);
				})
				
				if(_arr[1]){
					_arr.splice(0,1);
				};
		};
		for(var i=0;i<data.nowColLen;i++){
			data.arrDate[data.arrDate.length - 1 - i] = [];
			fn(data.arrDate.length-1-i);
		};
		
	
	
	}
	//添加用户数据
	data.addUserData = function(){
	
	var fn = function(len){
			var arr = data.arrDate[len];
			$('#tbody tr').each(function(i){
				$(this).find('td').eq(len).find('input').val(arr[i]);
			})
	};
	
	for(var i=0;i<data.nowColLen;i++){
			fn(data.arrDate.length - 1 - i);			
		}
	}
	//合并行
	data.spanRow = function(colNum,trNum){
		var arr = [],
			rowSpanLen = 1;
			
		if((colNum - 1)<0){
			rowSpanLen = trNum/data.arrDate[colNum].length;
		}else{
			
			//分行 等于 现在数组 循环至第一例数组长度
			for(var i = 0;i<=colNum;i++){
				rowSpanLen = rowSpanLen*data.arrDate[colNum-i].length;
			}
			rowSpanLen = trNum/rowSpanLen;
		};
	
		
		$('#tbody tr').each(function(){
			$(this).find('td').eq(colNum).each(function(i){
				arr.push($(this));
			});
		});
	
		$.each(arr,function(i){
			if(i%rowSpanLen == 0){
				arr[i].attr('rowspan',rowSpanLen);
			}else{
				arr[i].css('display','none');
			};
		});
		
	}	

	//重绘表格
	data.drawTable = function(arr){
		var trNum = 1,
			arrLen = $('.color-list').length,
			frag = document.createDocumentFragment();
		
		$('#tbody').html('');
		
		var sign = function(a,Len){
			var t =1;
			for(var i=0;i< Len;i++){
				 t=t*a[i].length;
			}
			
			return t;
		}
		
		trNum = sign(arr,arrLen);
		//添加行 tr 到 tbody
		for(var i = 0;i<trNum;i++){
			var ele = $('<tr></tr>');
			ele.html(data.tdHtml);
			//frag.appendChild(ele);
			$('#tbody').append(ele)
		};
		
		//document.getElementById('tbody').appendChild(frag);
		
		//合并行
		for(var i = 0;i<arrLen-1;i++){
				data.spanRow(i,trNum);
		};
		
		
	}
	
	
/*--------------------------------------------------------------------------------------------------------------*/	
	// 添加数据
	data.addTdData = function(){
		var arr = data.arrDate,
			_len = $('.color-list').length-1;
			
			
		
		var fn = function(tdarr,arr,i){
				var rearr = arr,
					icount = i;
					
				$.each(tdarr,function(i){
					var n = i%rearr.length;
					tdarr[i].html(data.searchInfo(arr[n],icount));
				});
				
			};	
			
		var fnarr = function(i){
			var _icount = i,
				_arr = [];
			$('#tbody td').each(function(){
						var num = $(this).index();
						if($(this).css('display')!='none' && num == _icount){
								_arr.push($(this));
						}
				});
			return _arr;
		};
		
		$.each(arr,function(i,val){
			var _icount = i;
			fn(fnarr(_icount),val,_icount);
			
		})
	
		data.addInput(data.arrDate[_len-1].length);
	}
		/*
		点击checkBox 
		每次数组清空
		checkBox勾选是 值添加到数组
	*/
		$('.color-list').each(function(i){
			data.arrDate[i] = [];
		});
		
		$('.color-checkbox').change(function(){
			var icount = $(this).parent().parent().parent().index('.color-list'),
			    _txt = $(this).attr('initvalue'),
				timer = null;
				
			data.bstop = true;
			
			data.saveData();
			
			$('#tbody').html('加载中');
			
			if($(this).attr('checked') == 'checked'){
				data.arrDate[icount].push(_txt);
			}else{
				data.delArray(data.arrDate[icount],_txt,icount);
			};
			$.each(data.arrDate,function(i,val){
				if(data.arrDate[i].length == 0){
					data.bstop = false;
					return;
				}
			});
			
			/*
				 根据数组重绘表格
			*/
			$('.right-list').css('visibility','hidden');
			$('#ske-table-box').css('display','none');
			
			timer = setTimeout(function(){
				if(data.bstop){
					$('.right-list').css('visibility','visible');
					$('#ske-table-box').css('display','block');				
				};
				data.drawTable(data.arrDate);
				data.addTdData();
				retData(data.arrDate,data.bstop);
				data.addUserData();
				clearTimeout(timer);
			},1000);
				
		});
		
		
		
			

		
	//返回数据
 function retData(arr,bstop){
		var retarr = [],
			retarrlen  = $('#tbody tr').length
			retarr.length = 0;
		
		if(!data.bstop){
			arr = [];
		}
		
		$('#tbody tr').each(function(){
			var a = [];
			$(this).find('td').each(function(i){
				var _val ;
				if($(this).html() == ''){
					$(this).html($(this).parent().prev().find('td').eq(i).html());
				}
				
				
				if($(this).find('input').length > 0){
					
					_val = $(this).find('input').val();
			
				}else{
					_val = $(this).html();
				}
				a.push(_val);
			})
			retarr.push(a);
		});
		
	
		//创建返回二维数组
		
	
		
		//console.log(retarr.join(';'))
		
		$("#dataRetStr").val(retarr.join(';'));
//	console.log(retarr)
	}	
	
	
	

	//获取数量总数
	data.getUserNumber = function(){
			
			var objarr = [];
			$('#tbody td').each(function(){
				if($(this).attr('uid') == 'number'){
					$(this).find('input').each(function(){
						objarr.push($(this));
					})
				}
			})
			
			$.each(objarr,function(i){
					var sum = 0;
					$.each(objarr,function(i){
						var val =Number(objarr[i].val());
						
						if(isNaN(val)){
							val = 0;
						}
						sum+=val;
						$('#total').val(sum);
					})
			})
			
			$.each(objarr,function(i){
				objarr[i].keyup(function(){
						var sum = 0;
					$.each(objarr,function(i){
						var val =Number(objarr[i].val());
						
						if(isNaN(val)){
							val = 0;
						}
						sum+=val;
						$('#total').val(sum);
					})
					
				})
			})
	}

	//获取价格
	
	data.getUserPrize = function(){
		
		var objarr = [];
			$('#tbody td').each(function(){
				if($(this).attr('uid') == 'prize'){
					$(this).find('input').each(function(){
						objarr.push($(this));
					})
				}
			})
			
			
			$.each(objarr,function(i){
			
						var arr = [];
					$.each(objarr,function(i){
						var val =Number(objarr[i].val());
						
						if(isNaN(val)){
							val = 0;
						}
						arr.push(val);
						$('#MaxNumber').val(Math.max.apply(Math,arr));
						//console.log(Math.max.apply(Math,arr))
						$('#MinNumber').val(Math.min.apply(Math,arr));
					})
			})
			
			
			
			$.each(objarr,function(i){
			
				objarr[i].keyup(function(){
						var arr = [];
					$.each(objarr,function(i){
						var val =Number(objarr[i].val());
						
						if(isNaN(val)){
							val = 0;
						}
						arr.push(val);
						$('#MaxNumber').val(Math.max.apply(Math,arr));
						//console.log(Math.max.apply(Math,arr))
						$('#MinNumber').val(Math.min.apply(Math,arr));
					})
					
				})
			})
	}	
	//获取APP价格
	
	data.getUserAppPrize = function(){
		
		var objarr = [];
			$('#tbody td').each(function(){
				if($(this).attr('uid') == 'app'){
					$(this).find('input').each(function(){
						objarr.push($(this));
					})
				}
			})
			
			$.each(objarr,function(i){
					var arr = [];
					$.each(objarr,function(i){
						var val =Number(objarr[i].val());
						
						if(isNaN(val)){
							val = 0;
						}
						arr.push(val);
						$('#MaxAppNumber').val(Math.max.apply(Math,arr));
						//console.log(Math.max.apply(Math,arr))
						$('#MinAppNumber').val(Math.min.apply(Math,arr));
					})
			})
			
			
			
			$.each(objarr,function(i){
			
				objarr[i].keyup(function(){
						var arr = [];
					$.each(objarr,function(i){
						var val =Number(objarr[i].val());
						
						if(isNaN(val)){
							val = 0;
						}
						arr.push(val);
						$('#MaxAppNumber').val(Math.max.apply(Math,arr));
						//console.log(Math.max.apply(Math,arr))
						$('#MinAppNumber').val(Math.min.apply(Math,arr));
					})
					
				})
			})
	}	
	//显示 隐藏INPUT
	data.showInput('.color-checkbox');
	data.init('.color-val');
	//创建table
	data.createThead();
	data.createTd();

	data.checkbox = function(arr){
		var _len = arr.length - data.nowColLen;
		
		for(var i=0;i<_len;i++){
			var icount = i;
			$.each(arr[i],function(i,val){
				
				(function(txt,num){
					
					$('.color-list').eq(num).find('input:checkbox').each(function(){
							
							if($(this).attr('initvalue') == txt){
								
								$(this).attr('checked','checked');
							}
						
					})
					
				})(val,icount);
			})
			
		}
		
		
	}
	
	if(objarr){
		data.arrDate = eval(objarr);
		data.drawTable(data.arrDate);
		data.addTdData();
		data.addUserData();
		data.getUserNumber();
		data.getUserPrize();
		data.getUserAppPrize();
		$('.right-list').css('visibility','visible');
		$('#ske-table-box').css('display','block');	
		data.checkbox(data.arrDate);
	}
	
	
	
}