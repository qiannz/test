var bStop = true
function CountDown(json)
	{		
		if(!(this instanceof CountDown))
			return new CountDown(json)
		this.btn = document.getElementById(json.id);
		this.setFn = json.setFn;
		this.timer = null;
		this.oEvent();
		
		
		
	}
	CountDown.prototype = {
		oEvent:function(){
			var _this = this;
				if(bStop)
				{
					_this.setFn();
					bStop = false;
					_this.btn.value = '30秒后重新发送';
					_this.btn.className = 'button_off';
					_this.Time();
				}
		},
			
		Time:function(){
			var _this = this;
			var iTime = 0;
			clearInterval(this.timer)
			this.timer = setInterval(function(){
				_this.btn.value = (29-iTime)+'秒后重新发送';
				iTime++;
			if(iTime>30)
			{
				clearInterval(_this.timer);
				_this.btn.value = '发送';
				_this.btn.className = 'button_on';
				bStop = true;
			}
			},1000)
		}
		
	}
	
//	function fnEvent(id){
//			var obj = document.getElementById(id),
//				ClickEvent= new CountDown({id:id});
//				
//			obj.onclick = function(){
//				
//				ClickEvent.oEvent()
//			}
//	}