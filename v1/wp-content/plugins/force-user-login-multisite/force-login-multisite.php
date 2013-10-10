<?php
/*
	Plugin Name: Force User Login Multisite
	Plugin URI: http://jameslow.com/2011/10/11/force-user-login-multisite/
	Description: Makes your wordpress blog private unless the user is logged in, optionally setting a minium user level. Modified from http://wordpress.org/extend/plugins/force-user-login/
	Version: 1.2.1
	Author: James David Low (originally: The Integer Group Development Team)
	Author URI: http://www.jameslow.com
*/

add_action('admin_menu', 'force_login_multisite_menu');
function force_login_multisite_menu() {
	if (function_exists('add_options_page')) {
		add_options_page('Force Login Multisite', 'Force Login Multisite', 9, __FILE__, 'force_login_multisite_options');
	}
}

function force_login_multisite_canview($level = null) {
	global $userdata;
	get_currentuserinfo();
	if ($userdata->user_login) {
		$level = ($level != null ? $level : get_option('force_login_multisite_minlevel',-1));
		if ($userdata->user_level >= $level) {
			return true;
		}
	}
	return false;
}

function force_login_multisite_none($level) {
	return $level < 0;
}
function force_login_multisite_subscriber($level) {
	return $level == 0;
}
function force_login_multisite_contributor($level) {
	return $level == 1;
}
function force_login_multisite_author($level) {
	return ($level >= 2 && $level <= 4);
}
function force_login_multisite_editor($level) {
	return ($level >= 5 && $level <= 7);
}
function force_login_multisite_admin($level) {
	return ($level >= 8 && $level <= 10);
}
function force_login_multisite_option($level, $name, $select) {
	echo "<option value=\"$level\" " . ($select ? "selected" : "") . ">$name</option>";
}

function force_login_multisite_options() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	} else {
		$level = get_option('force_login_multisite_minlevel',-1);
?>
<div class="wrap">
  <h2>Force Login Multisite Options</h2>
  <form action="options.php" method="post">
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="page_options" value="force_login_multisite_minlevel" />
    <?php if (function_exists('wp_nonce_field')): wp_nonce_field('update-options'); endif; ?>
    <table class="form-table">
      <tr valign="top">
        <th scope="row"><label for="force_login_multisite_minlevel">Minimum Level</label></th>
        <td>
          <select name="force_login_multisite_minlevel" id="force_login_multisite_minlevel">
          <?php
          force_login_multisite_option(-1, "(Disabled)", force_login_multisite_subscriber($level));
          force_login_multisite_option(0, "Subscriber", force_login_multisite_subscriber($level));
          force_login_multisite_option(1, "Contributor", force_login_multisite_contributor($level));
          force_login_multisite_option(2, "Author", force_login_multisite_author($level));
          force_login_multisite_option(5, "Editor", force_login_multisite_editor($level));
          force_login_multisite_option(10, "Admin", force_login_multisite_admin($level));
          ?>
          </select>
          Miniumum level Required to view content<br />
        </td>
      </tr>
      </table>
    <p class="submit"><input type="submit" name="Submit" value="Save Changes" /></p>
  </form>
</div>
<?php
	}
}

add_action('template_redirect', 'force_login');
function force_login() {
	$level = get_option('force_login_multisite_minlevel',-1);
	if ($level >= 0) {
		if (!is_user_logged_in() || !force_login_multisite_canview($level)) {
			$redirect_to = $_SERVER['REQUEST_URI']; // Change this line to change to where logging in redirects the user, i.e. '/', '/wp-admin', etc.
			if (is_feed()) {
				$credentials = array();
				$credentials['user_login'] = $_SERVER['PHP_AUTH_USER'];
				$credentials['user_password'] = $_SERVER['PHP_AUTH_PW'];
				$user = wp_signon( $credentials );
				if (is_wp_error($user) || !force_login_multisite_canview($level)) {
					header('WWW-Authenticate: Basic realm="' . $_SERVER['SERVER_NAME'] . '"');
					header('HTTP/1.0 401 Unauthorized');
					die();
				}
			} else {
				header('Location: /wp-login.php?redirect_to=' . $redirect_to);
				die();
			}
		}
	}
}
?>