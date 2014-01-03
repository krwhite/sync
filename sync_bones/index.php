<!doctype html>

<!--[if lt IE 7]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->

<?php get_template_part('html_head'); ?>

<body <?php body_class(); ?>>
	<div id="container">
	
		<?php get_template_part('page_header'); ?>
		
		<div id="main">
			<div class="inner">
				<?php get_sidebar(); ?>			
				<div id="content">
					<?php if ( is_active_sidebar( 'content_widgets_top' ) ) : ?>
						<ul id="contentTopWidgets">
						<?php dynamic_sidebar( 'content_widgets_top' ); ?>
						</ul>
					<?php endif; ?>
		
					<div id="posts">
						<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
							<article id="post-<?php the_ID(); ?>" <?php post_class( 'clearfix' ); ?> role="article">
								<header class="article-header">
									<h1 class="h2"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
									<p class="byline vcard">
										<?php	printf( __( 'Posted <time class="updated" datetime="%1$s" pubdate>%2$s</time> by <span class="author">%3$s</span> <span class="amp">&</span> filed under %4$s.', 'bonestheme' ), get_the_time('Y-m-j'), get_the_time(get_option('date_format')), bones_get_the_author_posts_link(), get_the_category_list(', '));?>
									</p>
								</header>
								<section class="entry-content">
									<?php the_content(); ?>
								</section>
								<footer class="article-footer">
									<p class="tags"><?php the_tags( '<span class="tags-title">' . __( 'Tags:', 'bonestheme' ) . '</span> ', ', ', '' ); ?></p>
								</footer>
								<?php // comments_template(); // uncomment if you want to use them ?>
							</article>
						<?php endwhile; ?>
			
						<?php if ( function_exists( 'bones_page_navi' ) ) { ?>
							<?php bones_page_navi(); ?>
						<?php } else { ?>
						<nav class="wp-prev-next">
								<ul class="clearfix">
									<li class="prev-link"><?php next_posts_link( __( '&laquo; Older Entries', 'bonestheme' )) ?></li>
									<li class="next-link"><?php previous_posts_link( __( 'Newer Entries &raquo;', 'bonestheme' )) ?></li>
								</ul>
							</nav>
						<?php } ?>
						<?php else : ?>
							<article id="post-not-found" class="hentry clearfix">
								<header class="article-header">
									<h1><?php _e( 'Oops, Post Not Found!', 'bonestheme' ); ?></h1>
								</header>
								<section class="entry-content">
									<p><?php _e( 'Uh Oh. Something is missing. Try double checking things.', 'bonestheme' ); ?></p>
								</section>
								<footer class="article-footer">
									<p><?php _e( 'This is the error message in the index.php template.', 'bonestheme' ); ?></p>
								</footer>
							</article>
						<?php endif; ?>
					</div><!-- #posts -->
				</div><!-- #content -->
			</div><!-- #main .inner -->
		</div><!-- #main -->
		<?php get_template_part('page_footer'); ?>
	</div><!-- #container -->
</body>
		