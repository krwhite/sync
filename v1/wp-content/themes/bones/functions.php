<?php

/*

Author: Eddie Machado

URL: htp://themble.com/bones/



This is where you can drop your custom functions or

just edit things like thumbnail sizes, header images,

sidebars, comments, ect.

*/



/************* INCLUDE NEEDED FILES ***************/



/*

1. library/bones.php

    - head cleanup (remove rsd, uri links, junk css, ect)

	- enqueueing scripts & styles

	- theme support functions

    - custom menu output & fallbacks

	- related post function

	- page-navi function

	- removing <p> from around images

	- customizing the post excerpt

	- custom google+ integration

	- adding custom fields to user profiles

*/

require_once('library/bones.php'); // if you remove this, bones will break

/*

2. library/custom-post-type.php

    - an example custom post type

    - example custom taxonomy (like categories)

    - example custom taxonomy (like tags)

*/

require_once('library/custom-post-type.php'); // you can disable this if you like

/*

3. library/admin.php

    - removing some default WordPress dashboard widgets

    - an example custom dashboard widget

    - adding custom login css

    - changing text in footer of admin

*/

// require_once('library/admin.php'); // this comes turned off by default

/*

4. library/translation/translation.php

    - adding support for other languages

*/

// require_once('library/translation/translation.php'); // this comes turned off by default



add_theme_support( 'buddypress' );





// new file size limits

@ini_set( 'upload_max_size' , '25M' );

@ini_set( 'post_max_size', '25M');

@ini_set( 'max_execution_time', '30' );



/************* THUMBNAIL SIZE OPTIONS *************/



// Thumbnail sizes

add_image_size( 'bones-thumb-600', 600, 150, true );

add_image_size( 'bones-thumb-300', 300, 100, true );

/*

to add more sizes, simply copy a line from above

and change the dimensions & name. As long as you

upload a "featured image" as large as the biggest

set width or height, all the other sizes will be

auto-cropped.



To call a different size, simply change the text

inside the thumbnail function.



For example, to call the 300 x 300 sized image,

we would use the function:

<?php the_post_thumbnail( 'bones-thumb-300' ); ?>

for the 600 x 100 image:

<?php the_post_thumbnail( 'bones-thumb-600' ); ?>



You can change the names and dimensions to whatever

you like. Enjoy!

*/



/************* ACTIVE SIDEBARS ********************/



// Sidebars & Widgetizes Areas

function bones_register_sidebars() {

    register_sidebar(array(

    	'id' => 'sidebar1',

    	'name' => __('Sidebar 1', 'bonestheme'),

    	'description' => __('The first (primary) sidebar.', 'bonestheme'),

    	'before_widget' => '<div id="%1$s" class="widget %2$s">',

    	'after_widget' => '</div>',

    	'before_title' => '<h4 class="widgettitle">',

    	'after_title' => '</h4>',

    ));



    /*

    to add more sidebars or widgetized areas, just copy

    and edit the above sidebar code. In order to call

    your new sidebar just use the following code:



    Just change the name to whatever your new

    sidebar's id is, for example:



    register_sidebar(array(

    	'id' => 'sidebar2',

    	'name' => __('Sidebar 2', 'bonestheme'),

    	'description' => __('The second (secondary) sidebar.', 'bonestheme'),

    	'before_widget' => '<div id="%1$s" class="widget %2$s">',

    	'after_widget' => '</div>',

    	'before_title' => '<h4 class="widgettitle">',

    	'after_title' => '</h4>',

    ));



    To call the sidebar in your template, you can just copy

    the sidebar.php file and rename it to your sidebar's name.

    So using the above example, it would be:

    sidebar-sidebar2.php



    */

} // don't remove this bracket!



/************* COMMENT LAYOUT *********************/



// Comment Layout

