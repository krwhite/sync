<?php
/*
Plugin Name: Sync Menu: Retros
Plugin URI: www.asynchrony.com
Description: Display Subnav for Retrospective Pages
Author: James McKie
Version: 0.1
*/


class syncMenuRetros extends WP_Widget
{
  function syncMenuRetros()
  {
    $widget_ops = array('classname' => 'syncMenuRetros', 'description' => 'Display Subnav for Retrospective Pages.' );
    $this->WP_Widget('syncMenuRetros', 'Sync Menu: Retros', $widget_ops);
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
	 
	 
	<?php
		wp_nav_menu( array('menu' => 'Retrospectives', 'container_class' => 'tabs unit', 'menu_class' => 'nav nav-pills'));
	?>
	
	<?php if( current_user_can( 'edit_posts' ) ) { ?>
	<div class="lastUnit section-actions">
		<a href="<?php echo site_url(); ?>/new-retrospective-article" id="" class="pull-right action btn btn-warning btn-mini"><i class="icon-plus"></i> new article</a>
	</div>
	<?php } ?>
	 
	 
    <?php
 
    echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("syncMenuRetros");') );

?>