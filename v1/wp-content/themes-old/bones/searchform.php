<?php
/**
 * The template for displaying search forms in Twenty Eleven
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
?>
	<form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<input type="text" class="field search-query" name="s" id="s" placeholder="Search intranet" />
		<button type="submit" class="submit " name="submit" id="searchsubmit"><i class="icon-search"></i></button>
	</form>
	<div id="custom-search" class="search-form hide">
		<input type="text" class="field search-query" name="s" id="search-input" placeholder="Search intranet" />
		<a class="submit" id="search-submit"><i class="icon-search"></i></a>
	</div>
