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
			<tr>
				<td>PHP</td>
				<td><?php echo ($exts['php']['status']) ? 'Ok' : 'Need' ; ?></td>
				<td><?php echo $exts['php']['version']; ?></td>
			</tr>
			<tr>
				<td>My SQL</td>
				<td colspan=2><?php echo ($exts['mysql']['status']) ? 'Ok' : 'Need' ; ?></td>
			</tr>
			<tr>
				<td>Apache Mod Rewrite</td>
				<td colspan=2><?php echo ($exts['mod_rewrite']['status']) ? 'Ok' : 'Need' ; ?></td>
			</tr>
			<tr>
				<td>cUrl</td>
				<td colspan=2><?php echo ($exts['curl']['status']) ? 'Ok' : 'Need' ; ?></td>
			</tr>
		</table>
	</div>

	<div class="clear"></div>

	<div class="btn-area">
		<?php if ($status) : ?>
		<a href="<?php echo $url.'step2'; ?>" class="btn">Next Step</a>
		<?php else : ?>
		<p class="info">
			Please try to set manually set permission [chmod (0777) for folder and (0666) for file] for red color lists.
		</p>
		<?php endif; ?>
	</div>
<?php

	include 'footer.php';
?>