function bones_comments($comment, $args, $depth) {

   $GLOBALS['comment'] = $comment; ?>

	<li <?php comment_class('activity-item'); ?>>

		<article id="comment-<?php comment_ID(); ?>" class="clearfix">

			<header class="comment-author vcard line">

                <div class="activity-avatar unit">

                    <a href="<?php echo get_comment_author_url(); ?>">

			         <?php echo get_avatar($comment,$size='40',$default='<path_to_url>');?>

                    </a>

                </div>

			    <!-- custom gravatar call

			    <?php

			    	// create variable

			    	$bgauthemail = get_comment_author_email();

			    ?>

			    <img data-gravatar="http://www.gravatar.com/avatar/<?php echo md5($bgauthemail); ?>?s=32" class="load-gravatar avatar avatar-48 photo" height="32" width="32" src="<?php echo get_template_directory_uri(); ?>/library/images/nothing.gif" />

			    <!-- end custom gravatar call -->

                <div class="activity-content">

                    <div class="activity-header line">

                        <?php echo get_comment_author_link(); ?>

                        <time datetime="<?php echo comment_time('Y-m-j'); ?>"><span class="timeago"> <?php comment_time(__('F jS, Y', 'bonestheme')); ?> </span></time>

                    </div>

                    <div class="activity-inner">

                       <?php comment_text() ?>

                    </div>

                    <div class="activity-meta">

                        <?php comment_reply_link(array_merge( $args, array('reply_text' => '<i class="icon-reply"></i> <span>Reply</span>','depth' => $depth, 'max_depth' => $args['max_depth']))) ?>

                        <?php edit_comment_link('<i class="icon-pencil"></i> <span>Edit</span>') ?>

                    </div>

                </div>







			</header>

			<?php if ($comment->comment_approved == '0') : ?>

       			<div class="alert info">

          			<p><?php _e('Your comment is awaiting moderation.', 'bonestheme') ?></p>

          		</div>

			<?php endif; ?>





		</article>

    <!-- </li> is added by WordPress automatically -->

<?php

} // don't remove this bracket!



/************* SEARCH FORM LAYOUT *****************/



// Search Form

function bones_wpsearch($form) {

    $form = '<form role="search" method="get" id="searchform" action="' . home_url( '/' ) . '" >

    <label class="screen-reader-text" for="s">' . __('Search for:', 'bonestheme') . '</label>

    <input type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="'.esc_attr__('Search the Site...','bonestheme').'" />

    <input type="submit" id="searchsubmit" value="'. esc_attr__('Search') .'" />

    </form>';

    return $form;

} // don't remove this bracket!



function wpse28145_add_custom_types( $query ) {

    if( is_tag() && empty( $query->query_vars['suppress_filters'] ) ) {



        // this gets all post types:

        $post_types = get_post_types();



        // alternately, you can add just specific post types using this line instead of the above:

        // $post_types = array( 'post', 'your_custom_type' );





        $query->set( 'post_type', $post_types );

        return $query;

    }

}

add_filter( 'pre_get_posts', 'wpse28145_add_custom_types' );







/************* Custom Search Stuff *****************/







///Unified search



/* Add these code to your functions.php to allow Single Search page for all buddypress components*/

//  Remove Buddypress search drowpdown for selecting members etc

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

<div id="members" class="members-search-result search-result tab-pane active">

   <h4 class="content-title"><?php _e('Members',"bpmag");?></h4>

    <?php locate_template( array( 'members/members-loop.php' ), true ) ;  ?>

        <?php global $members_template;

            if($members_template->total_member_count>1):?>

        <a href="<?php echo bp_get_root_domain().'/'.  bp_get_members_slug().'/?s='.$_REQUEST['search-terms']?>" ><?php _e(sprintf('View all %d matched Members',$members_template->total_member_count),"bpmag");?></a>

    <?php   endif; ?>

</div>

<?php

 }

//Hook Member results to search page

add_action('advance-search','bpmag_show_member_search',10); //the priority defines where in page this result will show up(the order of member search in other searchs)



//Group search

function bpmag_show_groups_search(){

    ?>

<div id="groups" class="groups-search-result search-result tab-pane">

    <h4 class="content-title"><?php _e('Group Search','bpmag');?></h4>

    <?php locate_template( array('groups/groups-loop.php' ), true ) ;  ?>

        <a href="<?php echo bp_get_root_domain().'/'.  bp_get_groups_slug().'/?s='.$_REQUEST['search-terms']?>" ><?php _e("View All matched Groups","bpmag");?></a>

</div>

    <?php

 //endif;

  }



//Hook Groups results to search page

 if(bp_is_active( 'groups' ))

    add_action('advance-search','bpmag_show_groups_search',15);



 /**activity update search*/

 //Group search

