<?php

?>

<?php get_header(); ?>
			<div id="content" class="news-article">
				<div id="inner-content" class="wrap clearfix line">
				<div id="main" class="unit" role="main">
					<div class="line page_header">
						<h1 class="page-title unit" itemprop="headline">News</h1>
						<?php
							wp_nav_menu( array('menu' => 'News', 'container_class' => 'tabs unit', 'menu_class' => 'nav nav-pills'));
						?>
						<div class="lastUnit section-actions">
							<a href="<?php echo site_url(); ?>/new-news-article" id="" class="pull-right action btn btn-warning btn-mini"><i class="icon-plus"></i> new article</a>
						</div>
					</div>
					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
						<div class="line article">
							<header class="article-header">
								<h1 class="page-title" itemprop="headline"><?php the_title(); ?></h1>
								<i class="date"><?php the_date(''); ?></i>
								<?php
									if( current_user_can('editor') || $getid = $posts[0]->post_author):
									    // true if user is an editor
									 ?>
									 <a href="<?php echo site_url(); ?>/edit-article/?pid=<?php the_ID(); ?>" class="btn btn-mini pull-right">edit</a>
									 <?php endif;
									?>
							</header> <!-- end article header -->
							<section class="entry-content clearfix" itemprop="articleBody">
								<?php the_content(); ?>
							</section> <!-- end article section -->
							<div class="comments-container custom-display">
								<?php comments_template(); ?>
							</div>
						</div>
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
				<div class="lastUnit">
					<div class="sidebar">
						<h3>Details</h3>
						<hr>
						<div class="category">
							<i class="icon-reorder"></i>
							<?php the_terms($post->ID, 'news-category',  '', ', '); ?>
						</div>
						<hr>
						<div class="section">
							<i class="icon-user"></i>
							<?php $user_id=$post->post_author; ?>
							<?php echo bp_core_get_userlink( $user_id); ?>
						</div>
						<hr>
						<div class="section tag-list">
							<i class="icon-tags"></i>
							<?php the_tags('', '', ''); ?>
						</div>
						<?php
							$images = get_post_meta( $post->ID, 'attach_file' );
							if ( $images ) {
								echo '<hr><div class="section attachment-list">';
								foreach ( $images as $attachment_id ) {
									$thumb = wp_get_attachment_image( $attachment_id, 'thumbnail' );
									$full_size = wp_get_attachment_url( $attachment_id );
									printf( '<a href="%s" class="btn"><i class="icon-paper-clip"></i> <span>%s</span></a>', $full_size, basename($full_size));
								}
								?></div><?php
							}
						?>
						</div>
					</div>
				</div> <!-- end #inner-content -->
			</div> <!-- end #content -->
<?php get_footer(); ?>
