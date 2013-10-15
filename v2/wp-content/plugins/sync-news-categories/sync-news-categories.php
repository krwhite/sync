<?php
/*
Plugin Name: Sync News Categories
Plugin URI: www.asynchrony.com
Description: Display news categories
Author: James McKie
Version: 0.1
*/


class syncNewsCategories extends WP_Widget
{
  function syncNewsCategories()
  {
    $widget_ops = array('classname' => 'syncNewsCategories', 'description' => 'Display news categories.' );
    $this->WP_Widget('syncNewsCategories', 'Sync News Categories', $widget_ops);
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
	
	<div class="lastUnit">

		<div class="sidebar">

			<h2>News Categories</h2>

			<div class="list-wrapper">

				<?php

					$terms = get_terms('news-category', array('hide_empty' => 0));

					foreach ($terms as $term) {

				?>

					<div class='group-wrapper'><div class='group'>

						<a href="<?php echo site_url(); ?>/news-category/<?php echo $term->slug; ?>"><strong><?php echo $term->name; ?></strong></a>

						<p><?php echo $term->description; ?></p>

				<?php

						echo "</ul>";

						echo "</div></div>";

					}

				?>

			</div>

		</div>

	</div>
	
    <?php
 
    echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("syncNewsCategories");') );

?>