function bpmag_show_activity_search(){

    ?>

<div id="activity" class="activity-search-result search-result tab-pane">

    <h4 class="content-title"><?php _e('Activity Updates','bpmag');?></h4>

    <?php locate_template( array('activity/activity-loop.php' ), true ) ;  ?>



        <a href="<?php echo bp_get_root_domain().'/'.  bp_get_activity_slug().'/?s='.$_REQUEST['search-terms']?>" ><?php _e("View all matched updates","bpmag");?></a>

</div>

    <?php

 //endif;

  }



//Hook Groups results to search page

 if(bp_is_active( 'activity' ))

    add_action('advance-search','bpmag_show_activity_search',20);



/**

 *

 * Show blog posts in search

 */

function bpmag_show_site_blog_search(){

    ?>

 <div id="help" class="blog-search-result search-result tab-pane">



  <h4 class="content-title">Posts</h4>

   <?php locate_template( array( 'search-loop.php' ), true ) ;  ?>

   <a href="<?php echo bp_get_root_domain().'/?s='.$_REQUEST['search-terms']?>" ><?php _e("View All matched Posts","bpmag");?></a>

</div>

   <?php

  }



//Hook Blog Post results to search page

 add_action('advance-search',"bpmag_show_site_blog_search",25);





//show blogs search result



function bpmag_show_blogs_search(){



    ?>

  <div class="blogs-search-result search-result">

    <h2 class="content-title"><?php _e('Blogs Search',"bpmag");?></h2>

    <?php locate_template( array( 'blogs/blogs-loop.php' ), true ) ;  ?>

    <a href="<?php echo bp_get_root_domain().'/'. bp_get_blogs_slug().'/?s='.$_REQUEST['search-terms']?>" ><?php _e("View All matched Blogs","bpmag");?></a>



 </div>

  <?php

  }



//Hook Blogs results to search page if blogs comonent is active

 if(is_multisite()&&bp_is_active( 'blogs' ))

    add_action('advance-search','bpmag_show_blogs_search',10);



 //show forums search

function bpmag_show_forums_search(){

    ?>

 <div class="forums-search-result search-result">

   <h2 class="content-title"><?php _e("Forums Search","bpmag");?></h2>

  <?php locate_template( array( 'forums/forums-loop.php' ), true ) ;  ?>

   <a href="<?php echo bp_get_root_domain().'/'.  bp_get_forums_slug().'/?s='.$_REQUEST['search-terms']?>" ><?php _e("View All matched forum posts","bpmag");?></a>

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

  <a href="<?php echo get_permalink($page).'?ts='.$_REQUEST['search-terms']?>" ><?php _e("View All matched topics","bpmag");?></a>

 </div>

  <?php

  }



//Hook Blogs results to search page if blogs comonent is active

 if(function_exists( 'bbp_has_topics' ))

    add_action('advance-search','bpmag_show_bbpress_topic_search',10);













/************* add taxonomy field to page *****************/



add_action( 'show_user_profile', 'my_show_extra_profile_fields' );

add_action( 'edit_user_profile', 'my_show_extra_profile_fields' );

function show_the_tags(){

    $tags = get_tags();

    $html = '';

    foreach ( $tags as $tag ) {

        $html .= "{$tag->slug},";

    }

    echo $html;

}

function my_show_extra_profile_fields( $user ) { ?>



    <h3>Extra profile information</h3>



    <table class="form-table">



        <tr>

            <th><label for="twitter">Skills</label></th>



            <td>

                <input type="text" name="skills" id="skills" data-tag-list="<?php show_tags(); ?>" value="<?php echo esc_attr( get_the_author_meta( 'skills', $user->ID ) ); ?>" class="autocomplete"  multiple /><br />

                <span class="description">Please enter your skills.</span>

            </td>

        </tr>



    </table>

<?php }



add_action( 'personal_options_update', 'my_save_extra_profile_fields' );

add_action( 'edit_user_profile_update', 'my_save_extra_profile_fields' );



function my_save_extra_profile_fields( $user_id ) {



    if ( !current_user_can( 'edit_user', $user_id ) )

        return false;



    /* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */

    update_usermeta( $user_id, 'skills', $_POST['skills'] );

}



/* custom login screen */

function custom_login() { echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo('template_directory') . '/custom-login/custom-login.css" />'; } add_action('login_head', 'custom_login');



function addUploadMimes($mimes) {

    $mimes = array_merge($mimes, array(

        'epub|mobi' => 'application/octet-stream'

    ));

    return $mimes;

}

add_filter('upload_mimes', 'addUploadMimes');



?>

