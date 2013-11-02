<style type="text/css">
	#reborn-profiler {
		position:fixed !important;
		bottom:0 !important;
		right:0 !important;
		width:100%;
		z-index: 9999 !important;
		color: #ffffff;
		border-top: 3px solid #454545;
		font-family:Helvetica, "Helvetica Neue", Arial, sans-serif !important;
		font-size:14px !important;
		background-color:#EFEFEF !important;
	}
	#reborn-profiler.rbp-non-fixed {
		position:relative !important;
	}
	#rbp-mode {
		position: absolute;
		right: 10px;
		top: 30px;
		display: block;
		padding: 3px 10px;
		font-size: 12px !important;
	}
	#rbp-name {
		position: absolute;
		right: 10px;
		top: -3px;
		display: block;
		padding: 5px;
		font-size: 18px !important;
		text-shadow: 1px 1px 0 rgba(255, 255, 255, 0.3);
		color: #7B0000 !important;
	}
	#rbp-linker {
		height: 45px;
		padding: 4px 10px 0 15px;
		background-color:#222 !important;
		margin: 0;
	}

	.rbp-color-red {
		background: #E54B4B !important;
		color: #FFFFFF !important;
	}
	.rbp-color-green {
		background: #56BE8E !important;
		color: #FFFFFF !important;
	}
	.rbp-color-blue {
		background: #0082C8 !important;
		color: #FFFFFF !important;
	}
	.rbp-color-orange {
		background: #F17541 !important;
		color: #FFFFFF !important;
	}

    .rbp-tab { display: inline-block; zoom:1; *display:inline; background: #eee; margin: 0; }
    .rbp-tab a { line-height: 2em; display: block; padding: 3px 16px; outline: none; }
    .rbp-tab.active { background: #fff; position: relative; }
    .rbp-panel-container { margin-bottom: 10px; padding: 10px; }
    .rbp-panel-container h2 {

    	font-size: 22px;
    }
    .rbp-panel-container h2 small {
		font-size: 16px;
	}
    .rbp-panel-container .rbp-panel {
    	padding: 10px;
    	/*background: #efefef;*/
    }
    .rbp-panel-container table {
    	width: 100%;
    }
    .rbp-panel-container table thead {
    	background: #E54B4B;
    }
    .rbp-panel-container table thead td {
    	color: #FFFFFF;
    }
    .rbp-panel-container table td {
    	border: 1px solid #E46A68;
    	color: #565656;
    }
    .rbp-panel-container pre {
    	color: #4E6184;
    }
    #rbp-files-wrap {
    	height: 250px;
    	overflow: auto;
    }
</style>

<div id="reborn-profiler">

	<h1 id="rbp-name">Reborn CMS Profiler</h1>
	<a href="#" id="rbp-mode" class="rbp-color-orange">Change Profiler Position Mode</a>

	<ul class="rbp-tabs" id="rbp-linker">
		<li class="rbp-tab"><a href="#rbp-request" class="rbp-color-green">Request Data</a></li>
		<li class="rbp-tab"><a href="#rbp-querys" class="rbp-color-red">Querys</a></li>
		<li class="rbp-tab"><a href="#rbp-passing" class="rbp-color-blue">Passing Data</a></li>
		<li class="rbp-tab"><a href="#rbp-time" class="rbp-color-green">Times</a></li>
		<li class="rbp-tab"><a href="#rbp-memory" class="rbp-color-red">Memory</a></li>
		<li class="rbp-tab"><a href="#rbp-files" class="rbp-color-blue">Files</a></li>
	</ul>

	<div class="rbp-panel-container">

		<div id="rbp-request">
			<h2>Request Data</h2>
			<div class="rbp-panel">
				<table>
					<tbody>
						<?php foreach ($request as $n => $d) : ?>
						<tr>
							<td><?php echo $n ;?></td>
							<td><?php echo $d ;?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div> <!-- end of div#rbp-request -->

		<div id="rbp-querys">
			<h2>Query <small>[ Total Querys - <?php echo $total_querys ?>]</small></h2>
			<div class="rbp-panel">
				<table>
					<thead>
						<tr>
							<td>Query</td>
							<td width="20%">Time</td>
						</tr>
					</thead>
					<tbody>
					<?php foreach($querys as $k => $q) : ?>
						<tr>
							<td><pre><?php echo $q['query'] ?></pre></td>
							<td><?php echo $q['time'] ?></td>
						</tr>
					</tbody>
					<?php endforeach; ?>
				</table>
			</div>
		</div> <!-- end of div#rbp-querys -->

		<div id="rbp-passing">
			<h2>Passing Data</h2>
			<div class="rbp-panel">
				<table>
					<thead>
						<tr>
							<td colspan="2">$_Get Data</td>
						</tr>
					</thead>
					<tbody>
						<?php if(count($get)) : ?>
						<?php foreach ($get as $k=> $g) : ?>
						<tr>
							<td><?php echo $k ;?></td>
							<td><?php echo $g ;?></td>
						</tr>
						<?php endforeach; ?>
						<?php else : ?>
						<tr>
							<td colspan="2">No GET data exists</td>
						</tr>
						<?php endif; ?>
					</tbody>
				</table>
				<table>
					<thead>
						<tr>
							<td colspan="2">$_POST Data</td>
						</tr>
					</thead>
					<tbody>
						<?php if(count($post)) : ?>
						<?php foreach ($post as $k=> $p) : ?>
						<tr>
							<td><?php echo $k ;?></td>
							<td><?php echo $p ;?></td>
						</tr>
						<?php endforeach; ?>
						<?php else : ?>
						<tr>
							<td colspan="2">No POST data exists</td>
						</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div> <!-- end of div#rbp-passing -->

		<div id="rbp-files">
			<h2>Include Files <small>[ Total - <?php echo $total_files ?>] files</small></h2>
			<div id="rbp-files-wrap">
				<table>
					<thead>
						<tr>
							<td>
							</td>
						</tr>
					</thead>
					<tbody>
					<?php foreach($files as $f) : ?>
						<tr>
							<td><?php echo $f; ?></td>
						</tr>
					</tbody>
					<?php endforeach; ?>
				</table>
			</div>
		</div>

		<div id="rbp-time">
			<h2>Time</h2>
			<table>
				<thead>
					<tr>
						<td>Profiler Name</td>
						<td>Time</td>
					</tr>
				</thead>
				<tbody>
				<?php foreach($time as $n => $t) : ?>
					<tr>
						<td><?php echo $n; ?></td>
						<td><?php echo $t; ?></td>
					</tr>
				</tbody>
				<?php endforeach; ?>
			</table>
		</div>

		<div id="rbp-memory">
			<h2>Memory Usage</h2>
			<table>
				<thead>
					<tr>
						<td>Profiler Name</td>
						<td>Memory Usage</td>
					</tr>
				</thead>
				<tbody>
				<?php foreach($memory as $n => $m) : ?>
					<tr>
						<td><?php echo $n; ?></td>
						<td><?php echo $m; ?></td>
					</tr>
				</tbody>
				<?php endforeach; ?>
			</table>
		</div>

	</div> <!-- end of div.panel-container -->

</div> <!-- end of div#reborn-profiler -->

<script type="text/javascript">
	window.jQuery || document.write('<script src="<?php echo rbUrl("global/assets/js"); ?>jquery.min.js"><\/script>')
</script>

<?php
	echo global_asset('js', 'jquery.hashchange.min.js');
	echo global_asset('js', 'jquery.easytabs.min.js');
?>

<script type="text/javascript">
	$('#reborn-profiler').easytabs();

	$('#rbp-mode').on('click', function(e){
		e.preventDefault();

		$('#reborn-profiler').toggleClass('rbp-non-fixed');
	});
</script>

</body>
