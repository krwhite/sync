<?php

 //// New Avatar Sizes
if ( !defined( 'BP_AVATAR_THUMB_WIDTH' ) )
define( 'BP_AVATAR_THUMB_WIDTH', 50 ); //change this with your desired thumb width

if ( !defined( 'BP_AVATAR_THUMB_HEIGHT' ) )
define( 'BP_AVATAR_THUMB_HEIGHT', 50 ); //change this with your desired thumb height

if ( !defined( 'BP_AVATAR_FULL_WIDTH' ) )
define( 'BP_AVATAR_FULL_WIDTH', 282 ); //change this with your desired full size,weel I changed it to 260 <img src="http://buddydev.com/wp-includes/images/smilies/icon_smile.gif?84cd58" alt=":)" class="wp-smiley">

if ( !defined( 'BP_AVATAR_FULL_HEIGHT' ) )
define( 'BP_AVATAR_FULL_HEIGHT', 282 ); //change this to default height for full avatar


 define( 'BP_DEFAULT_COMPONENT', 'profile' );


function my_bp_get_options_nav() {
global $bp;

	// If we are looking at a member profile, then the we can use the current component as an
	// index. Otherwise we need to use the component's root_slug
	$component_index = !empty( $bp->displayed_user ) ? bp_current_component() : bp_get_root_slug( bp_current_component() );

	if ( !bp_is_single_item() ) {
		if ( !isset( $bp->bp_options_nav[$component_index] ) || count( $bp->bp_options_nav[$component_index] ) < 1 ) {
			return false;
		} else {
			$the_index = $component_index;
		}
	} else {
		if ( !isset( $bp->bp_options_nav[bp_current_item()] ) || count( $bp->bp_options_nav[bp_current_item()] ) < 1 ) {
			return false;
		} else {
			$the_index = bp_current_item();
		}
	}


	// reorder menu items
	  // $bp->bp_nav['members']['position'] = 1;
      $bp->bp_nav['discussions']['position'] = 1;
      $bp->bp_nav['members']['position'] = 10;
      $bp->bp_nav['home']['position'] = 80;
      $bp->bp_nav['send-invites']['position'] = 90;
      $bp->bp_nav['admin']['position'] = 100;


	// Loop through each navigation item
	foreach ( (array) $bp->bp_options_nav[$the_index] as $subnav_item ) {
		if ( !$subnav_item['user_has_access'] )
			continue;

		// If the current action or an action variable matches the nav item id, then add a highlight CSS class.
		if ( $subnav_item['slug'] == bp_current_action() ) {
			$selected = ' class="current selected"';
		} else {
			$selected = '';
		}

		$iconClass = '';
		if( $subnav_item['slug'] == "home"){
			$iconClass = 'icon-bolt';
			$titleText = "Activty";
		}
		elseif( $subnav_item['slug'] == "members"){
			$iconClass = 'icon-group';
			$titleText = "Members";
		}
		elseif( $subnav_item['slug'] == "admin"){
			$iconClass = 'icon-cogs';
			$titleText = "Group Settings";
		}
		elseif( $subnav_item['slug'] == "invite-anyone"){
			$iconClass = 'icon-ticket';
			$titleText = "Invite People";
		}
		elseif( $subnav_item['slug'] == "discussions"){
			$iconClass = 'icon-comments';
			$titleText = "Discussions";

		}


		// List type depends on our current component
		$list_type = bp_is_group() ? 'groups' : 'personal';

		// echo out the final list item
		echo apply_filters( 'bp_get_options_nav_' . $subnav_item['css_id'], '<li id="' . $subnav_item['css_id'] . '-' . $list_type . '-li" ' . $selected . '><a id="' . $subnav_item['css_id'] . '" href="' . $subnav_item['link'] . '" title="'. $titleText .'" class="tips"><i class="' . $iconClass . '"></i><span class="hide">' . $subnav_item['name'] . '</span><b></b></a></li>', $subnav_item );
	}
}


/**
 * Uses the $bp->bp_nav global to render out the user navigation when viewing another user other than
 * yourself.
 *
 * @package BuddyPress Core
 * @global BuddyPress $bp The one true BuddyPress instance
 */
function my_bp_get_displayed_user_nav() {
	global $bp;

	// reorder menu items
	    //$bp->bp_nav['members']['position'] = 1;
      $bp->bp_nav[20]['position'] = 1;
      $bp->bp_nav[10]['position'] = 10;
      $bp->bp_nav[70]['position'] = 600;
      $bp->bp_nav[50]['position'] = 700;
      $bp->bp_nav[100]['position'] = 900;

      // hide friends and invites screen
	  $bp->bp_nav[60]['show_for_displayed_user'] = false;

	foreach ( (array) $bp->bp_nav as $user_nav_item ) {
		if ( empty( $user_nav_item['show_for_displayed_user'] ) && !bp_is_my_profile() )
			continue;


		$selected = '';
		if ( bp_is_current_component( $user_nav_item['slug'] ) ) {
			$selected = ' class="current selected"';
		}

		$iconClass = '';
		if( $user_nav_item['slug'] == "profile"){
			$iconClass = 'icon-list-ul';
			$titleText = "Profile Details";
		}
		elseif( $user_nav_item['slug'] == "activity"){
			$iconClass = 'icon-bolt';
			$titleText = "Recent Activity";
		}
		elseif( $user_nav_item['slug'] == "messages"){
			$iconClass = 'icon-comment';
			$titleText = "Group Settings";
		}
		elseif( $user_nav_item['slug'] == "groups"){
			$iconClass = 'icon-group';
			$titleText = "Groups";
		}
		elseif( $user_nav_item['slug'] == "friends"){
			$iconClass = 'icon-group';
			$titleText = "Groups";
		}
		elseif( $user_nav_item['slug'] == "settings"){
			$iconClass = 'icon-cogs';
			$titleText = "Settings";

		}

		// List type depends on our current component
		$list_type = bp_is_group() ? 'groups' : 'personal';

		if ( bp_loggedin_user_domain() ) {
			$link = str_replace( bp_loggedin_user_domain(), bp_displayed_user_domain(), $user_nav_item['link'] );
		} else {
			$link = trailingslashit( bp_displayed_user_domain() . $user_nav_item['link'] );
		}
// echo out the final list item
		echo apply_filters( 'bp_member_options_nav' . $user_nav_item['css_id'], '<li id="' . $user_nav_item['css_id'] . '-' .$list_type.'-li"' . $selected . '><a id="' . $user_nav_item['css_id'] . '" href="' . $link . '" title="'. $titleText .'" class="tips"><i class="' . $iconClass . '"></i><span class="hide">' . $user_nav_item['name'] . '</span><b></b></a></li>', $user_nav_item  );
	}
}


