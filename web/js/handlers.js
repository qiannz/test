
/* This is an example of how to cancel all the files queued up.  It's made somewhat generic.  Just pass your SWFUpload
object in to this method and it loops through cancelling the uploads. */


/* **********************
   Event Handlers
   These are my custom event handlers to make my
   web application behave the way I went when SWFUpload
   completes different tasks.  These aren't part of the SWFUpload
   package.  They are part of my application.  Without these none
   of the actions SWFUpload makes will show up in my application.
   ********************** */

function fileDialogStart() {
	/* I don't need to do anything here */
}
function fileQueued(file) {
	try {
	} catch (ex) {
		this.debug(ex);
	}
}
function fileQueueError(file, errorCode, message) {
	try {
		var _messge;
		switch(errorCode){
			case -110:
				_messge="<p>您上传的图片大小超过最大容量</p>";
				break;
			case -100:
				_messge="<p>您选择的文件数量超出最大限制</p>";
				break;
			case -120:
				_messge="<p>您选择的文件类型不对</p>";
				break;
			case -130:
				_messge="<p>您选择的文件类型不对</p>";
				break;
		}
		//文件选择错误
		global_pup({
			"message":_messge
		})
	} catch (ex) {
        this.debug(ex);
    }
	//return false;
}
function uploadError(file, errorCode, message) {
	try {

	} catch (ex) {
		this.debug(ex);
	}
}
function fileDialogComplete(numFilesSelected, numFilesQueued) {
	try {
		/* I want auto start and I can do that here */
		this.startUpload();
	} catch (ex)  {
        this.debug(ex);
	}
}
function uploadStart(file) {
	try {
	}
	catch (ex) {
	}
	return true;
}
function uploadProgress(file, bytesLoaded, bytesTotal) {
	try {
		$("#loadNum").html("<em>上传中...</em>")
		var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);
		$("#loadBar").css("width",percent+"%");
	} catch (ex) {
		this.debug(ex);
	}
}
function uploadSuccess(file, serverData) {
	try {
		postImage.getHtml(eval("("+serverData+")"));
	} catch (ex) {
		this.debug(ex);
	}
}
function uploadComplete(file) {
	try {
		//多图上传是否为最后一张0表示最后一张
		if (this.getStats().files_queued === 0) {
			$("#loadNum").html("<em>上传完成</em>")
			window.setTimeout(function(){
				$("#loadBar").css("width","0%");
				$("#loadNum").html("<em>等待上传</em>")
			},5000)
		} else {
			this.startUpload();
		}
	} catch (ex) {
		this.debug(ex);
	}
}
