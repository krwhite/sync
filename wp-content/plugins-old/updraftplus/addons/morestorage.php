<?php
/*
UpdraftPlus Addon: morestorage:Multiple storage options
Description: Provides the ability to back up to multiple remote storage facilities, not just one
Version: 1.0
Shop: /shop/morestorage/
Latest Change: 1.7.14
*/

$updraftplus_addon_morestorage = new UpdraftPlus_Addon_MoreStorage;

class UpdraftPlus_Addon_MoreStorage {

	function __construct() {
		add_filter('updraftplus_storage_printoptions', array($this, 'storage_printoptions'), 10, 2);
		#add_action('updraftplus_config_print_after_storage', array($this, 'config_print_after_storage'));
		add_action('updraftplus_config_print_before_storage', array($this, 'config_print_before_storage'));
		add_filter('updraftplus_savestorage', array($this, 'savestorage'), 10, 2);
	}

	function admin_print_footer_scripts() {
		?>
		<script>
		jQuery(document).ready(function() {
			jQuery('.updraft_servicecheckbox').change(function() {
				jQuery('.updraft_servicecheckbox').each(function(ind, obj) {
					var sclass = jQuery(obj).attr('id');
					if ('updraft_servicecheckbox_' == sclass.substring(0,24)) {
						var serv = sclass.substring(24);
						if (null != serv && '' != serv) {
							if (jQuery(obj).is(':checked')) {
								jQuery('.'+serv).fadeIn();
							} else {
								jQuery('.'+serv).hide();
							}
						}
					}
				});
			});
		});
		</script>
		<?php
	}

	function config_print_before_storage($storage) {
		global $updraftplus;
		?>
		<tr class="updraftplusmethod <?php echo $storage;?>"><th><h3><?php echo $updraftplus->backup_methods[$storage]; ?></h3></th><td></td></tr>
		<?php
		
	}

	function savestorage($rinput, $input) {
		return $input;
	}

	function config_print_after_storage($storage) {
		?>
		<tr class="updraftplusmethod <?php echo $storage;?>"><td colspan="2"><hr></td></tr>
		<?php
		
	}

	function storage_printoptions($ret, $active_service) {

		global $updraftplus;
		add_action('admin_print_footer_scripts', array($this, 'admin_print_footer_scripts'));

		foreach ($updraftplus->backup_methods as $method => $description) {
			echo "<input name=\"updraft_service[]\" class=\"updraft_servicecheckbox\" id=\"updraft_servicecheckbox_$method\" type=\"checkbox\" value=\"$method\"";
			if ($active_service === $method || (is_array($active_service) && in_array($method, $active_service))) echo ' checked="checked"';
			echo '> <label for="updraft_servicecheckbox_'.$method.'">'.$description.'</label><br>';
		}

		?>

		</td>
		</tr>
		<tr>
			<th colspan="2"><h2><?php _e('Remote Storage Options', 'updraftplus');?></h2>
		</tr>

		<?php
		return true;

	}

}