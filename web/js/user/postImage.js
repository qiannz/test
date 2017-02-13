/**
 * Created with JetBrains PhpStorm.
 * User: Administrator
 * Date: 12-11-1
 * Time: 下午4:57
 * To change this template use File | Settings | File Templates.
 */
var bbsPostImage=function(){
	this.elem=$("#postImageList ul");
	this.Li = $("#postImageList li");
	this.Input = $("#postImageList input");
	this.DelBtn = $("DelBtn");
	this.imgCache={};
	this.init();
}
bbsPostImage.prototype={
	init:function(){
		var _this=this;
		_this.elem.delegate(".del","click",function(){
			var _aid=$(this).attr("data-aid");
			_this.dele($(this),_aid);
		})
		//设置封面
		_this.elem.delegate(".setCover","click",function(){
			var _aid=$(this).attr("data-aid");
			_this.setCover($(this),_aid);
		})
		//批量删除
		_this.DelBtn.click(function(){
			_this.Input.each(function(i){
				if($(this).is(":checked"))
				{
					var _aid=$(this).attr("data-aid");
					this.dele($(this),_aid);
				}
			});
		})
		_this.elem.delegate(".inset","click",function(){
			//alert(1)
			var _aid=$(this).attr("data-aid");
			_this.addHtml(_aid);
		})
		$("#allAdd").bind("click",function(){
			_this.elem.find(".inset").each(function(){

				var _aid=$(this).attr("data-aid");
				_this.addHtml(_aid);
			})
		})
	},
	getHtml:function(jsonData){
	//	console.log(.aid);
		var _data=(jsonData.data)[0];
		//var _len=_data.length;
		var _html="";
		_html+="<li>";
		_html+=' <p class="picBox">';
		_html+='<img src="'+_data.img_url+'">';
		_html+='</p>'
		_html+='<p class="setBox"><a  class="del" data-aid="'+_data.aid+'">删除</a> <a  class="setCover"  data-aid="'+_data.aid+'">插入</a><input name="" type="checkbox"  data-aid="'+_data.aid+'"></p>';
		_html+='</li>'
		this.imgCache[_data.aid]=_data.img_url;
//		for(var i=0;i<_len;i++){
//
//		}
		this.elem.append(_html).parent().css("display","block");
	},
	setCover:function(elem,aid){
		var _this=this;
		var _elem=elem;
		var _img;
		$.ajax({
			url:"../upload.php",
			data:{"aid":aid},
			dataType:"json",
			success:function(data){
				if(data.status=="ok"){
					_this.Li.each(function(i){
						$(this).addClass("picBox");
					});
					_img = getParent(_elem,'li').children[0];
					_img.className = 'sel';
				}
			}
		})
	},
	dele:function(elem,aid){
		var _this=this;
		var _elem=elem;
		$.ajax({
			url:"../upload.php",
			data:{"aid":aid},
			dataType:"json",
			success:function(data){
				if(data.status=="ok"){
					console.log(upload1.getStats());
					var _len=_this.elem.find("li").length;
					_elem.closest("li").remove();
					delete _this.imgCache[aid];
					if(_len<2){
						_this.elem.parent().css("display","none");
					}
				}
			}
		})
	},
	addHtml:function(aid){
		var _this=this;
		var _html="";
		_html+="<p>"
		_html+='<img src="'+_this.imgCache[aid].replace("small","large")+'" alt="">'
		_html+="</p>"
		//console.log(_this.imgCache[aid]);
		editor.insertHtml(_html);
	}
}