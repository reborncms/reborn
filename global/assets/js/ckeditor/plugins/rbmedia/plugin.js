CKEDITOR.plugins.add('rbmedia', {
	icons	: 'rbmedia',
	init	: function(editor)
	{
		editor.addCommand('rbmedia', new CKEDITOR.dialogCommand('rbmedia'));

		editor.ui.addButton('rbmedia', {
			label	: 'Insert Images From Media',
			command : 'rbmedia'
		});

		/*var headTag = window.document.getElementsByTagName('head').item(0);

		var css 	= document.createElement('link');
		css.type 	= 'text/css';
		css.rel 	= 'stylesheet';
		css.media 	= 'all';
		css.href	= this.path + 'style/wysiwyg.css';

		headTag.appendChild(css);*/
		
		/*$.ajax({
			type: 'GET',
			url: SITEURL+ADMIN+'/media/wysiwyg/',
			success: function(result) {
				test(result);
			}
		});*/

		CKEDITOR.dialog.add('rbmedia', function(editor){

			return {
				title 	: 'Insert Image from Gallery',
				minWidth: 1100,
				minHeight: 462,
				resizeable : CKEDITOR.DIALOG_RESIZE_NONE,
				contents: [{
				    elements : [{
				        	type	: 'iframe',
				           	src		: SITEURL+ADMIN+'/media/wysiwyg/',
				           	width 	: 1100, 
				           	height 	: 462 - (CKEDITOR.env.ie ? 10 : 0)
				    }]
				}],
				buttons : []
			};
		});
	}
});

/*function test(result) {
	CKEDITOR.dialog.add('rbmedia', function(editor){

		return {
			title 	: 'Insert Image from Gallery',
			minWidth: 1100,
			minHeight: 462,
			resizeable : CKEDITOR.DIALOG_RESIZE_NONE,
			contents: [{
			    elements : [{
			           	type	: 'html',
			           	html	: result,
			           	width 	: 1100, 
			           	height 	: 462 - (CKEDITOR.env.ie ? 10 : 0)
			    }]
			}],
			buttons : []
		};
	});
}*/
