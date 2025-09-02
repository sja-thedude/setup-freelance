/**
 * @license Copyright (c) 2003-2018, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';

    config.contentsCss = '/assets/fonts/fonts.css';
    //the next line add the new font to the combobox in CKEditor
    config.font_names = 'Oswald;Lato;Rahayu;Big Snow;Romantina;Shorelines Script;Sweet Beaches;Sweet Hipster;The Queenthine;' + config.font_names;

};
