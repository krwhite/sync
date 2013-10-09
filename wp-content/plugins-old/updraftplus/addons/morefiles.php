<?php
/*
UpdraftPlus Addon: morefiles:Back up more files, including WordPress core
Description: Creates a backup of WordPress core (including everything in that directory WordPress is in), and any other directory you specify too.
Version: 1.5
Shop: /shop/more-files/
Latest Change: 1.6.51
*/

$updraftplus_addons_morefiles = new UpdraftPlus_Addons_MoreFiles;

class UpdraftPlus_Addons_MoreFiles {

	private $wpcore_foundyet = 0;

	public function __construct() {
		add_filter('updraft_backupable_file_entities', array($this, 'backupable_file_entities'), 10, 2);
		add_filter('updraft_backupable_file_entities_final', array($this, 'backupable_file_entities_final'), 10, 2);

		add_filter('updraftplus_restore_movein_wpcore', array($this, 'restore_movein_wpcore'), 10, 2);
		add_filter('updraftplus_backup_makezip_wpcore', array($this, 'backup_makezip_wpcore'), 10, 3);
		add_filter('updraftplus_backup_makezip_more', array($this, 'backup_makezip_more'), 10, 3);

		add_filter('updraftplus_defaultoption_include_more', array($this, 'return_false'));
		add_filter('updraftplus_defaultoption_include_wpcore', array($this, 'return_false'));

		add_filter('updraftplus_admin_directories_description', array($this, 'admin_directories_description'));

		add_action('updraftplus_config_option_include_more', array($this, 'config_option_include_more'));

		add_action('updraftplus_config_option_include_wpcore', array($this, 'config_option_include_wpcore'));

		add_action('updraftplus_restore_form_wpcore', array($this, 'restore_form_wpcore'));
		add_filter('updraftplus_checkzip_wpcore', array($this, 'checkzip_wpcore'), 10, 4);
		add_filter('updraftplus_checkzip_end_wpcore', array($this, 'checkzip_end_wpcore'), 10, 3);

		add_filter('updraftplus_dirlist_more', array($this, 'backup_more_dirlist'));
		add_filter('updraftplus_dirlist_wpcore', array($this, 'backup_wpcore_dirlist'));
	}

	public function return_false() {
		return false;
	}

	public function restore_form_wpcore() {

		?>
		<div id="updraft_restorer_wpcoreoptions" style="display:none; padding:12px; margin: 8px 0 4px; border: dashed 1px;"><h4 style="margin: 0px 0px 6px; padding:0px;"><?php echo sprintf(__('%s restoration options:','updraftplus'),__('WordPress Core','updraftplus')); ?></h4>

			<?php

			echo '<input name="updraft_restorer_wpcore_includewpconfig" id="updraft_restorer_wpcore_includewpconfig" type="checkbox" value="1"><label for="updraft_restorer_wpcore_includewpconfig"> '.__('Over-write wp-config.php','updraftplus').'</label> <a href="http://updraftplus.com/faqs/when-i-restore-wordpress-core-should-i-include-wp-config-php-in-the-restoration/">'.__('(learn more about this important option)','updraftplus').'</a>';

			?>

			<script>
				jQuery('#updraft_restore_wpcore').change(function(){
					if (jQuery('#updraft_restore_wpcore').is(':checked')) {
						jQuery('#updraft_restorer_wpcoreoptions').slideDown();
					} else {
						jQuery('#updraft_restorer_wpcoreoptions').slideUp();
					}
				});
			</script>

			</div>
		<?php
	}

	public function admin_directories_description() {
		return __('The above files comprise everything in a WordPress installation.' ,'updraftplus');
	}

	public function backupable_file_entities($arr, $full_info) {
		if ($full_info) {
			$arr['wpcore'] = array(
				'path' => untrailingslashit(ABSPATH),
				'description' => __('WordPress core (including any additions to your WordPress root directory)', 'updraftplus'),
				'htmltitle' => sprintf(__('WordPress root directory server path: %s', 'updraftplus'), ABSPATH)
			);
		} else {
			$arr['wpcore'] = untrailingslashit(ABSPATH);
		}
		return $arr;
	}

	public function checkzip_wpcore($zipfile, &$mess, &$warn, &$err) {
		if (!empty($this->wpcore_foundyet) && 3 == $this->wpcore_foundyet) return;
		if (!class_exists('UpdraftPlus_PclZip')) require(UPDRAFTPLUS_DIR.'/class-zip.php');

		$zip = new UpdraftPlus_PclZip;

		if (!is_readable($zipfile)) {
			$warn[] = sprintf(__('Unable to read zip file (%s) - could not pre-scan it to check its integrity.','updraftplus'), basename($zipfile));
			return;
		}

		if (!$zip->open($zipfile)) {
			$warn[] = sprintf(__('Unable to open zip file (%s) - could not pre-scan it to check its integrity.','updraftplus'), basename($zipfile));
			return;
		}

		# Don't put this in the for loop, or the magic __get() method gets called and opens the zip file every time the loop goes round
		$numfiles = $zip->numFiles;

		for ($i=0; $i < $numfiles; $i++) {
			$si = $zip->statIndex($i);
			if ($si['name'] == 'wp-admin/index.php') { $this->wpcore_foundyet = $this->wpcore_foundyet | 1; if (3 == $this->wpcore_foundyet) return; }
			if ($si['name'] == 'xmlrpc.php' || $si['name'] == 'xmlrpc.php/xmlrpc.php') { $this->wpcore_foundyet = $this->wpcore_foundyet | 2; if (3 == $this->wpcore_foundyet) return; }
		}

		@$zip->close();

	}

