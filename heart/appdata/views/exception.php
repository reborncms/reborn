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
			height: 100%;
			background: #333;
		}
		.black_box {
			text-shadow: 2px 2px 1px rgba(0, 0, 0, 0.2);
			margin: 0 20px 20px 0;
			font-size: 18px;
			color: #fff;
			box-sizing: border-box;
			-moz-box-sizing:border-box;
			font-family: Verdana, helvetica, arial, sans-serif;
		}
		.black_box span {
			display: block;
			line-height: 1.7em;
			color: #DCE185;
			border-bottom: 1px dashed #454545;
		}
		.black_box p {
			display: block;
			line-height: 1.5em;
			margin: 0 0 10px 0;
		}

		#rb_exception_header {
			width: 65%;
			padding: 5px 1% 80px;
			background: url(<?php echo GLOBAL_URL; ?>assets/img/reborncms.png) center bottom no-repeat;
		}

		#rb_exception {
			width: 98%;
			padding: 1% 1% 10% 5%;
			margin: 0;
		}
		#rb_ex_is strong {
			font-size: 13px;
			font-weight: normal;
		}
		#rb_ex_is small {
			display: block;
			font-size: 13px;
		}
		.code {
			display: none;
		}
		pre {
			border: 2px solid #dfdfdf;
			margin: 0;
			background: #333;
			overflow: auto;
			line-height: 1.5em;
			color: #CDE64B;
		}
		pre strong {
			background: #E1E7A7;
			color: #1078C2;
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
			background: #F8F8F8;
			border: 1px solid #D3C6C6;
			margin: 10px auto;
		}
		.trace_head {
			padding: 10px;
			color: #fff;
			background: #333;
			cursor: pointer;
			position: relative;
		}
		.trace_body {
			padding: 15px 10px;
			color: #286BB3;
			position: relative;
			border-top: 1px solid #b9b9b9;
		}
		.line_no {
			bottom: 8px;
		    color: #DCE185;
		    font-size: 16px;
		    position: absolute;
		    right: 7px;
		    text-align: right;
		}
	</style>

</head>
<body>
	<div class="left_side">
		<div id="rb_exception_header" class="black_box">
		</div>
		<div id="rb_exception" class="black_box">
			<p><span id="rb_exc_caller">Exception Class !</span><?php echo $caller; ?></p>
			<p id="rb_ex_message"><span>Exception Message !</span> <?php echo $message; ?></p>
			<p id="rb_ex_is">
				<span>Exception From !</span>
				<strong><?php echo str_replace(BASE, '{{ CMS }} &raquo; ', $file); ?></strong>
				<small># Line No. <?php echo $line; ?></small>
			</p>
		</div>
	</div>


	<div class="rb_main_wrap">
		<div class="rb_exception_trace">
			<div class="trace_wrap">
				<div class="trace_head">
					<?php echo $caller; ?>
					{ Throw Exception in here. }
					<span class="line_no"># Line No. <?php echo $line; ?></span>
				</div>
				<div class="trace_body">
					<?php echo str_replace(BASE, '{{ CMS }} &raquo; ', $file); ?>
				</div>
				<div class="code">
					<?php echo $code; ?>
				</div>
			</div>

		<?php foreach($traces as $t) : ?>
			<div class="trace_wrap">
				<div class="trace_head">
					<?php echo $this->getClass($t); ?><?php echo $this->getFunction($t); ?>
					<span class="line_no"># Line No. <?php echo $this->getLine($t); ?></span>
				</div>
				<div class="trace_body">
					<?php echo $this->getFile($t, true); ?>
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





