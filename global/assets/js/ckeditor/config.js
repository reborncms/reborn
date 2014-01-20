/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	config.uiColor = '#efefef';
	// Add Font Family with Myanmar3
	config.font_names = 'Arial/Arial, Helvetica, sans-serif;' +
	'Comic Sans MS/Comic Sans MS, cursive;' +
	'Courier New/Courier New, Courier, monospace;' +
	'Georgia/Georgia, serif;' +
	'Lucida Sans Unicode/Lucida Sans Unicode, Lucida Grande, sans-serif;' +
	'Tahoma/Tahoma, Geneva, sans-serif;' +
	'Times New Roman/Times New Roman, Times, serif;' +
	'Trebuchet MS/Trebuchet MS, Helvetica, sans-serif;' +
	'Myanmar3/Myanmar3,TharLon,Masterpiece Uni Sans,Yunghkio,Myanmar Sangam MN;' +
	'Verdana/Verdana, Geneva, sans-serif';

	config.pbckcode = {
		modes : [['HTML', 'html'], ['CSS', 'css'], ['PHP', 'php'], ['JS', 'javascript'], ["Markdown", "markdown"], ["SQL", "sql"], ["LESS", "less"], ["JSON", "json"], ["XML", "xml"]]
	}

	config.allowedContent = true;
};
