jQuery(document).ready( function($){
	var range = $('#skill_level'),
		value = $('.range__value');
	
	
	// Set value to the current or default value of the range input	
	value.html(range.val() + '%');
	
	// Update value to the new value on change
	range.on('input', function(){
		value.html(range.val() + '%');
	});
	
	
});