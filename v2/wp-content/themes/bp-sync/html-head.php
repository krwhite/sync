<!-- html-head.php -->
<head profile="http://gmpg.org/xfn/11">
    <meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
    <?php if ( current_theme_supports( 'bp-default-responsive' ) ) : ?>
	    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php endif; ?>
    <title>
    	<?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?>
    </title>
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
    <?php bp_head(); ?>
    <?php wp_head(); ?>
</head>
<!-- End html-head.php -->