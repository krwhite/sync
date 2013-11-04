<?php get_header( 'buddypress' ); ?>
	<div id="buddypress" class="wrapped-content">
		<div class="wrap line">
			<?php if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group(); ?>

			<?php do_action( 'bp_before_group_plugin_template' ); ?>

			<div id="item-header">
				<?php locate_template( array( 'groups/single/group-header.php' ), true ); ?>
			</div><!-- #item-header -->
			<div class="line group">
				<div class="unit size1of3">
					<div class="inner">
						<?php bp_get_template_part( 'groups/single/group-sidebar' ); ?>
					</div>
				</div>
				<div class="lastUnit">
				<div class="inner">
					<div id="item-nav">
						<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
							<ul class="nav nav-pills">
								<?php my_bp_get_options_nav(); ?>

								<?php do_action( 'bp_group_plugin_options_nav' ); ?>
							</ul>
						</div>
					</div><!-- #item-nav -->

					<div id="item-body">

						<?php do_action( 'bp_before_group_body' ); ?>

						<?php do_action( 'bp_template_content' ); ?>

						<?php do_action( 'bp_after_group_body' ); ?>
					</div><!-- #item-body -->

					<?php do_action( 'bp_after_group_plugin_template' ); ?>

				</div>
				</div>


			</div>


			<?php endwhile; endif; ?>

		</div><!-- .padder -->
	</div><!-- #content -->


<?php get_footer( 'buddypress' ); ?>