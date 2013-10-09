<?php

/*

Template Name: Help Index

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

										<li>

											<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>

											<br><span>

												in <b>

												<?php

													$terms = wp_get_object_terms( $post->ID, 'help-category' );

													foreach( $terms as $term ){

														//$term_names[] = $term->name;

														echo $term->name;	// .' - '

													 }

												?>

											</b> <span>//</span> <i><?php the_time('F j, Y'); ?></i></span>

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

												<?php

														 echo "</ul>";

														 echo "</div></div>";

												}

											?>

							</div>

						</div>

					</div> <!-- end #main -->

					<div class="lastUnit">

						<div class="sidebar">

							<?php include('help-callout.php'); ?>

						</div>

					</div>

				</div> <!-- end #inner-content -->

			</div> <!-- end #content -->



<?php get_footer(); ?>

