/**
 * Created by mephisto on 2016/1/26.
 */



var gameCanvas = function(res){

    return new game(res);
}

var game = function(res){
    this.canvas = document.querySelector('#canvas');
    this.context = this.canvas.getContext('2d');
    this. pushObj = new Array();
    this.iCur = this.imgIndex = 0;
    this.res = res;
    this.imgData = null;
    this.winTxt = [];
	this.callback = null;
}


game.prototype = {

    //移动事件
    touchStart:function(obj,callback){

        obj.addEventListener('touchstart',callback,false);
    },

    touchMove:function(obj,callback){

        obj.addEventListener('touchmove',callback,false);
    },

    touchEnd:function(obj,callback){

        obj.addEventListener('touchend',callback,false);

    },

    //删除移动事件
    removeTouchStart:function(obj,callback){

        obj.removeEventListener('touchstart',callback,false);
    },

    removeTouchMove:function(obj,callback){
        obj.removeEventListener('touchmove',callback,false);
    },
    removeTouchEnd:function(obj,callback){
        obj.removeEventListener('touchend',callback,false);

    },

    // 加载图片
    loadImg:function(callback){
        var img = new Image(),
        that = this;
        img.src = that.res[that.imgIndex];

        img.onload = function(){
            that.pushObj[that.imgIndex] = this;
            if(that.imgIndex >= that.res.length -1){
                callback(that.pushObj);
                return;
            }
            that.imgIndex++;
            that.loadImg(callback);
        }

    },
    drawText:function(txt,size,x,y){
        var w = null;
        this.context.save();
        this.context.fillStyle = '#714823';
        this.context.font = size + ' yahei';
        this.context.textBaseline = 'top';
        w = this.context.measureText(txt);
        this.context.fillText(txt,(canvas.width - w.width)/2,y);

        this.context.restore();
    },
    init:function(text,callback){
        var that = this;
        this.winTxt = text;
		this.callback = callback;
        this.loadImg(function(arr){
            //
            that.drawBg('#e5e5e5');
            that.addTouchEvent(arr);

        })
    },
    getParent:function (obj,sClass){
        var top = obj.parentNode.offsetTop;
        var parent = obj.parentNode;
        do{
            parent = parent.parentNode;
            top+=parent.offsetTop;
        } while(parent.className == sClass);
        return top;
    },

    drawBg:function(bgColor){
        this.context.beginPath();
        this.context.fillStyle = bgColor;
        this.context.fillRect(0,0, this.canvas.width, this.canvas.height);
        this.context.closePath();

    },
    drawClip:function(x,y){

        var dx = x - (document.body.clientWidth - this.canvas.offsetWidth)/2 ;
        var dy = y - this.getParent(this.canvas,'game-box');
        var scaleX = this.canvas.width / this.canvas.offsetWidth;
        var scaleY = this.canvas.height / this.canvas.offsetHeight;

        this.context.beginPath();
        this.context.arc(dx*scaleX,dy*scaleY,50,0,Math.PI/180*360,false);
        this.context.clip();
        this.context.closePath();

    },
    addTouchEvent:function(arr){

        var that = this,
        pointMove  = function(){
            event.preventDefault();
            var x = event.targetTouches[0].pageX,
            y = event.targetTouches[0].pageY;

            that.context.save();
            that.drawClip(x,y);
            that.context.drawImage(arr[0],0,0,that.canvas.width,that.canvas.height);
            that.drawText(that.winTxt[0],'60pt',200,80);
            that.drawText(that.winTxt[1],'22pt',50,160);
            that.context.restore();
        },
        pointEnd = function(){
            var sum = 0,max = 0;
            that.imgData =  that.context.getImageData(0,0, that.canvas.width, that.canvas.height);
            for(var i = 0 ;i<that.imgData.data.length;i+=4){

                if(that.imgData.data[i] == 229){
                    sum++;
                }
            }
            max = sum/(that.imgData.data.length/4);

            if(max < 0.5){
                that.removeTouchStart(that.canvas,pointMove);
                that.removeTouchMove(that.canvas,pointMove);
                that.removeTouchEnd(that.canvas,pointEnd);
                that.context.drawImage(arr[0],0,0,that.canvas.width,that.canvas.height);
                that.drawText(that.winTxt[0],'60pt',200,80);
                that.drawText(that.winTxt[1],'22pt',50,160);
				that.callback();
            }

        };
        this.touchStart(that.canvas,pointMove);
        this.touchMove(that.canvas,pointMove);
        this.touchEnd(that.canvas,pointEnd)
    }

}



