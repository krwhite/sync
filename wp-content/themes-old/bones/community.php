<?php
/*
Template Name: Community Page
*/
?>
<?php get_header(); ?>
			<div id="content" class="community">
				<div id="inner-content" class="wrap clearfix line">
					<div id="main" class="unit" role="main">
						<header class="article-header">
							<div class="line page_header">
								<h1 class="page-title" itemprop="headline">Community</h1>
								<?php wp_nav_menu( array('menu' => 'Community', 'container_class' => 'tabs unit', 'menu_class' => 'nav nav-pills'));
								?>
							</div>
						</header> <!-- end article header -->

						<div class="content line">
							<div class="line">
								<h2>New People</h2>
								<ul class="postList">
								<?php
									if ( bp_has_members( 'type=newest&max=12' ) ) : ?>
									<?php while ( bp_members() ) : bp_the_member(); ?>
										<li class="unit size1of4">
											<a href="<?php bp_member_permalink() ?>">
												<?php bp_member_avatar('type=full') ?>
												<p class="title"><?php bp_member_name() ?></p>
											</a>
										</li>
									<?php endwhile; ?>
								<?php endif;?>
								</ul><br>
								<div class="clearfix">
									<a href="<?php echo esc_url( home_url( '/' ) ); ?>members" class="btn btn-warning pull-right">View All People</a>
								</div>
								<h2>New Groups</h2>
								<ul class="postList groupList">
								<?php
									if ( bp_has_groups( 'type=newest&max=3' ) ) : ?>
									<?php while ( bp_groups() ) : bp_the_group(); ?>
										<li class="line">
											<a href="<?php bp_group_permalink() ?>">
												<?php bp_group_avatar( 'type=full' ) ?>
											</a>
											<div>
												<a href="<?php bp_group_permalink() ?>"><p class="title"><?php bp_group_name() ?></p></a>
												<?php bp_group_description() ?>
											</div>
										</li>
									<?php endwhile; ?>
								<?php endif;?>
								</ul>
								<div class="clearfix">
									<a href="<?php echo esc_url( home_url( '/' ) ); ?>groups" class="btn btn-warning pull-right">View All Groups</a>
								</div>
							</div>
						</div> <!-- end article -->
					</div> <!-- end #main -->
					<div class="lastUnit">
					</div>
				</div> <!-- end #inner-content -->
			</div> <!-- end #content -->


<?php get_footer(); ?>

