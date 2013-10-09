<?php
/*
UpdraftPlus Addon: webdav:WebDAV Support
Description: Allows UpdraftPlus to back up to WebDAV servers
Version: 1.8
Shop: /shop/webdav/
Include: includes/PEAR
Latest Change: 1.7.15
*/

add_action('updraft_webdav_config_javascript', array('UpdraftPlus_Addons_WebDAV', 'updraft_webdav_config_javascript'));
add_action('updraft_webdav_credentials_test', array('UpdraftPlus_Addons_WebDAV', 'updraft_webdav_credentials_test'));
add_filter('updraft_webdav_upload_files', array('UpdraftPlus_Addons_WebDAV', 'upload_files'), 10, 2);
add_filter('updraft_webdav_delete_files', array('UpdraftPlus_Addons_WebDAV', 'delete_files'), 10, 3);
add_action('updraft_webdav_download_file', array('UpdraftPlus_Addons_WebDAV', 'download_file'));
add_filter('updraft_webdav_config_print', array('UpdraftPlus_Addons_WebDAV', 'updraft_webdav_config_print'));
add_filter('updraft_webdav_exists', array('UpdraftPlus_Addons_WebDAV', 'return_yes'));

class UpdraftPlus_Addons_WebDAV {

	public static function upload_files($ret, $backup_array) {

		// If successful, then you must do this:
		// $updraftplus->uploaded_file($file);
		
		global $updraftplus, $updraftplus_backup;
		$webdav = self::bootstrap();

		if (is_wp_error($webdav)) {
			foreach ($webdav->get_error_messages() as $key => $msg) {
				$updraftplus->log($msg);
				$updraftplus->log($msg, 'error');
			}
			return false;
		}

		$options = UpdraftPlus_Options::get_updraft_option('updraft_webdav_settings');
		if (!array($options) || !isset($options['url'])) {
			$updraftplus->log('No WebDAV settings were found');
			$updraftplus->log(sprintf(__('No %s settings were found','updraftplus'),'WebDAV'), 'error');
			return false;
		}

		$any_failures = false;

		$updraft_dir = untrailingslashit($updraftplus->backups_dir_location());
		$url = untrailingslashit($options['url']);

		foreach ($backup_array as $file) {
			$updraftplus->log("WebDAV upload: attempt: $file");
			if (self::chunked_upload($updraft_dir.'/'.$file, $url.'/'.$file)) {
				$updraftplus->uploaded_file($file);
			} else {
				$any_failures = true;
				$updraftplus->log('ERROR: WebDAV: Failed to upload file: '.$file);
				$updraftplus->log(__('Error','updraftplus').': WebDAV: '.sprintf(__('Failed to upload to %s','updraftplus'),$file), 'error');
			}
		}

		if (!$any_failures) {
			return array('webdav_url' => $url);
		} else {
			return null;
		}

	}

