<?php
/*
Plugin Name: Sync Help Articles
Plugin URI: www.asynchrony.com
Description: Display articles for help section with sort options
Author: James McKie
Version: 0.1
*/


class syncHelpArticles extends WP_Widget
{
  function syncHelpArticles()
  {
    $widget_ops = array('classname' => 'syncHelpArticles', 'description' => 'Display articles for help section with sort options.' );
    $this->WP_Widget('syncHelpArticles', 'Sync Help Articles', $widget_ops);
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
	

	<div class="sync-help-articles">

			<?php

				$terms = get_terms('help-category', array('hide_empty' => false));

				foreach ($terms as $term) {

					$wpq = array('posts_per_page'=> 1000,'taxonomy'=>'help-category','term'=>$term->slug);

					$myquery = new WP_Query ($wpq);

					$article_count = $myquery->post_count;

			?>


				<div class='articles-list'>

					<h1><a href="<?php echo site_url(); ?>/help-category/<?php echo $term->slug; ?>"><?php echo $term->name; ?></a> <span><?php echo $article_count; ?></span></h1>

					<p><?php echo $term->description; ?></p>

					<?php if ($article_count) { ?>

						 <ul class="query-content">

							 <?php while ($myquery->have_posts()) : $myquery->the_post(); ?>

								<li class="article query-row"><a href="<?php echo get_permalink() ?>"><?php echo get_the_title($ID) ?></a>
									<span class="date"><?php the_modified_date(); ?></span>
								</li>

							 <?php endwhile; ?>

						 </ul>

					<?php } ?>

				</div>


			<?php } ?>

		</div>



<?php
 
    echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("syncHelpArticles");') );

?>
