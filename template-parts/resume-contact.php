<?php
/**
 * Template part for displaying contact information on a resume page
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Single_Page_Resume
 */

?>

<?php $contact = get_userdata(1); ?>

<section id="contact">
	<div class="row res-section">
		
		<h2 class="text-center">Contact</h2>
		<hr />
		<h4 class="text-center"><a href="mailto:<?php echo $contact->user_email; ?>"><?php echo $contact->user_email; ?></a></h4>
		<h4 class="text-center"><?php echo $contact->phone; ?></h4>
		
		<p class="text-center">
			<?php
				if($contact->github)
					echo '<a href="https://github.com/' . $contact->github . '" target="_blank"><i class="fa fa-github-square fa-2x" aria-hidden="true"></i></a>';
							
				if($contact->linkedin)
					echo '<a href="https://linkedin.com/in/' . $contact->linkedin . '" target="_blank"><i class="fa fa-linkedin-square fa-2x" aria-hidden="true"></i></a>';
			?>
		</p>
				
	</div><!-- .row .res-section -->
</section>