(function($) {
	$(function(){

		// Ajax Search Function
		function ajaxSearch(form, url) {
			$.ajax({
				url: url,
				type: 'POST',
				data: $(form).serialize(),
				beforeSend: function() {
					$('#filter-form').append('<span id="waiting" class="loading"></span>');
				},
				complete: function() {
					$('#waiting').remove();
				},
				error: function(jqXHR, textStatus, errorThrown) {
					$('#waiting').remove();
					$('#filter-form').append('<span id="result-error">'+errorThrown+'</span>');
				},
				success: function(data) {
					$('#data_table_wrapper').html(data);
				}
			});
		};

		var timeoutReference;
		$('#filter-box').on('keyup', function(e){
			var form = $('#filter-form');
			var url = form.attr('action');
			if (timeoutReference) clearTimeout(timeoutReference);
			timeoutReference = setTimeout(function() {
				ajaxSearch(form, url);
			}, 500);
		});

		$('#fiter-form').submit(function(e){
			e.preventDefault();
		});

		/* ========== End of Search Function List ============== */

		$('.inline-label input').bind('click', function(){
			var val = $(this).val(),
				group = $(this).parents('div.ckeck-group-block'),
				row = $(group).parent().parent(),
				col = $(row).find('td:first-child'),
				checked = $(group).find("input:checked");

			console.log(val);

			if (checked.length > 0) {
				var mod = $(col).find('input');

				$(mod).attr('checked', 'checked');
			}
		});

		$('.module-role input').on('click', function() {
			var check = $(this).attr('checked'),
				type = typeof(check),
				module = $(this).data('module');

			if (type === 'undefined' ) {
				var row = $('tr#' + module);
					group = $(row).find('td.action_roles'),
					actions = $(group).find('input:checked');

				if (actions.length > 0) {
					$(actions).each(function(index) {
						$(actions[index]).removeAttr('checked');
					});
				}
			}
		});

	});
})(jQuery);