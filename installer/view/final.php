<?php

	include 'header.php';
?>
<h2><?php echo $site; ?> is successfully created.</h2>

<div class="left final-msg">
	<p>Dear <?php echo $user; ?>, your site is successfully created with Reborn CMS.</p>
	<ul>
		<li><a href="<?php echo $url; ?>">Go to Site Frontend</a></li>
		<li><a href="<?php echo $url.'admin'; ?>">Go to Site Backend</a></li>
	</ul>
</div>

<?php

	include 'footer.php';
?>
