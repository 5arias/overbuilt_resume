<?php
/**
 * Template Name: Resume Layout
 *
 * @package Overbuilt_Resume
 */

get_header();
$profile = get_userdata(1);
while ( have_posts() ) : the_post(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
			
			
		<div id="banner" class="small-12" role="banner" data-interchange="[<?php $img = wp_get_attachment_image_src($profile->user_meta_image, 'medium'); echo $img[0]; ?>, small], [<?php $img = wp_get_attachment_image_src( $profile->user_meta_image, 'large'); echo $img[0]; ?>, large]">
			<div class="site-branding valign-wrapper small-12 text-right">
				<div class="valign">
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
				</div>
			</div>
		</div>
		
			
			
			
		<div id="about">
			<div class="row res-section">
				<div class="small-12 medium-4 large-3 columns">
					<h2>Call me<br /><span><?php echo $profile->nickname; ?></span></h2>
				</div>
					
				<div class="small-12 medium-8 large-9 columns">
						<p><?php echo $profile->description; ?></p>
						<p>I'm <?php echo $profile->age; ?> years old, grew up in <?php echo $profile->hometown; ?>, and currently living in <?php echo $profile->residence; ?>.</p>
				</div>
			</div>
		</div>
		
		
			
		<div id="work-experience">
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
					$history = new WP_Query( $args ); 
					
					if ( $history->have_posts() ) :
						while ( $history->have_posts() ) : $history->the_post();
					?>
					<article <?php post_class('row'); ?>>
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
							
							<div class="work-details"><i class="fa fa-map-marker"></i> <?php echo $post->rcmb_location; ?> <?php $url = $post->rcmb_website_url; if ($url) : ?><span class="divider">|</span><i class="fa fa-link fa-flip-horizontal"></i> <a href="http://www.<?php echo $url; ?>" target="_blank"><?php echo $url; ?></a><?php endif; ?></div>
							
						</div>
					</article>
					
					<?php  endwhile; endif; ?>
			</div>
		</div>
			
			
			
		<div id="abilities">
			<div class="row res-section">
				<h2 class="text-center">Skills</h2>
				<hr />
				<div id="skills-wrap">
					<?php 
						$skillset = Skillset::getAll( 'level', 'DESC' );
					
						foreach($skillset as $skill) {
					?>
						<div class="ability small-12 medium-6 columns">
							<p><?php echo $skill->name; ?></p>
							<div class="progress" role="progressbar" tabindex="0" aria-valuenow="<?php echo $skill->level; ?>" aria-valuemin="0" aria-valuemax="100">
								<div class="progress-meter" data-width="<?php echo $skill->level; ?>" style="width: 0%;"></div>
							</div>
						</div>
					<?php } ?>
					
					<div id="theme-repo" class="text-center">
						<p><em>This custom theme is built on Foundation for Sites 6</em></p>
						<p><a href="https://github.com/5arias/overbuilt_resume" class="button" target="_blank">View on <i class="fa fa-github"></i> Github</a></p>
					</div>
				</div>
				
				
				<h2 class="text-center">Tools</h2>
				<hr />
				<div id="toolbox-wrap">
				<?php 
					$toolbox = Toolbox::getAll( 'level', 'DESC' );
					
					foreach($toolbox as $tool) {
				?>
					<div class="ability small-12 medium-6 columns">
						<p><?php 
							echo $tool->name; 
							if($tool->experience != 0 )
								echo ' <small>' . $tool->experience . ' years</small>';
						?></p>
						<div class="progress" role="progressbar" tabindex="0" aria-valuenow="<?php echo $tool->level; ?>" aria-valuemin="0" aria-valuemax="100">
							<div class="progress-meter" data-width="<?php echo $tool->level; ?>" style="width: 0%;"></div>
						</div>
					</div>
				<?php } ?>
				</div>
				
			</div>
		</div>
			
			
			
		
		<div id="portfolio">
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
					
					<?php  endwhile; endif; ?>
			</div>
		</div>
			
			
			
			
		<div id="contact">
			<div class="row res-section">
				<h2 class="text-center">Contact</h2>
				<hr />
				<h4 class="text-center"><a href="mailto:<?php echo $profile->user_email; ?>"><?php echo $profile->user_email; ?></a></h4>
				<h4 class="text-center"><?php echo $profile->phone; ?></h4>
				<p class="text-center">
					<?php
						if($profile->github)
							echo '<a href="https://github.com/' . $profile->github . '" target="_blank"><i class="fa fa-github-square fa-2x" aria-hidden="true"></i></a>';
							
						if($profile->linkedin)
							echo '<a href="https://linkedin.com/in/' . $profile->linkedin . '" target="_blank"><i class="fa fa-linkedin-square fa-2x" aria-hidden="true"></i></a>';
					?>
				</p>
				
			</div>
		</div>
				


	</main><!-- #main -->
</div><!-- #primary -->

<?php
endwhile; // End of the loop.			
get_footer();