	public static function chunked_upload($file, $url) {

		global $updraftplus;

		$orig_file_size = filesize($file);

		$start_offset = 0;
		if (is_file($url)) {
			$url_size = filesize($url);
			if ($url_size == $orig_file_size) {
				$updraftplus->log("WebDAV: This file has already been successfully uploaded");
				return true;
			} elseif ($url_size > $orig_file_size) {
				$updraftplus->log("WebDAV: A larger file than expected ($url_size > $orig_file_size) already exists");
				return false;
			}
			$updraftplus->log("WebDAV: $url_size bytes already uploaded; resuming");
			$start_offset = $url_size;
		}

		$chunks = floor($orig_file_size / 2097152);
		// There will be a remnant unless the file size was exactly on a 5Mb boundary
		if ($orig_file_size % 2097152 > 0 ) $chunks++;

		if (!$fh = fopen($url, 'a')) {
			$updraftplus->log('WebDAV: Failed to open remote file');
			return false;
		}
		if (!$rh = fopen($file, 'rb')) {
			$updraftplus->log('WebDAV: Failed to open local file');
			return false;
		}
		global $updraftplus_webdav_filepath;
		$updraftplus_webdav_filepath = $file;

		$last_touch = time();
		for ($i = 1 ; $i <= $chunks; $i++) {

			$chunk_start = ($i-1)*2097152;
			$chunk_end = min($i*2097152-1, $orig_file_size);

			if ($start_offset > $chunk_end) {
				$updraftplus->log("WebDAV: Chunk $i: Already uploaded");
			} else {

				fseek($fh, $chunk_start);
				fseek($rh, $chunk_start);

				$bytes_left = $chunk_end - $chunk_start;
				while ($bytes_left > 0) {
					if ($buf = fread($rh, 131072)) {
						if (fwrite($fh, $buf, strlen($buf))) {
							$bytes_left = $bytes_left - strlen($buf);
							if (time()-$last_time > 15) { $last_time = time(); touch($file); }
						} else {
							$updraftplus->log('WebDAV: '.sprintf(__("Chunk %s: A %s error occurred",'updraftplus'),$i,'write'), 'error');
							return false;
						}
					} else {
						$updraftplus->log('WebDAV: '.sprintf(__("Chunk %s: A %s error occurred",'updraftplus'),$i,'read'), 'error');
						return false;
					}
				}
			}

			$updraftplus->record_uploaded_chunk(round(100*$i/$chunks,1), "$i", $file);

		}

		if (!fclose($fh)) {
			$updraftplus->log('WebDAV: Upload failed (fclose error)');
			$updraftplus->log('WebDAV: '.__('Upload failed', 'updraftplus'), 'error');
			return false;
		}
		fclose($rh);

		return true;

	}

	public static function delete_files($ret, $files, $webdav_arr = false) {

		global $updraftplus;

		if (is_string($files)) $files = array($files);

		if ($webdav_arr) {
			$url = $webdav_arr['webdav_url'];
		} else {
			UpdraftPlus_Addons_WebDAV::bootstrap();
			$options = UpdraftPlus_Options::get_updraft_option('updraft_webdav_settings');
			if (!array($options) || !isset($options['url'])) {
				$updraftplus->log('No WebDAV settings were found');
				$updraftplus->log(sprintf(__('No %s settings were found','updraftplus'),'WebDAV'), 'error');
				return false;
			}
			$url = untrailingslashit($options['url']);
		}

		$logurl = preg_replace('/:([^\@:]*)\@/', ':(password)@', $url);

		foreach ($files as $file) {
			$updraftplus->log("WebDAV: Delete remote: $logurl/$file");
			if (!unlink("$url/$file")) {
				$updraftplus->log("WebDAV: Delete failed");
			}
		}
		
	}

	public static function download_file($files) {

		global $updraftplus;

		if (is_string($files)) $files = array($files);

		$webdav = self::bootstrap();
		if (is_wp_error($webdav)) {
			foreach ($webdav->get_error_messages() as $key => $msg) {
				$updraftplus->log($msg);
				$updraftplus->log($msg, 'error');
			}
			return false;
		}

		$options = UpdraftPlus_Options::get_updraft_option('updraft_webdav_settings');

		if (!array($options) || !isset($options['url'])) {
			$updraftplus->log('No WebDAV settings were found');
			$updraftplus->log(sprintf(__('No %s settings were found','updraftplus'),'WebDAV'), 'error');
			return false;
		}

		$ret = true;
		foreach ($files as $file) {

			$fullpath = $updraftplus->backups_dir_location().'/'.$file;
			$url = untrailingslashit($options['url']).'/'.$file;

			$start_offset =  (file_exists($fullpath)) ? filesize($fullpath): 0;

			if (@filesize($url) == $start_offset) { $ret = false; continue; }

			if (!$fh = fopen($fullpath, 'a')) {
				$updraftplus->log("WebDAV: Error opening local file: Failed to download: $file");
				$updraftplus->log("$file: ".__("WebDAV Error",'updraftplus').": ".__('Error opening local file: Failed to download','updraftplus'), 'error');
				$ret = false;
				continue;
			}

			if (!$rh = fopen($url, 'rb')) {
				$updraftplus->log("WebDAV: Error opening remote file: Failed to download: $file");
				$updraftplus->log("$file: ".__("WebDAV Error",'updraftplus').": ".__('Error opening remote file: Failed to download','updraftplus'), 'error');
				$ret = false;
				continue;
			}

			if ($start_offset) {
				fseek($fh, $start_offset);
				fseek($rh, $start_offset);
			}

			while (!feof($rh) && $buf = fread($rh, 131072)) {
				if (!fwrite($fh, $buf, strlen($buf))) {
					$updraftplus->log("WebDAV Error: Local write failed: Failed to download: $file");
					$updraftplus->log("$file: ".__("WebDAV Error",'updraftplus').": ".__('Local write failed: Failed to download','updraftplus'), 'error');
					$ret = false;
					continue;
				}
			}
		}

		return $ret;

	}

