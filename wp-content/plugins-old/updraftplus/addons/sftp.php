<?php

/*

UpdraftPlus Addon: sftp:SFTP, SCP and FTPS Support

Description: Allows UpdraftPlus to back up to both SFTP, SSH and encrypted FTP servers

Version: 1.9

Shop: /shop/sftp/

Latest Change: 1.7.6

*/



if (!defined ('ABSPATH')) die('No direct access allowed');



$updraftplus_addons_sftp = new UpdraftPlus_Addons_SFTP;

class UpdraftPlus_Addons_SFTP {



	public function __construct() {

		add_action('updraft_sftp_config_javascript', array($this, 'updraft_sftp_config_javascript'));

		add_action('updraft_sftp_credentials_test', array($this, 'updraft_sftp_credentials_test'));

		add_filter('updraft_sftp_upload_files', array($this, 'upload_files'), 10, 2);

		add_filter('updraft_sftp_delete_files', array($this, 'delete_files'), 10, 3);

		add_action('updraft_sftp_download_file', array($this, 'download_file'));

		add_filter('updraft_sftp_config_print', array($this, 'updraft_sftp_config_print'));

		add_filter('updraft_sftp_exists', array($this, 'return_yes'));

		add_filter('updraft_sftp_ftps_notice', array($this, 'ftps_notice'));

	}



	public function ftps_notice() {

		return __("Encrypted FTP is available, and will be automatically tried first (before falling back to non-encrypted if it is not successful), unless you disable it using the expert options. The 'Test FTP Login' button will tell you what type of connection is in use.",'updraftplus').' '.__('Some servers advertise encrypted FTP as available, but then time-out (after a long time) when you attempt to use it. If you find this happenning, then go into the "Expert Options" (below) and turn off SSL there.', 'updraftplus').' '.__('Explicit encryption is used by default. To force implicit encryption (port 990), add :990 to your FTP server below.',' updraftplus');

	}



	public function do_connect_and_chdir() {

		$options = UpdraftPlus_Options::get_updraft_option('updraft_sftp_settings');

		if (!array($options)) return new WP_Error('no_settings', sprintf(__('No %s settings were found','updraftplus'),'SCP/SFTP'));

		

		if (empty($options['host'])) return new WP_Error('missing_setting', sprintf(__('No %s found','updraftplus'),__('SCP/SFTP host setting','updraftplus')));

		if (empty($options['user'])) return new WP_Error('missing_setting', sprintf(__('No %s found','updraftplus'),__('SCP/SFTP user setting','updraftplus')));

		if (empty($options['pass'])) return new WP_Error('missing_setting', sprintf(__('No %s found','updraftplus'),__('SCP/SFTP password','updraftplus')));

		$host = $options['host'];

		$user = $options['user'];

		$pass = $options['pass'];

		$port = empty($options['port']) ? 22 : (int)$options['port'];

		$path = empty($options['path']) ? '' : $options['path'];

		$scp = empty($options['scp']) ? false : true;



		global $updraftplus_addons_sftp;

		$updraftplus_addons_sftp->path = $path;



		$fingerprint = '';



		$sftp = $updraftplus_addons_sftp->connect($host, $port, $fingerprint, $user, $pass, $scp);



		if (is_wp_error($sftp)) return $sftp;



		// So far, so good

		if ($path) {

			if ($scp) {

				# May fail - e.g. if directory already exists, or if the remote shell is restricted

				@$updraftplus_addons_sftp->ssh->exec("mkdir ".escapeshellarg($path));

				# N.B. - have not changed directory (since cd may not be an available command)

			} else {

				@$sftp->mkdir($path);

				// See if the directory now exists

				if (!$sftp->chdir($path)) {

					@$sftp->disconnect();

					return new WP_Error('nochdir', __("Check your file permissions: Could not successfully create and enter directory:",'updraftplus')." $path");

				}

			}

		}



		return $sftp;



	}



