<?php 

/**
 * BuddyPress - Members Directory
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

get_header( 'buddypress' ); ?>

	<?php do_action( 'bp_before_directory_members_page' ); ?>

	<div id="content">
	<div id="inner-content" class="wrap line clearfix">

		<header class="article-header">
			<div class="line page_header">
			    <h1 class="page-title" itemprop="headline">People <i><?php echo bp_get_total_member_count();?>  </i></h1>
				<div class="lastUnit section-actions">
					<a href="#" id="searchMembers" class="pull-right action tips" title="" data-original-title="search people"><i class="icon-search"></i></a>
				</div>
				<div class="hidden search">
					<?php bp_directory_members_search_form(); ?>
				</div>
			</div>
		</header> <!-- end article header -->

		<?php do_action( 'bp_before_directory_members' ); ?>

		<form action="" method="post" id="members-directory-form" class="dir-form">


			<?php do_action( 'bp_before_directory_members_content' ); ?>

			<div class="item-list-tabs hidden" role="navigation">
				<ul>
					<li class="selected" id="members-all"><a href="<?php echo trailingslashit( bp_get_root_domain() . '/' . bp_get_members_root_slug() ); ?>"><?php printf( __( 'All Members <span>%s</span>', 'buddypress' ), bp_get_total_member_count() ); ?></a></li>

					<?php if ( is_user_logged_in() && bp_is_active( 'friends' ) && bp_get_total_friend_count( bp_loggedin_user_id() ) ) : ?>

						<li id="members-personal"><a href="<?php echo bp_loggedin_user_domain() . bp_get_friends_slug() . '/my-friends/' ?>"><?php printf( __( 'My Friends <span>%s</span>', 'buddypress' ), bp_get_total_friend_count( bp_loggedin_user_id() ) ); ?></a></li>

					<?php endif; ?>

					<?php do_action( 'bp_members_directory_member_types' ); ?>

				</ul>
			</div><!-- .item-list-tabs -->

			<div class="item-list-tabs hidden" id="subnav" role="navigation">
				<ul class=" nav nav-pills">

					<?php do_action( 'bp_members_directory_member_sub_types' ); ?>

					<li id="members-order-select" class="last filter">

						<label for="members-order-by"><?php _e( 'Order By:', 'buddypress' ); ?></label>
						<select id="members-order-by">
							
							<?php if ( bp_is_active( 'xprofile' ) ) : ?>

								<option value="alphabetical" selected="selected"><?php _e( 'Alphabetical', 'buddypress' ); ?></option>

							<?php endif; ?>
							<option value="active"><?php _e( 'Last Active', 'buddypress' ); ?></option>
							<option value="newest"><?php _e( 'Newest Registered', 'buddypress' ); ?></option>

							

							<?php do_action( 'bp_members_directory_order_options' ); ?>

						</select>
					</li>
				</ul>
			</div>

			<div id="members-dir-list" class="members dir-list">

				<?php locate_template( array( 'members/members-loop.php' ), true ); ?>

			</div><!-- #members-dir-list -->

			<?php do_action( 'bp_directory_members_content' ); ?>

			<?php wp_nonce_field( 'directory_members', '_wpnonce-member-filter' ); ?>

			<?php do_action( 'bp_after_directory_members_content' ); ?>

		</form><!-- #members-directory-form -->

		<?php do_action( 'bp_after_directory_members' ); ?>


	</div><!-- inner-content -->
	</div><!-- #content -->

	<?php do_action( 'bp_after_directory_members_page' ); ?>
<?php get_footer( 'buddypress' ); ?>
