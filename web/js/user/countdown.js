function CountDown(json)
	{
		this.btn = document.getElementById(json.id);
		this.setFn = json.setFn;
		this.timer = null;
		this.bStop = true;
	}
	CountDown.prototype = {
		oEvent:function(){
			var _this = this;
				if(_this.bStop)
				{
					_this.setFn();
					_this.bStop = false;
					_this.btn.innerHTML = '60秒后可重新获取';
					_this.btn.className = 'btn_03';
					_this.Time();
				}
		},
		Time:function(){
			var _this = this;
			var iTime = 0;
			clearInterval(this.timer)
			this.timer = setInterval(function(){
				_this.btn.innerHTML = (59-iTime)+'秒后可重新获取';
				iTime++;
			if(iTime>60)
			{
				clearInterval(_this.timer);
				_this.btn.innerHTML = '免费获取验证码';
				_this.btn.className = 'btn_01';
				_this.bStop = true;
			}
			},1000)
		}
	}