	public function upload_files($ret, $backup_array) {



		// If successful, then you must do this:

		// $updraftplus->uploaded_file($file);



		global $updraftplus, $updraftplus_backup, $updraftplus_addons_sftp;

		$sftp = $updraftplus_addons_sftp->do_connect_and_chdir();

		if (is_wp_error($sftp)) {

			foreach ($sftp->get_error_messages() as $key => $msg) {

				$updraftplus->log($msg);

				$updraftplus->log($msg, 'error');

			}

			return false;

		}



		if (empty($updraftplus_addons_sftp->scp)) {

			$updraftplus->log("SFTP: Successfully logged in");

		} else {

			$updraftplus->log("SCP: Successfully logged in");

		}



		$any_failures = false;



		$updraft_dir = $updraftplus->backups_dir_location().'/';



		foreach ($backup_array as $file) {

			$updraftplus->log("SCP/SFTP upload: attempt: $file");

			if (empty($updraftplus_addons_sftp->scp)) {

				if ($sftp->put($file, $updraft_dir.'/'.$file , NET_SFTP_LOCAL_FILE)) {

					$updraftplus->uploaded_file($file);

				} else {

					$any_failures = true;

					$updraftplus->log('ERROR: SFTP: Failed to upload file: '.$file);

					$updraftplus->log(sprintf(__('%s Error: Failed to upload','updraftplus'),'SFTP').": $file", 'error');

				}

			} else {

				$rfile = (empty($updraftplus_addons_sftp->path)) ? $file : trailingslashit($updraftplus_addons_sftp->path).$file;



				if ($sftp->put($rfile, $updraft_dir.'/'.$file, NET_SCP_LOCAL_FILE)) {

					$updraftplus->uploaded_file($file);

				} else {

					$any_failures = true;

					$updraftplus->log('ERROR: SCP: Failed to upload file: '.$file);

					$updraftplus->log(sprintf(__('%s Error: Failed to upload','updraftplus'),'SCP').": $file", 'error');

				}

			}

		}



// 		if (empty($updraftplus_addons_sftp->scp)) {

// 			@$sftp->disconnect();

// 		} else {

// 			@$updraftplus_addons_sftp->ssh->disconnect();

// 		}



		if (!$any_failures) {

			return array('sftp_object' => $sftp);

		} else {

			return null;

		}



	}



	public function delete_files($ret, $files, $sftp_arr = false) {



		global $updraftplus, $updraftplus_addons_sftp;

		if (is_string($files)) $files=array($files);



		if ($sftp_arr) {

			$sftp = $sftp_arr['sftp_object'];

		} else {

			$sftp = $updraftplus_addons_sftp->do_connect_and_chdir();

			if (is_wp_error($sftp)) {

				foreach ($sftp->get_error_messages() as $key => $msg) {

					$updraftplus->log($msg);

					$updraftplus->log($msg, 'error');

				}

				return false;

			}

		}



		$some_success = false;



		foreach ($files as $file) {



			if (empty($updraftplus_addons_sftp->scp)) {

				$updraftplus->log("SFTP: Delete remote: $file");

			} else {

				$updraftplus->log("SCP: Delete remote: $file");

			}



			if (empty($updraftplus_addons_sftp->scp)) {

				if (!$sftp->delete($file, false)) {

					$updraftplus->log("SFTP: Delete failed: $file");

				} else {

					$some_success = true;

				}

			} else {

				$rfile = (empty($updraftplus_addons_sftp->path)) ? $file : trailingslashit($updraftplus_addons_sftp->path).$file;

				if (!$updraftplus_addons_sftp->ssh->exec("rm -f ".escapeshellarg($rfile))) {

					$updraftplus->log("SCP: Delete failed: $rfile");

				} else {

					$some_success = true;

				}

			}

		}



		return $some_success;



	}



	public function download_file($file) {



		global $updraftplus, $updraftplus_addons_sftp;



		$sftp = $updraftplus_addons_sftp->do_connect_and_chdir();

		if (is_wp_error($sftp)) {

			foreach ($sftp->get_error_messages() as $key => $msg) {

				$updraftplus->log($msg);

				$updraftplus->log($msg, 'error');

			}

			return false;

		}



		$fullpath = $updraftplus->backups_dir_location().'/'.$file;



		$rfile = (empty($updraftplus_addons_sftp->scp) || empty($updraftplus_addons_sftp->path)) ? $file : trailingslashit($updraftplus_addons_sftp->path).$file;

		if (!$sftp->get($rfile, $fullpath)) {

			$updraftplus->log("SFTP Error: Failed to download: $rfile");

			$updraftplus->log(sprintf(__('%s Error: Failed to download','updraftplus'),'SCP/SFTP').": $rfile", 'error');

		}



	}



