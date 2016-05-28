jQuery(document).ready( function($){
	
	
	//Variables for Slider
	var range = $('#skill_level'),
		value = $('.range__value');
	
	
	// Set value to the current or default value of the range input	
	value.html(range.val() + '%');
	
	// Update value to the new value on change
	range.on('input', function(){
		value.html(range.val() + '%');
	});
	
	
	//AJAX Submit for "add_skill" form
	
	var formOptions = { 
        target:   '#add_skill_response',   // target element(s) to be updated with server response
        };
        
	// bind to the form's submit event 
    $('#add_skill').submit(function() { 
        // inside event callbacks 'this' is the DOM element so we first 
        // wrap it in a jQuery object and then invoke ajaxSubmit 
        $(this).ajaxSubmit(formOptions); 
 
        // !!! Important !!! 
        // always return false to prevent standard browser submit and page navigation 
        return false; 
    });
	
});