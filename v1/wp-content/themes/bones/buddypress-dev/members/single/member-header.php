<?php
 
/**
 * BuddyPress - Users Header
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

?>

<?php do_action( 'bp_before_member_header' ); ?>
	<div class="line page_header">
	    <h1 class="page-title" itemprop="headline"><a href="<?php echo site_url(); ?>/members">People </a> <i class="icon-angle-right"></i> <span><?php bp_displayed_user_fullname(); ?></span></h1>
		<div class="lastUnit section-actions">
			<a href="#" id="searchMembers" class="pull-right action tips" title="" data-original-title="search groups"><i class="icon-search"></i></a>
		</div>
		<?php do_action ('bp_profile_search_form'); ?>
	</div>






