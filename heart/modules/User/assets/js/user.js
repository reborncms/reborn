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
