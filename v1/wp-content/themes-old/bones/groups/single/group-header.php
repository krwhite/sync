<?php

do_action( 'bp_before_group_header' );

?>
<div class="line page_header">
	<div class="unit">
		<h1><a href="../">Groups</a> <i class="icon-angle-right"></i> <span><?php bp_group_name(); ?></span></h1>
	</div>
	<div class="lastUnit section-actions">
		<?php bp_group_join_button(); ?>
	</div>




</div>
<?php
do_action( 'bp_after_group_header' );
do_action( 'template_notices' );
?>