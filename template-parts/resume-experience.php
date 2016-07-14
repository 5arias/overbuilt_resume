<?php
/**
 * Template part for displaying Work History CPT posts (aka Work Experience).
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Single_Page_Resume
 */

?>

<section id="work-experience">
	<div class="row res-section">
				
		<h2 class="text-center">Professional Experience</h2>
		<hr />
				
		<?php
			$args = array(
				'post_type' => 'work_history',
				'orderby'   => array( 'rcmb_start_date' => 'DESC', 'rcmb_end_date' => 'DESC' ),
				'meta_query'      => array(
					'relation'    => 'AND',
					'rcmb_start_date' => array(
						'key'     => 'rcmb_start_date',
						'compare' => 'EXISTS',
					),
					'rcmb_end_date'    => array(
						'key'     => 'rcmb_end_date',
						'type'    => 'CHAR',
						'compare' => 'EXISTS',
					),
				)
			);
					
			$experience = new WP_Query( $args ); 
					
			if ( $experience->have_posts() ) :
				while ( $experience->have_posts() ) : $experience->the_post();
		?>
					
					<article <?php post_class('row'); ?>>
						<?php 
						/*
						 * The double h4 and h5 tags below are dumb little "responsive" hacks.
						 * I would normally use a media query but I wanted an excuse to use Foundation's visibility classes.
						 */
						 ?>
						<div class="small-12 medium-4 large-3 columns">
							<h4 class="hide-for-medium-only"><?php echo $post->rcmb_organization; ?></h4>
							<h5 class="show-for-medium-only"><?php echo $post->rcmb_organization; ?></h5>
						</div>
						
						<div class="small-12 medium-8 large-9 columns">
							<h4 class="hide-for-medium-only"><?php echo the_title(); ?></h4>
							<h5 class="show-for-medium-only"><?php echo the_title(); ?></h5>
						</div>
						
						<h5 class="small-12 medium-12 large-3 columns"><?php echo $post->rcmb_start_date; ?> - <?php echo $post->rcmb_end_date; ?></h5>		
						
						<div class="small-12 medium-8 large-9 columns">
							
							<div class="show-for-medium">
								<?php 
									$content = html_entity_decode($post->rcmb_job_description);
									echo wpautop($content); 
								?>
							</div>
							
							<div class="work-details">
								<i class="fa fa-fw fa-map-marker"></i> <?php echo $post->rcmb_location; ?> 
								
								<?php 
									$url = $post->rcmb_website_url; 
									
									if ($url) : 
								?>
								
								<span class="divider hide-for-small-only">|</span>
								<span class="show-for-small-only"><br /></span>
								<i class="fa fa-fw fa-link fa-flip-horizontal"></i> <a href="http://www.<?php echo $url; ?>" target="_blank"><?php echo $url; ?></a>
								
								<?php endif; ?>
							</div><!-- .work-details -->
							
						</div>
					</article>
					
		<?php  
				endwhile; 
			endif; 
		?>
		
	</div><!-- .row .res-section -->
</section>