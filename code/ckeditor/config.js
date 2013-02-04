/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/


CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// Defines a toolbar with only one strip containing the "Source" button, a
	// separator and the "Bold" and "Italic" buttons.
	config.extraPlugins = 'contractvars'; 
	config.toolbar_BaBeSK =
	[
	    [ 'Bold', 'Italic','-','Cut','Copy','Paste','PasteFromWord','-','contractforename', 'contractname' ]
	];
	config.toolbar = 'BaBeSK';
	 config.language = 'de';
	 config.uiColor = '#AADC6E';
	 
};

