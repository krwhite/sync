<?php
/*
Plugin Name: Discussions
Plugin URI:
Description: Show Group Discussions on the home page
Version: 1.0
Requires at least: 3.3
Tested up to: 3.3.1
License: GPL3
Author: Adam Lorentzen
Author URI: http://YourCoolWebsite.com
*/
function bp_group_discussions() {
    //require( dirname( __FILE__ ) . '/buddypress-discussions.php' );
	//code if using seperate files require( dirname( __FILE__ ) . '/buddypress-group-meta.php' );
	if ( class_exists( 'BP_Group_Extension' ) ) : // Recommended, to prevent problems during upgrade or when Groups are disabled
    class My_Group_Extension extends BP_Group_Extension {
        function __construct() {
            $this->name = 'Discussions';
            $this->slug = 'discussions';
            $this->nav_item_position = 1;
            $this->enable_create_step = false;
        }



        /**
         * Use this function to display the actual content of your group extension when the nav item is selected
         */
        function display() {
            ?>

                <?php do_action( 'bp_before_activity_loop' ); ?>
                        <div class="update update-post">
                            <?php do_action( 'bp_before_group_activity_post_form' ); ?>
                            <?php if ( is_user_logged_in() && bp_group_is_member() ) : ?>
                                <?php locate_template( array( 'activity/post-form.php'), true ); ?>
                            <?php endif; ?>
                            <?php do_action( 'bp_after_group_activity_post_form' ); ?>
                        </div>
                <?php if ( bp_has_activities( 'action=activity_update' ) ) : ?>

                    <?php /* Show pagination if JS is not enabled, since the "Load More" link will do nothing */ ?>
                    <noscript>
                        <div class="pagination">
                            <div class="pag-count"><?php bp_activity_pagination_count(); ?></div>
                            <div class="pagination-links"><?php bp_activity_pagination_links(); ?></div>
                        </div>
                    </noscript>

                    <?php if ( empty( $_POST['page'] ) ) : ?>

                        <div id="activity-stream" class="activity-list item-list custom-display">
                    <?php endif; ?>

                    <?php while ( bp_activities() ) : bp_the_activity(); ?>

                        <div class="<?php bp_activity_css_class(); ?> activity" id="activity-<?php bp_activity_id(); ?>">
                            <div class="activity-avatar unit ">
                                <a href="<?php bp_activity_user_link(); ?>"><?php bp_activity_avatar(); ?></a>
                            </div>

                            <div class="activity-content hover">
                                <div class="activity-header line">
                                    <a href="<?php bp_activity_user_link(); ?>" class="unit">
                                        <strong><?php bp_activity_user_name(); ?></strong>
                                    </a>
                                </div>
                                <?php if ( bp_activity_has_content() ) : ?>
                                    <div class="activity-inner">
                                        <?php bp_activity_content_body(); ?>
                                    </div>
                                <?php endif; ?>

                                <?php do_action( 'bp_activity_entry_content' ); ?>
                                <div class="activity-meta">
                                    <?php if ( bp_get_activity_type() == 'activity_comment' ) : ?>
                                        <a href="<?php bp_activity_thread_permalink(); ?>" class=" view bp-secondary-action action" title="<?php _e( 'View Conversation', 'buddypress' ); ?>"><?php _e( 'View Conversation', 'buddypress' ); ?> <?php bp_activity_get_comment_count(); ?></a>
                                    <?php endif; ?>

                                    <?php if ( is_user_logged_in() ) : ?>

                                        <?php if ( bp_activity_can_favorite() ) : ?>
                                            <?php if ( !bp_get_activity_is_favorite() ) : ?>
                                                <a href="<?php bp_activity_favorite_link(); ?>" class=" fav bp-secondary-action action" title="<?php esc_attr_e( 'Mark as Favorite', 'buddypress' ); ?>"><i class="icon-star-empty"></i> <span>Favorite</span></a>
                                            <?php else : ?>
                                                <a href="<?php bp_activity_unfavorite_link(); ?>" class=" unfav bp-secondary-action action" title="<?php esc_attr_e( 'Remove Favorite', 'buddypress' ); ?>"><i class="icon-star"></i> <span>Unfavorite</span></a>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <?php if ( bp_activity_can_comment() ) : ?>
                                            <a href="<?php bp_activity_comment_link(); ?>" class=" acomment-reply bp-primary-action action action-reply" id="acomment-comment-<?php bp_activity_id(); ?>"><i class="icon-reply"></i> <span>Reply</span></a>
                                        <?php endif; ?>

                                        <?php if ( bp_activity_user_can_delete() ) bp_delete_link_custom(); ?>
                                        <?php do_action( 'bp_activity_entry_meta' ); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php do_action( 'bp_before_activity_entry_comments' ); ?>

                            <?php if ( ( is_user_logged_in() && bp_activity_can_comment() ) || bp_activity_get_comment_count() ) : ?>
                                <div class="clearfix"></div>
                                <div class="activity-comments">
                                    <?php bp_activity_comments(); ?>
                                    <?php if ( is_user_logged_in() ) : ?>
                                        <form action="<?php bp_activity_comment_form_action(); ?>" method="post" id="ac-form-<?php bp_activity_id(); ?>" class="hide ac-form">
                                            <div class="ac-reply-avatar unit"><?php bp_loggedin_user_avatar( 'width=' . BP_AVATAR_THUMB_WIDTH . '&height=' . BP_AVATAR_THUMB_HEIGHT ); ?></div>
                                            <div class="ac-reply-content lastUnit">
                                                <div class="ac-textarea">
                                                    <textarea id="ac-input-<?php bp_activity_id(); ?>" class="ac-input" name="ac_input_<?php bp_activity_id(); ?>"></textarea>
                                                    <input type="submit" class="btn btn-success" name="ac_form_submit" value="<?php _e( 'Post', 'buddypress' ); ?>" /> &nbsp; <a href="#" class="ac-reply-cancel"><?php _e( 'Cancel', 'buddypress' ); ?></a>
                                                </div>

                                                <input type="hidden" name="comment_form_id" value="<?php bp_activity_id(); ?>" />
                                            </div>
                                            <?php do_action( 'bp_activity_entry_comments' ); ?>
                                            <?php wp_nonce_field( 'new_activity_comment', '_wpnonce_new_activity_comment' ); ?>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <?php do_action( 'bp_after_activity_entry_comments' ); ?>
                        </div>
                    <?php endwhile; ?>

                    <?php if ( bp_activity_has_more_items() ) : ?>
                        <div class="load-more">
                            <a href="#more"><?php _e( 'Load More', 'buddypress' ); ?></a>
                        </div>
                    <?php endif; ?>
                    <?php if ( empty( $_POST['page'] ) ) : ?>
            </div>
                    <?php endif; ?>
                    <?php else : ?>
                    <div id="activity-stream" class="activity-list item-list custom-display">
                        <div id="message" class="info">
                            <p><?php _e( '-', 'buddypress' ); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php do_action( 'bp_after_activity_loop' ); ?>

                    <form action="" name="activity-loop-form" id="activity-loop-form" method="post">

                        <?php wp_nonce_field( 'activity_filter', '_wpnonce_activity_filter' ); ?>

                    </form>
            <?php
        }
    }

    bp_register_group_extension( 'My_Group_Extension' );

endif; // class_exists( 'BP_Group_Extension' )

}
add_action( 'bp_include', 'bp_group_discussions' );
/* If you have code that does not need BuddyPress to run, then add it here. */
?>
