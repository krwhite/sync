<?php get_header(); ?>
			<div id="content">
				<div id="inner-content" class="line wrap clearfix">
					<div id="main" class="unit" role="main">
						<header class="page_header">
							<h1><a href="<?php echo esc_url( home_url( '/' ) ); ?>news">Asynchrony News</a></h1>
						</header>
						<div class="list-wrapper line">
							<ul class="latest">
								<?php
									$args = array( 'post_type' => 'news-article','showposts' => 1, 'order' => 'DESC', 'orderby' => 'post_date' );
									$lastposts = query_posts( $args );
									foreach($lastposts as $post) : setup_postdata($post);
									//foreach ($terms as $term){ $GLOBAL["termss"]= $term->name}
								?>
								<li>
									<article>
										<a class="post-title" href="<?php the_permalink(); ?>">
											<?php the_title(); ?>
										</a>
										by <b><?php the_author_link(); ?></b> on <i><?php the_time('F j, Y'); ?></i> in
										<?php
											$terms = wp_get_object_terms( $post->ID, 'news-category' );
											foreach( $terms as $term ){
												//$term_names[] = $term->name;
												echo $term->name;
											 }
										?>
										<div class="post">
											<?php echo apply_filters('the_content', $post->post_content); ?>
										</div>
									</article>
								</li>
								<?php endforeach; ?>
							</ul>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>news" class="pull-right">View All News</a>
						</div>
						<div class="new-new line">
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
								<a href="<?php echo esc_url( home_url( '/' ) ); ?>members" class="pull-right">View All People</a>
							</div>
						</div>
						<br>
						<div class="new-new line">
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
								<a href="<?php echo esc_url( home_url( '/' ) ); ?>groups" class="pull-right">View All Groups</a>
							</div>
						</div>
					</div> <!-- end #main -->
					<div class="sidebar">
						<div class="profile-card">
							<a href="<?php echo bp_loggedin_user_domain() . 'profile'; ?>">
								<?php bp_loggedin_user_avatar( 'width=60' . '&height=60'); ?>
								<div class="lastUnit">
									<strong><?php bp_loggedin_user_fullname(); ?></strong>
									<span><?php echo xprofile_get_field_data('Job Category', bp_loggedin_user_id() ); ?></span>
								</div>
							</a>
						</div>
						<ul class="menu">
							<li><a href="">Notifications</a></li>
							<li><a href="<?php echo bp_loggedin_user_domain() . 'groups'; ?>">My Groups <span class="count">
								<?php echo bp_get_total_group_count_for_user(bp_loggedin_user_id()); ?>
							</span></a></li>
						</ul>
					</div>
					<div id="twitterbox">
						<a class="twitter-timeline" href="https://twitter.com/asynchrony" data-widget-id="345623555263836160" width="257" height="400" data-tweet-limit="4" data-chrome="nofooter" data-border-color="c4c4c4">Tweets by @asynchrony</a>
						<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
					</div>
				</div> <!-- end #inner-content -->
			</div> <!-- end #content -->
<?php get_footer(); ?>
