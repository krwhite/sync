<header class="header" role="banner">
	<div class="inner">
		<div class="siteInfo">
			<h1><a href="<?php echo home_url(); ?>" rel="nofollow"><?php bloginfo('name'); ?></a></h1>
		</div>
	
		<?php if ( is_active_sidebar( 'header_widgets' ) ) : ?>
			<ul id="headerWidgets">
			<?php dynamic_sidebar( 'header_widgets' ); ?>
			</ul>
		<?php endif; ?>
	
		<nav role="navigation">
			<?php bones_main_nav(); ?>
		</nav>
	</div>
</header>