	public static function return_yes() {
		return 'yes';
	}

	public static function bootstrap() {

		if (!class_exists('HTTP_WebDAV_Client_Stream')) {
			set_include_path(get_include_path().PATH_SEPARATOR.UPDRAFTPLUS_DIR.'/includes/PEAR');
			require('HTTP/WebDAV/Client.php');
		}
		return true;

	}

	public static function updraft_webdav_config_print() {

		$options = UpdraftPlus_Options::get_updraft_option('updraft_webdav_settings');
		$url = isset($options['url']) ? htmlspecialchars($options['url']) : '';

		?>
			<tr class="updraftplusmethod webdav">
				<td></td>
				<td><p><em><?php printf(__('%s is a great choice, because UpdraftPlus supports chunked uploads - no matter how big your site is, UpdraftPlus can upload it a little at a time, and not get thwarted by timeouts.','updraftplus'),'WebDAV');?></em></p></td>
			</tr>
			<tr class="updraftplusmethod webdav">
				<th><?php _e('WebDAV URL','updraftplus');?>:</th>
				<td>
					<input type="text" style="width: 332px" id="updraft_webdav_settings_url" name="updraft_webdav_settings[url]" value="<?php echo($url);?>" />
					<br>
					<?php printf(__('Enter a complete URL, beginning with webdav:// or webdavs:// and including path, username, password and port as required - e.g.%s','updraftplus'),'webdavs://myuser:password@example.com/dav');?>
				</td>
			</tr>

		<tr class="updraftplusmethod webdav">
		<th></th>
		<td><p><button id="updraft-webdav-test" type="button" class="button-primary" style="font-size:18px !important"><?php printf(__('Test %s Settings','updraftplus'),'WebDAV');?></button></p></td>
		</tr>

		<?php

	}

	public static function updraft_webdav_config_javascript() {
		?>
		jQuery('#updraft-webdav-test').click(function(){
			jQuery('#updraft-webdav-test').html('<?php echo esc_js(sprintf(__('Testing %s Settings...', 'updraftplus'),'WebDAV')); ?>');
			var data = {
				action: 'updraft_ajax',
				subaction: 'credentials_test',
				method: 'webdav',
				nonce: '<?php echo wp_create_nonce('updraftplus-credentialtest-nonce'); ?>',
				url: jQuery('#updraft_webdav_settings_url').val()
			};
				//fingerprint: jQuery('#updraft_webdav_settings_fingerprint').val(),
			jQuery.post(ajaxurl, data, function(response) {
				jQuery('#updraft-webdav-test').html('<?php echo esc_js(sprintf(__('Test %s Settings', 'updraftplus'),'WebDAV')); ?>');
				alert('<?php echo esc_js(sprintf(__('%s settings test result:', 'updraftplus'), 'WebDAV'));?> ' + response);
			});
		});
		<?php
	}

	public static function updraft_webdav_credentials_test() {
		if (empty($_POST['url'])) {
			printf(__("Failure: No %s was given.",'updraftplus'),'URL');
			return;
		}

		$url = untrailingslashit($_POST['url']);

		$webdav = self::bootstrap();

		if (is_wp_error($webdav) || $webdav !== true) {
			echo __("Failed",'updraftplus').": ";
			foreach ($webdav->get_error_messages() as $key => $msg) {
				echo "$msg\n";
			}
			die;
		}

		@mkdir($url);

		$testfile = $url.'/'.md5(time().rand());
		if (file_put_contents($testfile, 'test')) {
			_e("Success",'updraftplus');
			@unlink($testfile);
		} else {
			_e("Failed: We were not able to place a file in that directory - please check your credentials.",'updraftplus');
		}

		die;

	}

}

?>
