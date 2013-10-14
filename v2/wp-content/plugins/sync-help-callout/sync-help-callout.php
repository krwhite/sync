<?php
/*
Plugin Name: Sync Help Callout
Plugin URI: www.asynchrony.com
Description: Display help section action links
Author: James McKie
Version: 0.1
*/


class syncHelpCallout extends WP_Widget
{
  function syncHelpCallout()
  {
    $widget_ops = array('classname' => 'syncHelpCallout', 'description' => 'Display help section action links.' );
    $this->WP_Widget('syncHelpCallout', 'Sync Help Callout', $widget_ops);
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
	
	<div class="help-callout">
		<h2>Need Assistance?</h2>
		<div class="alert">
			<p>There are 2 ways of getting technical support:</p>
			<div class="btn-group"> 
				<a href="<?php echo site_url(); ?>/ticket-support" class="btn"><i class="icon-magic"></i> support ticket </a> 
				<a href="<?php echo site_url(); ?>/email-support" class="btn "><i class="icon-envelope-alt"></i> email</a> 
			</div>
			<a href="#" class="showMore">Is this an Emergency?</a>
			<div class="showMoreData hidden">
				<p>If you need immediate technical assistance please call:</p>
				<p>Ext: 699  or <a href="tel://(314) 735-7699">(314) 735-7699</a></p>
			</div>
		</div>
		<h2>We Want Articles</h2>
		<p>If you know something or find you keep giving the same answers to Asynchronites, write an article!</p>
		<a href="<?php echo site_url(); ?>/new-article" class="btn btn-warning btn-block"><i class="icon-plus"></i> Write an Article</a> 
	</div>
	
    <?php
 
    echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("syncHelpCallout");') );

?>