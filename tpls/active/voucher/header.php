<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Expires" content="0">
<meta name="viewport" content="width=device-width">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no"/>
<meta name="description" content="">
<meta name="keywords" content="">
<title>{{$title}}</title>
<link href="/css/active/voucher/common.css?version={{$version}}" rel="stylesheet" type="text/css">
<script type="text/javascript" src="http://mpimg.cn/web/20120615/jquery.js"></script>
<script type="text/javascript">
//screen width
var os = navigator.userAgent.toLowerCase(),
deviceWidth =$(window).innerWidth(),
setScreenWidth = 640,
phoneScale = deviceWidth/setScreenWidth;

if (os.indexOf("android") != -1) {


	if( parseFloat(os.slice(os.indexOf("android")+8)) > 4){

$('meta[name="viewport"]').attr('content', 'width='+setScreenWidth+',minimum-scale =' + phoneScale +' maximum-scale = '+phoneScale+' , target-densitydpi=device-dpi');


	}else{

 $('meta[name="viewport"]').attr('content', 'width= '+setScreenWidth  +', target-densitydpi=device-dpi');

			
	}
}else{
	 $('meta[name="viewport"]').attr('content', 'width='+setScreenWidth+', target-densitydpi=device-dpi,user-scalable=no');
}

//canvas
$(function(){
    if(!document.getElementById('canvas')){
        return false
    }
    var canvas = document.getElementById('canvas'),
        context = canvas.getContext('2d'),
        img = new Image(),
        icount = 0,
        times = 0,
        setMs = 2500,
        max = setMs/40,
        eleSize = 186,
        iLen = 6;

        function draw(icount){
            var x = eleSize*icount;
            context.clearRect(0,0,canvas.width,canvas.height)
            context.drawImage(img,x,0,eleSize,eleSize,0,0,eleSize,eleSize);
        }
        function loop(){
            icount++;
            times++;
            if(icount>iLen ){
                icount = iLen ;
            }
            if(times>max){
                times = icount = 0;
            }
            setTimeout(loop,40);
            draw(icount);
        }

        img.src="/images/active/voucher/c.png";
        img.onload =loop;

});
</script>
</head>