<?php
/*
Plugin Name: Sync Help Article Sidebar
Plugin URI: www.asynchrony.com
Description: Display help section article sidebar information
Author: James McKie
Version: 0.1
*/


class syncHelpArticleSidebar extends WP_Widget
{
  function syncHelpArticleSidebar()
  {
    $widget_ops = array('classname' => 'syncHelpArticleSidebar', 'description' => 'Display help section article sidebar information.' );
    $this->WP_Widget('syncHelpArticleSidebar', 'Sync Help Article Sidebar', $widget_ops);
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
	
			<h3>Details</h3>
	
			<hr>
	
			<div class="section category">
	
				<i class="icon-reorder"></i>
	
				<?php the_terms($post->ID, 'help-category',  '', ', '); ?>
	
			</div>
	
			<hr>
	
			<div class="section author">
	
				<i class="icon-user"></i>
	
				<?php $user_id=$post->post_author; ?>
	
				<?php echo bp_core_get_userlink( $user_id); ?>
	
			</div>
	
			<hr>
	
			<div class="section tag-list">
	
				<i class="icon-tags"></i>
	
				<?php the_tags('', '', ''); ?>
	
			</div>
	
			<?php
	
				$images = get_post_meta( $post->ID, 'attach_file' );
	
				if ( $images ) {
	
					echo '<hr><div class="section attachment-list">';
	
					foreach ( $images as $attachment_id ) {
	
						$thumb = wp_get_attachment_image( $attachment_id, 'thumbnail' );
	
						$full_size = wp_get_attachment_url( $attachment_id );
	
						printf( '<a href="%s" class="btn"><i class="icon-paper-clip"></i> <span>%s</span></a>', $full_size, basename($full_size));
	
					}
	
					?></div><?php
	
				}
	
			?>
	
			</div>
	
		</div>
	
    <?php
 
    echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("syncHelpArticleSidebar");') );

?>