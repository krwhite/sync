<?php
/*
Template Name: Blank Page
*/
?>

<?php get_header(); ?>
			<div id="content">
				<div id="inner-content" class="wrap clearfix">
			
				    <div id="main" class="line" role="main">

					    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
							<?php the_content(); ?>					
						    <footer class="article-footer">
							    <p class="clearfix"><?php the_tags('<span class="tags">' . __('Tags:', 'bonestheme') . '</span> ', ', ', ''); ?></p>
							
						    </footer> <!-- end article footer -->
						    
					

					
					    <?php endwhile; ?>	
					
					    <?php else : ?>
					
        					<article id="post-not-found" class="hentry clearfix">
        					    <header class="article-header">
        						    <h1><?php _e("Oops, Post Not Found!", "bonestheme"); ?></h1>
        						</header>
        					    <section class="entry-content">
        						    <p><?php _e("Uh Oh. Something is missing. Try double checking things.", "bonestheme"); ?></p>
        						</section>
        						<footer class="article-footer">
        						    <p><?php _e("This is the error message in the page-custom.php template.", "bonestheme"); ?></p>
        						</footer>
        					</article>
					
					    <?php endif; ?>
			
				    </div> <!-- end #main -->
    

				    
				</div> <!-- end #inner-content -->
    
			</div> <!-- end #content -->

<?php get_footer(); ?>