<?php
/*
Template Name: Retrospective Categories List
*/
?>

<?php get_header(); ?>

			<div id="content" class="news">


				<div id="inner-content" class="wrap clearfix">
					<div id="main" class="unit" role="main">
						<div class="line page_header">
							<h1 class="page-title unit" itemprop="headline">Retrospectives</h1>
							<?php
								wp_nav_menu( array('menu' => 'Retrospectives', 'container_class' => 'tabs unit', 'menu_class' => 'nav nav-pills'));
							?>
							<div class="lastUnit section-actions">
								<a href="<?php echo site_url(); ?>/new-retrospective-article" id="" class="pull-right action btn btn-warning btn-mini"><i class="icon-plus"></i> new article</a>
							</div>
						</div>
						<h1>Retro Articles</h1>
						<div class="groups-wrapper">
							<?php
								$terms = get_terms('retrospective-category', array('hide_empty' => false));
								foreach ($terms as $term) {
									$wpq = array('posts_per_page'=> 4,'taxonomy'=>'news-category','term'=>$term->slug);
									$myquery = new WP_Query ($wpq);
									$article_count = $myquery->post_count;
							?>
								<div class='group-wrapper'><div class='group'>
									<a href="<?php echo site_url(); ?>/retrospective-category/<?php echo $term->slug; ?>"><strong><?php echo $term->slug; ?></strong><span class="pull-right"><?php echo $article_count; ?></span>
										<p><?php echo $term->description; ?></p>
									</a>
							<?php
								  if ($article_count) {
									 echo "<ul>";
									 while ($myquery->have_posts()) : $myquery->the_post();
										echo "<li><a href=\"".get_permalink()."\">".$post->post_title."</a></li>";
									 endwhile;
									 echo '</ul> <a href="'.site_url().'/retrospective-category/'.$term->slug.'">Read More</a>';
								  }
								  ?>
									</div></div>
							<?php	}
							?>
						</div>
					</div> <!-- end #main -->
				</div> <!-- end #inner-content -->

			</div> <!-- end #content -->

<?php get_footer(); ?>
