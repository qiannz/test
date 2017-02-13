function HoverBg()
{
	if(!(this instanceof HoverBg))
		return new HoverBg();
	this.aList = document.getElementById('listtxt').getElementsByTagName('tr');
	this.hover();
}
HoverBg.prototype.hover = function()
{
	var _this = this;
	  for(var i=0;i<this.aList.length;i++)
		{
				this.aList[i].sClass = this.aList[i].className;
				this.aList[i].onmouseover = function()
				{
					  this.className = 'selbg'
				}
				
				this.aList[i].onmouseout = function()
				{
					  this.className = this.sClass;
				}
		}
}