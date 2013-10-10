<!doctype html>

<!--[if lt IE 7]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

		<title><?php wp_title(''); ?></title>

		<!-- Google Chrome Frame for IE -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

		<!-- mobile meta (hooray!) -->
		<meta name="HandheldFriendly" content="True">
		<meta name="MobileOptimized" content="320">
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

		<!-- icons & favicons (for more: http://www.jonathantneal.com/blog/understand-the-favicon/) -->
		<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/library/images/favicon-16x16.png">
		<!-- For Retina: -->
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo get_template_directory_uri(); ?>/library/images/apple-touch-icon-114x114.png">
		<!-- For iPad: -->
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo get_template_directory_uri(); ?>/library/images/apple-touch-icon-72x72.png">

		<!--[if IE]>
			<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico">
		<![endif]-->
		<!-- or, set /favicon.ico for IE10 win -->
		<meta name="msapplication-TileColor" content="#f01d4f">
		<meta name="msapplication-TileImage" content="<?php echo get_template_directory_uri(); ?>/library/images/win8-tile-icon.png">

  	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

		<!-- wordpress head functions -->
		<?php wp_head(); ?>
		<!-- end of wordpress head -->


		<!-- drop Google Analytics Here -->
		<!-- end analytics -->

	</head>

	<body <?php body_class(); ?> data-url="<?php echo site_url(); ?>">

		<div id="container">

			<header class="header site-header" role="banner">
				<div id="inner-header" class="wrap line">
					<div class="navigation unit size2of3">
						<hgroup class="unit">
							<a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
								<img src="<?php echo get_template_directory_uri() . '/library/images/logo-sync.png' ?>" />
							</a>
						</hgroup>
						<nav id="site-navigation" class="main-navigation lastUnit" role="navigation">
							<?php bones_main_nav(); ?>
						</nav><!-- #site-navigation -->
					</div>
					<div class="account">
						<div class="search-container">
							<?php get_search_form(); ?>

						</div>
						<div class="avatar-container"><!-- size1of6 -->
							<a class="pops" href="#" data-placement="bottom" data-content="<ul class='my-profile-dropdown'><li><a href='<?php echo bp_loggedin_user_domain()?>profile'>My Profile</a></li><li><a href='<?php echo wp_logout_url(); ?>'' title='Logout'>Logout</a></li></ul>"><?php bp_loggedin_user_avatar( "width=30" . "&height=30"); ?></a>

						</div>
						<!-- <div class="what-new-container lastUnit"><a href="#update-modal" class="tips" title="post an update" data-placement="bottom" role="button" data-toggle="modal"> -->
							<!-- <img src=" --> <?php // echo get_template_directory_uri(); ?> <!-- /library/images/syncUP.png"/></a> -->
						<!-- </div> -->
				</div> <!-- end #inner-header -->

			</header> <!-- end header -->
