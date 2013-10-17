<?php
/*
Plugin Name: Sync Menu: Company
Plugin URI: www.asynchrony.com
Description: Display Subnav for Company Pages
Author: James McKie
Version: 0.1
*/


class syncMenuCompany extends WP_Widget
{
  function syncMenuCompany()
  {
    $widget_ops = array('classname' => 'syncMenuCompany', 'description' => 'Display Subnav for Company Pages.' );
    $this->WP_Widget('syncMenuCompany', 'Sync Menu: Company', $widget_ops);
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
	 
	 
	 <?php wp_nav_menu( array('menu' => 'Company-Sub', 'container_class' => 'tabs subnav', 'menu_class' => 'nav nav-pills')); ?>
	 
	 
    <?php
 
    echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("syncMenuCompany");') );

?>