<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Single_Page_Resume
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php

			/*
			 * Banner
			 * Data from user's account profile.
			 */
			get_template_part('template-parts/resume', 'banner');
				
				
			/*
			 * About
			 * Data from user's account profile.
			 */
			get_template_part('template-parts/resume', 'about');
				
				
			/*
			 * Experience
			 * Data from work_history CPT posts.
			 */
			get_template_part('template-parts/resume', 'experience');
				
				
			/*
			 * Abilities
			 * Data from Skillset and Toolbox classes.
			 */
			get_template_part('template-parts/resume', 'abilities');
				
				
			/*
			 * Portfolio (aka Works)
			 * Data from portfolio CPT posts.
			 */
			get_template_part('template-parts/resume', 'portfolio');
				
				
			/*
			 * Contact
			 * Data from user's account profile.
			 */
			get_template_part('template-parts/resume', 'contact');
				
		?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
