$(document).ready(function(){
    
    timeFn($('.over-time'));

});

var timeFn = function(id){
	
	var id = id,
    getTime = function(time,obj){

        var day = Math.floor(time/86400),
            hour = Math.floor((time - day*86400)/3600),
            min =  Math.floor((time - day*86400 - hour*3600)/60),
            sec = time - day*86400 - hour*3600 - min*60,
            timer = null,
            html;
        var timeStr = "";
        var status = obj.data("status");
        if( "in" == status ){
        	timeStr = "距离结束";
        }else if("new" == status ){
        	timeStr = "距离开始";
        }else if("expire" == status ){
        	timeStr = "距离开奖";
        }
        if( parseInt(time) ==0){
            obj.text( obj.data("datetime") );
            return;
        }
        timer = setInterval(function(){
            sec--;
            if(sec<0){
                sec = 59;
                min--;
            }
            if( min<0){
                min = 59;
                hour--;
            }
            if(hour<0){
                hour = 23;
                day--;
            }
            if( day<=0 && hour<=0 && min<=0 && sec<=0){
               clearInterval(timer);
            }

            html = timeStr+' '+doubleNum(day)+'天 '+doubleNum(hour)+':'+doubleNum(min)+':'+doubleNum(sec);
            obj.text(html);
        },1000);

    },
    doubleNum = function(num){
        if(num<10 && num>=0){
            return '0'+num;
        }
        return num;
    };

    id.each(function(){
        var overTime = $(this).attr('data-value');
        getTime(overTime,$(this));
    })
	
}

var processFn = function( id ){
  var id = id;
	id.each(function(){
		var bar = $(this).find('.bar-color'),
			clear = $(this).find('.clear'),
			stock = $(this).find('.stock'),
			sum = parseInt(clear.text())+parseInt(stock.text()),
			value  = (parseInt(clear.text())/sum*100).toFixed(2);
			value = (value<1 && value>0)?1:parseInt(value);

		bar.css('width', value+'%').text(value+'%');
	});
}