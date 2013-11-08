<?php

/**
 * CKEditor Config File
 *
 * @package Reborn
 * @author Myanmar Links Professional Web Development Team
 **/
return array(

	/**
	 * Toolbar for CKEditor Standard Type.
	 * See detail at http://www.ckeditor.com/
	 *
	 */
	'toolbar' => function(){
		return <<<toolbar
		[
            ['Maximize'],
            ['PasteFromWord', 'Paste'],
            ['Image', 'Smiley'],
            ['Undo','Redo','-','Find','Replace'],
            ['Bold','Italic', 'Underline','Strike'],
            ['Link','Unlink'],
            ['Subscript','Superscript', 'NumberedList','BulletedList','Blockquote'],

            ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
            ['Format', 'Font', 'FontSize'],
            ['ShowBlocks', 'RemoveFormat'],
            ['Table'],
            ['rbmedia'],
            ['Youtube'],
            ['postlink'],
            ['Source'],
            ['pbckcode']
        ]
toolbar;
	},

	/**
	 * Extra Plugin for CKEditor Standard Type
	 *
	 */
	'extra' => 'rbmedia,iframedialog,pbckcode,postlink,youtube',

	/**
	 * Toolbar for CKEditor Mini
	 *
	 */
	'mini_toolbar' => function(){
		return <<<mini
		[
			['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink']
		]
mini;
	},

	/**
	 * Toolbar for CKEditor Simple
	 *
	 */
	'simple_toolbar' => function(){
		return <<<mini
		[
            ['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink','-', 'Source']
        ]
mini;
	},

);
