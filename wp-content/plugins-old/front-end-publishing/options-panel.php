<?php
/*
The settings page
*/

// create custom plugin settings menu
add_action('admin_menu', 'fep_create_menu');

function fep_create_menu() {

	//create new top-level menu
	add_menu_page('FEP Plugin Settings', 'FEP Settings', 'administrator', __FILE__, 'fep_settings_page');

	//call register settings function
	add_action( 'admin_init', 'register_fep_settings' );
}


function register_fep_settings() {
	register_setting( 'fep-settings-group', 'fep_post_restrictions' );
	register_setting( 'fep-settings-group', 'fep_role_settings' );
	register_setting( 'fep-settings-group', 'fep_misc' );
}

function fep_settings_page() {

?>
<div class="wrap">
<h1>Front-End Publishing</h1>

<form method="post" action="options.php">


    <?php settings_fields( 'fep-settings-group' ); ?>
    <?php do_settings_sections( 'fep-settings-group' ); ?>
	<?php $fep_post_restrictions = 	get_option('fep_post_restrictions');
		  $fep_role_settings = get_option('fep_role_settings');
		  $fep_misc = get_option('fep_misc');	?>
	
<h2 style="margin-top:30px;">Restrictions</h2>
		Each post will be checked according to the following rules at the time of submission.
		<table class="form-table">
		<tr valign="top">
        <th scope="row">Words In Title</th>
        <td>Minimum: <input type="text" name="fep_post_restrictions[min_words_title]" value="<?php echo $fep_post_restrictions['min_words_title']; ?>" /> Maximum: <input type="text" name="fep_post_restrictions[max_words_title]" value="<?php echo $fep_post_restrictions['max_words_title']; ?>" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Words In Content</th>
        <td>Minimum: <input type="text" name="fep_post_restrictions[min_words_content]" value="<?php echo $fep_post_restrictions['min_words_content']; ?>" /> Maximum: <input type="text" name="fep_post_restrictions[max_words_content]" value="<?php echo $fep_post_restrictions['max_words_content']; ?>" /></td>
        </tr>
        
		<tr valign="top">
        <th scope="row">Words In Bio</th>
        <td>Minimum: <input type="text" name="fep_post_restrictions[min_words_bio]" value="<?php echo $fep_post_restrictions['min_words_bio']; ?>" /> Maximum: <input type="text" name="fep_post_restrictions[max_words_bio]" value="<?php echo $fep_post_restrictions['max_words_bio']; ?>" /></td>
        </tr>
 
		<tr valign="top">
        <th scope="row">Number of Tags</th>
        <td>Minimum: <input type="text" name="fep_post_restrictions[min_tags]" value="<?php echo $fep_post_restrictions['min_tags']; ?>" /> Maximum: <input type="text" name="fep_post_restrictions[max_tags]" value="<?php echo $fep_post_restrictions['max_tags']; ?>" /></td>
        </tr> 
		 
        <tr valign="top">
        <th scope="row">Max Number of Links</th>
        <td>In Body:&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="fep_post_restrictions[max_links]" value="<?php echo $fep_post_restrictions['max_links']; ?>" /> In Bio:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="fep_post_restrictions[max_links_bio]" value="<?php echo $fep_post_restrictions['max_links_bio']; ?>" /></td>
        </tr>
		</table>
	
	<br/>
	<h2 style="margin-top:30px;">Roles &amp; Capabilities</h2>
	<table class="form-table">	
		<tr valign="top">
        <th scope="row">Don't check posts submitted by</th>
        <td><select name="fep_role_settings[no_check]" style="width:200px;"> 
			<option value="false" <?php echo (!$fep_role_settings['no_check'])?'selected':''; ?>>---</option>
			<option value="update_core" <?php echo ($fep_role_settings['no_check']=='update_core')?'selected':''; ?>>Administrator</option>
			<option value="moderate_comments" <?php echo ($fep_role_settings['no_check']=='moderate_comments')?'selected':''; ?>>Editor</option>
			<option value="edit_published_posts" <?php echo ($fep_role_settings['no_check']=='edit_published_posts')?'selected':''; ?>>Author</option>
			<option value="edit_posts" <?php echo ($fep_role_settings['no_check']=='edit_posts')?'selected':''; ?>>Contributor</option>
			<option value="read" <?php echo ($fep_role_settings['no_check']=='read')?'selected':''; ?>>Subscriber</option>
		</select></td>
        </tr>
		
		<tr valign="top">
        <th scope="row">Instantly publish posts submitted by<br/></th>
        <td><select name="fep_role_settings[instantly_publish]" style="width:200px;">
			<option value="false" <?php echo (!$fep_role_settings['instantly_publish'])?'selected':''; ?>>---</option>
			<option value="update_core" <?php echo ($fep_role_settings['instantly_publish']=='update_core')?'selected':''; ?>>Administrator</option>
			<option value="moderate_comments" <?php echo ($fep_role_settings['instantly_publish']=='moderate_comments')?'selected':''; ?>>Editor</option>
			<option value="edit_published_posts" <?php echo ($fep_role_settings['instantly_publish']=='edit_published_posts')?'selected':''; ?>>Author</option>
			<option value="edit_posts" <?php echo ($fep_role_settings['instantly_publish']=='edit_posts')?'selected':''; ?>>Contributor</option>
			<option value="read" <?php echo ($fep_role_settings['instantly_publish']=='read')?'selected':''; ?>>Subscriber</option>
		</select></td>
        </tr>
    </table>
    
	<br/><p><b>Note:</b> Users with higher levels than the ones specified above will also get the same treatment</p>
	
	<h2 style="margin-top:30px;">Misc Options</h2>
	
	<table class="form-table">
	<tr valign="top">
		<th scope="row">The contents of this textarea will be added above the author bio on every post page<br/></th>
        <td><textarea cols="50" rows="10" name="fep_misc[before_author_bio]"><?php echo esc_textarea($fep_misc['before_author_bio']); ?></textarea></td>
    </tr>
	</table>
	
	<table class="form-table">
	<tr valign="top">
        <td><input type="checkbox" name="fep_misc[disable_author_bio]" value="true" <?php echo ($fep_misc['disable_author_bio'])?'checked':''; ?>></td>
		<td>Disable author bio box</td>
	</tr>
	
	<tr valign="top">
        <td><input type="checkbox" name="fep_misc[remove_bios]" value="true" <?php echo ($fep_misc['remove_bios'])?'checked':''; ?>></td>
		<td>Remove author bios from all published posts (You can enable them again)</td>
	</tr>
	
	<tr valign="top">
        <td><input type="checkbox" name="fep_misc[nofollow_body_links]" value="true" <?php echo ($fep_misc['nofollow_body_links'])?'checked':''; ?>></td>
		<td>Nofollow all the links in post body</td>
	</tr>
	
	<tr valign="top">
        <td><input type="checkbox" name="fep_misc[nofollow_bio_links]" value="true" <?php echo ($fep_misc['nofollow_bio_links'])?'checked':''; ?>></td>
		<td>Nofollow all the links in author bio</td>
	</tr>
	</table>
	<br/>
	
    <?php submit_button(); ?>

</form>

</div>
<?php } ?>