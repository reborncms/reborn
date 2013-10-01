<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Exception Information for Reborn CMS</title>

	<link rel="shortcut icon" type="image/x-icon"
			href="<?php echo GLOBAL_URL; ?>favicon.ico">

	<style type="text/css">
		body {
			font: 14px consolas, Monnaco, helvetica, arial, sans-serif;
			color: #2B2B2B;
			background-color: #EFEFEF;
			padding:0;
			margin: 0;
			max-height: 100%;
		}
		.left_side {
			position: fixed;
			overflow: auto;
			z-index: 200;
			top: 0;
			left: 0;
			width: 35%;
		}
		.black_box {
			background: #333;
			text-shadow: 2px 2px 1px rgba(0, 0, 0, 0.6);
			border-right: 1px solid #fff;
			border-bottom: 1px solid #fff;
			margin: 0 20px 20px 0;
			font-size: 22px;
			color: #fff;
			font-family: Verdana, helvetica, arial, sans-serif;
		}
		.black_box span {
			display: block;
			line-height: 1.7em;
			color: #F24F4F;
		}
		.black_box p {
			display: block;
			line-height: 1.5em;
			margin: 0;
		}

		#rb_exception_header {
			width: 65%;
			padding: 1% 1%;
		}

		#rb_exception {
			width: 98%;
			padding: 1% 1% 10% 1%;
		}
		.code {
			display: none;
		}
		pre {
			border: 5px solid #9f9f9f;
			margin: 0;
			background: #535353;
			overflow: auto;
			line-height: 1.7em;
			color: #9AD245;
		}
		pre strong {
			background: #c9c9c9;
			color: #D64926;
			width: 100%;
			display: block;
			font-weight: normal;
		}

		.rb_main_wrap {
			position: relative;
			left: 38%;
			width: 60%;
		}

		.rb_exception_trace {
			padding: 1% 1% 1% 0;
		}

		.trace_wrap {
			background: #fff;
			border: 1px solid #ababab;
			margin: 10px auto;
		}
		.trace_head {
			padding: 10px;
			color: #333;
			background: #cdcdcd;
			cursor: pointer;
		}
		.trace_body {
			padding: 15px 10px;
			color: #2D3E50;
			position: relative;
			border-top: 1px solid #b9b9b9;
		}
		.line_no {
			position: absolute;
			right: 0;
			display: block;
			padding: 12px 10px 13px 10px;
			min-width: 35px;
			text-align: right;
			color: #7B0000;
			background: #efefef;
			border-left: 1px solid #b9b9b9;
			bottom: 0;
			font-size: 18px;
			font-weight: bold;
		}
	</style>

</head>
<body>
	<div class="left_side">
		<div id="rb_exception_header" class="black_box">
			<span>Reborn CMS</span>
			<p>Exception Information.</p>
		</div>
		<div id="rb_exception" class="black_box">
			<span id="rb_exc_caller"><?php echo $caller; ?></span>
			<p id="rb_ex_message">[ ! ] <?php echo $message; ?></p>
		</div>
	</div>


	<div class="rb_main_wrap">
		<div class="rb_exception_trace">
			<div class="trace_wrap">
				<div class="trace_head">
					<?php echo $caller; ?>
					Throw Exception in here.
				</div>
				<div class="trace_body">
					<?php echo str_replace(BASE, 'BASE'.DS, $file); ?>
					<span class="line_no"><?php echo $line; ?></span>
				</div>
				<div class="code">
					<?php echo $code; ?>
				</div>
			</div>

		<?php foreach($traces as $t) : ?>
			<div class="trace_wrap">
				<div class="trace_head">
					<?php echo $this->getClass($t); ?>
					<?php echo $this->getFunction($t); ?>
				</div>
				<div class="trace_body">
					<?php echo $this->getFile($t, true); ?>
					<span class="line_no"><?php echo $this->getLine($t); ?></span>
				</div>
				<div class="code">
					<?php echo $this->getCodeLine($t); ?>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
	</div>

	<?php echo global_asset('js', 'jquery.min.js'); ?>
	<script type="text/javascript">
		$('.trace_head').click(function() {
			var par = $(this).parent(),
				code = $(par).find('.code');
			$('.code:visible').not(code).hide();
			$(code).slideToggle();
		})
	</script>

</body>
</html>





