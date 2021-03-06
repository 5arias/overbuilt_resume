<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WP_Single_Page_Resume
 */

?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="site-info text-center">
			<?php printf( esc_html__( ' %s &copy; %s', 'spr' ), date('Y'), '<a href="https://www.stephenbrody.com" rel="designer">Stephen Brody</a>' ); ?>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<a href="javascript:" id="backtotop" class="hide-for-small-only"><i class="fa fa-chevron-up" aria-hidden="true"></i></a>

<?php wp_footer(); ?>

</body>
</html>
