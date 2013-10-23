<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title><?php echo Setting::get('site_title'); ?> - Under Construction</title>
	<?php $theme = Setting::get('public_theme'); ?>
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo rbUrl('content/themes/').$theme; ?>/assets/img/favicon.ico">
	<style type="text/css">
		body  {
			background: #f9f9f9;
		}
		#container {
			width: 780px;
			margin: 0 auto;
		}
		#body {
			font-size: 26px;
			margin: 20px 0;
			color: #575757;
			border-radius: 4px;
			text-align: center;
		}

		#site-title {
			padding: 10px;
			border-bottom: 1px solid #e9e9e9;
			margin-top: 0;
			text-align: left;
			font-size: 24px;
		}
		#site-title small {
			font-size: 18px;
		}
		#text-maintain h2 {
			padding: 30px 10px;
			border: 1px solid #efefef;
			background: #fff;
			margin-top: 60px;
		}
		#img-container {
			margin-top: 50px;
		}
		#img-container p {
			margin: 0 0 5px 0;
			font-size: 16px;
		}
		#img-container img {
			background: #7b0000;
			padding: 10px;
			border-radius: 4px;
		}
	</style>
</head>
<body>
	<div id="container">
		<div id="body">
			<div id="text-maintain">
				<h1 id="site-title">
					<?php echo Setting::get('site_title'); ?>
					<small>{{ <?php echo Setting::get('site_slogan'); ?> }}</small>
				</h1>
				<h2>This site is under construction!</h2>
			</div>
			<div id="img-container">
				<p>Powered by </p>
				<?php echo global_asset('img', 'reborncms.png'); ?>
			</div>
		</div>
	</div>
</body>
</html>