	public function return_yes() {

		return 'yes';

	}



	public function connect($host, $port = 22, $fingerprint, $user, $pass, $scp = false) {



		global $updraftplus, $updraftplus_addons_sftp;



		$updraftplus_addons_sftp->scp = $scp;



		if ($scp) {

			$updraftplus->ensure_phpseclib('Net_SSH2', 'Net/SSH2');

			$updraftplus->ensure_phpseclib('Net_SCP', 'Net/SCP');

			$updraftplus_addons_sftp->ssh = new Net_SSH2($host, $port, 7);

		} else {

			$updraftplus->ensure_phpseclib('Net_SFTP', 'Net/SFTP');

			if (!defined('NET_SFTP_LOGGING')) define('NET_SFTP_LOGGING', NET_SFTP_LOG_SIMPLE);

			$updraftplus_addons_sftp->ssh = new Net_SFTP($host, $port, 7);

		}





		if (!$updraftplus_addons_sftp->ssh->login($user, $pass)) {

			 return new WP_Error('ssh2_nologin', 'SSH 2 login failed');

		}



// 		if ($fingerprint) {

// 			$fingerprint = str_replace(':', '', $fingerprint);

// 			// Fingerprint checking not yet supported by phpseclib

// 			return new WP_Error('debug', "Remove fingerprint: $remote_finger");

// 		}



		if ($scp) {

			return new Net_SCP($updraftplus_addons_sftp->ssh);

		} else {

			return $updraftplus_addons_sftp->ssh;

		}



	}



	public function updraft_sftp_config_print() {



		$options = UpdraftPlus_Options::get_updraft_option('updraft_sftp_settings');

		$host = isset($options['host']) ? htmlspecialchars($options['host']) : '';

		$user = isset($options['user']) ? htmlspecialchars($options['user']) : '';

		$pass = isset($options['pass']) ? htmlspecialchars($options['pass']) : '';

		$port = isset($options['port']) ? htmlspecialchars($options['port']) : 22;

		$path = isset($options['path']) ? htmlspecialchars($options['path']) : '';

		$scp = (isset($options['scp']) && $options['scp']) ? true : false;

		$fingerprint = isset($options['fingerprint']) ? htmlspecialchars($options['fingerprint']) : '';



		?>

			<tr class="updraftplusmethod sftp">

				<th>SFTP/SCP:</th>

				<td>

					<p><em><?php _e('Resuming partial uploads is not supported, so you will need to ensure that your webserver allows PHP processes to run long enough to upload your largest backup file.','updraftplus');?></em></p>

				</td>

			</tr>



			<tr class="updraftplusmethod sftp">

				<th><?php _e('Host','updraftplus');?>:</th>

				<td>

					<input type="text" style="width: 292px" id="updraft_sftp_settings_host" name="updraft_sftp_settings[host]" value="<?php echo $host;?>" />

				</td>

			</tr>



			<tr class="updraftplusmethod sftp">

				<th><?php _e('Port','updraftplus');?>:</th>

				<td>

					<input type="text" style="width: 292px" id="updraft_sftp_settings_port" name="updraft_sftp_settings[port]" value="<?php echo $port; ?>" />

				</td>

			</tr>



			<tr class="updraftplusmethod sftp">

				<th><?php _e('Username','updraftplus');?>:</th>

				<td>

					<input type="text" autocomplete="off" style="width: 292px" id="updraft_sftp_settings_user" name="updraft_sftp_settings[user]" value="<?php echo $user; ?>" />

				</td>

			</tr>



			<tr class="updraftplusmethod sftp">

				<th><?php _e('Password','updraftplus');?>:</th>

				<td>

					<input type="<?php echo apply_filters('updraftplus_admin_secret_field_type', 'text'); ?>" autocomplete="off" style="width: 292px" id="updraft_sftp_settings_pass" name="updraft_sftp_settings[pass]" value="<?php echo $pass;?>" />

				</td>

			</tr>



			<!--

			<tr class="updraftplusmethod sftp">

				<th>Fingerprint:</th>

				<td>

					<input type="text" style="width: 292px" id="updraft_sftp_settings_fingerprint" name="updraft_sftp_settings[fingerprint]" value="$fingerprint" /><br><em>MD5 (128-bit) fingerprint, in hex format - should have the same length and general appearance as this (colons optional): 73:51:43:b1:b5:fc:8b:b7:0a:3a:a9:b1:0f:69:73:a8. Using a fingerprint is not essential, but you are not secure against <a href="http://en.wikipedia.org/wiki/Man-in-the-middle_attack">MITM attacks</a> if you do not use one</em>.

				</td>

			</tr>

			-->



			<tr class="updraftplusmethod sftp">

				<th><?php _e('Directory path','updraftplus');?>:</th>

				<td>

					<input type="text" style="width: 292px" id="updraft_sftp_settings_path" name="updraft_sftp_settings[path]" value="<?php echo $path; ?>" /><br><em><?php _e('Where to change directory to after logging in - often this is relative to your home directory.','updraftplus');?></em>.

				</td>

			</tr>



			<tr class="updraftplusmethod sftp">

				<th>SCP:</th>

				<td>

					<input type="checkbox" id="updraft_sftp_settings_scp" name="updraft_sftp_settings[scp]" value="1"<?php if ($scp) echo ' checked="checked"'; ?>> <label for="updraft_sftp_settings_scp"><?php _e('Use SCP instead of SFTP', 'updraftplus');?></label>

				</td>

			</tr>



		<tr class="updraftplusmethod sftp">

		<th></th>

		<td><p><button id="updraft-sftp-test" type="button" class="button-primary" style="font-size:18px !important"><?php echo sprintf(__('Test %s Settings','updraftplus'),'SFTP/SCP');?></button></p></td>

		</tr>

		<?php



	}



