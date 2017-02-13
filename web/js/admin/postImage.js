var bbsPostImage=function(){
	this.elem=$("#postImageList ul");
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
		
		_this.elem.delegate(".add","click",function(){
			var _aid=$(this).attr("data-aid");
			_this.addHtml(_aid);
		})	
			
		$("#firstAdd").bind("click",function(){
			var _aid = _this.elem.find("input[name=ck]:checked").val();
			var _gid = _this.elem.find("input[name=ck]:checked").attr("gid");
			if(_aid == undefined || _aid == '') {
				alert('请选择一张图片');
			} else {
				_this.setFirst(_aid, _gid);
			}
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
		_html+='<img src="'+_data.img_url+'" />';
		_html+='</p>';
		_html+='<p><a href="javascript:;" class="del" data-aid="'+_data.aid+'">删除</a> <input type="radio" class="ck" name="ck" value="'+_data.aid+'" gid="'+_data.gid+'"></p>';
		_html+='</li>';
		this.imgCache[_data.aid] = _data.img_url;
		if(this.aid == 0) {
			this.aid = _data.aid;
		}
		this.elem.append(_html).parent().css("display","block");
	},
	getHtmlTicket:function(jsonData){
		var _data=(jsonData.data)[0];
		var _html="";
		_html+="<li>";
		_html+='<p class="img">';
		_html+='<img src="'+_data.img_url+'" />';
		_html+='</p>';
		_html+='<p class="setBox"><a href="javascript:;" class="del" data-aid="'+_data.aid+'">删除</a> <a class="add" href="javascript:;" data-aid="'+_data.aid+'">插入</a></p>';
		_html+='</li>';
		this.imgCache[_data.aid] = _data.img_url;
		if(this.aid == 0) {
			this.aid = _data.aid;
		}
		this.elem.append(_html).parent().css("display","block");
	},
	dele:function(elem,aid){
		var _this=this;
		var _elem=elem;
		$.ajax({
			url:"/admin/good/del-img/aid:" + aid,
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
	setFirst:function(imgId, gid) {
		var _this=this;
		$.ajax({
			url:"/admin/good/set-first/imgId:" + imgId + "/gid:" + gid,
			dataType:"json",
			success:function(data){
				if(data.status=="ok"){
					alert('设置商品封面成功');
					//location.href = '/admin/good/edit/gid:' + gid;
				}
			}
		})
	},
	getCache:function(){
		return this.imgCache.slice(this.aid);
	},
	addHtml:function(aid){
		var _this=this;
		var _html="";
		_html+="<p>"
		_html+='<img src="'+_this.imgCache[aid].replace("small","740")+'" alt="">'
		_html+="</p>"
		editor.insertHtml(_html);
	}	
}