<?php
/**
 * Template part for displaying information about the selected user from their account profile.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Single_Page_Resume
 */

?>

<?php $about_id = 1; ?>

<section id="about">
	<div class="row res-section">
				
		<div class="small-12 medium-4 large-3 columns">
			<h2>Call me<br /><span><?php echo get_the_author_meta('nickname', $about_id); ?></span></h2>
		</div>
					
		<div class="small-12 medium-8 large-9 columns">
			<p><?php echo get_the_author_meta('description', $about_id); ?></p>
			<p class="hide-for-small-only">I'm <?php echo get_the_author_meta('age', $about_id); ?> years old, grew up in <?php echo get_the_author_meta('hometown', $about_id); ?>, and currently live in <?php echo get_the_author_meta('residence', $about_id); ?>.</p>
			
			<h5><i class="fa fa-graduation-cap" aria-hidden="true"></i> Education</h5>
			<p>
			<?php 
				echo get_the_author_meta('bachelor_degree', $about_id); 
				echo '<br />';
				echo get_the_author_meta('master_degree', $about_id);
			?>	
			</p>
		</div>
				
	</div><!-- .row .res-section -->
</section>