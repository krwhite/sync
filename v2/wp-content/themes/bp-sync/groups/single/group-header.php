<?php

do_action( 'bp_before_group_header' );

?>
<div class="widget_nav_menu">
	<h3><?php bp_group_name(); ?></h3>
	<?php bp_group_join_button(); ?>
</div>




<?php
do_action( 'bp_after_group_header' );
do_action( 'template_notices' );
?>