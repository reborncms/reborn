CKEDITOR.plugins.add('rbmedia', {
	requires: ['iframedialog'],
	init: function(editor)
	{
		var pluginName = 'rbmedia';
		editor.addCommand(pluginName,
			{
				exec: function(){
					update_instance();
					CKEDITOR.currentInstance.openDialog('rbmedia');
				}
			}
		);
		editor.ui.addButton('rbmedia', {
			label: 'Insert Images',
			command: pluginName,
			icon: this.path + 'img/rbmedia.png'
		});
		CKEDITOR.dialog.addIframe( 'rbmedia', 'Insert Image', ADMIN + '/media/rbCK/', 750, 400);
	}
});