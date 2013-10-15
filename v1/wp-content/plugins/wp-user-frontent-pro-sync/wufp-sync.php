<?php

/*

Plugin Name: Sync User Frontend Pro

Plugin URI:

Description: Custom Adjustments for WUFP Forms

Version: 1.0

Requires at least: 3.3

Tested up to: 3.3.1

License: GPL3

Author: Asynchrony

Author URI: http://www.asynchrony.com

*/

function check_for_images() {

    //require( dirname( __FILE__ ) . '/buddypress-discussions.php' );

	//code if using seperate files require( dirname( __FILE__ ) . '/buddypress-group-meta.php' );

	
 return apply_filters( 'bp_get_activity_feed_item_date', $activities_template->activity->date_recorded );
}


?>

