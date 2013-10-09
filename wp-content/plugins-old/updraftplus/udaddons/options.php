<?php

/* For all copyright, version, etc. information, please see the main plugin file */

# http://codex.wordpress.org/Creating_Options_Pages

if (!defined ('ABSPATH')) die ('No direct access allowed');

class UpdraftPlusAddOns_Options2 {

	var $slug;
	var $title;
	var $mother;

	// Called in admin_init
	public function __construct($slug, $title, $mother) {
		$this->slug = $slug;
		$this->title = $title;
		$this->mother = $mother;
		# We are called in admin_menu
// 		add_action('admin_menu', array($this, 'options_menu'));
		$this->options_menu();
 		add_action('admin_init', array($this, 'show_admin_notices'));
		add_action('admin_init', array($this, 'options_init'));
		register_activation_hook(UDADDONS2_SLUG, array($this, 'options_setdefaults'));

		add_filter('plugin_action_links', array($this, 'action_links'), 10, 2 );
	}

	public function show_admin_notices() {
		global $pagenow, $updraftplus;

// 		if (!method_exists($updraftplus, 'log') && current_user_can('install_plugins')) {
// 			add_action('admin_notices', array($this,'show_admin_warning_noupdraftplus') );
// 		} elseif (version_compare($updraftplus->version, '1.4.6', '<') && current_user_can('install_plugins')) {
// 			add_action('admin_notices', array($this,'show_admin_warning_insufficientupdraftplus') );
// 		}

		if ($pagenow == 'options-general.php' && version_compare(phpversion(), '5.2.0', '<') && isset($_REQUEST['page']) && $_REQUEST['page'] == UDADDONS2_PAGESLUG) {
			add_action('admin_notices', array($this,'show_admin_warning_php') );
		}

		$options = get_option(UDADDONS2_SLUG.'_options');
		if (empty($options['email']) && current_user_can('manage_options') && isset($_REQUEST['page']) && 'updraftplus' == $_REQUEST['page']) {
			add_action('admin_notices', array($this,'show_admin_warning_notconnected') );
		}
	}

	function show_admin_warning($message, $class = "updated") {
		echo '<div class="'.$class.' fade">'."<p>$message</p></div>";
	}

	function show_admin_warning_notconnected() {
		$this->show_admin_warning('You have not yet connected with your UpdraftPlus.Com account, to enable you to list your purchased add-ons. <a href="options-general.php?page='.UDADDONS2_PAGESLUG.'">Go here to connect.</a>');
	}

	function show_admin_warning_noupdraftplus() {
		if (is_file(WP_PLUGIN_DIR.'/updraftplus/updraftplus.php')) {
			global $pagenow;
			$msg = 'UpdraftPlus is not yet activated.';
			if ($pagenow != 'plugins.php') $msg .= ' <a href="plugins.php">Go here to activate it.</a>';
			$this->show_admin_warning($msg);
		} else {
			$warning = 'UpdraftPlus is not yet installed. <a href="'.$this->mother.'/download/">Go here to begin installing it.</a>';
			if (file_exists(WP_PLUGIN_DIR.'/updraft')) $warning .= ' You do seem to have the obsolete Updraft plugin installed - perhaps you got them confused?';
			$this->show_admin_warning($warning);
		}
	}

	function show_admin_warning_insufficientupdraftplus() {
		$this->show_admin_warning('You need, but do not have, at least version 1.4.6 of UpdraftPlus to use UpdraftPlus add-ons or Premium - <a href="update-core.php">please upgrade</a>');
	}

	function show_admin_warning_php() {
		$this->show_admin_warning('Your web server\'s version of PHP is too old ('.phpversion().') - UpdraftPlus expects at least 5.2.0. You can try it, but don\'t be surprised if it does not work. To fix this problem, contact your web hosting company', 'error');
	}

	public function options_menu() {
		# http://codex.wordpress.org/Function_Reference/add_options_page
		add_options_page($this->title, $this->title, 'manage_options', UDADDONS2_PAGESLUG, array($this, 'options_printpage'));
	}

