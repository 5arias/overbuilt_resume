<?php
/**
 * Template part for displaying information about the selected user from their account profile.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Overbuilt_Resume
 */

?>

<?php $about = get_userdata(1); ?>

<section id="about">
	<div class="row res-section">
				
		<div class="small-12 medium-4 large-3 columns">
			<h2>Call me<br /><span><?php echo $about->nickname; ?></span></h2>
		</div>
					
		<div class="small-12 medium-8 large-9 columns">
			<p><?php echo $about->description; ?></p>
			<p>I'm <?php echo $about->age; ?> years old, grew up in <?php echo $about->hometown; ?>, and currently living in <?php echo $about->residence; ?>.</p>
		</div>
				
	</div><!-- .row .res-section -->
</section>