	public function checkzip_end_wpcore(&$mess, &$warn, &$err) {
		if (!empty($this->wpcore_foundyet) && 3 == $this->wpcore_foundyet) return;
		if (0 == ($this->wpcore_foundyet & 1)) $warn[] = sprintf(__('This does not look like a valid WordPress core backup - the file %s was missing.', 'updraftplus'), 'wp-admin/index.php').' '.__('If you are not sure then you should stop; otherwise you may destroy this WordPress installation.', 'updraftplus');
		if (0 == ($this->wpcore_foundyet & 2)) $warn[] = sprintf(__('This does not look like a valid WordPress core backup - the file %s was missing.', 'updraftplus'), 'xmlrpc.php').' '.__('If you are not sure then you should stop; otherwise you may destroy this WordPress installation.', 'updraftplus');
	}

	public function backupable_file_entities_final($arr, $full_info) {
		if ($full_info) {
			$arr['more'] = array(
				'path' => untrailingslashit(UpdraftPlus_Options::get_updraft_option('updraft_include_more_path')),
				'description' => __('Any other directory on your server that you wish to back up', 'updraftplus'),
				'shortdescription' => __('More Files','updraftplus'),
				'restorable' => false
			);
		} else {
			$arr['more'] = untrailingslashit(UpdraftPlus_Options::get_updraft_option('updraft_include_more_path'));
		}
		return $arr;
	}

	public function config_option_include_more() {

		$display = (UpdraftPlus_Options::get_updraft_option('updraft_include_more')) ? '' : 'style="display:none;"';

		echo "<div id=\"updraft_include_more_options\" $display>";

			echo '<label for="updraft_include_more_path">'.__('Enter the directory:', 'updraftplus').'</label>';

			echo '<input type="text" id="updraft_include_more_path" name="updraft_include_more_path" size="54" value="'.htmlspecialchars(UpdraftPlus_Options::get_updraft_option('updraft_include_more_path')).'" />'.' - ';

			echo __('If you are not sure what this option is for, then you will not want it, and should turn it off.','updraftplus').' '.__('If using it, enter an absolute path (it is not relative to your WordPress install).', 'updraftplus');
			
			echo ' '.__('Be careful what you enter - if you enter / then it really will try to create a zip containing your entire webserver.', 'updraftplus');

			echo '<br>';

		echo '</div>';

		echo <<<ENDHERE
		<script>
			jQuery(document).ready(function() {
				jQuery('#updraft_include_more').click(function() {
					if (jQuery('#updraft_include_more').is(':checked')) {
						jQuery('#updraft_include_more_options').slideDown();
					} else {
						jQuery('#updraft_include_more_options').slideUp();
					}
				});
			});
		</script>
ENDHERE;

	}

	public function config_option_include_wpcore() {

		$display = (UpdraftPlus_Options::get_updraft_option('updraft_include_wpcore')) ? '' : 'style="display:none;"';

		echo "<div id=\"updraft_include_wpcore_exclude\" $display>";

			echo '<label for="updraft_include_wpcore_exclude">'.__('Exclude these:', 'updraftplus').'</label>';

			echo '<input title="'.__('If entering multiple files/directories, then separate them with commas. You can use a * at the end of any entry as a wildcard.', 'updraftplus').'" type="text" id="updraft_include_wpcore_exclude" name="updraft_include_wpcore_exclude" size="54" value="'.htmlspecialchars(UpdraftPlus_Options::get_updraft_option('updraft_include_wpcore_exclude')).'" />';

			echo '<br>';

		echo '</div>';

		echo <<<ENDHERE
		<script>
			jQuery(document).ready(function() {
				jQuery('#updraft_include_wpcore').click(function() {
					if (jQuery('#updraft_include_wpcore').is(':checked')) {
						jQuery('#updraft_include_wpcore_exclude').slideDown();
					} else {
						jQuery('#updraft_include_wpcore_exclude').slideUp();
					}
				});
			});
		</script>
ENDHERE;

	}

