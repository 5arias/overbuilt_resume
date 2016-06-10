<?php
/**
 * Template part for displaying results from the Skillset and Toolbox classes.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Overbuilt_Resume
 */

?>


<section id="abilities">
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
					
				</div><!-- .ability -->
		<?php } ?>
					
		
			<div id="theme-repo" class="text-center">
				<p><em>This custom theme is built on Foundation for Sites</em></p>
				<p><a href="https://github.com/5arias/overbuilt_resume" class="button" target="_blank">View on <i class="fa fa-github"></i> Github</a></p>
			</div>
			
		</div><!-- #skills-wrap -->
				
				
		<h2 class="text-center">Tools</h2>
		<hr />
		<div id="toolbox-wrap">
		
		<?php 
			$toolbox = Toolbox::getAll( 'level', 'DESC' );
					
			foreach($toolbox as $tool) {
		?>
				
				<div class="ability small-12 medium-6 columns">
					<p>
					<?php 
						echo $tool->name; 
						if($tool->experience != 0 )
							echo ' <small>' . $tool->experience . ' years</small>';
					?>
					</p>
					
					<div class="progress" role="progressbar" tabindex="0" aria-valuenow="<?php echo $tool->level; ?>" aria-valuemin="0" aria-valuemax="100">
						<div class="progress-meter" data-width="<?php echo $tool->level; ?>" style="width: 0%;"></div>
					</div>
					
				</div><!-- .ability -->
		<?php } ?>
		</div><!-- #toolbox-wrap -->
				
	</div><!-- .row .res-section -->
</section>