<?php
/**
 * Template part for displaying Portfolio CPT post data.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Overbuilt_Resume
 */

?>

<section id="portfolio">
	<div class="row res-section">
				
		<h2 class="text-center">Works</h2>
		<hr />
				
		<?php
					
			$args = array(
				'post_type' => 'portfolio'
			);
					
			$portfolio = new WP_Query( $args ); 
					
			if ( $portfolio->have_posts() ) :
				while ( $portfolio->have_posts() ) : $portfolio->the_post();
		?>
				
				<div class="small-12 medium-6 columns">
					
					<?php echo wp_get_attachment_image( $post->rcmb_screenshot , 'large'); ?>
					
					<div class="overlay" style="display: none;">
						<h3><?php the_title(); ?></h3>
						<p class="tags"><?php get_post_taxonomies('post_tag'); ?></p>
								
						<a href="<?php echo $post->rcmb_website; ?>" target="_blank">Link</a>
								
						<a rel="" target="_blank">Link</a>
								
					</div>
							
				</div>
					
		<?php  
				endwhile; 
			endif; 
		?>
		
	</div><!-- .row .res-section -->
</section>