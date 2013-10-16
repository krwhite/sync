<?php
/*
Plugin Name: Sync Help Overview
Plugin URI: www.asynchrony.com
Description: Display latest posts and categories for help section
Author: James McKie
Version: 0.1
*/


class syncHelpOverview extends WP_Widget
{
  function syncHelpOverview()
  {
    $widget_ops = array('classname' => 'syncHelpOverview', 'description' => 'Display latest posts and categories for help section.' );
    $this->WP_Widget('syncHelpOverview', 'Sync Help Overview', $widget_ops);
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
	


	<div class="line page_header">
		<?php

								wp_nav_menu( array('menu' => 'Help', 'container_class' => 'tabs unit', 'menu_class' => 'nav nav-pills'));

							?>
		<div class="lastUnit section-actions"> 
			<a href="<?php echo site_url(); ?>/new-article" id="" class="pull-right action btn btn-warning btn-mini"><i class="icon-plus"></i> new article</a> 
		</div>
	</div>
	<div class="unit">
		<h1>Latest Articles</h1>
		<div class="list-wrapper">
			<ul class="latest">
				<?php
				$args = array( 'post_type' => 'help-article','showposts' => 5, 'order' => 'DESC', 'orderby' => 'post_date' );

				$lastposts = query_posts( $args );

				foreach($lastposts as $post) : setup_postdata($post);

					//foreach ($terms as $term){ $GLOBAL["termss"]= $term->name}

				?>
				<li> <a href="<?php the_permalink(); ?>">
					<?php the_title(); ?>
					</a> <br>
					<span> in <b>
					<?php

						$terms = wp_get_object_terms( $post->ID, 'help-category' );

						foreach( $terms as $term ){

							//$term_names[] = $term->name;

							echo $term->name;	// .' - '

						 }

					?>
					</b> <span>//</span> <i> <?php the_time('F j, Y'); ?> </i></span> 
				</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<h1>Categories</h1>
		<div class="list-wrapper">
			<?php

				$terms = get_terms('help-category', array('hide_empty' => 0));

				foreach ($terms as $term) {
			?>
				<div class='group-wrapper'>
					<div class='group'> 
						<a href="<?php echo site_url(); ?>/help-category/<?php echo $term->slug; ?>"><?php echo $term->slug; ?></a>
						<p><?php echo $term->description; ?></p>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>


<?php
 
    echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("syncHelpOverview");') );

?>
