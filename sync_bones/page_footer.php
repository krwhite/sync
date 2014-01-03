<footer class="footer" role="contentinfo">
	<div class="inner wrap clearfix">
		<h3>footer</h3>
		<nav role="navigation">
				<?php bones_footer_links(); ?>
		</nav>
		<p class="source-org copyright">&copy; <?php echo date('Y'); ?> <?php bloginfo( 'name' ); ?>.</p>
	</div>
</footer>

<?php // all js scripts are loaded in library/bones.php ?>
<?php wp_footer(); ?>