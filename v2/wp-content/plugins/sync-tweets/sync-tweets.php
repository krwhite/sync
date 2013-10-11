<?php
/*
Plugin Name: Sync Tweets
Plugin URI: www.asynchrony.com
Description: Display Tweets related to @asynchrony
Author: Ben Self
Version: 0.1
*/


class syncTweets extends WP_Widget
{
  function syncTweets()
  {
    $widget_ops = array('classname' => 'syncTweets', 'description' => 'Display Tweets related to @asynchrony.' );
    $this->WP_Widget('syncTweets', 'Sync Tweets', $widget_ops);
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
	<div id="twitterbox">
		<a class="twitter-timeline" href="https://twitter.com/asynchrony" data-widget-id="345623555263836160" width="257" height="400" data-tweet-limit="4" data-chrome="nofooter" data-border-color="c4c4c4">
			Tweets by @asynchrony
		</a> 
      <script>
	  	!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
      </script> 
    </div>
    <?php
 
    echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("syncTweets");') );

?>