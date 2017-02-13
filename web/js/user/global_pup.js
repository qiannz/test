/**
 * Created with JetBrains PhpStorm.
 * User: Administrator
 * Date: 12-10-17
 * Time: 下午5:28
 * To 通用的信息弹出层
 */
var  global_pup=function(opts){
	if (!(this instanceof global_pup)) {
		return new global_pup(opts);
	}
	this.message=opts.message;
	this.closeCallBack=opts.closeCallBack||false;
	if($("#global-pup")[0]==undefined){
		this.elem=$('<div class="global-pup" id="global-pup"></div>');
		$("body").append(this.elem);
	}else{
		this.elem=$("#global-pup")
	}
	this.setPup()
}
global_pup.prototype={
	setPup:function(){
		var _this=this;
		var _html="";
		_html+=' <h2 class="title">提示<b class="close"></b></h2>';
		_html+='<div class="pupCont">'+_this.message+'</div>';
		_this.elem.html(_html);
		myPopup({
			"elem":_this.elem,
			"close":_this.elem.find(".close"),
			"closeCallBack":_this.closeCallBack
		})
	}
  }

/**
 * 模拟confirm弹出层
 * */
var Confirm=function(opts){
	if (!(this instanceof Confirm)) {
		return new Confirm(opts);
	}
	this.callback={};
	if($("#global-confirm")[0]==undefined){
		this.elem=$('<div class="global-pup" id="global-confirm"></div>');
		$("body").append(this.elem);
	}else{
		this.elem=$("#global-pup")
	}
	this.init();
}
Confirm.prototype={
	init:function(){
		var _this=this
		_this.elem.delegate("#confirm-true","click",function(){
			_this.callback()
		})
	},
	setHandler:function(status,callback){
		var _this=this;
		var _message="";
		switch (status){
			case "tipDel":
				_message="<p>是否确定<em>删除</em>操作？</p>"
				break;
			case "tipTop":
				_message="<p>是否确定<em>置顶</em>操作？</p>"
				break;
			case "tipGag":
				_message="<p>是否确定<em>禁言</em>操作？</p>"
				break;
			case "perSeal":
				_message="<p>是否确定<em>封贴</em>操作？</p>"
				break;
			case "delTipTop":
				_message="<p>是否<em>取消置顶</em>操作？</p>"
				break;
			default:
				return false;
				break;
		}
		_this.callback=callback;
		_this.setPup(_message);
	},
	setPup:function(message){
		var _this=this;
		var _html="";
		_html+=' <h2 class="title">提示<b class="close"></b></h2>';
		_html+='<div class="pupCont">'+message+'';
		_html+='<p class="btn-box">';
		_html+='<input class="btn" value="是" type="button" id="confirm-true">';
		_html+='<input class="btn" value="否" type="button" id="confirm-false">';
		_html+='</p></div>';
		_this.elem.html(_html);
		myPopup({
			"elem":_this.elem,
			"close":"#global-confirm .close,#confirm-false"
		})
	}
}

/**
 * 获取版块数组信息
 * */
var getForum=function(opts){
	this.url="http://127.0.0.1/select.php";//ajax地址
	this.html={
		"forum":"",
		"subForumHtml":{},
		"forumName":{}
	};
	this.isAjax=true;
}
getForum.prototype={
	init:function(opts){
		this.callback=opts.callback;
	},
	getHtml:function(Forumdata,subId){
		var _this=this;
		if(_this.isAjax){
			$.ajax({
				url:_this.url,
				dataType:"json",
				data:{},
				success:function(jsondata){
					var data=jsondata.data;
					var _len=data.length;
					var _forumHtml="";
					_forumHtml+='<select class="select">';
					for(var i=0;i<_len;i++){
						_forumHtml+='<option value="'+data[i].class_id+'">';
						_forumHtml+=data[i].class_name;
						_forumHtml+='</option>';
						var _sub=data[i].child;
						var _sublen=_sub.length;
						var _subForumHtml="";
						if(_sublen>0){
							for(var j=0;j<_sublen;j++){
								_subForumHtml+='<li>';
								_subForumHtml+='<input value="'+_sub[j].class_id+'" type="radio" class="input-radio" name="subForum">';
								_subForumHtml+='<span>';
								_subForumHtml+=_sub[j].class_name;
								_subForumHtml+='</span>';
								_subForumHtml+='</li>';
							}
						}
						_this.html.subForumHtml[data[i].class_id]=_subForumHtml;
						_this.html.forumName[data[i].class_id]=data[i].class_name;
					}
					_forumHtml+='</select>';
					_this.html.forum=_forumHtml;
					if(_this.callback){
						_this.callback({
							html:_this.html,
							Forumdata:Forumdata,
							subId :subId
						})
					}
					
					_this.isAjax=false;

				}
			})
		}else{
			_this.callback({
				html:_this.html,
				Forumdata:Forumdata,
				subId :subId
			});
		}

	}
}

/**
 * 移动帖子的弹出框
 * */

