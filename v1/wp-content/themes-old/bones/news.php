<?php
/*
Template Name: News Index
*/
?>

<?php get_header(); ?>

			<div id="content" class="news">


				<div id="inner-content" class="wrap clearfix">
					<div id="main" class="unit" role="main">
						<div class="line page_header">
							<h1 class="page-title unit" itemprop="headline">News</h1>
							<?php
								wp_nav_menu( array('menu' => 'News', 'container_class' => 'tabs unit', 'menu_class' => 'nav nav-pills'));
							?>
							<?php if( current_user_can( 'edit_posts' ) ) { ?>
							<div class="lastUnit section-actions">
								<a href="<?php echo site_url(); ?>/new-news-article" id="" class="pull-right action btn btn-warning btn-mini"><i class="icon-plus"></i> new article</a>
							</div>
							<?php } ?>
						</div>
						<div class="line">
							<!-- <h1>Latest Articles</h1> -->
							<div class="list-wrapper">
								<ul class="latest">
									<?php
										$args = array( 'post_type' => 'news-article', 'order' => 'DESC', 'orderby' => 'post_date' );
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
											$terms = wp_get_object_terms( $post->ID, 'news-category' );
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
							<h2>News Categories</h2>
							<div class="list-wrapper">
								<?php
									$terms = get_terms('news-category', array('hide_empty' => 0));
									foreach ($terms as $term) {
								?>
									<div class='group-wrapper'><div class='group'>
										<a href="<?php echo site_url(); ?>/news-category/<?php echo $term->slug; ?>"><strong><?php echo $term->name; ?></strong></a>
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
