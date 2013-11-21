<?php
/*
Plugin Name: Sync Retro Project Menu
Plugin URI: www.asynchrony.com
Description: Display menu of retrospective projects
Author: James McKie
Version: 0.1
*/


class retroProjectsMenu extends WP_Widget
{
  function retroProjectsMenu()
  {
    $widget_ops = array('classname' => 'retroProjectsMenu', 'description' => 'Display menu of retrospective projects.' );
    $this->WP_Widget('retroProjectsMenu', 'Sync Retro Project Menu', $widget_ops);
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
	

	<h2>Project Filter</h2>
	<div class="form">
		<select id="retroProjectFilter">
			<option value="">Select a project</option>
			<?php
				$terms = get_terms('retrospective-category', array('hide_empty' => 0));
				foreach ($terms as $term) {
			?>
				<option value="<?php echo site_url(); ?>/retrospective-category/<?php echo $term->slug; ?>" title="<?php echo $term->description; ?>"><?php echo $term->slug; ?></option>
			<?php } ?>
		</select>
		<script>
			document.getElementById("retroProjectFilter").onchange = function() {
				if (this.selectedIndex!==0) {
					window.location.href = this.value;
				}        
			};
		</script>
	</div>




<?php
 
    echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("retroProjectsMenu");') );

?>
