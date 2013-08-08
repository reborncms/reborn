jQuery(function() {

	$("#widget_areas").accordion();

	$('.drop_area').height('auto');

	// ===== Add Widget to Area ====== //

	$('.widget_add_btn').on('click', function(e) {

		e.preventDefault();

		var active_area = $('.ui-accordion-content-active');

		active_area.find('#no-index-data').remove();

		var widget = $(this).parent().clone();

		var area = active_area.attr('id').replace('_area', '');

		var widget_name = widget.attr('data-widget');

		$.ajax({
			url: SITEURL + ADMIN + '/widget/add',
			type: "POST",
			data: {
				name: widget_name,
				area: area,
			},
			success: function(data) {

				var obj = jQuery.parseJSON(data);

				if (obj.status == 'ok') {

					var append_obj = widget.appendTo('.ui-accordion-content-active');

					active_area.height('auto');

					var icon = widget.find('a.widget_add_btn i');
					var link = widget.children('a');
					link.attr('href', SITEURL + ADMIN + '/widget/remove/' + obj.id);
					widget.find('a.widget-info').remove();
					link.removeClass('widget_add_btn').addClass('widget_remove_btn');
					icon.removeClass('icon-circleplus').addClass('icon-circleminus');

					$.ajax({
						url: SITEURL + ADMIN + '/widget/has-options',
						type: "POST",
						data: {
							name: widget_name,
						},
						success: function(data) {
							var dt = jQuery.parseJSON(data);
							if (dt.status == 'ok') {
								var setting_btn = '<a href="' + SITEURL + ADMIN + '/widget/settings/' + widget_name + '/' + obj.id + '" class="widget_settings_btn"><i class="icon-setting icon-gray icon-12 widget-ico"></i></a>';
								widget.find('a.widget_remove_btn').after(setting_btn);
							};
						}
					});

					//add if option exist


					$('#msg_area').html("<span class='show-alert-success'>New Widget Added!</span>");

				} else {
					$('#msg_area').html("<span class='show-alert-error'>Error Occur!! New Widget cannot be added!</span>");
				};

				$('#msg_area').animate({
					opacity: 1.0
				}, 4000).fadeOut('slow');

			}
		});

	});

	$('.widget_move_btn').click(function(e) {

		e.preventDefault();

		var active_area = $('.ui-accordion-content-active');

		active_area.find('#no-index-data').remove();

		var or_widget = $(this).parent();

		var widget = or_widget.clone();

		var area = active_area.attr('id').replace('_area', '');

		var widget_name = widget.attr('data-widget');

		var url = $(this).attr('href');

		$.ajax({
			url: url,
			type: "POST",
			data: {
				area: area,
			},
			success: function(data) {
				var obj = jQuery.parseJSON(data);

				if (obj.status == 'ok') {

					var append_obj = widget.appendTo('.ui-accordion-content-active');

					or_widget.remove();

					active_area.height('auto');

					widget.find('a.widget_move_btn').remove();

					widget.find('a.widget_settings_btn').show();

					$('#msg_area').html("<span class='show-alert-success'>Successfully Move to area.</span>");

				} else {
					$('#msg_area').html("<span class='show-alert-error'>Error Occur!! Cannot move the widget!</span>");
				};

				$('#msg_area').animate({
					opacity: 1.0
				}, 4000).fadeOut('slow');

			}
		});

	});

	// ==== Remove Widget from Area ===== //

	$('.widget_remove_btn').livequery('click', function(e) {

		e.preventDefault();

		if (confirm("Are you sure you want to remove this widget ?")) {

			var active_area = $('.ui-accordion-content-active');

			var link = $(this).attr('href');

			var ele = $(this).parent();

			$.ajax({
				url: link,
				type: "POST",
				success: function(data) {
					ele.remove();
					var remain_widgets = active_area.children('.single_widget').length;
					if (remain_widgets < 1) {
						active_area.append('<div id="no-index-data">No widgets yet in this area.</div>');
					};
					active_area.height('auto');
				}
			});
		}

	});

	// === Sort widgets === //

	$('.drop_area').sortable({
		items: 'div.single_widget',
		stop: function(event, ui) {
			var sorted = $(".ui-accordion-content-active").sortable("toArray");
			var area = $(this).attr('id').replace('_area', '');
			$.ajax({
				url: SITEURL + ADMIN + '/widget/order',
				type: "POST",
				data: {
					area: area,
					order: sorted,
				},
				success: function(data) {
					var obj = jQuery.parseJSON(data);
					if (obj.status == 'ok') {
						$('#msg_area').html("<span class='show-alert-success'>Widgets sorted in " + obj.area + " area.</span>");
					} else {
						$('#msg_area').html("<span class='show-alert-error'>Sorting Fail!!</span>");
					}

					$('#msg_area').show();

					$('#msg_area').animate({
						opacity: 1.0
					}, 4000).fadeOut('slow');
				}
			});
		}
	});

	// === Widget Settings === //

	$('.widget_settings_btn').livequery(function() {
		$(this).colorbox({
			width: "30%",
			onComplete: function() {

				$.colorbox.resize();

				$('#save_option_btn').on('click', function(e) {

					e.preventDefault();

					var form = $('.option-form-wrapper form');

					var url = form.attr('action');

					var data = form.serialize();

					$.post(url, data, function(data) {
						var dt = jQuery.parseJSON(data);
						if (dt.status == 'ok') {

							$.colorbox.close();

						} else {

							$('#error-options').addClass('show-alert show-alert-error');
							$('#error-options').html("Error Occur!!!");

						}
					});
				});

				$('#option-cancel-btn').on('click', function(e) {

					$.colorbox.close();

				})
			}
		});
	});

});
