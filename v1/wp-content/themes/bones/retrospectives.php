<?php

/*

Template Name: Retrospective Index

*/

?>



<?php get_header(); ?>



			<div id="content" class="news">





				<div id="inner-content" class="wrap clearfix">

					<div id="main" class="unit farts" role="main">

						<div class="line page_header">

							<h1 class="page-title unit" itemprop="headline">Retrospectives</h1>

							<?php

								wp_nav_menu( array('menu' => 'Retrospectives', 'container_class' => 'tabs unit', 'menu_class' => 'nav nav-pills'));

							?>

							<?php if( current_user_can( 'edit_posts' ) ) { ?>

							<div class="lastUnit section-actions">

								<a href="<?php echo site_url(); ?>/new-retrospective-article" id="" class="pull-right action btn btn-warning btn-mini"><i class="icon-plus"></i> new article</a>

							</div>

							<?php } ?>

						</div>

						<div class="line">

							<!-- <h1>Latest Articles</h1> -->

							<div class="list-wrapper">
								

								<ul class="latest">
									<li><p>A retrospective is an all-hands team meeting that happens on a regular basis, preferably weekly, where the team comes together to ask the question &ldquo;How are we doing?&rdquo; or in some cases &ldquo;What the heck is going wrong?&rdquo; Here, you might just find answers.</p>
										<p>Don't see your project? Let <a href="mailto:kelly.white@asynchrony.com">Kelly White</a> know what project needs to be added to the list.</p>
									</li>
									<?php

										$args = array( 'post_type' => 'retrospective', 'order' => 'DESC', 'orderby' => 'post_date' );

										$lastposts = query_posts( $args );

										foreach($lastposts as $post) : setup_postdata($post);

										//foreach ($terms as $term){ $GLOBAL["termss"]= $term->name}

									?>

									<li>

										<a href="<?php the_permalink(); ?>">

											<?php the_title(); ?>

										</a>

										by <b><?php the_author_link(); ?></b> on <i><?php the_time('F j, Y'); ?></i> in

										<?php

											$terms = wp_get_object_terms( $post->ID, 'retrospective-category' );

											foreach( $terms as $term ){

												//$term_names[] = $term->name;

												echo $term->name;

											 }

										?>

									</li>

									<?php endforeach; ?>

								</ul>

							</div>

						</div>

					</div> <!-- end #main -->

					<div class="lastUnit">

						<div class="sidebar">

						<!-- <div class="news-callout">

							<h2>News Sidebar</h2>

							<div>

								<p>content can go here</p>

							</div>

						</div> -->

							<h2>Projects</h2>

							<div class="list-wrapper">

								<?php

									$terms = get_terms('retrospective-category', array('hide_empty' => 0));

									foreach ($terms as $term) {

								?>

									<div class='group-wrapper'><div class='group'>

										<a href="<?php echo site_url(); ?>/retrospective-category/<?php echo $term->slug; ?>"><strong><?php echo $term->name; ?></strong></a>

										<p><?php echo $term->description; ?></p>

								<?php

										echo "</ul>";

										echo "</div></div>";

									}

								?>

							</div>

						</div>

					</div>

				</div> <!-- end #inner-content -->

			</div> <!-- end #content -->

<?php get_footer(); ?>

