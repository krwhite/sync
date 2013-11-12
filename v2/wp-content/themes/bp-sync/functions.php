<?php

/****  Set up Widget Areas *****/
if (function_exists('register_sidebar')) {
	register_sidebar(array(
		'name'=> 'Header Widgets',
		'id' => 'header-widgets',
		'description' => 'A place for widgets such as search in the page header.',
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	));
	register_sidebar(array(
		'name'=> 'Content Widgets Top',
		'id' => 'content_widgets_top',
		'description' => 'For widgets that need to go just above the main content.',
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	));
	register_sidebar(array(
		'name'=> 'Full Width Footer Widgets',
		'id' => 'full-footer-widgets',
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	));
}

/****************************** Unified Search ***************************************/
///Unified search

/* Add these code to your functions.php to allow Single Search page for all buddypress components*/
//	Remove Buddypress search drowpdown for selecting members etc
add_filter('bp_search_form_type_select', 'bpmag_remove_search_dropdown'  );
function bpmag_remove_search_dropdown($select_html){
    return '';
}

//force buddypress to not process the search/redirect
remove_action( 'bp_init', 'bp_core_action_search_site', 7 );

//let us handle the unified page ourself
add_action( 'init', 'bp_buddydev_search', 10 );// custom handler for the search
function bp_buddydev_search(){
global $bp;
	if ( bp_is_current_component(BP_SEARCH_SLUG) )//if thids is search page
		bp_core_load_template( apply_filters( 'bp_core_template_search_template', 'search-single' ) );//load the single searh template
}

add_action('advance-search','bpmag_show_search_results',1);//highest priority

/* we just need to filter the query and change search_term=The search text*/
function bpmag_show_search_results(){
    //filter the ajaxquerystring
     add_filter('bp_ajax_querystring','bpmag_global_search_qs',100,2);
}
 //modify the query string with the search term
function bpmag_global_search_qs(){
	return 'search_terms='.$_REQUEST['search-terms'];
}

function bpmag_is_advance_search(){
global $bp;
if(bp_is_current_component( BP_SEARCH_SLUG))
	return true;
return false;
}

//show the search results for member*/
function bpmag_show_member_search(){
    ?>
   <div class="members-search-result search-result">
   <h2 class="content-title"><?php _e('Members',"bpmag");?></h2>
  <?php locate_template( array( 'members/members-loop.php' ), true ) ;  ?>
  <?php global $members_template;
	if($members_template->total_member_count>1):?>
   <a href="<?php echo bp_get_root_domain().'/'.  bp_get_members_slug().'/?s='.$_REQUEST['search-terms']?>" ><?php _e(sprintf('View all %d matched Members',$members_template->total_member_count),"bpmag");?></a>
	<?php 	endif; ?>
    </div>
<?php	
 }
//Hook Member results to search page
add_action('advance-search','bpmag_show_member_search',10); //the priority defines where in page this result will show up(the order of member search in other searchs)

//Group search
function bpmag_show_groups_search(){
    ?>
<div class="groups-search-result search-result">
 	<h2 class="content-title"><?php _e('Groups','bpmag');?></h2>
	<?php locate_template( array('groups/groups-loop.php' ), true ) ;  ?>
	
        <a href="<?php echo bp_get_root_domain().'/'.  bp_get_groups_slug().'/?s='.$_REQUEST['search-terms']?>" ><?php _e("View all matched groups","bpmag");?></a>
</div>
	<?php
 //endif;
  }

//Hook Groups results to search page
 if(bp_is_active( 'groups' ))
    add_action('advance-search','bpmag_show_groups_search',15);

/**
 *
 * Show blog posts in search
 */
function bpmag_show_site_blog_search(){
    ?>
 <div class="blog-search-result search-result">
 
  <h2 class="content-title"><?php _e('Articles','bpmag');?></h2>
   
   <?php locate_template( array( 'search-loop.php' ), true ) ;  ?>
   <a href="<?php echo bp_get_root_domain().'/?s='.$_REQUEST['search-terms']?>" ><?php _e("View all matched articles","bpmag");?></a>
</div>
   <?php
  }

//Hook Blog Post results to search page
 add_action('advance-search',"bpmag_show_site_blog_search",20);


//show blogs search result