	public function backup_more_dirlist($whichdir) {
		// Need to properly analyse the plugins, themes, uploads, content paths in order to strip them out (they may have various non-default manual values)

		global $updraftplus;

		$possible_backups = $updraftplus->get_backupable_file_entities(false);
		# We don't want to exclude the very thing we are backing up
		unset($possible_backups['more']);
		# We do want to exclude everything in WordPress and in wp-content
		$possible_backups['wp-content'] = WP_CONTENT_DIR;
		$possible_backups['wordpress'] = untrailingslashit(ABSPATH);
		$possible_backups_dirs = array_flip($possible_backups);

		if (!empty($whichdir) && is_dir($whichdir)) {
			// Removing the slash is important (though ought to be redundant by here); otherwise path matching does not work
			$wpcore_dirlist = $updraftplus->compile_folder_list_for_backup(untrailingslashit($whichdir), $possible_backups_dirs, array());
		} else {
			$wpcore_dirlist = array();
			if (!empty($whichdir)) {
				$updraftplus->log("We expected to find a directory to back up at: ".$whichdir);
				$updraftplus->log(sprintf(__("No backup of %s directories: there was nothing found to back up", 'updraftplus'), __('more',' updraftplus')), 'warning');
			}
		}

		return $wpcore_dirlist;

	}

	public function backup_makezip_more($whichdir, $backup_file_basename, $index) {

		global $updraftplus, $updraftplus_backup;

		// Actually create the thing

		$wpcore_dirlist = $this->backup_more_dirlist($whichdir);

		if (count($wpcore_dirlist)>0) {
			$updraft_dir = $updraftplus->backups_dir_location();
			$created = $updraftplus_backup->create_zip($wpcore_dirlist, 'more', $backup_file_basename, $index);
			if (is_string($created) || is_array($created)) {
				return $created;
			} else {
				$updraftplus->log("More files backup: create_zip returned an error");
				return false;
			}
		} else {
			$updraftplus->log("No backup of 'more' directories: there was nothing found to back up");
			return false;
		}
	}

	public function backup_wpcore_dirlist($whichdir) {

		// Need to properly analyse the plugins, themes, uploads, content paths in order to strip them out (they may have various non-default manual values)

		global $updraftplus;

		$possible_backups = $updraftplus->get_backupable_file_entities(false);
		# We don't want to exclude the very thing we are backing up
		unset($possible_backups['wpcore']);
		# We do want to exclude everything in wp-content
		$possible_backups['wp-content'] = WP_CONTENT_DIR;
		$possible_backups_dirs = array_flip($possible_backups);

		# Create an array of directories to be skipped
		$wpcore_skip = preg_split("/,/",UpdraftPlus_Options::get_updraft_option('updraft_include_wpcore_exclude', UPDRAFT_DEFAULT_OTHERS_EXCLUDE));
		# Make the values into the keys
		$wpcore_skip = array_flip($wpcore_skip);
		$wpcore_skip['wp_content'] = 0;

		// Removing the slash is important (though ought to be redundant by here); otherwise path matching does not work
		$wpcore_dirlist = $updraftplus->compile_folder_list_for_backup(untrailingslashit($whichdir), $possible_backups_dirs, $wpcore_skip);

		return $wpcore_dirlist;

	}

	// $whichdir will equal untrailingslashit(ABSPATH) (is ultimately sourced from our backupable_file_entities filter callback)
	public function backup_makezip_wpcore($whichdir, $backup_file_basename, $index) {

		global $updraftplus, $updraftplus_backup;

		// Actually create the thing

		$wpcore_dirlist = $this->backup_wpcore_dirlist($whichdir);

		if (count($wpcore_dirlist)>0) {
			$updraft_dir = $updraftplus->backups_dir_location();
			$created = $updraftplus_backup->create_zip($wpcore_dirlist, 'wpcore', $backup_file_basename, $index);
			if (is_string($created) || is_array($created)) {
				return $created;
			} else {
				$updraftplus->log("WP Core backup: create_zip returned an error");
				return false;
			}
		} else {
			$updraftplus->log("No backup of WP core directories: there was nothing found to back up");
			$updraftplus->log(sprintf(__("No backup of %s directories: there was nothing found to back up", 'updraftplus'), __('WordPress Core',' updraftplus')), 'error');
			return false;
		}

	}

	// $wp_dir is trailingslashit($wp_filesystem->abspath())
	// Must only use $wp_filesystem methods
	// $working_dir is the directory which contains the backup entity/ies. It is a child of wp-content/upgrade
	// We need to make sure we do not over-write any entities that are restored elsewhere. i.e. Don't touch plugins/themes etc. - but use backupable_file_entities in order to be fully compatible, but with an additional over-ride of touching nothing inside WP_CONTENT_DIR. Can recycle code from the 'others' handling to assist with this.
	public function restore_movein_wpcore($working_dir, $wp_dir) {

		global $updraftplus_restorer;

		# On subsequent archives of a multi-archive set, don't move anything; but do on the first
		$preserve_existing = (isset($updraftplus_restorer->been_restored['wpcore'])) ? 3 : 0;

		return $updraftplus_restorer->move_backup_in($working_dir, $wp_dir, $preserve_existing, array(basename(WP_CONTENT_DIR)), 'wpcore');

	}

}
