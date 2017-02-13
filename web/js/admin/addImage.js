/**
 * Created with JetBrains PhpStorm.
 * User: Administrator
 * Date: 12-11-1
 * Time: 下午4:57
 * To change this template use File | Settings | File Templates.
 */
var couponImage=function(){
	this.elem=$("#postImageList ul");
	this.imgCache={};
	this.init();
}
couponImage.prototype={
	init:function(){
		var _this=this;
		_this.elem.delegate(".del","click",function(){
			var _aid=$(this).attr("data-aid");
			_this.dele($(this),_aid);
		})
		_this.elem.delegate(".add","click",function(){
			var _aid=$(this).attr("data-aid");
			_this.addHtml(_aid);
		})
		$("#allAdd").bind("click",function(){
			_this.elem.find(".add").each(function(){
				var _aid=$(this).attr("data-aid");
				_this.addHtml(_aid);
			})
		})
	},
	getHtml:function(jsonData){
		var _data=(jsonData.data)[0];
		var _html="";
		_html+="<li>";
		_html+='<p class="img">';
		_html+='<img src="'+_data.img_url+'">';
		_html+='</p>'
		_html+='<p><a href="javascript:;" class="del" data-aid="'+_data.aid+'">删除</a> <a class="add" href="javascript:;" data-aid="'+_data.aid+'">插入</a></p>';
		_html+='</li>'
		this.imgCache[_data.aid]=_data.img_url;
		this.elem.append(_html).parent().css("display","block");
	},
	dele:function(elem,aid){
		var _this=this;
		var _elem=elem;
		$.ajax({
			url:"/home/good/del-img?folder=ticket",
			data:{"aid":aid},
			dataType:"json",
			success:function(data){
				if(data.status="ok"){
					var stats = uploadSwf.getStats();
					stats.successful_uploads--;
					uploadSwf.setStats(stats);
   
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
		_html+='<img src="'+_this.imgCache[aid].replace("small","740")+'" alt="">'
		_html+="</p>"
		editor.insertHtml(_html);
	},
	addImgCache:function( aid , imgsrc ){
		this.imgCache[aid]=imgsrc;
	}
}