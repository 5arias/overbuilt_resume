<?php
/**
 * Template part for displaying page content in page.php.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Overbuilt_Resume
 */

?>

	<header class="entry-header">
		<h1 class="entry-title"> <?php global $title; echo __($title, 'overbuilt-resume'); ?> </h1>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<form id="add_skill" method="POST">
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
			<input type="submit" value="Add Skill" class="button button-primary button-large">
		</form>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		
	</footer><!-- .entry-footer -->