	public function updraft_sftp_config_javascript() {

		?>

		jQuery('#updraft-sftp-test').click(function(){

			jQuery('#updraft-sftp-test').html('<?php echo esc_js(sprintf(__('Testing %s Settings...', 'updraftplus'),'SCP/SFTP'));?>');

			scp = jQuery('#updraft_sftp_settings_scp').is(':checked') ? 1 : 0;

			var data = {

				action: 'updraft_ajax',

				subaction: 'credentials_test',

				method: 'sftp',

				nonce: '<?php echo wp_create_nonce('updraftplus-credentialtest-nonce'); ?>',

				user: jQuery('#updraft_sftp_settings_user').val(),

				pass: jQuery('#updraft_sftp_settings_pass').val(),

				host: jQuery('#updraft_sftp_settings_host').val(),

				port: jQuery('#updraft_sftp_settings_port').val(),

				path: jQuery('#updraft_sftp_settings_path').val(),

				scp: scp,

			};

				//fingerprint: jQuery('#updraft_sftp_settings_fingerprint').val(),

			jQuery.post(ajaxurl, data, function(response) {

				jQuery('#updraft-sftp-test').html('<?php echo esc_js(sprintf(__('Test %s Settings', 'updraftplus'),'SCP/SFTP')); ?>');

				if (scp) {

					alert('<?php echo esc_js(sprintf(__('%s settings test result:', 'updraftplus'), 'SCP'));?> ' + response);

				} else {

					alert('<?php echo esc_js(sprintf(__('%s settings test result:', 'updraftplus'), 'SFTP'));?> ' + response);

				}

			});

		});

		<?php

	}



