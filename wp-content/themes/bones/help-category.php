<?php

/*

Template Name: Help Categories List

*/

?>



<?php get_header(); ?>

			<div id="content" class="help">

				<div id="inner-content" class="wrap clearfix">

					<div id="main" class="unit" role="main">

						<div class="line page_header">

							<h1 class="page-title unit" itemprop="headline">Help</h1>

							<?php

								wp_nav_menu( array('menu' => 'Help', 'container_class' => 'tabs unit', 'menu_class' => 'nav nav-pills'));

							?>

							<div class="lastUnit section-actions">

								<a href="<?php echo site_url(); ?>/new-article" id="" class="pull-right action btn btn-warning btn-mini"><i class="icon-plus"></i> new article</a>

							</div>

						</div>

						<!-- <h1>Help Articles</h1> -->

						<div class="groups-wrapper">

							<?php

								$terms = get_terms('help-category', array('hide_empty' => false));

								foreach ($terms as $term) {

									$wpq = array('posts_per_page'=> 1000,'taxonomy'=>'help-category','term'=>$term->slug);

									$myquery = new WP_Query ($wpq);

									$article_count = $myquery->post_count;

							?>

								<div class='group-wrapper'>

									<div class='group'>

										<a href="<?php echo site_url(); ?>/help-category/<?php echo $term->slug; ?>"><?php echo $term->slug; ?></a> <span>(<?php echo $article_count; ?>)</span>

										<p><?php echo $term->description; ?></p>

								<?php

									  if ($article_count) {

										 echo "<ul>";

										 while ($myquery->have_posts()) : $myquery->the_post();

											echo "<li><a href=\"".get_permalink()."\">".$post->post_title."</a></li>";

										 endwhile;

										 echo '</ul>';

									  }

									  ?>

									</div>

								</div>

							<?php	}

							?>

						</div>

					</div> <!-- end #main -->

						<div class="lastUnit">

							<div class="sidebar">

								<?php // include('help-callout.php'); ?>

							</div>

						</div>

				</div> <!-- end #inner-content -->

			</div> <!-- end #content -->

<?php get_footer(); ?>

