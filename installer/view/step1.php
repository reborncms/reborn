<?php

	include 'header.php';
?>
	<h2>Directory Path and File Writable Access</h2>
	<table class="access">
		<?php $checks = array(); ?>
		<tr>
			<th>Storages Path</th>
		</tr>

		<?php foreach($result['storages'] as $k => $list) : ?>
		<?php
			$class = $list ? 'ok' : 'fail';
			$checks[] = $list;
		?>
		<tr class="<?php echo $class; ?>">
			<td><?php echo $k; ?></td>
		</tr>
		<?php endforeach ?>

		<tr>
			<th>Content Path</th>
		</tr>

		<?php foreach($result['content'] as $k => $list) : ?>
		<?php
			$class = $list ? 'ok' : 'fail';
			$checks[] = $list;
		?>
		<tr class="<?php echo $class; ?>">
			<td><?php echo $k; ?></td>
		</tr>
		<?php endforeach ?>

		<tr>
			<th>Config Path</th>
		</tr>

		<?php foreach($result['config'] as $k => $list) : ?>
		<?php
			$class = $list ? 'ok' : 'fail';
			$checks[] = $list;
		?>
		<tr class="<?php echo $class; ?>">
			<td><?php echo $k; ?></td>
		</tr>
		<?php endforeach ?>

		<tr>
			<th>Config File</th>
		</tr>

		<?php foreach($result['config_files'] as $k => $list) : ?>
		<?php
			$class = $list ? 'ok' : 'fail';
			$checks[] = $list;
		?>
		<tr class="<?php echo $class; ?>">
			<td><?php echo $k; ?></td>
		</tr>
		<?php endforeach ?>
	</table>

	<div class="extension">
		<h2>Extension Check</h2>
		<table>
			<?php
				if($exts['php']['status']) {
					$msg = 'PHP Version match.';
					$class= "ok";
				} else {
					$msg = 'Need PHP Version 5.3.6';
					$class= "fail";
				}
			?>
			<tr class="<?php echo $class; ?>">
				<td width="47%">PHP</td>
				<td><?php echo $msg ; ?></td>
			</tr>
			<?php
				if($exts['mysql']['status']) {
					$msg = 'MySql is ok.';
					$class= "ok";
				} else {
					$msg = 'Need MySql!';
					$class= "fail";
				}
			?>
			<tr class="<?php echo $class; ?>">
				<td>My SQL</td>
				<td><?php echo $msg ?></td>
			</tr>
			<?php
				if($exts['mod_rewrite']['status']) {
					$msg = 'Apache ModRewrite is ok.';
					$class= "ok";
				} else {
					$msg = 'Need ModRewrite';
					$class= "fail";
				}
			?>
			<tr class="<?php echo $class; ?>">
				<td>Apache Mod Rewrite</td>
				<td colspan=2><?php echo $msg; ?></td>
			</tr>
			<?php
				if($exts['curl']['status']) {
					$msg = 'cURL is ok.';
					$class= "ok";
				} else {
					$msg = 'Need cURL Extension';
					$class= "fail";
				}
			?>
			<tr class="<?php echo $class; ?>">
				<td>cUrl</td>
				<td colspan=2><?php echo $msg; ?></td>
			</tr>
		</table>
	</div>

	<div class="clear"></div>

	<div class="btn-area">
		<?php if ($status) : ?>
		<a href="<?php echo $url.'step2'; ?>" class="btn">Next Step</a>
		<?php else : ?>
		<p class="info">
			- Please try to set manually set permission [chmod (0777) for folder and (0666) for file] for red color lists.
			<br>
			- Check Your PHP Version and Extension.
		</p>
		<?php endif; ?>
	</div>
<?php

	include 'footer.php';
?>
