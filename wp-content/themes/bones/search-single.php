<?php
/*
Template Name: Buddypress Search
*/
?>

<?php get_header();?>
	<div id="content" class="wrapped-content">
		<div id="inner-content" class="padder wrap line">
			<div id="main">
				<header class="article-header">
						<div class="line page_header">
							<h1 class="page-title" itemprop="headline">Search Results for: <span id="results-header"></span></h1>
						</div>
				</header> <!-- end article header -->
				<!-- <ul class="nav nav-pills" id="nav-tabs">
					<li class="active"><a href="#members" data-toggle="pill">Members </a></li>
					<li><a href="#groups" data-toggle="pill">Groups </a></li>
					<li><a href="#activity" data-toggle="pill">Activity </a></li>
					<li><a href="#help" data-toggle="pill">Posts </a></li>
				</ul> -->
				<div>
					<?php do_action("advance-search");//this is the only line you need?>
					<!-- let the search put the content here -->
				</div>
			</div>
		</div> <!-- end of padder... -->
		<div class="clear"> </div>
	</div><!-- Container ends here... -->
  <?php get_footer();?>
