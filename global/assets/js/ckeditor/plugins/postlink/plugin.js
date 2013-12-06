CKEDITOR.plugins.add('postlink', {
	icons	: 'postlink',
	init	: function(editor)
	{
		editor.addCommand('postlink', new CKEDITOR.dialogCommand('postlink'));

		editor.ui.addButton('postlink', {
			label	: 'Insert Blog Post Link',
			command : 'postlink'
		});

		CKEDITOR.dialog.add('postlink', function(editor){

			if ((typeof RB == 'undefined') || RB.post_id == undefined) {
				var src_link = SITEURL+ADMIN+'/blog/post-links/';
			} else {
				var src_link = SITEURL+ADMIN+'/blog/post-links/'+RB.post_id;
			}

			return {
				title 	: 'Insert Blog Post Link',
				minWidth: 600,
				minHeight: 460,
				resizeable : CKEDITOR.DIALOG_RESIZE_NONE,
				contents: [{
				    elements : [{
				        	type	: 'iframe',
				           	src		: src_link,
				           	width 	: 600,
				           	height 	: 460 - (CKEDITOR.env.ie ? 10 : 0)
				    }]
				}],
				buttons : []
			};
		});
	}
});