function bpmag_show_blogs_search(){

    ?>
  <div class="blogs-search-result search-result">
  <h2 class="content-title"><?php _e('Articles',"bpmag");?></h2>
  <?php locate_template( array( 'blogs/blogs-loop.php' ), true ) ;  ?>
  <a href="<?php echo bp_get_root_domain().'/'. bp_get_blogs_slug().'/?s='.$_REQUEST['search-terms']?>" ><?php _e("View all matched articles","bpmag");?></a>
 </div>
  <?php
  }

//Hook Blogs results to search page if blogs comonent is active
 if(is_multisite()&&bp_is_active( 'blogs' ))
    add_action('advance-search','bpmag_show_blogs_search',10);

 /**activity update search*/
 //Group search
function bpmag_show_activity_search(){
    ?>
<div class="activity-search-result search-result">
 	<h2 class="content-title"><?php _e('Discussions and Activity','bpmag');?></h2>
	<?php locate_template( array('activity/activity-loop.php' ), true ) ;  ?>
	
        <a href="<?php echo bp_get_root_domain().'/'.  bp_get_activity_slug().'/?s='.$_REQUEST['search-terms']?>" ><?php _e("View all matched updates","bpmag");?></a>
</div>
	<?php
 //endif;
  }

//Hook Groups results to search page
 if(bp_is_active( 'activity' ))
    add_action('advance-search','bpmag_show_activity_search',25);



 //show forums search
function bpmag_show_forums_search(){
    ?>
 <div class="forums-search-result search-result">
   <h2 class="content-title"><?php _e("Forums Search","bpmag");?></h2>
  <?php locate_template( array( 'forums/forums-loop.php' ), true ) ;  ?>
   <a href="<?php echo bp_get_root_domain().'/'.  bp_get_forums_slug().'/?s='.$_REQUEST['search-terms']?>" ><?php _e("View all matched forum posts","bpmag");?></a>
</div>
  <?php
  }

//Hook Forums results to search page
if ( bp_is_active( 'forums' ) && bp_forums_is_installed_correctly() && bp_forums_has_directory() )
 add_action('advance-search',"bpmag_show_forums_search",20);


 
 function bpmag_show_bbpress_topic_search(){
     $_REQUEST['ts']=$_REQUEST['search-terms'];//put it for bbpress topic search
    ?>
  <div class="bbp-topic-search-result search-result">
  <h2 class="content-title"><?php _e('Global Topic Search',"bpmag");?></h2>
  <?php bbp_get_template_part('bbpress/content','archive-topic') ;  ?>
  <?php
  global $bbp;
    $page = bbp_get_page_by_path( $bbp->root_slug );
    
  ?>
  <a href="<?php echo get_permalink($page).'?ts='.$_REQUEST['search-terms']?>" ><?php _e("View all matched topics","bpmag");?></a>
 </div>
  <?php
  }

//Hook Blogs results to search page if blogs comonent is active
 if(function_exists( 'bbp_has_topics' ))
    add_action('advance-search','bpmag_show_bbpress_topic_search',10);


/* Don't set background image css if no image is set in the theme preferences */
if ( !function_exists( 'bp_dtheme_custom_background_style' ) ) :
/**
 * The style for the custom background image or colour.
 *
 * Referenced via add_custom_background() in bp_dtheme_setup().
 *
 * @see _custom_background_cb()
 * @since BuddyPress (1.5)
 */
function bp_dtheme_custom_background_style() {
	$background = get_background_image();
	$color = get_background_color();
	if ( ! $background && ! $color )
		return;

	$style = $color ? "background-color: #$color;" : '';

	if ( $style && !$background ) {
		$style .= '';

	} elseif ( $background ) {
		$image = " background-image: url('$background');";

		$repeat = get_theme_mod( 'background_repeat', 'repeat' );
		if ( ! in_array( $repeat, array( 'no-repeat', 'repeat-x', 'repeat-y', 'repeat' ) ) )
			$repeat = 'repeat';
		$repeat = " background-repeat: $repeat;";

		$position = get_theme_mod( 'background_position_x', 'left' );
		if ( ! in_array( $position, array( 'center', 'right', 'left' ) ) )
			$position = 'left';
		$position = " background-position: top $position;";

		$attachment = get_theme_mod( 'background_attachment', 'scroll' );
		if ( ! in_array( $attachment, array( 'fixed', 'scroll' ) ) )
			$attachment = 'scroll';
		$attachment = " background-attachment: $attachment;";

		$style .= $image . $repeat . $position . $attachment;
	}
?>
	<style type="text/css">
		body { <?php echo trim( $style ); ?> }
	</style>
<?php
}
endif;
?>