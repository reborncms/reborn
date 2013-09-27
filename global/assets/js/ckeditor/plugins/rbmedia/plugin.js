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
			label: 'Insert Images From Media',
			command: pluginName,
			icon: this.path + 'img/rbmedia.png'
		});

		CKEDITOR.dialog.add('rbmedia', function(editor){
			return {
				title : 'Insert Image from Gallery',
				minWidth: 1100,
				minHeight: 462,
				contents : [ {
				    id : 'tab1', label : '', title : '', expand : true, padding : 0,
				    elements : [ {
				           type : 'iframe',
				           src : SITEURL + ADMIN + '/media/thumbnail/0/wysiwyg',
				           width : 1100, height : 462 - (CKEDITOR.env.ie ? 10 : 0)
				    } ]
				} ]
				, buttons : []
			};
		});
	}
});