	public function updraft_sftp_credentials_test() {

		if (empty($_POST['host'])) {

			printf(__("Failure: No %s was given.",'updraftplus'),__('host name','updraftplus'));

			return;

		}

		if (empty($_POST['user'])) {

			printf(__("Failure: No %s was given.",'updraftplus'),__('username','updraftplus'));

			return;

		}

		if (empty($_POST['pass'])) {

			printf(__("Failure: No %s was given.",'updraftplus'),__('password','updraftplus'));

			return;

		}

		$port = empty($_POST['port']) ? 22 : $_POST['port'];

		if (!is_numeric($port)) {

			_e("Failure: Port must be an integer.",'updraftplus');;

			return;

		}

		$path = empty($_POST['path']) ? '' : $_POST['path'];



		$fingerprint = empty($_POST['fingerprint']) ? '' : $_POST['fingerprint'];



		$scp = empty($_POST['scp']) ? 0 : 1;



		$host = $_POST['host'];

		$user = stripslashes($_POST['user']);

		$pass = stripslashes($_POST['pass']);



		global $updraftplus_addons_sftp;

		$sftp = $updraftplus_addons_sftp->connect($host, $port, $fingerprint, $user, $pass, $scp);



		if (is_wp_error($sftp)) {

			_e("Failed",'updraftplus').": ";

			foreach ($sftp->get_error_messages() as $key => $msg) {

				echo "$msg\n";

			}

			die;

		}



		// So far, so good

		if (empty($scp)) {

			if ($path) {

				@$sftp->mkdir($path);

				// See if the directory now exists

				if (!$sftp->chdir($path)) {

					echo __('Check your file permissions: Could not successfully create and enter:','updraftplus')." (".htmlspecialchars($path).")";

					@$sftp->disconnect();

					die;

				}

			}

		} elseif ($path) {

			$updraftplus_addons_sftp->ssh->exec('mkdir '.escapeshellarg($path));

		}



		$testfile = md5(time().rand());

		if (!empty($scp) && !empty($path)) $testfile = trailingslashit($path).$testfile;

		// Now test uploading a file

		$putfile = $sftp->put($testfile, 'test');

		if (empty($scp)) {

			$sftp->delete($testfile);

		} else {

			$updraftplus_addons_sftp->ssh->exec('rm -f '.escapeshellarg($testfile));

		}



		if ($putfile) {

			_e('Success','updraftplus');

		} else {

			if (empty($scp)) {

				_e("Failed: We were able to log in and move to the indicated directory, but failed to successfully create a file in that location.");

			} else {

				_e("Failed: We were able to log in, but failed to successfully create a file in that location.");

			}

		}



		if ($updraftplus_addons_sftp->scp) {

			@$updraftplus_addons_sftp->ssh->disconnect();

		} else {

			@$sftp->disconnect();

		}

		die;



	}



}

	

/*



Adapted from http://www.solutionbot.com/2009/01/02/php-ftp-class/



Our main tweaks to this class are to enable SSL with fallback for explicit encryption, and to provide rudimentary implicit support (the support for implicit is via Curl (since PHP's functions do not support it), and only extends to methods that we know we use).



We somewhat crudely detect the request for implicit via use of port 990. But in the real world, it's unlikely we'll come across anything else - if we do, we can abstract a little more.



*/

class UpdraftPlus_ftp_wrapper {

	private $conn_id;

	private $host;

	private $username;

	private $password;

	private $port;

	public  $timeout = 90;

	public  $passive = true;

	// Whether to *allow* (not necessarily require) SSL

	public  $ssl = true;

	public  $system_type = '';

	public $login_type = 'non-encrypted';



	public $use_server_certs = false;

	public $disable_verify = true;



	public $curl_handle;

 

	public function __construct($host, $username, $password, $port = 21) {

		$this->host     = $host;

		$this->username = $username;

		$this->password = $password;

		$this->port     = $port;

	}

 

