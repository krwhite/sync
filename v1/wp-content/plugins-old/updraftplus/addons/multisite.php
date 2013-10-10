<?php
/*
UpdraftPlus Addon: multisite:Multisite/Network
Description: Makes UpdraftPlus compatible with a WordPress Network (a.k.a. multi-site) and adds Network-related features
Version: 1.9
Shop: /shop/network-multisite/
Latest Change: 1.7.11
*/

// Options handling
if (!defined ('ABSPATH')) die ('No direct access allowed');

if (class_exists('UpdraftPlus_Options')) return;

if (is_multisite()) {

	class UpdraftPlus_Options {

		public static function user_can_manage() {
			return is_super_admin();
		}

		public static function get_updraft_option($option, $default = false) {
			$tmp = get_site_option('updraftplus_options');
			if (isset($tmp[$option])) {
				return $tmp[$option];
			} else {
				return $default;
			}
		}

		public static function update_updraft_option($option, $value) {
			$tmp = get_site_option('updraftplus_options');
			$tmp[$option] = $value;
			update_site_option('updraftplus_options', $tmp);
		}

		public static function delete_updraft_option($option) {
			$tmp = get_site_option('updraftplus_options');
			if (isset($tmp[$option])) unset($tmp[$option]);
			update_site_option('updraftplus_options', $tmp);
		}

		public static function add_admin_pages() {
			if (is_super_admin()) add_options_page('UpdraftPlus', __('UpdraftPlus Backups','updraftplus'), 'manage_options', 'updraftplus', array('UpdraftPlus_Options', 'options_printpage'));
		}

		public static function setdefaults() {
			$tmp = get_site_option('updraftplus_options');
			if (!is_array($tmp)) {
				$arr = array(
					'updraft_encryptionphrase' => '',
					'updraft_service' => '',

					'updraftplus_dismissedautobackup' => 0,

					'updraft_s3_login' => '',
					'updraft_s3_pass' => '',
					'updraft_s3_remote_path' => '',

					'updraft_dreamobjects_login' => '',
					'updraft_dreamobjects_pass' => '',
					'updraft_dreamobjects_remote_path' => '',

					'updraft_s3generic_login' => array(),
					'updraft_s3generic_pass' => array(),
					'updraft_s3generic_remote_path' => array(),
					'updraft_s3generic_endpoint' => array(),

					'updraft_cloudfiles_user' => '',
					'updraft_cloudfiles_apikey' => '',
					'updraft_cloudfiles_path' => '',
					'updraft_cloudfiles_authurl' => 'https://auth.api.rackspacecloud.com',

					'updraft_dropbox_appkey' => '',
					'updraft_dropbox_secret' => '',
					'updraft_dropbox_folder' => '',

					'updraft_ssl_nossl' => 0,
					'updraft_ssl_useservercerts' => 0,
					'updraft_ssl_disableverify' => 0,
					'updraft_split_every' => 1024,

					'updraft_sftp_settings' => array(),
					'updraft_webdav_settings' => array(),

					'updraft_googledrive_clientid' => '',
					'updraft_googledrive_secret' => '',
					'updraft_googledrive_remotepath' => '',
					'updraft_ftp_login' => '',
					'updraft_ftp_pass' => '',
					'updraft_ftp_remote_path' => '',
					'updraft_server_address' => '',
					'updraft_dir' => '',
					'updraft_email' => '',
					'updraft_delete_local' => 1,
					'updraft_debug_mode' => 1,
					'updraft_include_plugins' => 1,
					'updraft_include_themes' => 1,
					'updraft_include_uploads' => 1,
					'updraft_include_others' => 1,
					'updraft_include_wpcore' => 0,
					'updraft_include_wpcore_exclude' => '',
					'updraft_include_more' => 0,
					'updraft_include_more_path' => '',
					'updraft_include_muplugins' => 1,
					'updraft_include_blogs' => 1,
					'updraft_include_others_exclude' => UPDRAFT_DEFAULT_OTHERS_EXCLUDE,
					'updraft_interval' => 'manual',
					'updraft_interval_database' => 'manual',
					'updraft_retain' => 1,
					'updraft_retain_db' => 1,
					'updraft_starttime_files' => date('H:i', time()+600),
					'updraft_starttime_db' => date('H:i', time()+600),
					'updraft_startday_files' => date('w', time()+600),
					'updraft_startday_db' => date('w', time()+600),
					'updraft_disable_ping' => 0
				);
				update_site_option('updraftplus_options', $arr);
			}
		}

		public static function options_form_begin() {
			echo '<form method="post" action="">';
		}

		public static function admin_init() {
			global $updraftplus, $updraftplus_admin;
			add_filter('wpmu_options', array($updraftplus_admin, 'settings_formcontents'));
			$updraftplus->plugin_title .= " - ".__('Multisite Install','updraftplus');
		}

		# This is the function outputing the HTML for our options page
		public static function options_printpage() {
			if (!self::user_can_manage())  {
				wp_die( __('You do not have sufficient permissions to access this page.') );
			}

			if (isset($_POST['action']) && $_POST['action'] == 'update' && isset($_POST['updraft_interval'])) {
				$result = self::update_wpmu_options();
				if (count($result) > 0) {
					echo "<div class='error'>\n";
					echo implode("<br />\n", $result);
					echo "</div>\n";
				}
			}

			global $updraftplus_admin;
			$updraftplus_admin->settings_output();

		}

		public static function update_wpmu_options() {

			if ( !self::user_can_manage() ) wp_die( __( 'You do not have permission to access this page.' ) );

			global $updraftplus, $updraftplus_admin;

			$options=get_site_option('updraftplus_options');

			$errors = array();

			foreach ($_POST as $key => $value) {
				if ('updraft_delete_local' == $key || 'updraft_debug_mode' == $key || (preg_match('/^updraft_include_/', $key) && 'updraft_include_others_exclude' != $key && 'updraft_include_wpcore_exclude' != $key && 'updraft_include_more_path' != $key)) {
					# Booleans/numeric
					$options[$key] = absint($value);
				} elseif ('updraft_googledrive_remotepath' == $key) {
					$options[$key] = $updraftplus_admin->googledrive_remove_folderurlprefix($value);
				} elseif ('updraft_split_every' == $key) {
					$options[$key] = $updraftplus_admin->optionfilter_split_every($value);
				} elseif ('updraft_retain' == $key || 'updraft_retain_db' == $key) {
					$options[$key] = $updraftplus->retain_range($value);
				} elseif ('updraft_interval' == $key) {
					$options[$key] = $updraftplus->schedule_backup($value);
				} elseif ('updraft_interval_database' == $key) {
					$options[$key] = $updraftplus->schedule_backup_database($value);
				} elseif ('updraft_service' == $key) {
					$options[$key] = $updraftplus->just_one($value);
				} elseif ('updraft_starttime_files' == $key || 'updraft_starttime_db' == $key) {
					if (preg_match("/^[0-2][0-9]:[0-5][0-9]$/", $value)) {
						$options[$key] = $value;
					} elseif ($value == '') {
						$options[$key] = date('H:i', time()+300);
					} else {
						$options[$key] = '00:00';
					}
				} elseif ('updraft_startday_files' == $key || 'updraft_startday_db' == $key) {
					$value=absint($value);
					if ($value>6) $value=0;
					$options[$key] = $value;
				} elseif ('updraft_dir' == $key) {
					$options[$key] = $updraftplus_admin->prune_updraft_dir_prefix($value);
				} elseif (preg_match("/^updraft_/", $key)) {
					$options[$key] = $value;
				}
			}

			foreach (array('updraft_delete_local', 'updraft_debug_mode', 'updraft_include_plugins', 'updraft_include_themes', 'updraft_include_uploads', 'updraft_include_others', 'updraft_include_blogs', 'updraft_include_wpcore', 'updraft_include_more', 'updraft_include_mu-plugins', 'updraft_ssl_useservercerts', 'updraft_ssl_disableverify', 'updraft_ssl_nossl') as $key) {
				if (empty($_POST[$key])) $options[$key] = false;
			}

			$value = (empty($_POST['updraft_disable_ping'])) ? false : true;
			$options['updraft_disable_ping'] = apply_filters('updraftplus_pingfilter', $value);

			update_site_option('updraftplus_options', $options);

			return $errors;
		}


	}

	register_activation_hook('updraftplus', array('UpdraftPlus_Options', 'setdefaults'));
	add_filter('update_wpmu_options', array('UpdraftPlus_Options', 'update_wpmu_options'));
	add_action('admin_menu', array('UpdraftPlus_Options', 'add_admin_pages'));
	add_action('admin_init', array('UpdraftPlus_Options', 'admin_init'), 15);

	class UpdraftPlusAddOn_MultiSite {
		function add_backupable_file_entities($arr, $full_info) {
			// Post-3.5, WordPress multisite puts uploads from blogs by default into the uploads directory (i.e. no separate location). This is indicated not by the WP version number, but by the option ms_files_rewriting (which won't exist pre-3.5). See wp_upload_dir()
			// This is a compatible way of getting the current blog's upload directory. Because of our access setup, that always resolves to the site owner's upload directory
			if ($full_info) {
				$arr['mu-plugins'] = array(
					'path' => WPMU_PLUGIN_DIR,
					'description' => __('Must-use plugins','updraftplus')
				);
				if (!get_option('ms_files_rewriting') && defined('UPLOADBLOGSDIR')) {
					$ud = wp_upload_dir();
					if (strpos(UPLOADBLOGSDIR, $ud['basedir'] === false)) {
						$arr['blogs'] = array(
							'path' => ABSPATH.UPLOADBLOGSDIR,
							'description' => __('Blog uploads','updraftplus')
						);
					}
				}
			} else {
				$arr['mu-plugins'] = WPMU_PLUGIN_DIR;
				if (!get_option('ms_files_rewriting') && defined('UPLOADBLOGSDIR')) {
					$ud = wp_upload_dir();
					if (strpos(UPLOADBLOGSDIR, $ud['basedir'] === false)) {
						$arr['blogs'] = ABSPATH.UPLOADBLOGSDIR;
					}
				}
			}
			return $arr;
		}
	}

	add_filter('updraft_backupable_file_entities', array('UpdraftPlusAddOn_MultiSite', 'add_backupable_file_entities'), 10, 2);

}

?>