	# Registered under admin_init
	public function options_init(){
		# Register a new set of options, named $slug_options, stored in the database entry $slug_options

		register_setting( UDADDONS2_SLUG.'_options', UDADDONS2_SLUG.'_options' , array($this, 'options_validate') );

		add_settings_section ( UDADDONS2_SLUG.'_options', 'Connect with your UpdraftPlus.Com account', array($this, 'options_header') , UDADDONS2_SLUG);

		add_settings_field ( UDADDONS2_SLUG.'_options_email', 'Email', array($this, 'options_email'), UDADDONS2_SLUG , UDADDONS2_SLUG.'_options' );

		add_settings_field ( UDADDONS2_SLUG.'_options_password', 'Password', array($this, 'options_password'), UDADDONS2_SLUG , UDADDONS2_SLUG.'_options' );


	}

	public function options_setdefaults() {
		$tmp = get_option(UDADDONS2_SLUG.'_options');
		if (!is_array($tmp)) {
			$arr = array(
				"email" => "",
				"password" => ""
			);
			update_option(UDADDONS2_SLUG.'_options', $arr);
		}
	}

	# Various functions for outputing each of the options fields
	public function options_email() {
		$options = get_option(UDADDONS2_SLUG.'_options');
		?>
		<label for="<?php echo UDADDONS2_SLUG ?>_options_email">
		<input id="<?php echo UDADDONS2_SLUG ?>_options_email" type="text" size="36" name="<?php echo UDADDONS2_SLUG ?>_options[email]" value="<?php print htmlspecialchars($options['email']); ?>" />
		<br /><a href="<?php echo $this->mother ?>/my-account/">Not yet got an account (it's free)? Go get one!</a>
		</label>
		<?php
	}

	public function options_password() {
		$options = get_option(UDADDONS2_SLUG.'_options');
		?>
		<label for="<?php echo UDADDONS2_SLUG ?>_options_password">
		<input id="<?php echo UDADDONS2_SLUG ?>_options_password" type="password" size="36" name="<?php echo UDADDONS2_SLUG ?>_options[password]" value="<?php print htmlspecialchars($options['password']); ?>" />
		<br /><a href="<?php echo $this->mother ?>/my-account/?action=lostpassword">Forgotten your details?</a>
		</label>
		<?php
	}

	public function options_header() {
		settings_errors();
	}

	# This function is registered via register_setting. It is intended to return sanitised output, and can optionally call add_settings_error to whinge about anything faulty
	public function options_validate($input) {

		# See: http://codex.wordpress.org/Function_Reference/add_settings_error

		// When the options are re-saved, clear any previous cache of the connection status
		$ehash = substr(md5($input['email']),0,24);
		delete_transient('udaddons_connect_'.$ehash);

	// 	add_settings_error( UDADDONS2_SLUG."_options", UDADDONS2_SLUG."_options_nodb", "Whinge, whinge", "error" );

		return $input;
	}

	# This is the function outputing the HTML for our options page
	public function options_printpage() {
		if (!current_user_can('manage_options'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}

		$options = get_option(UDADDONS2_SLUG.'_options');
		$user_and_pass_at_top = (empty($options['email'])) ? true : false;

		$title = $this->title;
		$mother = $this->mother;

		echo <<<ENDHERE
	<div class="wrap">
		<h2>$title Options</h2>

ENDHERE;

		$enter_credentials_begin = <<<ENDHERE
		<form method="post" action="options.php" autocomplete="off">
ENDHERE;

		$enter_credentials_end = <<<ENDHERE
			<p style="margin-left: 228px;">
				<input id="ud_connectsubmit" type="submit" class="button-primary" value="Connect" />
			</p>
			<p style="margin-left: 228px; font-size: 70%"><em><a href="http://updraftplus.com/faqs/tell-me-about-my-updraftplus-com-account/">Interested in knowing about your UpdraftPlus.Com password security? Read about it here.</a></em></p>
		</form>
ENDHERE;

		global $updraftplus_addons2;
		$connected = (!empty($options['email']) && !empty($options['password'])) ? $updraftplus_addons2->connection_status() : false;

		if (true !== $connected) {
			if (is_wp_error($connected)) {
				$connection_errors = array();
				foreach ($connected->get_error_messages() as $key => $msg) {
					$connection_errors[] = $msg;
				}
			} else {
				if (!empty($options['email']) && !empty($options['password'])) $connection_errors = array('An unknown error occured when trying to connect to UpdraftPlus.Com');
			}
			$connected = false;
		}

		$present_status = ($connected) ? "connected" : "not connected";
		echo "<p>You are presently <strong>$present_status</strong> to an UpdraftPlus.Com account.";

		if ($connected) {
			echo 'If you bought new add-ons, then <a href="#" onclick="jQuery(\'#ud_connectsubmit\').click();">follow this link to refresh your connection</a>.';
		}

		echo '</p>';

		if (isset($connection_errors)) {
			echo '<div class="error"><p><strong>Errors occurred when trying to connect to UpdraftPlus.Com:</strong></p><ul>';
			foreach ($connection_errors as $err) {
				echo '<li style="list-style:disc inside;">'.htmlspecialchars($err).'</li>';
			}
			echo '</ul></div>';
		}

		global $updraftplus_addons2;
		$sid = $updraftplus_addons2->siteid();

		// Enumerate possible unclaimed purchases, and what should be active on this site
		$unclaimed_available = array();
		$assigned = array();
		$have_all = false;
		if ($connected && isset($updraftplus_addons2->user_addons) && is_array($updraftplus_addons2->user_addons)) {
			foreach ($updraftplus_addons2->user_addons as $akey => $addon) {
				// Keys: site, sitedescription, key, status
				if (isset($addon['status']) && 'active' == $addon['status'] && isset($addon['site']) && ('unclaimed' == $addon['site'] || 'unlimited' == $addon['site'])) {
					$key = $addon['key'];
					$unclaimed_available[$key] = $akey;
				} elseif (isset($addon['status']) && 'active' == $addon['status'] && isset($addon['site']) && $addon['site'] == $sid) {
					$key = $addon['key'];
					$assigned[$key] = $akey;
					if ($key == 'all') $have_all=true;
				}
			}
		}

		if (!$connected) $this->show_credentials_form($enter_credentials_begin, $enter_credentials_end);

		$email = isset($options['email']) ? $options['email'] : '';
		$pass = isset($options['password']) ? base64_encode($options['password']) : '';
		$sn = base64_encode(get_bloginfo('name'));
		$su = base64_encode(home_url());
		$ourpageslug = UDADDONS2_PAGESLUG;
		$mother = $this->mother;

		if (count($unclaimed_available) >0) {
			$nonce = wp_create_nonce('udmanager-nonce');
			echo <<<ENDHERE
		<div id="udm_pleasewait" class="updated" style="border: 1px solid; padding: 10px; margin-top: 10px; margin-bottom: 10px; clear: left; float: left; display:none;"><strong>Please wait whilst we make the claim...</strong></div>
		<script type="text/javascript">
			function udm_claim(key) {
				var data = {
						action: 'udaddons_claimaddon',
						nonce: '$nonce',
						key: key
				};
				jQuery('#udm_pleasewait').fadeIn();
				jQuery.post(ajaxurl, data, function(response) {
					if (response == 'ERR') {
						alert("Claim not granted - perhaps you have already used this purchase somewhere else?");
					} else if (response == 'OK') {
						window.location.href = 'options-general.php?page=$ourpageslug&udm_refresh=1';
					} else if (response == 'BADAUTH') {
						alert("Claim not granted - your account login details were wrong");
					} else {
						alert("An unknown response was received. Response was: "+response);
					}
					jQuery('#udm_pleasewait').fadeOut();
				});
			}
		</script>
ENDHERE;
		}
		echo '<h3 style="clear:left; margin-top: 10px;">UpdraftPlus Addons</h3><div>';

		$addons = $updraftplus_addons2->get_available_addons();
		if (is_array($addons)) {
			foreach ($addons as $key => $addon) {
				extract($addon);
				if (empty($addon['latestversion'])) $latestversion = false;
				if (empty($addon['installedversion'])) $installedversion = false;
				if (empty($addon['installed']) && $installedversion == false) $installed = false;
				$unclaimed = (isset($unclaimed_available[$key])) ? $unclaimed_available[$key] : false;
				$is_assigned = (isset($assigned[$key])) ? $assigned[$key] : false;
				$this->addonbox($key, $name, $shopurl, $description, trim($installedversion), trim($latestversion), $installed, $unclaimed, $is_assigned, $have_all);
			}
		} else {
			echo "<em>An error occurred when trying to retrieve your add-ons.</em>";
		}

echo <<<ENDHERE
		</div>
ENDHERE;

		// TODO: Show their support package, if any - ?
		if (is_array($updraftplus_addons2->user_support)) {
			// Keys: 
		}

		echo <<<ENDHERE
<h3>UpdraftPlus Support</h3>
<ul>
<li style="list-style:disc inside;">Need to get support? <a href="$mother/support/">Go here</a>.</li>
</ul>
ENDHERE;

	if ($connected) {
		echo "<hr>";
		$this->show_credentials_form($enter_credentials_begin, $enter_credentials_end);
	}


	echo '</div>';

	}

	function addonbox($key, $name, $shopurl, $description, $installedversion, $latestversion = false, $installed = false, $unclaimed = false, $is_assigned = false, $have_all = false) {
		$urlbase = UDADDONS2_URL;
		$mother = $this->mother;
		if ($installed || ($have_all && $key == 'all')) {
			$blurb="<p>";
			$preblurb="<div style=\"float:right;\"><img src=\"$urlbase/yes.png\" width=\"85\" height=\"98\" alt=\"You've got it\"></div>";
			if ($key !='all') {
				$blurb.="Your version: $installedversion";
				if (!empty($latestversion) && $latestversion == $installedversion) {
					$blurb .= " (latest)";
				} elseif (!empty($latestversion) && version_compare($latestversion, $installedversion, '>')) {
					$blurb.=" (latest: $latestversion - <a href=\"update-core.php\">update</a>)";
				} else {
					$blurb .= " (apparently a pre-release or withdrawn release)";
				}
			}
			$blurb.="</p>";
		} else {
			if ($have_all && $key != 'all') {
				$blurb='<p><strong>Available for this site (via your all-addons purchase) - <a href="update-core.php">please update the plugin in order to get it</a></strong></p>';
				$preblurb="";
			} elseif ($is_assigned) {
				$blurb='<p><strong>Assigned to this site - <a href="update-core.php">please update the plugin in order to activate it</a></strong></p>';
				$preblurb="";
			} elseif ($unclaimed) {
				// Value of $unclaimed is a unique id, though we won't particularly use it
				global $updraftplus_addons2;
				$sid = $updraftplus_addons2->siteid();
				$options = get_option(UDADDONS2_SLUG.'_options');
				$blurb='<p><strong>You have an inactive purchase - <a href="#" onclick="udm_claim(\''.$key.'\');">activate it on this site</a></strong></p>';
				$preblurb="";
			} else {
				$blurb='<p><a href="'.$mother.$shopurl.'">Get it from the UpdraftPlus.Com Store</a></p>';
				$preblurb="<div style=\"float:right;\"><a href=\"${mother}${shopurl}\"><img src=\"$urlbase/shopcart.png\" width=\"120\" height=\"98\" alt=\"Buy It\"></a></div>";
			}
		}
		echo <<<ENDHERE
			<div style="border: 1px solid; border-radius: 4px; padding: 0px 12px 0px; min-height: 110px; width: 680px; margin-bottom: 16px;">
			$preblurb
			<div style="width: 580px;"><h2 style="">$name</h2>
			$description<br>
			$blurb
			</div>
			</div>
ENDHERE;

	}

	function show_credentials_form($enter_credentials_begin, $enter_credentials_end) {
		echo $enter_credentials_begin;
		settings_fields(UDADDONS2_SLUG.'_options');
		do_settings_sections(UDADDONS2_SLUG);
		echo $enter_credentials_end;
	}

	public function action_links($links, $file) {
		if ( $file == "updraftplus/updraftplus.php" ){
			array_unshift( $links, '<a href="options-general.php?page='.UDADDONS2_PAGESLUG.'">Manage Addons</a>');
		}
		return $links;
	}

}

?>