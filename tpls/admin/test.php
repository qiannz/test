<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>无标题文档</title>
<link rel="stylesheet" type="text/css" href="/js/uploadify/uploadify.css" />
<script charset="utf-8" type="text/javascript" src="/js/jquery.js"></script>
<script charset="utf-8" type="text/javascript" src="/js/uploadify/jquery.uploadify.min.js"></script>

</head>

<body>
<div id="fileQueue"></div>
<input type="file" id="uploadFile" />
<script>
	$('#uploadFile').uploadify({
		//'formData': {session_name : session_id},
		'debug' : false,
		//在浏览窗口底部的文件类型下拉菜单中显示的文本
		'fileTypeDesc': 'Image Files',
		//允许上传的文件后缀
		'fileTypeExts': '*.gif; *.jpg; *.png',
		'cancelImage': '/js/uploadify/uploadify-cancel.png',
		'swf'      : '/js/uploadify/uploadify.swf',
		'uploader' : '/home/test/upload',
		'height': 20,
        'width': 80,
		'buttonText' : '图片上传',
		'queueID' : 'fileQueue',//显示上传文件队列的元素id，可以简单用一个div来显示
		'onUploadComplete' : function(file) {
								//alert(file.filestatus)
								//alert('The file ' + file.name + ' finished processing.');
							},
		'onUploadSuccess' : function(file,data,response) {//上传完成时触发（每个文件触发一次）
						　
/*									alert( 'id: ' + file.id
						　　+ ' - 索引: ' + file.index
						　　+ ' - 文件名: ' + file.name
						　　+ ' - 文件大小: ' + file.size
						　　+ ' - 类型: ' + file.type
						　　+ ' - 创建日期: ' + file.creationdate
						　　+ ' - 修改日期: ' + file.modificationdate
						　　+ ' - 文件状态: ' + file.filestatus
						　　+ ' - 服务器端消息: ' + data
						　　+ ' - 是否上传成功: ' + response);*/
						$('#' + file.id).find('.data').html(' 上传完毕');
						
						},
		'onSelect' : function(file) {//当每个文件添加至队列后触发
						 /*								　　
							alert( 'id: ' + file.id
						　　+ ' - 索引: ' + file.index
						　　+ ' - 文件名: ' + file.name
						　　+ ' - 文件大小: ' + file.size
						　　+ ' - 类型: ' + file.type
						　　+ ' - 创建日期: ' + file.creationdate
						　　+ ' - 修改日期: ' + file.modificationdate
						　　+ ' - 文件状态: ' + file.filestatus);*/
						}
	});
</script>
</body>
</html>