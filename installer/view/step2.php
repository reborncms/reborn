<?php

	include 'header.php';
?>
<h2 class="title_text step_title">Installation : Step 2 - Database Information</h2>

<?php
	if(isset($error)) {
		echo '<p class="info" >'.$error.'</p>';
	}
?>

<form action="<?php echo $url.'step2'; ?>" method="post">
	<div class="left">
		<div class="row">
			<label for="db">Database Name</label>
			<input type="text" name="db" id="db">
		</div>
		<div class="row">
			<label for="db_create">Create Database</label>
			<input type="checkbox" name="db_create" id="db_create">
		</div>
		<div class="row">
			<label for="hostname">MySQL Hostname</label>
			<input type="text" name="hostname" id="hostname" value="localhost">
		</div>
		<div class="row">
			<label for="mysql_username">MySQL Username</label>
			<input type="text" name="mysql_username" id="mysql_username" value="root">
		</div>
		<div class="row">
			<label for="mysql_password">MySQL password</label>
			<input type="text" name="mysql_password" id="mysql_password">
		</div>
		<div class="row">
			<label for="port">MySQL Port</label>
			<input type="text" name="port" id="port" value="3306">
		</div>
	</div>

	<div class="clear"></div>

	<div class="row">
		<input type="submit" value="Next" class="btn">
	</div>

</form>

<?php

	include 'footer.php';
?>
