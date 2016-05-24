<?php
/**
 * Template Name: Resume Layout
 *
 * @package Overbuilt_Resume
 */

get_header();
$profile = get_userdata(1);
while ( have_posts() ) : the_post(); ?>

	<div id="primary" class="content-area small-12">
		<main id="main" class="site-main" role="main">
			
			<div id="banner" class="small-12" role="banner" data-interchange="[<?php echo wp_get_attachment_url($profile->user_meta_image, 'large'); ?>, large]">
				<div class="site-branding valign-wrapper small-12 text-right">
					<div class="valign">
						<h1><?php echo $profile->user_firstname . ' ' . $profile->user_lastname; ?></h1>
						<h2>Interactive Resume</h2>
					</div>
				</div>
			</div>
			
			<div id="about" class="row">
				<div class="small-12 medium-3 columns">
					<h2>Call me <?php echo $profile->nickname; ?></h2>
				</div>
					
				<div class="small-12 medium-9 columns">
						<p><?php echo $profile->description; ?></p>
						<p>I'm <?php echo $profile->age; ?> years old, grew up in <?php echo $profile->hometown; ?>, and currently living in <?php echo $profile->residence; ?>.</p>
				</div>
			</div>
			
			<div id="work-history" class="row">
				<h2>Work History</h2>
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
					$history = new WP_Query( $args ); 
					
					if ( $history->have_posts() ) :
						while ( $history->have_posts() ) : $history->the_post();
					?>
					<article <?php post_class('row'); ?>>
						<div class="small-12 medium-4 large-3 columns">
							<h5><?php echo $post->rcmb_start_date; ?> - <?php echo $post->rcmb_end_date; ?></h5>
							
							<?php if( $post->rcmb_organization_logo ) : ?>
								<p><img src="<?php echo wp_get_attachment_url($post->rcmb_organization_logo, 'medium'); ?>" /></p>
							<?php endif; ?>
							
						</div>
						
						<div class="small-12 medium-8 large-9 columns">
							<h5><?php echo the_title(); ?><br /><small><?php echo $post->rcmb_organization; ?></small></h5>
							
							<div>
								<?php echo $post->rcmb_job_description; ?>
								<p><i class="fa fa-map-marker"></i> <?php echo $post->rcmb_location; ?> <?php $url = $post->rcmb_website_url; if ($url) : ?> | <i class="fa fa-link"></i> <a href="<?php echo $url; ?>" target="_blank"><?php echo $url; ?></a><?php endif; ?></p>
							</div>
							
						</div>
					</article>
					
					<?php  endwhile; endif; ?>
			</div>
				


		</main><!-- #main -->
	</div><!-- #primary -->

<?php
endwhile; // End of the loop.			
get_footer();