/// remove the ability to upload an avatar
//function remove_change_avatar() {
//    bp_core_remove_subnav_item( 'profile', 'change-avatar' );
//}
//add_action( 'bp_setup_nav', 'remove_change_avatar', 100 );


/**
 * Outputs the activity user id
 *
 * @since BuddyPress (1.1)
 *
 * @uses bp_get_activity_user_id()
 */
function bp_activity_user_name() {
	echo bp_get_activity_user_name();
}

	/**
	 * Returns the activity user id
	 *
	 * @since BuddyPress (1.1)
	 *
	 * @global object $activities_template {@link BP_Activity_Template}
	 * @uses apply_filters() To call the 'bp_get_activity_user_id' hook
	 *
	 * @return int The activity user id
	 */
	function bp_get_activity_user_name() {
		global $activities_template;
		$thePosterID = $_REQUEST['id'];
		$thePoster = xprofile_get_field_data('Name', $activities_template->activity->user_id);
		return apply_filters('bp_get_activity_user_name', $thePoster);

	}


  /**
         * Display the activity delete link.
         *
         * @since BuddyPress (1.1)
         *
         * @uses bp_get_activity_delete_link()
         */
        function bp_delete_link_custom() {
            echo bp_get_delete_link_custom();
        }

            /**
             * Return the activity delete link.
             *
             * @since BuddyPress (1.1)
             *
             * @global object $activities_template {@link BP_Activity_Template}
             * @uses bp_get_root_domain()
             * @uses bp_get_activity_root_slug()
             * @uses bp_is_activity_component()
             * @uses bp_current_action()
             * @uses add_query_arg()
             * @uses wp_get_referer()
             * @uses wp_nonce_url()
             * @uses apply_filters() To call the 'bp_get_activity_delete_link' hook
             *
             * @return string $link Activity delete link. Contains $redirect_to arg if on single activity page.
             */
            function bp_get_delete_link_custom() {
                global $activities_template;

                $url   = bp_get_root_domain() . '/' . bp_get_activity_root_slug() . '/delete/' . $activities_template->activity->id;
                $class = 'delete-activity';

                // Determine if we're on a single activity page, and customize accordingly
                if ( bp_is_activity_component() && is_numeric( bp_current_action() ) ) {
                    $url   = add_query_arg( array( 'redirect_to' => wp_get_referer() ), $url );
                    $class = 'delete-activity-single';
                }

                $link = '<a href="' . wp_nonce_url( $url, 'bp_activity_delete_link' ) . '" class="action action-delete' . $class . '" rel="nofollow"><i class="icon-trash"></i> <span>Delete</span></a>';
                return apply_filters( 'bp_get_delete_link_custom', $link );
            }



add_filter('bp_groups_default_extension','bpdev_custom_group_default_tab');
function bpdev_custom_group_default_tab($default_tab){
	$group=groups_get_current_group();//get the current group
	 //if current group is not set, return the default tab
	 if(empty($group))
	 return $default_tab;
	// you may create a switch/if else to default to some other tab based on group slug or group id whichever you prefer
	 //here I am testing agains slug
	$default_tab = 'discussions';
	return $default_tab;
}


/*************  Custom nav for  Group Creation   *********************/
function my_bp_group_creation_tabs() {
	global $bp;

	if ( !is_array( $bp->groups->group_creation_steps ) )
		return false;

	if ( !bp_get_groups_current_create_step() )
		$bp->groups->current_create_step = array_shift( array_keys( $bp->groups->group_creation_steps ) );

	$counter = 1;

	foreach ( (array) $bp->groups->group_creation_steps as $slug => $step ) {
		$is_enabled = bp_are_previous_group_creation_steps_complete( $slug ); ?>

		<li<?php if ( bp_get_groups_current_create_step() == $slug ) : ?> class="active"<?php endif; ?>> <?php if ( $is_enabled ) : ?><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() ?>/create/step/<?php echo $slug ?>/"><?php else: ?><span><?php endif; ?><?php echo $counter ?>. <?php echo $step['name'] ?><?php if ( $is_enabled ) : ?></a><?php else: ?></span><?php endif ?> <span class="divider"><i class="icon-angle-right"></i></span></li><?php
		$counter++;
	}

	unset( $is_enabled );

	do_action( 'groups_creation_tabs' );
}





/*****

Small DEbugger

******/

function bp_dump() {
    global $bp;

    foreach ( (array)$bp as $key => $value ) {
        echo '<pre>';
        echo '<strong>' . $key . ': </strong><br />';
        print_r( $value );
        echo '</pre>';
    }
    die;
}






?>
