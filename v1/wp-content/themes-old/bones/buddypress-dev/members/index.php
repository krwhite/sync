<?php do_action( 'bp_before_directory_members_page' ); ?>

<div id="buddypress">
    <header class="article-header">
		<div class="line page_header">
		    <h1 class="page-title" itemprop="headline">People <i><?php echo bp_get_total_member_count();?>  </i></h1>
			<!-- <div class="lastUnit section-actions">
				<a href="#" id="searchMembers" class="pull-right action tips" title="" data-original-title="search groups"><i class="icon-search"></i></a>
			</div> -->
			<?php do_action ('bp_profile_search_form'); ?>
		</div>
    </header> <!-- end article header -->
	<?php do_action( 'bp_before_directory_members' ); ?>

	<?php do_action( 'bp_before_directory_members_content' ); ?>

	<div id="members-dir-search" class="dir-search hidden" role="search">
		<?php bp_directory_members_search_form(); ?>
	</div><!-- #members-dir-search -->

	<form action="" method="post" id="members-directory-form" class="dir-form">

		<div class="item-list-tabs hidden" role="navigation">
			<ul>
				<li class="selected" id="members-all"><a href="<?php bp_members_directory_permalink(); ?>"><?php printf( __( 'All Members <span>%s</span>', 'buddypress' ), bp_get_total_member_count() ); ?></a></li>

				<?php if ( is_user_logged_in() && bp_is_active( 'friends' ) && bp_get_total_friend_count( bp_loggedin_user_id() ) ) : ?>
					<li id="members-personal"><a href="<?php echo bp_loggedin_user_domain() . bp_get_friends_slug() . '/my-friends/'; ?>"><?php printf( __( 'My Friends <span>%s</span>', 'buddypress' ), bp_get_total_friend_count( bp_loggedin_user_id() ) ); ?></a></li>
				<?php endif; ?>

				<?php do_action( 'bp_members_directory_member_types' ); ?>

			</ul>
		</div><!-- .item-list-tabs -->

		<div class="item-list-tabs hidden" id="subnav" role="navigation">
				<?php do_action( 'bp_members_directory_member_sub_types' ); ?>

				<div id="members-order-select" class="item">
					<label for="members-order-by"><?php _e( 'Order By:', 'buddypress' ); ?></label>
					<select id="members-order-by">
						<option value="active"><?php _e( 'Last Active', 'buddypress' ); ?></option>
						<option value="newest"><?php _e( 'Newest Registered', 'buddypress' ); ?></option>

						<?php if ( bp_is_active( 'xprofile' ) ) : ?>
							<option value="alphabetical"><?php _e( 'Alphabetical', 'buddypress' ); ?></option>
						<?php endif; ?>

						<?php do_action( 'bp_members_directory_order_options' ); ?>
					</select>
				</div>
		</div>

		<div id="members-dir-list" class="members dir-list">
			<?php bp_get_template_part( 'members/members-loop' ); ?>
		</div><!-- #members-dir-list -->

		<?php do_action( 'bp_directory_members_content' ); ?>

		<?php wp_nonce_field( 'directory_members', '_wpnonce-member-filter' ); ?>

		<?php do_action( 'bp_after_directory_members_content' ); ?>

	</form><!-- #members-directory-form -->

	<?php do_action( 'bp_after_directory_members' ); ?>

</div><!-- #buddypress -->

<?php do_action( 'bp_after_directory_members_page' ); ?>
