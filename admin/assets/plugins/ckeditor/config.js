/*
Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
    config.forcePasteAsPlainText = true;
    config.format_tags = 'p;h2;h3;h4;h5;h6;pre;div'
    // config.protectedSource.push( //g ); // Allows PHP Code
    config.enterMode = CKEDITOR.ENTER_BR;
    config.shiftEnterMode = CKEDITOR.ENTER_BR;
    config.undoStackSize = 90;
    config.width = "98%";
    config.height = "600px";
	
	config.filebrowserBrowseUrl = '/admin/assets/plugins/ckeditor/kcfinder/browse.php?type=files';
	config.filebrowserImageBrowseUrl = '/admin/assets/plugins/ckeditor/kcfinder/browse.php?type=images';
	config.filebrowserFlashBrowseUrl = '/admin/assets/plugins/ckeditor/kcfinder/browse.php?type=flash';
//	config.filebrowserUploadUrl = '/admin/assets/plugins/ckeditor/kcfinder/upload.php?type=files'; Не работи с ?type=files
	config.filebrowserUploadUrl = '/admin/assets/plugins/ckeditor/kcfinder/upload.php'; //Не работи с ?type=files
	config.filebrowserImageUploadUrl = '/admin/assets/plugins/ckeditor/kcfinder/upload.php?type=images';
	config.filebrowserFlashUploadUrl = '/admin/assets/plugins/ckeditor/kcfinder/upload.php?type=flash';
	
    config.disableNativeSpellChecker = false;
    config.scayt_autoStartup = false;

    config.toolbarCanCollapse = false;
    config.toolbar = 'Cms';
    config.toolbar_Cms =
    [
        { name: 'document', items : [ 'Source','-','Templates' ] },
		{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
		{ name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','Scayt' ] },
		//{ name: 'forms', items : [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton']}, 
		'/',
		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
		{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv',
		'-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
		{ name: 'links', items : [ 'Link','Unlink','Anchor' ] },
		{ name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe' ] },
		'/',
		{ name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
		{ name: 'colors', items : [ 'TextColor','BGColor' ] },
		{ name: 'tools', items : [ 'Maximize', 'ShowBlocks','-','About' ] }
    ];
	
};
