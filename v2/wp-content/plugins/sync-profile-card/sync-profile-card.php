<?php
/*
Plugin Name: Sync Profile Card
Plugin URI: www.asynchrony.com
Description: Display User ID, Notifications, & Groups
Author: Ben Self
Version: 0.1
*/


class syncProfileCard extends WP_Widget
{
  function syncProfileCard()
  {
    $widget_ops = array('classname' => 'syncProfileCard', 'description' => 'Sync Profile ID, Notifications, and Groups' );
    $this->WP_Widget('syncProfileCard', 'Sync Profile Card', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    $title = $instance['title'];
?>
  <p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
 
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
 
    if (!empty($title))
      echo $before_title . $title . $after_title;;
 
    ?>
	<div class="profile-card"> 
		<a href="<?php echo bp_loggedin_user_domain() . 'profile'; ?>">
			<?php bp_loggedin_user_avatar( 'width=60' . '&height=60'); ?>
			<div class="lastUnit"> 
				<strong><?php bp_loggedin_user_fullname(); ?></strong> 
				<span><?php echo xprofile_get_field_data('Job Category', bp_loggedin_user_id() ); ?></span> 
			</div>
		</a> 
	</div>
	<ul class="menu">
		<li><a href="">Notifications</a></li>
		<li><a href="<?php echo bp_loggedin_user_domain() . 'groups'; ?>">My Groups 
				<span class="count"> <?php echo bp_get_total_group_count_for_user(bp_loggedin_user_id()); ?> </span>
			</a>
		</li>
	</ul>
    <?php
 
    echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("syncProfileCard");') );

?>