/**
 * Created with JetBrains PhpStorm.
 * User: Administrator
 * Date: 12-11-1
 * Time: 下午4:57
 * To change this template use File | Settings | File Templates.
 */
var bbsPostImage=function(){
	this.elem=$("#postImageList ul");
	this.Input = $("#postImageList input");
	this.DelBtn = $("DelBtn");
	this.imgCache=[];
	this.init();
	this.aid = 0;
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
			var _gid=$(this).attr("data-gid");
			_this.setCover(this,_aid,_gid);
		})
		
		$("#allDel").bind("click",function(){
			_this.elem.find("input[name=ck]:checked").each(function(){
				var _aid=$(this).attr("data-aid");				
				_this.dele($(this),_aid);
			})
		})
	},
	getHtml:function(jsonData){
		var _data=(jsonData.data)[0];
		var _html="";
		_html+="<li>";
		_html+=' <p class="picBox">';
		_html+='<img src="'+_data.img_url+'">';
		_html+='</p>'
		_html+='<p class="setBox"><a class="del" data-aid="'+_data.aid+'">删除</a><a class="setCover"  data-aid="'+_data.aid+'" data-gid="'+_data.gid+'">设为封面</a><input name="ck" type="checkbox"  data-aid="'+_data.aid+'"></p>';
		_html+='</li>'
		this.imgCache[_data.aid]=_data.img_url;
		if(this.aid == 0) {
			this.aid = _data.aid;
		}
		this.elem.append(_html).parent().css("display","block");
	},
	setCover:function(elem,aid,gid){
		var _this=this;
		var _elem=elem;
		var _img;
		$.ajax({
			url:"/home/good/set-cover",
			data:{"aid":aid,"gid":gid},
			dataType:"json",
			success:function(data){
				if(data.status=="ok"){
					$("#postImageList ul li").each(function(i, s){
						$(this).find('p:first').removeClass().addClass('picBox');
						$(this).find('p:last .setCover').hide();
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
			url:"/home/good/del-img",
			data:{"aid":aid},
			dataType:"json",
			success:function(data){
				if(data.status=="ok"){
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
		_html+='<img src="'+_this.imgCache[aid].replace("small","large")+'" alt="">'
		_html+="</p>"
		//console.log(_this.imgCache[aid]);
		editor.insertHtml(_html);
	},
	getCache:function(){
		return this.imgCache.slice(this.aid);
	}
}