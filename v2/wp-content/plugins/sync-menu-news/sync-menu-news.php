<?php
/*
Plugin Name: Sync Menu: Add News Button
Plugin URI: www.asynchrony.com
Description: Display Add News Article Button for Admins
Author: James McKie
Version: 0.1
*/


class syncMenuNews extends WP_Widget
{
  function syncMenuNews()
  {
    $widget_ops = array('classname' => 'sync_menu_news', 'description' => 'Display Add News button for admins.' );
    $this->WP_Widget('syncMenuNews', 'Sync Menu: Add News Button', $widget_ops);
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
	 
	 

	<?php if( current_user_can( 'edit_posts' ) ) { ?>
	<div class="admin-edit">
		<?php if( is_single() ) { ?>
			<div class="generic-button"><?php edit_post_link( __( 'Edit', 'buddypress' ) ); ?></div>
		<?php } 
			else { ?>
			<a href="<?php echo site_url(); ?>/new-news-article" id="" class="" title="Add a news article"><i class="icon-plus"></i> new article</a>
		<?php } ?>

	</div>
	<?php } ?>
	 
	 
    <?php
 
    echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("syncMenuNews");') );

?>