	public function connect() {



		// Implicit SSL - not handled via PHP's native ftp_ functions, so we use curl instead

		if ($this->port == 990) {

			if ($this->ssl == false) {

				$this->port = 21;

			} else {

				$this->curl_handle = curl_init();

				if (!$this->curl_handle) {

					$this->port = 21;

				} else {

					$options = array(

						CURLOPT_USERPWD        => $this->username . ':' . $this->password,

						CURLOPT_FTP_SSL        => CURLFTPSSL_TRY, //CURLFTPSSL_ALL, // require SSL For both control and data connections

						CURLOPT_FTPSSLAUTH     => CURLFTPAUTH_SSL, // CURLFTPAUTH_DEFAULT, // let cURL choose the FTP authentication method (either SSL or TLS)

						CURLOPT_PORT           => 990,

						CURLOPT_CONNECTTIMEOUT => 20,

						// CURLOPT_TIMEOUT timeout is not just a "no-activity" timeout, but a total time limit on any Curl operation - undesirable

						// CURLOPT_TIMEOUT        => 20,

						CURLOPT_FTP_CREATE_MISSING_DIRS => true

					);

					// Prints to STDERR by default - noisy

					if (defined('WP_DEBUG') && WP_DEBUG==true && UpdraftPlus_Options::get_updraft_option('updraft_debug_mode')) {

						$options[CURLOPT_VERBOSE] = true;

					}

					if ($this->disable_verify) {

						$options[CURLOPT_SSL_VERIFYPEER] = false;

						$options[CURLOPT_SSL_VERIFYHOST] = 0;

					} else {

						$options[CURLOPT_SSL_VERIFYPEER] = true;

					}

					if (!$this->use_server_certs) {

						$options[CURLOPT_CAINFO] = UPDRAFTPLUS_DIR.'/includes/cacert.pem';

					}

					if ($this->passive != true) $options[ CURLOPT_FTPPORT ] = '-';

					foreach ( $options as $option_name => $option_value ) {

						if ( ! curl_setopt( $this->curl_handle, $option_name, $option_value ) ) {

// 							throw new Exception( sprintf( 'Could not set cURL option: %s', $option_name ) );

							global $updraftplus;

							if (is_a($updraftplus, 'UpdraftPlus')) {

								$updraftplus->log("Curl exception: will revert to normal FTP");

							}

							$this->port = 21;

						}

					}

				}

				// All done - leave

				if ($this->port == 990) {

					$this->login_type = 'encrypted (implicit, port 990)';

					return true;

				}

			}

		}



		if (function_exists('ftp_ssl_connect') && $this->ssl !== false) $this->conn_id = ftp_ssl_connect($this->host, $this->port, 14);



		if ($this->conn_id) {

			$this->login_type = 'encrypted';

			$this->ssl = true;

		} else {

			$this->conn_id = ftp_connect($this->host, $this->port, 25);

		}



		if (!$this->conn_id) return false;

 

		$result = ftp_login($this->conn_id, $this->username, $this->password);

 

		if ($result == true) {

			ftp_set_option($this->conn_id, FTP_TIMEOUT_SEC, $this->timeout);

 

			if ($this->passive == true) {

				ftp_pasv($this->conn_id, true);

			} else {

				ftp_pasv($this->conn_id, false);

			}

 

			$this->system_type = ftp_systype($this->conn_id);

 

			return true;

		}

		else {

			return false;

		}

	}

 

	function curl_progress_function($download_size, $downloaded_size, $upload_size, $uploaded_size) {



		if ($uploaded_size<1) return;



		global $updraftplus;



		$percent = 100*($uploaded_size+$this->upload_from)/$this->upload_size;



		// Log every megabyte or at least every 20%

		if ($percent > $this->upload_last_recorded_percent + 20 || $uploaded_size > $this->uploaded_bytes + 1048576) {

			$updraftplus->record_uploaded_chunk(round($percent,1), '', $this->upload_local_path);

			$this->upload_last_recorded_percent=floor($percent);

			$this->uploaded_bytes = $uploaded_size;

		}



	}



