<?php

/**

 * The template for displaying Category pages.

 *

 * Used to display archive-type pages for posts in a category.

 *

 * Learn more: http://codex.wordpress.org/Template_Hierarchy

 *

 * @package WordPress

 * @subpackage Twenty_Twelve

 * @since Twenty Twelve 1.0

 */



get_header(); ?>



	<section id="primary" class="site-content news">

		<div id="content" role="main">

			<div class="wrap clear-fix" id="inner-content">

				<div id="main">

					<div class="line page_header">

						<h1 class="page-title unit" itemprop="headline">News</h1>

						<?php

							wp_nav_menu( array('menu' => 'News', 'container_class' => 'tabs unit', 'menu_class' => 'nav nav-pills'));

						?>

						<div class="lastUnit section-actions">

							<a href="<?php echo site_url(); ?>/new-news-article" id="" class="pull-right action btn btn-warning btn-mini"><i class="icon-plus"></i> new article</a>

						</div>

					</div>

					<header class="archive-header line">

						<h3 class="archive-title pull-left"><span><?php printf(  single_cat_title( '', false )); ?></span></h3>

					<?php if ( category_description() ) : // Show an optional category description ?>

						<div class="archive-meta pull-left"><?php echo category_description(); ?></div>

					<?php endif; ?>

					</header><!-- .archive-header -->

					<?php

					query_posts( $query_string . '&posts_per_page=-1' );

					?>

				<?php if ( have_posts() ) :

					/* Start the Loop */

					echo "<ul class=\"post-list\">";

					while ( have_posts() ) : the_post(); ?>

						<li class="line">

							<a href="<?php the_permalink(); ?>" class="line">

								<strong><?php the_title(); ?></strong>

							</a><br>

							<span class="line"><?php the_time('F j, Y'); ?></span>

							<div class="line tag-list"><i class="icon-tags"></i> <?php the_tags('', ''); ?></div>

						</li>

					<?php

					endwhile;

					echo "</ul>";

					?>



				<?php else : ?>

					<?php get_template_part( 'content', 'none' ); ?>

				<?php endif; ?>

				</div>

			</div>

		</div><!-- #content -->

	</section><!-- #primary -->



<?php get_footer(); ?>

