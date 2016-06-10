<?php
/**
 * Template part for displaying the main banner with data from the selected user's profile.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Overbuilt_Resume
 */

?>

<?php $profile = get_userdata(1); ?>

<section id="banner" role="banner" data-interchange="
	[<?php $img = wp_get_attachment_image_src($profile->user_meta_image, 'medium'); echo $img[0]; ?>, small],
	[<?php $img = wp_get_attachment_image_src( $profile->user_meta_image, 'large'); echo $img[0]; ?>, medium],
	[<?php $img = wp_get_attachment_image_src( $profile->user_meta_image, 'large'); echo $img[0]; ?>, large]"
>
			
		<div class="site-branding-wrapper">
			<div class="valign-wrapper site-branding mask-2">
				<div class="valign content-wrapper">
					<div>
						<h1><?php echo $profile->user_firstname . ' ' . $profile->user_lastname; ?></h1>
						<h2>Interactive Resume</h2>
						<p class="text-right">
						<?php
							if($profile->github)
								echo '<a href="https://github.com/' . $profile->github . '" target="_blank"><i class="fa fa-github-square fa-2x" aria-hidden="true"></i></a>';
							
							if($profile->linkedin)
								echo '<a href="https://linkedin.com/in/' . $profile->linkedin . '" target="_blank"><i class="fa fa-linkedin-square fa-2x" aria-hidden="true"></i></a>';
						?>
						</p>
					</div><!-- placeholder for animation sequence -->
				</div><!-- .valign .content-wrapper -->
			</div><!-- .valign-wrapper .site-branding .mask-2 -->
		</div><!-- .site-branding-wrapper -->

</section>