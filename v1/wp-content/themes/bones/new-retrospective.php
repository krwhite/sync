<?php

/*

Template Name: New Retrospective

*/

?>

<?php get_header(); ?>

			<div id="content" class="template with_sidebar">

				<div id="inner-content" class="wrap clearfix line">

				<div id="main" class="new-news-article" role="main">

					<div class="line page_header" >

						<h1 class="page-title unit" itemprop="headline">New Retrospective</h1>

					</div>

					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>



					<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">



						<section class="entry-content clearfix" itemprop="articleBody">

							<?php the_content(); ?>

						</section> <!-- end article section -->



					</article> <!-- end article -->



					<?php endwhile; else : ?>



						<article id="post-not-found" class="hentry clearfix">

							<header class="article-header">

								<h1><?php _e("Oops, Post Not Found!", "bonestheme"); ?></h1>

							</header>

							<section class="entry-content">

								<p><?php _e("Uh Oh. Something is missing. Try double checking things.", "bonestheme"); ?></p>

							</section>

							<footer class="article-footer">

								<p><?php _e("This is the error message in the page.php template.", "bonestheme"); ?></p>



							</footer>

						</article>



					<?php endif; ?>



				</div> <!-- end #main -->







				</div> <!-- end #inner-content -->



			</div> <!-- end #content -->





<?php get_footer(); ?>

 <!-- lets build those tags now eh!" -->

		<?php

			  $tags = get_tags(array( 'hide_empty' => false ));

			  $html = '[';

			  foreach ( $tags as $tag ) {

				$html .= "{id:'".$tag->slug."', text:'".$tag->slug."'},";

			  }

			   $html .= ']';

			  echo '<script type="text/javascript"> yohtml='.$html.'; </script>';



		?>

		<script type="text/javascript">

			jQuery('.wpuf-fields #tags').select2({

				data: yohtml,

				initSelection : function (element, callback) {

					var data = [];

					jQuery(element.val().split(",")).each(function () {

						data.push({id: this, text: this});

					});

					callback(data);

				},

				multiple: true,

				allowClear: true,

				tokenSeparators: [",", " "],

				createSearchChoice: function(term, data) {

					if (jQuery(data).filter(function() {

						return this.text.localeCompare(term) === 0;

					}).length === 0) {

						return {

							id: term,

							text: term

						};

					}

				}

			});

		</script>

