<?php

/*

Plugin Name: downML - Download Media Library

Plugin URI: http://aneeskA.com

Description: Download Media Library Plugin is a light weight plugin that helps admins to download and backup the images available in the media library of the wordpress site.

Version: 0.3.1

Author: aneeskA, contact@aneeska.com

Author URI: http://aneeska.com

License: GPL2

*/



/*  Copyright 2012  aneeskA  (email : contact@aneeska.com)



    This program is free software; you can redistribute it and/or modify

    it under the terms of the GNU General Public License, version 2, as 

    published by the Free Software Foundation.



    This program is distributed in the hope that it will be useful,

    but WITHOUT ANY WARRANTY; without even the implied warranty of

    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the

    GNU General Public License for more details.



    You should have received a copy of the GNU General Public License

    along with this program; if not, write to the Free Software

    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

?><?php



define( 'DML_PUGIN_NAME', 'downML - Download Media Library');

define( 'DML_PLUGIN_DIRECTORY', 'downml');

define( 'DML_CURRENT_VERSION', '0.3.1' );

define( 'DML_CURRENT_BUILD', '3' );

define( 'DML_LOGPATH', str_replace('\\', '/', WP_CONTENT_DIR).'/dml-logs/');

define( 'DML_DEBUG', false);		# never use debug mode on productive systems

// i18n plugin domain for language files

define( 'EMU2_I18N_DOMAIN', 'downml' );

define( 'DML_ZIP_FILE', '/medialibrary.zip' );

define( 'DML_TAR_FILE', '/medialibrary.tar.gz' );

define( 'DML_LOG_FILE', '/dml-logs');



// how to handle log files, don't load them if you don't log

require_once('downml_logfilehandling.php');



// load language files

function downml_set_lang_file() {

	# set the language file

	$currentLocale = get_locale();

	if(!empty($currentLocale)) {

		$moFile = dirname(__FILE__) . "/lang/" . $currentLocale . ".mo";

		if (@file_exists($moFile) && is_readable($moFile)) {

			load_textdomain(EMU2_I18N_DOMAIN, $moFile);

		}



	}

}

downml_set_lang_file();



// create custom plugin settings menu

add_action( 'admin_menu', 'downml_create_menu' );



// call register settings function

add_action( 'admin_init', 'downml_register_settings' );



// register functions

register_activation_hook(__FILE__, 'downml_activate');

register_deactivation_hook(__FILE__, 'downml_deactivate');

register_uninstall_hook(__FILE__, 'downml_uninstall');



// activating the default values

function downml_activate() {

	add_option('downml_option_3', 'any_value');

}



// deactivating

function downml_deactivate() {

	// needed for proper deletion of every option

	delete_option('downml_option_3');

}



// uninstalling

function downml_uninstall() {

	# delete all data stored

	delete_option('downml_option_3');

	// delete log files and folder only if needed

	if (function_exists('downml_deleteLogFolder')) downml_deleteLogFolder();

}



function downml_create_menu() {

	// create new top-level menu

	add_menu_page( 

	__('downML', EMU2_I18N_DOMAIN),

	__('downML', EMU2_I18N_DOMAIN),

	0,

	DML_PLUGIN_DIRECTORY.'/downml_settings_page.php',

	'',

	plugins_url('/images/icon.png', __FILE__));

}





function downml_register_settings() {

	//register settings

	register_setting( 'downml-settings-group', 'new_option_name' );

	register_setting( 'downml-settings-group', 'some_other_option' );

	register_setting( 'downml-settings-group', 'option_etc' );

}



// check if debug is activated

function downml_debug() {

	# only run debug on localhost

	if ($_SERVER["HTTP_HOST"]=="localhost" && defined('DML_DEBUG') && DML_DEBUG==true) return true;

}

?>

