<?php
/*
Plugin Name: Sync Unified Search
Plugin URI: www.asynchrony.com
Description: Display a search box that will work with the Global Unified Search plugin
Author: Ben Self
Version: 0.1
*/


class syncUnifiedSearch extends WP_Widget
{
  function syncUnifiedSearch()
  {
    $widget_ops = array('classname' => 'syncUnifiedSearch', 'description' => 'Display a search box that will work with the Global Unified Search plugin.' );
    $this->WP_Widget('syncUnifiedSearch', 'Sync Unified Search', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    $title = $instance['title'];
?>

<p>
  <label for="<?php echo $this->get_field_id('title'); ?>">Title:
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
  </label>
</p>
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
    <form action="<?php echo bp_search_form_action(); ?>" method="post" id="search-form">
      <label for="search-terms" class="accessibly-hidden">
        <?php _e( 'Search for:', 'buddypress' ); ?>
      </label>
      <input type="text" id="search-terms" name="search-terms" placeholder="Search sync" value="<?php echo isset( $_REQUEST['s'] ) ? esc_attr( $_REQUEST['s'] ) : ''; ?>" />
      <?php echo bp_search_form_type_select(); ?>
      <button type="submit" name="search-submit" id="search-submit" value="<?php _e( 'Search', 'buddypress' ); ?>" ></button>
      <?php wp_nonce_field( 'bp_search_form' ); ?>
    </form>
<?php
 
    echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("syncUnifiedSearch");') );

?>