	public function put($local_file_path, $remote_file_path, $mode = FTP_BINARY, $resume = false, $updraftplus = false) {



		$file_size = filesize($local_file_path);



		$existing_size = 0;

		if ($resume) {



			if ($this->curl_handle) {

				if ($this->curl_handle === true) $this->connect();

				curl_setopt($this->curl_handle, CURLOPT_URL, 'ftps://'.$this->host.'/'.$remote_file_path);

				curl_setopt($this->curl_handle, CURLOPT_NOBODY, true);

				curl_setopt($this->curl_handle, CURLOPT_HEADER, false);



				// curl_setopt($this->curl_handle, CURLOPT_FORBID_REUSE, true);



				$getsize = curl_exec($this->curl_handle);

				if ($getsize) {

					$sizeinfo = curl_getinfo($this->curl_handle);

					$existing_size = $sizeinfo['download_content_length'];

				} else {

					if (is_a($updraftplus, 'UpdraftPlus')) $updraftplus->log("Curl: upload error: ".curl_error($this->curl_handle));

				}

			} else {

				$existing_size = ftp_size($this->conn_id, $remote_file_path);

			}

			// In fact curl can return -1 as the value, for a non-existant file

			if ($existing_size <=0) {

				$resume = false;

				$existing_size = 0;

			} else {

				if (is_a($updraftplus, 'UpdraftPlus')) $updraftplus->log("File already exists at remote site: size $existing_size. Will attempt resumption.");

				if ($existing_size >= $file_size) {

					if (is_a($updraftplus, 'UpdraftPlus')) $updraftplus->log("File is apparently already completely uploaded");

					return true;

				}

			}

		}



		// From here on, $file_size is only used for logging calculations. We want to avoid divsion by zero.

		$file_size = max($file_size, 1);



		if (!$fh = fopen($local_file_path, 'rb')) return false;

		if ($existing_size) fseek($fh, $existing_size);



		// FTPS (i.e. implicit encryption)

		if ($this->curl_handle) {

			// Reset the curl object (because otherwise we get errors that make no sense)

			$this->connect();

			if (version_compare(phpversion(), '5.3.0', '>=')) {

				curl_setopt($this->curl_handle, CURLOPT_PROGRESSFUNCTION, array($this, 'curl_progress_function'));

				curl_setopt($this->curl_handle, CURLOPT_NOPROGRESS, false);

			}

			$this->upload_local_path = $local_file_path;

			$this->upload_last_recorded_percent = 0;

			$this->upload_size = max($file_size, 1);

			$this->upload_from = $existing_size;

			$this->uploaded_bytes = $existing_size;

			curl_setopt($this->curl_handle, CURLOPT_URL, 'ftps://'.$this->host.'/'.$remote_file_path);

			if ($existing_size) curl_setopt($this->curl_handle, CURLOPT_FTPAPPEND, true);



			// DOn't set CURLOPT_UPLOAD=true before doing the size check - it results in a bizarre error

			curl_setopt($this->curl_handle, CURLOPT_UPLOAD, true);

			curl_setopt($this->curl_handle, CURLOPT_INFILE, $fh);

			$output = curl_exec($this->curl_handle);

			fclose($fh);

			if (is_a($updraftplus, 'UpdraftPlus') && !$output) {

				$updraftplus->log("FTPS error: ".curl_error($this->curl_handle));

			} elseif ($updraftplus === true && !$output) {

				echo __('Error:','updraftplus').' '.curl_error($this->curl_handle)."\n";

			}

			// Mark as used

			$this->curl_handle = true;

			return $output;

		}



		$ret = ftp_nb_fput($this->conn_id, $remote_file_path, $fh, FTP_BINARY, $existing_size);



		// $existing_size can now be re-purposed



		while ($ret == FTP_MOREDATA) {

			if (is_a($updraftplus, 'UpdraftPlus')) {

				$new_size = ftell($fh);

				if ($new_size - $existing_size > 524288) {

					$existing_size = $new_size;

					$percent = round(100*$new_size/$file_size,1);

					$updraftplus->record_uploaded_chunk($percent, '', $local_file_path);

				}

			}

			// Continue upload

			$ret = ftp_nb_continue($this->conn_id);

		}



		fclose($fh);



		if ($ret != FTP_FINISHED) {

			if (is_a($updraftplus, 'UpdraftPlus')) $updraftplus->log("FTP upload: error ($ret)");

			return false;

		}



		return true;



	}

 



