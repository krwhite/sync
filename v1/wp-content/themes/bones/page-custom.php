<?php
/*
Template Name: No Sidebar
*/
?>
<?php get_header(); ?>
			<div id="content" class="template with_sidebar company">
				<div id="inner-content" class="wrap clearfix line">
			    <div id="main" class="" role="main">
					    <header class="article-header">
						<div class="line page_header">
						    <h1 class="page-title" itemprop="headline">Company</h1>
						    <?php wp_nav_menu( array('menu' => 'Company-Sub', 'container_class' => 'tabs unit', 'menu_class' => 'nav nav-pills'));
						    ?>
						</div>
					    </header> <!-- end article header -->
				    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

				    <article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

					    <section class="entry-content clearfix" itemprop="articleBody">
						    <?php the_content(); ?>
						</section> <!-- end article section -->

					    <footer class="article-footer">
              <?php the_tags('<span class="tags">' . __('Tags:', 'bonestheme') . '</span> ', ', ', ''); ?>

					    </footer> <!-- end article footer -->

					    <?php comments_template(); ?>

				    </article> <!-- end article -->

				    <?php endwhile; else : ?>

					    <article id="post-not-found" class="hentry clearfix">
					    	<header class="article-header">
					    		<h1><?php _e("Oops, Post Not Found!", "bonestheme"); ?></h1>
					    	</header>
					    	<section class="entry-content">
					    		<p><?php _e("Uh Oh. Something is missing. Try double checking things.", "bonestheme"); ?></p>
					    	</section>
					    	<footer class="article-footer">
					    	    <p><?php _e("This is the error message in the page.php template.", "bonestheme"); ?></p>
					    	</footer>
					    </article>

				    <?php endif; ?>

				</div> <!-- end #main -->



				</div> <!-- end #inner-content -->

			</div> <!-- end #content -->


<?php get_footer(); ?>