var ForumRemove=function(opts){
	//this.originalForum=opts.originalForum;
	this.ForumObj=opts.ForumObj;
	this.callback={};
	if($("#bbs-removePup")[0]==undefined){
		this.elem=$('<div class="bbs-removePup" id="bbs-removePup"></div>');
		$("body").append(this.elem);
	}else{
		this.elem=$("#bbs-removePup")
	}
	this.init();
}
ForumRemove.prototype={
	init:function(){
		var _this=this
		_this.elem.delegate("#ForumRemove-true","click",function(){
			_this.callback()
		})
		_this.elem.delegate(".select","change",function(){
			var _forumName=$(this).val();
			$("#sub-select").html(_this.ForumObj.subForumHtml[_forumName]);
		})
	},
	setHtml:function(forumName,subId){
		var _this=this;
		var _html="";
		var _forumName=_this.ForumObj.forumName[forumName];
		_html+='<h2 class="title">移动帖子<b class="close"></b></h2>';
		_html+='<div class="pupCont">';
		_html+='<p>原版块：'+_forumName+'</p>';
		_html+='<p class="select-box">';
		_html+='<label>选择版块：</label>';
		_html+=_this.ForumObj.forum;
		_html+='</p>';
		_html+='<ul class="sub-select" id="sub-select">'
		_html+=_this.ForumObj.subForumHtml[forumName];
		_html+='</ul>';
		_html+='<p class="btn-box">';
		_html+='<input class="btn" value="确定" type="button" id="ForumRemove-true">';
		_html+='</p>';
		_html+='</div>';
		_this.elem.html(_html);
		_this.elem.find("option[value='"+forumName+"']").attr("selected",true);
		$("#sub-select").find("input[value='"+subId+"']").attr("checked",true);
	},
	setPup:function(callback,originalForum,subId){
		var _this=this;
		_this.setHtml(originalForum,subId);
		//调用弹窗
		myPopup({
			"elem":_this.elem,
			"close":"#bbs-removePup .close"
		})
		_this.callback=callback;
	}
}

/**
 * 发表帖子页中版块输出JS
 * */
var postForum=function(opts){
	this.ForumObj=opts.ForumObj;
	this.elem=$(opts.elem);
	this.originalForum=opts.originalForum||"";
	this.subId=opts.subId||"";
	this.init();

}
postForum.prototype={
	init:function(){
		var _this=this;
		_this.elem.delegate(".select","change",function(){
			var _forumName=$(this).val();
			$("#sub-select").html(_this.ForumObj.subForumHtml[_forumName]);
		})
	},
	getHtml:function(){
		var _this=this
		var _html="";
		_html+=_this.ForumObj.forum;
        _html+='<ul class="sub-select" id="sub-select">'
		if(_this.ForumObj.subForumHtml[_this.originalForum]!=undefined){
			_html+=_this.ForumObj.subForumHtml[_this.originalForum];
		}
	    _html+='</ul>';

		_this.elem.html(_html);

		if(_this.originalForum!=""){
			_this.elem.find("option[value='"+_this.originalForum+"']").attr("selected",true);
			$("#sub-select").find("input[value='"+_this.subId+"']").attr("checked",true);
		}
	}
}
/**
 * BBS最终页标签跟改弹出层
 * */
var bbsTagPup=function(opts){
	this.originalTag="";
	//this.originalTag=opts.originalTag||"";

	this.callback=opts.callback;
	if($("#bbs-tagPup")[0]==undefined){
		this.elem=$('<div class="bbs-tagPup" id="bbs-tagPup"></div>');
		$("body").append(this.elem);
	}else{
		this.elem=$("#bbs-tagPup")
	}
	this.val="";
	this.init();
}
bbsTagPup.prototype={
	init:function(){
		var _this=this;
		_this.elem.delegate(".tagList b","click",function(){
			var _originalTag=_this.originalTag;
			var _i=$(this).parent().index();
			$(this).parent().remove();
			_originalTag.splice(_i,1);
			_this.val=_originalTag.toString();
		})
		_this.elem.delegate(".btn","click",function(){
			//判断是否为空，如果val为空，证明并未删除，返回原始数据
			if(_this.val==""){
				_this.val=_this.originalTag;
			}
			if($.trim($("#tagVal").val())!=""){
				_this.val+=","+$.trim($("#tagVal").val());
			}
			_this.callback(_this.val);
			_this.val="";
		})
	},
	getHtml:function(originalTag){
		var _html="";
		var _this=this;
	//	console.log(_this.originalTag)
		_html+='<h2 class="title">标签编辑<b class="close"></b></h2>';
		_html+='<div class="pupCont">';
		_html+='<div class="clearfix">';
		_html+='<label class="lable">当前标签：</label>';
		_html+='<ul class="tagList">';
		var _len=originalTag.length;
		for(var i=0;i<_len;i++){
			_html+=' <li>'+originalTag[i]+'<b></b></li>';
		}
		_html+='</ul>';
		_html+='</div>';
		_html+='<p class="setTagbox">';
		_html+='<label class="lable">新增标签：</label>';
		_html+='<input type="text" class="input-tx" id="tagVal">';
		_html+='</p>';
		_html+='<p class="btn-box">';
		_html+='<input class="btn" value="确定" type="button">';
		_html+='</p>';
		_html+='</div>';
		_html+='</div>';
		_this.elem.html(_html);
	},
	setPup:function(originalTag){
		var _this=this;
		_this.val="";
		_this.originalTag=originalTag;
		_this.getHtml(originalTag);
		//调用弹窗
		myPopup({
			"elem":_this.elem,
			"close":"#bbs-tagPup .close"
		})
	}
}