	public function get($local_file_path, $remote_file_path, $mode = FTP_BINARY, $resume = false,  $updraftplus = false) {



		$file_last_size = 0;



		if ($resume) {

			if (!$fh = fopen($local_file_path, 'ab')) return false;

			clearstatcache($local_file_path);

			$file_last_size = filesize($local_file_path);

		} else {

			if (!$fh = fopen($local_file_path, 'wb')) return false;

		}



		// Implicit FTP, for which we use curl (since PHP's native FTP functions don't handle implicit FTP)

		if ($this->curl_handle) {

			if ($resume) curl_setopt($this->curl_handle, CURLOPT_RESUME_FROM, $resume);

			curl_setopt($this->curl_handle, CURLOPT_NOBODY, false);

			curl_setopt($this->curl_handle, CURLOPT_URL, 'ftps://'.$this->host.'/'.$remote_file_path);

			curl_setopt($this->curl_handle, CURLOPT_UPLOAD, false);

			curl_setopt($this->curl_handle, CURLOPT_FILE, $fh);

			$output = curl_exec($this->curl_handle);

			if ($output) {

				if ($updraftplus) $updraftplus->log("FTP fetch: fetch complete");

			} else {

				if ($updraftplus) $updraftplus->log("FTP fetch: fetch failed");

			} 

			return $output;

		}



		$ret = ftp_nb_fget($this->conn_id, $fh, $remote_file_path, $mode, $file_last_size);



		if (false == $ret) return false;



		while ($ret == FTP_MOREDATA) {



			if ($updraftplus) {

				$file_now_size = filesize($local_file_path);

				if ($file_now_size - $file_last_size > 524288) {

					$updraftplus->log("FTP fetch: file size is now: ".sprintf("%0.2f",filesize($local_file_path)/1048576)." Mb");

					$file_last_size = $file_now_size;

				}

				clearstatcache($local_file_path);

			}



			$ret = ftp_nb_continue($this->conn_id);

		}



		fclose($fh);



		if ($ret == FTP_FINISHED) {

			if ($updraftplus) $updraftplus->log("FTP fetch: fetch complete");

			return true;

		} else {

			if ($updraftplus) $updraftplus->log("FTP fetch: fetch failed");

			return false;

		} 



	}



	public function chmod($permissions, $remote_filename)

	{

		if ($this->is_octal($permissions)) {

			$result = ftp_chmod($this->conn_id, $permissions, $remote_filename);

			return ($result) ? true : false;

		} else {

			throw new Exception('$permissions must be an octal number');

		}

	}

 

	public function chdir($directory) {

		ftp_chdir($this->conn_id, $directory);

	}

 

	public function delete($remote_file_path) {



		if ($this->curl_handle) {

			if ($this->curl_handle === true) $this->connect();

			curl_setopt($this->curl_handle, CURLOPT_URL, 'ftps://'.$this->host.'/'.$remote_file_path);

			curl_setopt($this->curl_handle, CURLOPT_RETURNTRANSFER, true);

			curl_setopt($this->curl_handle, CURLOPT_QUOTE, array('DELE '.$remote_file_path));

			// Unset some (possibly) previously-set options

			curl_setopt($this->curl_handle, CURLOPT_UPLOAD, false);

			curl_setopt( $this->curl_handle, CURLOPT_INFILE, STDIN );

			$output = curl_exec($this->curl_handle);

			return $output;

		}



		return (ftp_delete($this->conn_id, $remote_file_path)) ? true : false;



	}

 

	public function make_dir($directory) {

		if (ftp_mkdir($this->conn_id, $directory)) {

			return true;

		} else  {

			return false;

		}

	}

 

	public function rename($old_name, $new_name) {

		if (ftp_rename($this->conn_id, $old_name, $new_name)) {

			return true;

		} else {

			return false;

		}

	}

 

	public function remove_dir($directory)

	{

		if (ftp_rmdir($this->conn_id, $directory))

		{

			return true;

		}

		else

		{

			return false;

		}

	}

 

	public function dir_list($directory)

	{

		$contents = ftp_nlist($this->conn_id, $directory);

		return $contents;

	}

 

	public function cdup() {

		ftp_cdup($this->conn_id);

	}

 

	public function current_dir()

	{

		return ftp_pwd($this->conn_id);

	}

 

	private function is_octal($i) {

		return decoct(octdec($i)) == $i;

	}

 

	public function __destruct()

	{

		if ($this->conn_id)

		{

			ftp_close($this->conn_id);

		}

	}

}





?>

