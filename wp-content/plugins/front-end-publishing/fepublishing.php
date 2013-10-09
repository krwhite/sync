<?php
/*
Plugin Name: Front-End Publishing
Plugin URI: http://wpgurus.net/
Description: Accept guest posts without giving your authors access to the back-end area.
Version: 1.2
Author: Hassan Akhtar
Author URI: http://wpgurus.net/
License: GPL2
*/

//allow redirection, even if my theme starts to send output to the browser
add_action('init', 'fep_do_output_buffer');
function fep_do_output_buffer() {
        ob_start();
}

function fep_add_stylesheet() {
	//wp_register_style( 'fep_style', plugins_url('styles.css', __FILE__) );
    //wp_enqueue_style( 'fep_style' );
}
//add_action( 'wp_enqueue_scripts', 'fep_add_stylesheet' );

add_action('init', 'fep_register_script');
add_action('wp_footer', 'fep_print_script');

function fep_register_script() {
	wp_register_script( "fep_ajax_handler", plugins_url( 'js/fep_ajax_handler.js', __FILE__ ), array('jquery') );
	wp_register_script( "fep_tabs", plugins_url( 'js/tabs.js', __FILE__ ), array('jquery') );
	wp_localize_script( 'fep_ajax_handler', 'fepajaxhandler', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
	
	$fep_rules = get_option('fep_post_restrictions');
	wp_localize_script( 'fep_ajax_handler', 'fep_rules', $fep_rules);
}

function fep_print_script() {
	global $fep_shortcode_used;

	if ( ! $fep_shortcode_used ) return;

	wp_print_scripts('fep_ajax_handler');
	wp_print_scripts('fep_tabs');
}

function fep_initialize_options(){
	$activation_flag = get_option('fep_misc');
	
	if( $activation_flag )
	return;
	
	$fep_restrictions = array(
		'min_words_title' => 2,
		'max_words_title' => 12,
		'min_words_content' => 250,
		'max_words_content' => 2000,
		'min_words_bio' => 50,
		'max_words_bio' => 100,
		'min_tags' => 1,
		'max_tags' => 5,
		'max_links' => 2,
		'max_links_bio' => 2
	);
	
	$fep_roles = array(
		'no_check' => false,
		'instantly_publish' => false	
	);
	
	$fep_misc = array(
		'disable_author_bio' => false,
		'remove_bios' => false,
		'nofollow_body_links' => false,
		'nofollow_bio_links' => false
	);
	
	update_option('fep_post_restrictions', $fep_restrictions);
	update_option('fep_role_settings', $fep_roles);
	update_option('fep_misc', $fep_misc);
}
register_activation_hook(__FILE__, 'fep_initialize_options');

function fep_rollback(){
	wp_deregister_style( 'fep_style' );
	wp_deregister_script( 'fep_ajax_handler' );
	wp_deregister_script( 'fep_tabs' );
	delete_option('fep_post_restrictions');
	delete_option('fep_role_settings');
	delete_option('fep_misc');
}
register_uninstall_hook(__FILE__, 'fep_rollback');

add_filter( 'the_content', 'fep_add_author_bio', 100 );
function fep_add_author_bio( $content ){
	$fep_misc = get_option('fep_misc');
	global $post;
	$ID = $post->ID;
	$author_bio = get_post_meta($ID, 'about_the_author', true);
	if(!$author_bio || $fep_misc['remove_bios']) return $content;
	$before_bio = $fep_misc['before_author_bio'];
	return <<<HTML
	{$content}{$before_bio}
	<div class="fep-author-bio">{$author_bio}</div>
HTML;
}

function fep_post_has_errors($content){

	$fep_plugin_options = get_option('fep_post_restrictions');
	
	$min_words_title = $fep_plugin_options['min_words_title'];
	$max_words_title = $fep_plugin_options['max_words_title'];
	$min_words_content = $fep_plugin_options['min_words_content'];
	$max_words_content = $fep_plugin_options['max_words_content'];
	$min_words_bio = $fep_plugin_options['min_words_bio'];
	$max_words_bio = $fep_plugin_options['max_words_bio'];
	$max_links = $fep_plugin_options['max_links'];
	$max_links_bio = $fep_plugin_options['max_links_bio'];
	$min_tags = $fep_plugin_options['min_tags'];
	$max_tags = $fep_plugin_options['max_tags'];
	
	if( ($min_words_title && empty($content['post_title']) ) || ($min_words_content && empty($content['post_content'])) || ($min_words_bio && empty($content['about_the_author'])) || ($min_tags && empty($content['post_tags'])) ){
		$error_string .= 'You missed one or more required fields<br/>';
	}
	
	$tags_array = explode(',', $content['post_tags']);
	$stripped_bio = strip_tags ($content['about_the_author']);
	$stripped_content = strip_tags ($content['post_content']);
	
    if ( !empty($content['post_title']) && str_word_count( $content['post_title'] ) < $min_words_title )
        $error_string .= 'The title is too short<br/>';
    if ( !empty($content['post_title']) && str_word_count( $content['post_title'] ) > $max_words_title )
        $error_string .= 'The title is too long<br/>';
    if ( !empty($content['post_content']) && str_word_count( $stripped_content ) < $min_words_content )
        $error_string .= 'The article is too short<br/>';
    if ( str_word_count( $stripped_content ) > $max_words_content )
        $error_string .= 'The article is too long<br/>';
	if ( !empty($content['about_the_author']) && str_word_count( $stripped_bio ) < $min_words_bio )
        $error_string .= 'The bio is too short<br/>';
    if ( str_word_count( $stripped_bio ) > $max_words_bio )
        $error_string .= 'The bio is too long<br/>';
	if ( substr_count( $content['post_content'], '</a>' ) > $max_links )
        $error_string .= 'There are too many links in the article body<br/>';
	if ( substr_count( $content['about_the_author'], '</a>' ) > $max_links_bio )
        $error_string .= 'There are too many links in the bio<br/>';
	if ( !empty( $content['post_tags'] ) && count($tags_array) < $min_tags )
		$error_string .= 'You haven\'t added the required number of tags<br/>';
	if ( count($tags_array) > $max_tags )
		$error_string .= 'There are too many tags<br/>';
	
	if ( str_word_count( $error_string ) < 2 )
	return false;
	else
	return $error_string;
}

add_action( 'wp_ajax_fep_process_form_input', 'fep_process_form_input' );
add_action( 'wp_ajax_nopriv_fep_process_form_input', 'fep_process_form_input' );

function fep_process_form_input(){


	if( !wp_verify_nonce($_POST['post_nonce'], 'fepnonce_action') ){
		$data['error'] = false;
		$data['message'] = 'Sorry! You can\'t use this form';
		die( json_encode($data) );
	}	

	$fep_role_settings = get_option('fep_role_settings');
	$fep_misc = get_option('fep_misc');
	
	if($fep_role_settings['no_check'] && current_user_can( $fep_role_settings['no_check']) )
		$errors = 0;
	else
		$errors = fep_post_has_errors($_POST);

	
	if( !$errors ){
		$new_post = array(
			'post_title'   => sanitize_text_field( $_POST['post_title'] ),
			/*'post_category'  => array( $_POST['post_category'] ),*/
			/*'tax_input' => array('help-categories' => array($_POST['post_category']) ),*/
			'tags_input'  => sanitize_text_field( $_POST['post_tags'] ),
			'post_type' => 'help-article',
			'post_author' =>  get_current_user_id()
		);
		
		if($fep_misc['nofollow_body_links'])
			$new_post['post_content'] = convert_chars(utf8_encode(wp_rel_nofollow( $_POST['post_content'] )));
		else
			$new_post['post_content'] = convert_chars(utf8_encode( $_POST['post_content'] ));
			


		//if( $fep_role_settings['instantly_publish'] && current_user_can( $fep_role_settings['instantly_publish'] ) )
			$new_post['post_status'] = 'publish';
		//else
		//	$new_post['post_status'] = 'pending';
		
		if($_POST['post_id'] != -1){
			$new_post['ID'] = $_POST['post_id'];
			$new_post_id = $_POST['post_id'];
			wp_update_post($new_post);
			$data['message'] = '<div class="alert alert-success"><strong>Your article has been updated successfully!</strong> <br/><br/> read it now <a  class="btn" href="'. get_permalink($new_post_id).'">'.get_the_title($new_post_id).'</a> Author'. $new_post->post_date .'</div>';
			wp_set_object_terms($new_post_id, intval($_POST['post_category']),'help-categories');

		} else {
			$new_post_id = wp_insert_post($new_post);
			update_post_meta($new_post_id, '_wp_page_template', 'help-articles.php');
			////if( current_user_can( $fep_role_settings['instantly_publish'] ) ){
				$permalink = get_permalink( $new_post_id );
				//$data['message'] = 'Your article has been published successfully!';
				$data['message'] = '<div class="alert alert-success"><strong>Your article has been published successfully!</strong> <br/><br/> read it now <a  class="btn" href="'. get_permalink($new_post_id).'">'.get_the_title($new_post_id).'</a> Author'. the_author($new_post_id) .'</div>';
			////}
			////else
				//$data['message'] = 'Your article has been submitted successfully!';
			////$data['message'] = '<div class="alert alert-success"><strong>Your article has been submitted successfully!</strong></div>';
			wp_set_object_terms($new_post_id, intval($_POST['post_category']),'help-categories');
		}
		
		if(!$fep_misc['disable_author_bio']){
			if($fep_misc['nofollow_bio_links'])
				update_post_meta($new_post_id, 'about_the_author', wp_rel_nofollow($_POST['about_the_author']) );
			else
				update_post_meta($new_post_id, 'about_the_author', $_POST['about_the_author']);
		}
		
		$data['error'] = true;
	}
	else{
		$data['error'] = false;
		$data['message'] = '<h2>Your submission has errors. Please try again!</h2>'.$errors;
	}

	die( json_encode($data) );
}

function show_tags(){
	$tags = get_tags();
	$html = '';
	foreach ( $tags as $tag ) {	
		$html .= "{$tag->slug},";
	}
	echo $html;
}


//[fep_submission_form]
function fep_post_form( $post=0, $id=-1 ){
global $fep_shortcode_used;
$fep_shortcode_used = true;

$fep_role_settings = get_option('fep_role_settings');
$fep_misc = get_option('fep_misc');

echo current_user_can('publish_posts');
echo get_current_user_id();
echo get_current_user_id();

 if ( is_user_logged_in() ) {
?>	
	<noscript><div id="no-js" class="warning">This form needs JavaScript to finction properly. Please turn on JavaScript and try again!</div></noscript>
	<div class="line page_header" >
		<h1 class="page-title unit" itemprop="headline">Help</h1>
		<?php 
	    	wp_nav_menu( array('menu' => 'Help', 'container_class' => 'tabs unit', 'menu_class' => 'nav nav-pills')); 
	    ?>	
		<div class="lastUnit section-actions">
			<a href="<?php echo site_url(); ?>/new-article" id="" class="pull-right action btn btn-success btn-mini tips" title="view as list"><i class="icon-plus"></i> new article</a>
		</div>
	</div>

	<div id="new-post-form">
		<?php echo ($post) ? '<h1>Edit Article</h1>':'<h1>New Article</h1>'; ?>

	<div id="message" class=""></div>
	<form id="post-form">
	<label for="post_title">Title</label>
	<input type="text" name="post_title" id="post_title" value="<?php echo ($post) ? $post['title']:''; ?>" />
	<label for="post_content">Content</label>
	<?php wp_editor( $post['content'], 'post_content', $settings = array('textarea_name'=>'post_content', 'textarea_rows'=> 12, 'editor_class' => 'white') );
	wp_nonce_field('fepnonce_action','fepnonce'); ?>
	<?php if(!$fep_misc['disable_author_bio']): ?>
	<label for="about_the_author">Author Bio</label>
	<textarea name="about_the_author" id="about_the_author" rows="5"><?php echo ($post) ? $post['about_the_author']:''; ?></textarea>
	<?php else: ?>
	<input type="hidden" name="about_the_author" id="about_the_author" value="Not empty">
	<?php endif; ?>
	<label for="post_category">Category</label>
	<?php wp_dropdown_categories(array('id'=>'post_category', 'hide_empty' => 0, 'name' => 'post_category', 'orderby' => 'name', 'selected' => $post['category'], 'taxonomy' => 'help-categories', 'hierarchical' => true, 'show_option_none' => __('None'))); ?>
	<label for="post_tags">Tags</label>
	<input type="text" name="post_tags" id="post_tags" class="autocomplete" data-tag-list="<?php show_tags(); ?>" multiple value= "<?php echo ($post) ? $post['tags']:''; ?>">
	<input type="hidden" name="post_id" id="post_id" value="<?php echo $id ?>">
	<!--<img id="loading" src="<?php echo plugins_url( 'images/ajax-loading.gif', __FILE__ ); ?>">--> 
	<br/>
	<br/>
	<button type="button" id="submit" class="btn btn-success btn-large">Submit</button></form>
		<br/>
	<br/>
		<br/>
	<br/>
	</div>
<?php }
 else{
	auth_redirect();
 }
}

function fep_add_new_post(){
	fep_post_form();
}
add_shortcode( 'fep_submission_form', 'fep_add_new_post' );

function fep_display_post_list( $status ){
$page_url = ( isset( $_SERVER["HTTPS"] ) && $_SERVER["HTTPS"] == "on" ? "https://" : "http://" ).$_SERVER["SERVER_NAME"].strip_tags( $_SERVER["REQUEST_URI"] );
if ( is_user_logged_in() ):

    global $current_user;
    get_currentuserinfo();
    $author_posts = new WP_Query( array('post_type' => 'help-article', 'posts_per_page' => -1, 'orderby'=> 'DESC', 'author' => $current_user->ID, 'post_status' => $status )); ?>
	<table>
	<?php
	if( !$author_posts->have_posts() ):
		if($status=='publish') $status='live';
		echo 'You don\'t have any '.$status.' articles';
	endif;
	
    while($author_posts->have_posts()) : $author_posts->the_post();
	$postid = get_the_ID();
    ?>
		<tr id="row-<?php echo $postid; ?>">
			<?php if($status=='publish'): ?>
				<td class="post-title">
					<a href="<?php the_permalink(); ?>" title="View Post">
						<strong><?php the_title(); ?></strong> <br/>
					</a>
						<div class="tag-list">
							<i class="icon-tags"></i>
							<?php the_tags('', '', ''); ?> 
						</div>
					
				</td>
			<?php endif; ?>
		<td class="post-edit"><a href="<?php echo $page_url.'?action=edit&id='.$postid; ?>" class="btn"><i class="icon-pencil"></i> Edit</a></td>
		<td class="post-delete"><!--<img id="loading-<?php echo $postid; ?>" class="loading" src="<?php echo plugins_url( 'images/ajax-loading.gif', __FILE__ ); ?>">--><a id="<?php echo $postid; ?>" style="cursor:pointer;" class="btn btn-warning"><i class="icon-cross"></i> Delete</a></td>
		</tr>
    <?php
    endwhile;
	echo '</table>';
	wp_reset_query();
	wp_reset_postdata();
else :
    auth_redirect();
endif;
}

function fep_manage_posts(){
global $fep_shortcode_used;
$fep_shortcode_used = true;

if( isset($_GET['id']) ){
	if($_GET['action']=='edit'){
		$current_post_id = $_GET['id'];
		$p = get_post($current_post_id, 'ARRAY_A');
		$current_content['title'] = $p['post_title'];
		$current_content['content'] = $p['post_content'];
		//$category = the_terms($current_post_id);
		$category_terms = wp_get_object_terms($current_post_id, 'help-categories');
		    foreach($category_terms as $term){
		      	$category = $term->term_id;
		    }
		$current_content['category'] = $category;
		$tags = wp_get_post_tags( $current_post_id, array( 'fields' => 'names' ) );
		$current_content['tags'] = implode(', ', $tags);
		$current_content['about_the_author'] = get_post_meta($current_post_id, 'about_the_author', true);
		fep_post_form($current_content, $current_post_id);
	}
}else{ ?>
	<div class="line page_header" >
		<h1 class="page-title unit" itemprop="headline">Help</h1>
		<?php 
	    	wp_nav_menu( array('menu' => 'Help', 'container_class' => 'tabs unit', 'menu_class' => 'nav nav-pills')); 
	    ?>	
		<div class="lastUnit section-actions">
			<a href="<?php echo site_url(); ?>/new-article" id="" class="pull-right action btn btn-success btn-mini tips" title="view as list"><i class="icon-plus"></i> new article</a>
		</div>
	</div>
	<div id="message" style="clear:both;"></div>
<div id="tabs">
	<h1 class="page-title unit" itemprop="headline">My Articles</h1>
	<ul>
		<li><a href="#tab-1">Live</a></li>
		<li><a href="#tab-2">Pending</a></li>
	</ul>
	<div id="tab-1">
	<?php fep_display_post_list('publish'); ?>
	</div>
	<div id="tab-2">
	<?php fep_display_post_list('pending'); ?>
	</div>
</div>
<?php
}
}
add_shortcode( 'fep_article_list', 'fep_manage_posts' );

add_action( 'wp_ajax_fep_delete_posts', 'fep_delete_posts' );
add_action( 'wp_ajax_nopriv_fep_delete_posts', 'fep_delete_posts' );

function fep_delete_posts(){
	if( wp_delete_post( $_POST['post_id'], true ) )
	$data['message'] = 'Your article has been deleted successfully!';
	else
	$data['message'] = 'The article could not be deleted';
	
	die( json_encode($data) );
}

include('options-panel.php');
?>