<?php
/**
 * Template part for displaying page content in page.php.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Overbuilt_Resume
 */

?>
	<div class="wrap">
		<h1><?php global $title; echo __($title, 'overbuilt-resume'); ?></h1>
		
		<div class="wrap">
			<form id="add_skill" method="post" action="<?php echo admin_url('admin-ajax.php'); ?>">
				<div class="field-wrap">
					<label for="skill_name">Skill Name</label>
					<input type="text" name="skill_name" id="skill_name" value="">
				</div>
				<div class="field-wrap ">
					<label for="skill_level">How proficient are you?</label>
					<input type="range" name="skill_level" id="skill_level" value="50" min="0" max="100" step="1">
					<span class="range__value">0</span>
				</div>
				<label for="submit">&nbsp;</label>
				<?php wp_nonce_field( 'submit_skill_ajax', 'submit_skill_ajax_nonce' ); ?>
				<input type="hidden" name="action" id="action" value="submit_skill_ajax">
				<input type="submit" value="Add Skill" class="button button-primary button-large">
			</form>
			<div id="add_skill_response"></div>
		</div><!-- .wrap Form - Add Skill -->

		<div class="wrap">
			<?php
			$this->customers_obj->prepare_items();
			$this->customers_obj->display(); ?>
		</div>
	</div>
