<div class="form_builder">
	<h3 class="legend_headers"><?php echo $this->legend; ?></h3>

	<div class="form_wrapper">

	<?php echo $this->start; ?>

		<?php if (! empty($this->hiddens) ) : ?>

		<div class="hidden-area" style="display: none;">
		<?php foreach($this->hiddens as $name => $value) : ?>
			<?php echo \Form::hidden($name, $value); ?>
		<?php endforeach; ?>
		</div>

		<?php endif; ?>

		<?php foreach($this->fields as $name => $field) : ?>

		<div class="form-block">

			<?php echo $this->labels[$name]; ?>

			<div class="form-right-block">

				<?php echo $this->fields[$name]['html']; ?>

				<?php if(isset($this->errors[$name])) : ?>
				<span class="error"><?php echo $this->errors[$name]; ?></span>
				<?php endif; ?>

				<p class="info"><?php echo $this->fields[$name]['info']; ?></p>
			</div>
		</div> <!-- end of input_group -->

		<?php endforeach; ?>


		<div class="form-block form-action button-wrapper">

			<?php echo $this->submit; ?>

			<?php echo $this->reset; ?>

			<?php echo $this->cancel; ?>

		</div>

	</form>

	</div> <!-- end of form_wrapper -->
</div> <!-- end of form_builder -->
