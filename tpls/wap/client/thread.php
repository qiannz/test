<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Expires" content="0">
<meta name="format-detection" content="telephone=no"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<title>名品导购网</title>
<style type="text/css">
html,body,div,span,h1,h2,h3,h4,h5,h6,p,a,em,img,q,s,small,strong,button,input,select,textarea,b,u,i,dl,dt,dd,ol,ul,li,fieldset,form,label,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,canvas,embed,figure,figcaption,footer,header,menu,nav,output,section,summary,time,mark,audio,video{margin:0;padding:0;border:0;font-size:100%;font:inherit;vertical-align:baseline;}html{font-size:100%;overflow-y:scroll;-webkit-text-size-adjust:none;}ol,ul{list-style:none;}blockquote,q{quotes:none;}blockquote:before,blockquote:after,q:before,q:after{content:'';content:none;}table{border-collapse:collapse;border-spacing:0;}/* ==|== Clearing Float ====================================================== */.clearfix:before,.clearfix:after{content:"";display:table;}.clearfix:after{clear:both;overflow:hidden;}.clearfix{zoom:1;}/* ==|== Public Style ====================================================== */a,a:visited{text-decoration:none;outline:none;}a:hover,a:active{text-decoration:none;}body,button,input,select,textarea{background:#fff;-webkit-font-smoothing:antialiased;}article,aside,footer,header,menu,nav,section{display:block;}img{max-width:100%;height:auto;vertical-align:top;}.video embed,.video object,.video iframe{width:100%;height:auto;}input[type="button"],input[type="submit"],input[type="radio"]{-webkit-appearance:none;outline:none}
body{
	font-family:"Microsoft YaHei";
	font-size:12px;
	background:#fff;
}

img{ 
	vertical-align:top;

  }
html,body{
   height: 100%;
}



.viewport{
    max-width: 640px;
    min-width: 320px;
    width:100%;
    margin: 0 auto;
    min-height: 100%;
    background: #eee;
}
.login-wrap{
    height:100%;
    background: #3b3663;
}

.login-box{
    padding-top: 65px;
    width:81.25%;
    margin: 0 auto;
}

.login-col{
    display: -webkit-flex;
    display: flex;
    -webkit-align-item:center;
    align-item:center;
    -webkit-box-pack:  justify;
    box-pack:  justify;

    display: -webkit-box;
    display: box;
    box-align:center;
    -webkit-box-align:center;
    -webkit-justify-content:space-between;
    justify-content:space-between;

    border: 1px solid #b6b6b6;

    height:35px;
    border-radius: 18px;
}

.input_50{
    display: block;
    width:50%;
    height: 30px;
    background: none;

    color:#b6b6b6;
    margin-left: 16px;
    outline: none;
    font-size: 14px;
}
.input_100{
    display: block;
    width:100%;
    height: 30px;
    background: none;

    color:#b6b6b6;
    margin-left: 16px;
    outline: none;
    font-size: 14px;
}
.yzm-on,.yzm-off{
    display: block;
    width:40%;
    height: 35px;
    border-radius: 18px;
    text-align: center;
    position: relative;
    top:0;
    right:-1px;

}
.yzm-on{
    background: #ff4062;
    color:#fff;
}
.yzm-off{
    background: #ccc;
    color:#808080;
}
.login-error{
    height:30px;
    padding-left: 16px;
    color:#fff;
    font-size: 12px;
}


.login-btn{
    display: block;
    width:100%;

    height:35px;
    border-radius: 18px;

    color: #fff;
    background: #ff4062;
    font-size: 16px;
}
/*消息*/
.header{
    height:50px;
    background: #292d4b;
    font-size:18px;
    color:#fff;
    text-align: center;
    line-height:50px;
    position: relative;
    z-index: 1;
    border-bottom: 1px solid #a9aab0;
}

.refresh{
    position: absolute;
    right:15px;
    top:10px;
    width:30px;
    height:30px;
    background: url(/images/commodity/refresh-icon.png) no-repeat;
    background-size: contain;


}
.go-back{
    position: absolute;
    left:15px;
    top:10px;
    width:30px;
    height:30px;
    background: url(/images/commodity/go-back.png) no-repeat;
    background-size: contain;
}
.go-back-thread{
    position: absolute;
    left:15px;
    top:10px;
    width:30px;
    height:30px;
	font-size:14px;
	color:#fff;
	line-height:30px;
}
.del{
    position: absolute;
    right:15px;
    top:10px;
    width:30px;
    height:30px;
    background: url(/images/commodity/delete.png) no-repeat;
    background-size: contain;


}
/*消息列表*/
.news-list{
    padding:5px 5px 45px;
}

.news-col{
    overflow: hidden;
    background: #fff;
    position: relative;
    z-index: 1;
    margin-bottom: 5px;
}


.news-img-box{
    float: left;
    position: relative;
    z-index: 1;
    width:30%;
}

.news-img{
    position: absolute;
    width:100%;
    height: 100%;
    left:0;
    top:0;
    background-repeat: no-repeat;
    background-position:center center;
    z-index: 10;
}

.news-right{
    float: right;
    width:68%;
    padding-top:5px;
}

.news-title{
    font-size: 16px;
    color:#333;
    width:75%;
    display: block;
    height: 65px;
    overflow: hidden;
    text-overflow: ellipsis;
}

.red-num,.blue-num{
    width:40px;
    height: 40px;
    line-height: 40px;
    text-align: center;
    color: #fff;
    font-size: 14px;
    position: absolute;
    top:50%;
    right:10px;
    margin-top: -20px;

}

.red-num{
    background: url(/images/commodity/red-num.png) no-repeat;
    background-size: contain;
}

.blue-num{
    background: url(/images/commodity/blue-num.png) no-repeat;
    background-size: contain;
}

.time{
    position: absolute;
    left:32%;
    bottom:2px;
    height:18px;
    background: url(/images/commodity/time.png) no-repeat;
    background-size:auto 100%;
    padding-left:22px;
    line-height:18px;
    color:#666;
    font-size: 12px;
}


.popup-wrap{
    position: fixed;
    z-index: 9999;
    left:0;
    bottom: 0;
    width: 100%;
}

.return-home{
    max-width: 640px;
    min-width: 320px;
    width: 100%;
    height:40px;
    background: rgba(0,0,0,0.8);
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.link-home{
    width: 30px;
    height: 30px;
    position: absolute;
    top:5px;
    left:50%;
    margin-left: -15px;
    background: url(/images/commodity/home-icon.png) no-repeat;
    background-size: auto 100%;

}

/*对话*/
.talk-wrap{
    padding: 5px;
}
.shop-title{
    font-size:18px;
    padding-right:5px;
    height:45px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    display: block;
    line-height: 45px;

}
.shop-price{
    display: block;
    font-size: 22px;
    color:#414878;
    position: relative;
    top:-5px;
}
.shop-price:before{
    display: inline-block;
    content: "￥";

}

.talk-list{
    padding: 20px 10px 45px;
    background: #fff;
}

.talk-info{
    overflow: hidden;
}

.left{
    float:left;
}
.right{
    float:right;
}



.head-pic{
    display: inline-block;
    width:40px;
    height: 40px;
    background-position: center center;
    background-size: contain;
    background-repeat: no-repeat;
    border-radius: 20px;
    vertical-align: middle;
    position: relative;
    left: 0;
    bottom: -5px;
}


.name{
    font-size: 14px;
    color:#333;
    position: relative;
    left: 0;
    bottom: -5px;
}

.color{
    font-size: 12px;
    color:#666;
    line-height: 40px;
    position: relative;
    left:0;
    bottom: -5px;
}


.msg-box{
    width:88%;
    font-size: 16px;
    padding:5px;
    box-sizing: border-box;
    -webkit-box-sizing: border-box;
    border-radius: 5px;
    min-height: 60px;
    line-height: 22px;
    border: 1px solid #d9d9d9;
}

.blue-bg{
    background: #cfd3ec;
    color:#414878;

}


.gray-bg{
    background: #f2f2f2;
    color:#666;

}


.look-more{
    text-align: center;
    font-size: 14px;
    line-height: 20px;
    margin-top: 5px;
}

.color-pink{
    color:#ff4062;
}

.input-wrap{
    max-width: 640px;
    min-width: 320px;
    width: 100%;
    margin: 0 auto;
    height: 45px;
    background: #fff;
    border-top:1px solid #dfdfdf;
    position: relative;
    z-index: 1;
}
.input-icon{
    display: inline-block;
    width:48px;
    height: 48px;
    background: url(/images/commodity/input-icon.png) no-repeat center center;
    background-size: contain;
    position: relative;
    left:10px;
    top:-10px;
}

.send-btn{
    width:15%;
    height: 28px;
    position: absolute;
    right:15px;
    top:50%;
    margin-top: -14px;
    background: #292d4b;
    color:#fff;
    border-radius: 2px;
    font-size: 14px;
}

.text-box{
    width:60%;
   display: inline-block;
    height: 30px;
    vertical-align: top;
    margin-left: 10px;
    margin-top: 5px;
    border-bottom: 1px solid #d7d7d7;
    position: relative;
    z-index: 1;
}

.text-box textarea{
    height: 30px;
    width:100%;
    resize: none;
    font-size: 14px;
    outline: none;
    padding: 0 5px;
    box-sizing: border-box;
    -webkit-box-sizing: box-sizing: border-box;;
    line-height: 30px;

}

.text-box:before{
    content: '';

    height: 17px;
    width: 1px;
    background: #d7d7d7;
    overflow: hidden;
    position: absolute;
    bottom:0;
    left:0;
}

.text-box:after{
    content: '';

    height: 17px;
    width: 1px;
    background: #d7d7d7;
    overflow: hidden;
    position: absolute;
    bottom:0;
    right:0;
}
</style>
</head>
<body>
    <div class="viewport">
        <!--header-->
        <div class="header">
        <a class="go-back-thread" href="/home/message/logout">退出</a>
        消息
        <a class="refresh" href="/home/message/thread"></a>
        </div>
        <!--消息-->
        <div class="news-list">
			{{foreach from=$data item=item}}
            <a href="/home/message/post/tid/{{$item.tid}}/frid/{{$item.from_id}}">
            <div class="news-col">
                    <!--图片-->
                    
                    <div class="news-img-box">
                        <span class="news-img" style="background-image: url({{$item.img_first.img_url}})" ></span>
                        <img src="/images/commodity/news-blank.png">
                    </div>
                    <!--右-->
                    <div class="news-right">
                            <span class="news-title">{{$item.question}}</span>
                    </div>
                   <span class="{{if $item.reply_time eq 0}}red-num{{else}}blue-num{{/if}}">{{$item.floors}}</span>
                   <p class="time">{{$item.format_time}}</p>
                   
            </div>
            </a>
			{{/foreach}}
        </div>
    </div>

    <!----返回首页--->
    <div class="popup-wrap">
            <div class="return-home">
                    <a class="link-home"></a>
             </div>

    </div>
</body>
</html>