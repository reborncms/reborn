<?php

	include 'header.php';
?>
<h2>Fill your Database Data and User Data</h2>

<?php
	if(isset($error)) {
		echo '<p class="info" >'.$error.'</p>';
	}
?>

<form action="<?php echo $url.'step3'; ?>" method="post">
	<div class="left">
		<legend>
			User Data
		</legend>
		<div class="row">
			<label for="first_name">First Name</label>
			<input type="text" name="first_name" id="first_name">
		</div>
		<div class="row">
			<label for="last_name">Last Name</label>
			<input type="text" name="last_name" id="last_name">
		</div>
		<div class="row">
			<label for="email">Email</label>
			<input type="text" name="email" id="email">
		</div>
		<div class="row">
			<label for="passowrd">Password</label>
			<input type="password" name="password" id="password">
		</div>
		<div class="row">
			<label for="conf_passowrd">Confirm Password</label>
			<input type="password" name="conf_password" id="conf_password">
		</div>
	</div>

	<div class="right">
		<legend>
			Site Data
		</legend>
		<div class="row">
			<label for="site_title">Site Title</label>
			<input type="text" name="site_title" id="site_title">
		</div>
		<div class="row">
			<label for="slogan">Site Slogan</label>
			<input type="text" name="slogan" id="slogan" value="Your Site Slogan">
		</div>
	</div>

	<div class="clear"></div>

	<div class="row">
		<input type="submit" value="Install" class="btn">
	</div>

</form>

<?php

	include 'footer.php';
?>
