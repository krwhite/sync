			<?php /* this search loop shows your blog posts in the unified search
			you may modify it as you want, It is a copy from my theme

			*/


			?>
			<?php do_action( 'bp_before_blog_search' ) ?>
			<?php global $wp_query;
				$wp_query->is_search=true;
				$search_term=$_REQUEST['search-terms'];
				if(empty($search_term))
					$search_term=$_REQUEST['s'];
				$wp_query->query("s=".$search_term);?>
			<?php if ( have_posts() ) : ?>
            <?php while(have_posts()):the_post(); global $post;?>
			<?php do_action( 'bp_before_blog_post' ) ?>
                <div class="post is-searchable"> <!-- Post goes here... -->
                	<a href="<?php the_permalink();?>">
                		<?php
						   $post_type = get_post_type($post);
						   switch ($post_type) {
						       case "page":
						           echo '<i class="icon-file tips" title="page"></i>';
						           break;
						       case "post":
						         echo '<i class="icon-file tips" title="post"></i>';
						           break;
						        case "help-article":
						           echo '<i class="icon-book tips" title="help article"></i>';
						           break;
						   }
?>
                    	<strong class="post-title"><?php the_title();?></strong> <span><?php the_time('F j, Y') ?></span>
                    </a>
                    <?php the_excerpt();?>

                </div><!-- Post ends here... -->
				<?php do_action( 'bp_after_blog_post' ) ?>
                <?php endwhile;?>
				<?php if(!bpmag_is_advance_search()):?>
				<div class="navigation">
					<?php if(function_exists("wp_pagenavi"))wp_pagenavi();else{ ?>
					<div class="alignleft"><?php next_posts_link( __( '&larr; Previous Entries', 'bpmag' ) ) ?></div>
					<div class="alignright"><?php previous_posts_link( __( 'Next Entries &rarr;', 'bpmag' ) ) ?></div>
				<?php }?>
				</div>
				<?php endif;?>
				<?php else : ?>
				<div class="post">
					<div class="post-content 404">
					<?php echo sprintf(__("No results found for: '%s'","bpmag"),$search_term);?>

				<?php locate_template( array( 'searchform.php' ), true ) ?>
				</div>
				</div>


			<?php endif; ?>
                 <?php do_action( 'bp_after_blog_search' ) ?>
