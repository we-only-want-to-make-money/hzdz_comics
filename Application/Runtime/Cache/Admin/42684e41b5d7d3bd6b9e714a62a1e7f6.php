<?php if (!defined('THINK_PATH')) exit();?><html>
	<head>
		<meta charset="tuf8">
		<style>
		body{ padding:0; margin:0; background:#eee;}
		img.img,.file{ position:absolute; top:0; left:0; width:100%; height:100%;}
		.file{ z-index:888; opacity:0;}
		</style>
	</head>
	<body>
	<form method="post" name="fileForm" id="fileForm" enctype="multipart/form-data">
		<input type="file" name="file" onchange="uplaod()" class="file" />
		<?php if(empty($url)): ?><img src="/Public/images/upload.jpg" class="img" />
		<?php else: ?>
			<img src="<?php echo ($url); ?>" class="img" /><?php endif; ?>
	</form>
	<script>
	function uplaod(){
		fileForm.submit();
	}
	<?php if(!empty($errmsg)): ?>alert('<?php echo ($errmsg); ?>');<?php endif; ?>
	
	<?php if(!empty($url)): if($_GET['cur_index'] != ''): ?>parent.cur_index = <?php echo ($_GET['cur_index']); ?>;<?php endif; ?>
		parent.<?php echo ($_GET['event']); ?>('<?php echo ($url); ?>');<?php endif; ?>
	</script>
	</body>
</html>