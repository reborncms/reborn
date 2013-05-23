<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title>Maintainance Mode</title>
	<?php $theme = Setting::get('public_theme'); ?>
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo rbUrl('content/themes/').$theme; ?>/assets/img/favicon.ico">
	<style type="text/css">
		body  {
			background: #efefef;
		}
		#container, #rb_profiler {
			width: 980px;
			margin: 0 auto;
		}
		#body {
			padding: 20px 20px;
			font-size: 28px;
			border: 1px solid #dedede;
			background: #fff;
			margin: 20px 0;
			color: #882020;
			border-radius: 4px;
			box-shadow: inset 0 0 2px rgba(0, 0, 0, 0.2);
			text-align: center;
		}
		#rb_profiler {
			text-align: center;
			font-size: 16px;
			color: #882020;
		}
	</style>
</head>
<body>
	<div id="container">
		<div id="body">
			<h3>Site is currently maintain.</h3>
		</div>
	</div>
</body>
</html>
