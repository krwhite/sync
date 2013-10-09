<?php
/*
Obtain and manage extra features for UpdraftPlus Backup

This plugin communicates with the mothership via non-encrypted HTTP; nothing of significant value is thereby put at risk - see http://updraftplus.com/faqs/tell-me-about-my-updraftplus-com-account/
However, if your organisation has strong requirements for use of SSL, then add the following line to your wp-config.php:

define('UPDRAFTPLUS_ADDONS_SSL', true);

This plugin:
- over-rides the update mechanism for the UpdraftPlus plugin, so that we can get them from our site
- shows the user his installed and available add-ons
- also over-rides its own update mechanism

This directory should not be added to the wordpress.org SVN

*/

define('UDADDONS2_DIR', dirname(realpath(__FILE__)));
define('UDADDONS2_URL', plugins_url('updraftplus').'/udaddons');
define('UDADDONS2_SLUG', 'updraftplus-addons');
define('UDADDONS2_PAGESLUG', 'updraftplus-addons2');

$udaddons2_mothership = (defined('UPDRAFTPLUS_ADDONS_SSL') && UPDRAFTPLUS_ADDONS_SSL == true) ? 'https://www.simbahosting.co.uk' : 'http://updraftplus.com';

$udaddons2_mothership = (defined('UPDRAFTPLUS_ADDONS_TESTING') && UPDRAFTPLUS_ADDONS_TESTING == true && isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost') ? 'http://localhost/ud' : $udaddons2_mothership;

$updraftplus_addons2 = new UpdraftPlusAddons2('updraftplus', $udaddons2_mothership);
#$updraftplus_addons->debug = true;

class UpdraftPlusAddons2 {

	var $slug;
	var $url;
	public $debug = false;

	var $user_addons;
	var $user_support;

	var $available_addons;
	var $remote_addons;

	var $plug_updatechecker;

	function admin_menu() {

		global $pagenow;

		if (!function_exists('is_plugin_active')) require_once(ABSPATH.'wp-admin/includes/plugin.php');
		if (is_plugin_active('updraftplus-addons/updraftplus-addons.php')) {
			deactivate_plugins('updraftplus-addons/updraftplus-addons.php');
			if ('options-general.php' == $pagenow && !empty($_REQUEST['page']) && 'updraftplus-addons' == $_REQUEST['page']) {
				wp_redirect(admin_url('options-general.php?page='.UDADDONS2_PAGESLUG));
				exit;
			}
			// Do nothing more this time to avoid duplication
			return;
		} elseif (is_dir(WP_PLUGIN_DIR.'/updraftplus-addons') && current_user_can('delete_plugins')) {
			# Exists, but not activen - nag them
			if ('options-general.php' == $pagenow || 'plugins.php' == $pagenow) add_action('admin_notices', array($this, 'deinstall_udaddons'));
		} 

		if (class_exists('UpdraftPlusAddons')) return;

		// Refresh, if specifically requested
		if ('options-general.php' == $pagenow && isset($_GET['udm_refresh'])) {
			if ($this->plug_updatechecker) $this->plug_updatechecker->checkForUpdates();
		}

		require_once(UDADDONS2_DIR.'/options.php');
		$options = new UpdraftPlusAddOns_Options2($this->slug, 'UpdraftPlus Addons', $this->url);

	}

	function deinstall_udaddons() {
		$del = '<a href="' . wp_nonce_url('plugins.php?action=delete-selected&amp;checked[]=updraftplus-addons/updraftplus-addons.php&amp;plugin_status=all&amp;paged=1&amp;s=', 'bulk-plugins') . '" title="' . esc_attr__('Delete plugin') . '" class="delete">' . 'delete the UpdraftPlus Addons Manager plugin' . '</a>';
		$this->show_admin_warning('You can '.$del.' - it is obsolete (all of its functions, including your add-ons, are now included in the main UpdraftPlus plugin obtained from updraftplus.com).');
	}

	function show_admin_warning($message, $class = "updated") {
		echo '<div class="updraftmessage '.$class.'">'."<p>$message</p></div>";
	}

	function __construct($slug, $url) {
		$this->slug = $slug;
		$this->url = $url;
		#add_action('plugins_loaded', array($this, 'plugins_loaded'));

		add_action('admin_menu', array($this, 'admin_menu'));
		add_action('wp_ajax_udaddons_claimaddon', array($this, 'ajax_udaddons_claimaddon'));

		if (class_exists('UpdraftPlusAddons')) return;

		// Over-ride update mechanism for the plugin, and for ourselves
		if (is_readable(UDADDONS2_DIR.'/plugin-updates/plugin-update-checker.php')) {

			if (!class_exists('PluginUpdateChecker')) require(UDADDONS2_DIR.'/plugin-updates/plugin-update-checker.php');

			$options = get_option(UDADDONS2_SLUG.'_options');
			$email = isset($options['email']) ? $options['email'] : '';
			if ($email) {
				$plug_updatechecker = new PluginUpdateChecker($this->url."/plugin-info/", $this->slug.'/'.$this->slug.'.php', $this->slug, 24);
				$plug_updatechecker->addQueryArgFilter(array($this, 'updater_queryargs_plugin'));
				if ($this->debug) $plug_updatechecker->debugMode = true;
				$this->plug_updatechecker = $plug_updatechecker;
			}

		}

	}

	function ajax_udaddons_claimaddon() {

		$nonce = (empty($_REQUEST['nonce'])) ? "" : $_REQUEST['nonce'];
		if (! wp_verify_nonce($nonce, 'udmanager-nonce') || empty($_POST['key'])) die('Security check');

		$options = get_option(UDADDONS2_SLUG.'_options');

		// The 'password' encoded here is the updraftplus.com password. See here: http://updraftplus.com/faqs/tell-me-about-my-updraftplus-com-account/
		$post_array = array(
			'e' => $options['email'],
			'p' => base64_encode($options['password']),
			'sid' => $this->siteid(),
			'sn' => base64_encode(get_bloginfo('name')),
			'su' => base64_encode(home_url()),
			'key' => $_POST['key']
		);

		// The 'password' encoded here is the updraftplus.com password. See here: http://updraftplus.com/faqs/tell-me-about-my-updraftplus-com-account/
		$result = wp_remote_post($this->url.'/plugin-info/?udm_action=claimaddon',
			array(
				'timeout' => 10,
				'body' => $post_array
			)
		);

		if (is_array($result) && isset($result['body'])) {
			echo $result['body'];
		} elseif (is_wp_error($result)) {
			echo 'Errors occurred:<br>';
			show_message($result);
		} else {
			echo 'Errors occurred: '.htmlspecialchars(serialize($result));
		}
 
		die;

	}

	function siteid() {
		$sid = get_site_option(UDADDONS2_SLUG.'_siteid');
		if (!is_string($sid)) {
			$sid = md5(rand().time().home_url());
			update_site_option(UDADDONS2_SLUG.'_siteid', $sid);
		}
		return $sid;
	}

	function updater_queryargs_plugin($args) {
		if (!is_array($args)) return $args;

		$options = get_option(UDADDONS2_SLUG.'_options');
		$email = isset($options['email']) ? $options['email'] : '';
		$password = isset($options['password']) ? $options['password'] : '';

		$args['udm_action'] = 'updateinfo';

		$args['sid'] = $this->siteid();
		$args['su'] = urlencode(base64_encode(home_url()));
		$args['sn'] = urlencode(base64_encode(get_bloginfo('name')));
		$args['slug'] = urlencode($this->slug);
		$args['e'] = urlencode($email);
		$args['p'] = urlencode(base64_encode($password));
		if (defined('UPDRAFTPLUS_ADDONS_SSL') && UPDRAFTPLUS_ADDONS_SSL == true) $args['ssl']=1;

		// Some information on the server calling. This can be used - e.g. if they have an old version of PHP/WordPress, then this may affect what update version they should be offered
		include(ABSPATH.'wp-includes/version.php');
		global $wp_version;
		$sinfo = array(
			'wp' => $wp_version,
			'php' => phpversion(),
			'multi' => (is_multisite() ? 1 : 0),
			'mem' => ini_get('memory_limit')
		);

		global $updraftplus;
		if (isset($updraftplus->version)) {
			$sinfo['ud'] = $updraftplus->version;
			if (class_exists('UpdraftPlus_Options')) $sinfo['service'] = UpdraftPlus_Options::get_updraft_option('updraft_service');
		}

		$args['si'] = urlencode(base64_encode(serialize($sinfo)));

		return $args;
	}

	// This function, if ever changed, should be kept in sync with the same function in udmanager.php
	// Returns either false or an array
	function get_addon_info($file) {
		if ($f = fopen($file, 'r')) {
			$key = "";
			$name = "";
			$description = "";
			$version = "";
			$shopurl = "";
			$latestchange = null;
			$lines_read = 0;
			while ($lines_read<10 && $line = @fgets($f)) {
				if ($key == "" && preg_match('/UpdraftPlus Addon: ([^:]+):(.*)$/i', $line, $lmatch)) {
					$key = $lmatch[1]; $name = $lmatch[2];
				} elseif ($description == "" && preg_match('/Description: (.*)$/i', $line, $lmatch)) {
					$description = $lmatch[1];
				} elseif ($version == "" && preg_match('/Version: (.*)$/i', $line, $lmatch)) {
					$version = $lmatch[1];
				} elseif ($shopurl == "" && preg_match('/Shop: (.*)$/i', $line, $lmatch)) {
					$shopurl = $lmatch[1];
				} elseif ("" == $latestchange && preg_match('/Latest Change: (.*)$/i', $line, $lmatch)) {
					$latestchange = $lmatch[1];
				}
				$lines_read++;
			}
			fclose($f);
			if ($key && $name && $description && $version) {
				return array('key' => $key, 'name' => $name, 'description' => $description, 'installedversion' => $version, 'shopurl' => $shopurl, 'latestchange' => $latestchange);
			}
		}
		return false;
	}

	function get_default_addons() {
		return array(
			'noadverts' => array(
				'name' => 'Remove adverts',
				'description' => 'Removes all adverts from the control panel and emails',
				'shopurl' => '/shop/no-adverts/'
			),
			'all' => array (
				'name' => 'All addons',
				'description' => 'Access to all UpdraftPlus add-ons',
				'shopurl' => '/shop/updraftplus-premium/'
			),
			'multisite' => array(
				'name' => 'WordPress Network (multisite) support',
				'description' => 'Adds support for WordPress Network (multisite) installations, allowing secure backup by the super-admin only',
				'shopurl' => '/shop/network-multisite/'
			),
			'fixtime' => array (
				'name' => 'Fix Time',
				'description' => 'Allows you to specify the exact time at which backups will run',
				'shopurl' => '/shop/fix-time/'
			),
			'morefiles' => array (
				'name' => 'More Files',
				'description' => 'Allows you to back up WordPress core, and other files in your web space',
				'shopurl' => '/shop/more-files/'
			),
			'sftp' => array (
				'name' => 'SFTP and FTPS',
				'description' => 'Allows SFTP as a cloud backup method, and encrypted FTP',
				'shopurl' => '/shop/sftp/'
			),
			'dropbox-folders' => array (
				'name' => 'Dropbox Folders',
				'description' => 'Allows you to organise your backups into Dropbox sub-folders',
				'shopurl' => '/shop/dropbox-folders/'
			),
			'webdav' => array (
				'name' => 'WebDAV support',
				'description' => 'Allows you to use the WebDAV and encrypted WebDAV protocols for remote backups',
				'shopurl' => '/shop/webdav/'
			)
		);
	}

	function get_local_addons() {

		$plugin_dir = WP_PLUGIN_DIR.'/'.$this->slug;

		if (!is_dir($plugin_dir.'/addons')) return array();
		$local_addons = array();
		if ($dir_handle = @opendir($plugin_dir.'/addons')) {
			while (false !== ($e = readdir($dir_handle))) {
				if (is_file($plugin_dir.'/addons/'.$e) && preg_match('/^(.*)\.php$/i', $e, $matches)) {
					$addon = $this->get_addon_info($plugin_dir.'/addons/'.$e);
					if (is_array($addon)) { $key = $addon['key']; $local_addons[$key] = $addon; } 
				}
			}
		}

		return $local_addons;

	}

	function get_available_addons() {

		// Cached in this object?
		if (is_array($this->available_addons)) return $this->available_addons;

		// The remote list may be cached in a transient
		$potential_array = get_transient('upaddons_remote');
		if ($this->debug == false && is_array($potential_array) && isset($potential_array['all'])) {
			$this->remote_addons = $potential_array;
			$remote_addons = $potential_array;
		} else {

			$url = $this->url.'/plugin-info/?udm_action=listaddons';

			$result = wp_remote_get($url, array('timeout' => 10));
			
			if (!is_wp_error($result) & false !== $result) {
				$response = maybe_unserialize($result['body']);

				$remote_addons = array();

				if (is_array($response) && isset($response['addons'])) {
					$addons = $response['addons'];
					// One more sanity check
					if (isset($addons['all'])) {
						$remote_addons = $addons;
						// Cache it
						$this->remote_addons = $addons;
						set_transient('upaddons_remote', $addons, 14400);
					}
				}
			} else {
				// Populate with default
				$remote_addons = $this->get_default_addons();
			}

		}

		// Perhaps we have some installed that have been obsoleted/removed upstream

		$installed_addons = $this->get_local_addons();

		$all_addons = array();
		foreach ($remote_addons as $key => $addon) {
			// Can then get over-ridden
			$addon['installed'] = false;
			$all_addons[$key] = $addon;
		}

		foreach ($installed_addons as $key => $addon) {
			if (isset($all_addons[$key])) {
				// The remote info over-writes all else
				$newaddon = $all_addons[$key];
				$newaddon['installed'] = true;
				$newaddon['installedversion'] = $addon['installedversion'];
				
			} else {
				$newaddon = $addon;
			}
			$all_addons[$key] = $newaddon;
		}

		$this->available_addons = $all_addons;
		return $all_addons;

	}

	// Returns either true or a WP_Error
	function connection_status() {

		$options = get_option(UDADDONS2_SLUG.'_options');

		// Username and password set up?
		if (empty($options['email']) || empty($options['password'])) return new WP_Error('blank_details', 'You need to supply both an email address and a password');

		// Hash will change if the account changes (password change is handled by the options filter)
		$ehash = substr(md5($options['email']), 0, 24);
		$trans = get_transient('udaddons_connect_'.$ehash);

		// In debug mode, we don't cache
		if ($this->debug !== true && !isset($_GET['udm_refresh']) && is_array($trans)) {
			if (isset($trans['myaddons']) && is_array($trans['myaddons'])) {
				$this->user_addons = $trans['myaddons'];
			}
			if (isset($trans['availableaddons']) && is_array($trans['availableaddons'])) {
				$this->remote_addons = $trans['availableaddons'];
			}
			if (isset($trans['support']) && is_array($trans['support'])) {
				$this->user_support = $trans['support'];
			}
			return true;
		}

		$connect = $this->connect($options['email'], $options['password']);

		if (is_wp_error($connect)) return $connect;
		if (false === $connect) return new WP_Error('failed_connection', 'We failed to successfully connect to UpdraftPlus.Com');

		if (!is_bool($connect)) return new WP_Error('bad_response', 'UpdraftPlus.Com responded, but we did not understand the response');

		return true;

	}

	// Returns either true (in which case the add-ons array is populated), or a WP_Error
	function connect($email, $password) {

		// Used previous response, if available
		if (is_array($this->user_addons) && count($this->user_addons)>0) return true;

		// We sent the password in the clear; but then, so does the user every time they log in at updraftplus.com anyway, so there is no difference
		$url = $this->url.'/plugin-info/?udm_action=connect';

		// The 'password' encoded here is the updraftplus.com password. See here: http://updraftplus.com/faqs/tell-me-about-my-updraftplus-com-account/
		$result = wp_remote_post($url,
			array(
				'timeout' => 10,
				'body' => array(
					'e' => $email,
					'p' => base64_encode($password),
					'sid' => $this->siteid(),
					'sn' => base64_encode(get_bloginfo('name')),
					'su' => base64_encode(home_url())
				) 
			)
		);

		if (is_wp_error($result) || false === $result) return $result;

		$response = maybe_unserialize($result['body']);

		if (!is_array($response) || !isset($response['updraftpluscom']) || !isset($response['loggedin'])) return new WP_Error('unknown_response', 'UpdraftPlus.Com returned a response which we could not understand (data: '.serialize($response).')');

		switch ($response['loggedin']) {
			case 'connected':
				if (isset($response['myaddons']) && is_array($response['myaddons'])) {
					$this->user_addons = $response['myaddons'];
				}
				if (isset($response['availableaddons']) && is_array($response['availableaddons'])) {
					$this->remote_addons = $response['availableaddons'];
					set_transient('upaddons_remote', $this->remote_addons, 14400);
				}
				if (isset($response['support']) && is_array($response['support'])) {
					$this->user_support = $response['support'];
				}
				$ehash = substr(md5($email),0,24);

				set_transient('udaddons_connect_'.$ehash, $response, 7200);

				// Now, trigger an update check, since things may have changed
				if ($this->plug_updatechecker) $this->plug_updatechecker->checkForUpdates();

				break;
			case 'authfailed':
				return new WP_Error('authfailed', 'Your email address and password were not recognised by UpdraftPlus.Com');
				$ehash = substr(md5($options['email']),0,24);
				delete_transient('udaddons_connect_'.$ehash);
				break;
			default:
				return new WP_Error('unknown_response', 'UpdraftPlus.Com returned a response, but we could not understand it');
				break;
		}

		return true;

	}

}

?>
