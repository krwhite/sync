<?php
/*
Plugin Name: Sync Newest Groups
Plugin URI: www.asynchrony.com
Description: Show latest groups on home
Version: 0.1
Requires at least: Wordpress 3.0 and BuddyPress 1.5
Tested up to: Wordpress 3.6.1 and BuddyPress 1.8.1
Author: James McKie
*/




/*************Make sure BuddyPress is loaded ********************************/
if ( !function_exists( 'bp_core_install' ) ) {
	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	if ( is_plugin_active( 'buddypress/bp-loader.php' ) )
		require_once ( WP_PLUGIN_DIR . '/buddypress/bp-loader.php' );
	else
		return;
}
/*******************************************************************/


/**
 * bp_list_newest_groups_register_widgets
 * register widget.
 */

function bp_list_newest_groups_register_widgets() {
	add_action('widgets_init', create_function('', 'return register_widget("Bp_List_Newest_Groups_Widget");') );
}
add_action( 'plugins_loaded','bp_list_newest_groups_register_widgets' );

class Bp_List_Newest_Groups_Widget extends WP_Widget {
	
	function bp_list_newest_groups_widget() {
		$widget_ops = array('classname' => 'widget_list_newest_groups', 'description' => __( "List Photos And Names Of The Newest Groups", "bp-list-newest-groups") );
		
  
  parent::WP_Widget( false, __('Sync Newest Groups','bp-list-newest-groups'), $widget_ops);   
	}

function widget($args, $instance) {
		global $bp;

		extract( $args );

		echo $before_widget;
		echo $before_title
		   .$instance['title']
                   . $after_title; ?>

<?php if ( bp_has_groups( 'user_id=0&type=newest&max='. $instance['max_num'] .'&populate_extras=0' ) ) : ?>
<ul id="groups-list" class="item-list">
	 <?php while ( bp_groups() ) : bp_the_group(); ?>
         <li>
         <div class="item-avatar">
         <a href="<?php bp_group_permalink() ?>"><?php bp_group_avatar('type=full&width=40&height=40') ?></a></div>
         <div class="item">
         <div class="item-title">
	 <a href="<?php bp_group_permalink() ?>"><?php bp_group_name() ?></a>
         <div class="clear"></div></div>
	<?php endwhile; ?>
         </div>
		</li>
	<?php else: ?>

	<div class="widget-error">
	<?php _e( 'All the groups are old, or there are none. ', 'bp-list-newest-groups' ) ?>
	</div>

	<?php endif; ?>
</ul>
<?php echo $after_widget; ?>

<?php
	}

function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
                $instance['max_num'] = strip_tags( $new_instance['max_num'] );

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'max_num' => 5 ) );
		$title = strip_tags( $instance['title'] );
                $max_num = strip_tags( $instance['max_num'] );
		?>
		

<p><label for="bp-list-newest-groups-widget-title"><?php _e( 'Title' , 'bp-list-newest-groups'); ?>
 <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" class="widefat" value="<?php echo esc_attr( $title ); ?>"style="width: 100%"/></label></p>

<p><label for="bp-list-newest-groups-widget-max-num"><?php _e( 'Max Number of Groups:','bp-list-newest-groups' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'max_num' ); ?>" name="<?php echo $this->get_field_name( 'max_num' ); ?>" type="text" value="<?php echo attribute_escape( $max_num ); ?>" style="width: 30%" /></label></p>


		
	<?php
	}
}
?>