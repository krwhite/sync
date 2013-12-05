<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<?php get_template_part( 'html-head' ); ?>

<body <?php body_class(); ?> id="bp-default">
<div id="pageHeader">
  <?php get_header(); ?>
</div>
<div id="main">
  <?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
	  <?php get_sidebar('sidebar-1'); ?>
  <?php endif; ?>
  <div id="pageContent">
		<?php if ( is_active_sidebar( 'content_widgets_top' ) ) : ?>
			<ul id="contentWidgets">
				<?php dynamic_sidebar( 'content_widgets_top' ); ?>
			</ul>
		<?php endif; ?>

		<?php do_action( 'bp_before_archive' ); ?>

		<div class="page" id="blog-archives" role="main">
			<div class="archive-header">
				<h1 class="<?php if (category_description( $category ) == '') : ?>no-description<?php endif; ?>">
				<?php if( is_tag() ) { ?> Articles Tagged with <i class="icon-tags"></i><?php } ?>
					<?php printf( __( '%1$s', 'buddypress' ), wp_title( false, false ) ); ?>
				</h1>
				<div class="archive-description"><?php echo category_description( $category_id ); ?></div>	
				<div class="sort-menu" >
					<select id="changeSortOrder">
						<option value="">Sort by</option>
							<option value="<?php echo ($current_url) ?>?&orderby=date&order=ASC" title="Sort by creation date - oldest first" 
								<?php if($_GET['orderby'] == 'date' && $_GET['order'] == 'ASC') {
									echo "selected=selected"; }	?>>Oldest First</option>
							<option value="<?php echo ($current_url) ?>?&orderby=date&order=DESC" title="Sort by creation date - newest first"
								<?php if(!$_GET['orderby'] == 'date' && $_GET['order'] == 'DESC') {
									echo ""; } else { echo "selected=selected"; }	?>>Newest First</option>
							<option value="<?php echo ($current_url) ?>?&orderby=title&order=ASC" title="Sort by title alphabetical"
								<?php if($_GET['orderby'] == 'title' && $_GET['order'] == 'ASC') {
									echo "selected=selected"; }	?>>Title A - Z</option>
							<option value="<?php echo ($current_url) ?>?&orderby=title&order=DESC" title="Sort by title reverse alphabetical"
								<?php if($_GET['orderby'] == 'title' && $_GET['order'] == 'DESC') {
									echo "selected=selected"; }	?>>Title Z - A</option>
					</select>
					<script>
						document.getElementById("changeSortOrder").onchange = function() {
							if (this.selectedIndex!==0) {
								window.location.href = this.value;
							}        
						};
					</script>
				</div><!-- .sort-menu -->
			</div><!-- .archive-header -->
				
			<div class="post-list">
				<?php if ( have_posts() ) : ?>
	
					<?php while (have_posts()) : the_post(); ?>
	
						<?php do_action( 'bp_before_blog_post' ); ?>
	
						<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	
	
							<div class="post-content row">
								<div class="column size3of5">
									<a class="article-link" href="<?php the_permalink(); ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddypress' ); ?> <?php the_title_attribute(); ?>"><strong class="posttitle"><?php the_title(); ?></strong>
										<span class="date"><?php printf( __( '%1$s', 'buddypress' ), get_the_date(), get_the_category_list( ', ' ) ); ?></span>
									</a>
								</div>
								<div class="column size2of5">
									<div class="tag-list">
										<i class="icon-tags"></i> <?php the_tags( '<span class="tags">' . __( '', 'buddypress' ), ' ', '</span>' ); ?> 
									</div>
								</div>
							</div>
	
						</div>
	
						<?php do_action( 'bp_after_blog_post' ); ?>
	
					<?php endwhile; ?>
	
					<?php bp_dtheme_content_nav( 'nav-below' ); ?>
	
				<?php else : ?>
					<div class="item-body">
						<p><?php _e( 'Hmmm, looks like we don&rsquo;t have anything for this category yet.', 'buddypress' ); ?></p>
					</div>
				<?php endif; ?>
			</div><!-- .post-list -->
		</div><!-- .page -->

		<?php do_action( 'bp_after_archive' ); ?>

  </div><!-- End #pageContent -->
</div><!-- End #main -->
<div id="footer">
  <?php get_footer(); ?>
</div>
</body>
<!-- End page.php (after </html>) -->
</html>
