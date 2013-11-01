<?php
/*
Plugin Name: Sync Help Categories
Plugin URI: www.asynchrony.com
Description: Display latest posts and categories for help section
Author: James McKie
Version: 0.1
*/


class syncHelpCategories extends WP_Widget
{
  function syncHelpCategories()
  {
    $widget_ops = array('classname' => 'syncHelpCategories', 'description' => 'Display categories for help section.' );
    $this->WP_Widget('syncHelpCategories', 'Sync Help Categories', $widget_ops);
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
	

	<h1>Categories</h1>
	<div class="group-wrapper">
		<?php
			$terms = get_terms('help-category', array('hide_empty' => 0));
			foreach ($terms as $term) {
		?>
			<div class='group'> 
				<a href="<?php echo site_url(); ?>/help-category/<?php echo $term->slug; ?>"><?php echo $term->slug; ?></a>
				<p><?php echo $term->description; ?></p>
			</div>
		<?php } ?>
	</div>


<?php
 
    echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("syncHelpCategories");') );

?>
