<?php
/**
 * Template part for displaying Portfolio CPT post data.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Single_Page_Resume
 */

?>

<section id="portfolio">
	<div class="row res-section">
				
		<h2 class="text-center">Works</h2>
		<hr />
				
		<?php
					
			$args = array(
				'post_type' => 'portfolio',
				'order' 	=> 'ASC'
			);
					
			$portfolio = new WP_Query( $args ); 
					
			if ( $portfolio->have_posts() ) :
				while ( $portfolio->have_posts() ) : $portfolio->the_post();
		?>
				
				<div class="small-12 medium-12 large-6 columns portfolio-item">
					<a href="<?php echo $post->rcmb_website_url; ?>" target="_blank">
						
						<?php echo wp_get_attachment_image( $post->rcmb_screenshot , 'large'); ?>
					
						<div class="overlay">
							<h3><?php the_title(); ?></h3>
							<p class="tags">
								<?php 
									$tags = wp_get_post_terms( $post->ID, 'post_tag', array('fields' => 'names'));
									
									$count = count($tags);
									$i = 1;
									
									foreach($tags as $tag){
										if($i != $count)
											echo $tag . '<span class="divider hide-for-small-only">|</span><span class="show-for-small-only"><br /></span>';
										else
											echo $tag;
											
										$i++;
									}
									
								?>
							</p>
						</div>
					</a>
							
				</div>
					
		<?php  
				endwhile; 
			endif; 
		?>
		
	</div><!-- .row .res-section -->
</section>