jQuery(document).ready(function($){

	//Initialize Foundation
	$(document).foundation();
	
	/*
	 * Back to Top button
	 *
	 * Not used on small screens
	 */
	$(window).scroll(function() {
    	if ($(this).scrollTop() >= 50) {    // If page is scrolled more than 50px
        	$('#backtotop').fadeIn(200);    // Fade in the arrow
    	} else {
        	$('#backtotop').fadeOut(200);   // Else fade out the arrow
    	}
	});

	$('#backtotop').click(function() {      // When arrow is clicked
    	$('body,html').animate({
        	scrollTop : 0                   // Scroll to top of body
    	}, 500);
	});
	
	/*
	 * Animate Skills Section
	 */
	$('#skills-wrap').scrollfire({
		// Offsets
        offset: 0,
        topOffset: 150,
        bottomOffset: 150,
        
        // Fires once when element begins to come in from the bottom
        onBottomIn: function() {
            $('#skills-wrap .progress-meter').each(function(){
				var meter = $(this);
				var percent = meter.attr('data-width');
				meter.animate({ width: percent + '%' }, 1250);
			});
        }
	});
	
	/*
	 * Animate Tools Section
	 *
	 * Duplicate code because Scrollfire will animate Tools at same time as Skills if
	 * I add both selectors in a single call (obviously). Need to find a better way...
	 */
	$('#toolbox-wrap').scrollfire({
		// Offsets
        offset: 0,
        topOffset: 150,
        bottomOffset: 150,
        
        // Fires once when element begins to come in from the bottom
        onBottomIn: function( ) {
            $('#toolbox-wrap .progress-meter').each(function(){
				var meter = $(this);
				var percent = meter.attr('data-width');
				meter.animate({ width: percent + '%' }, 1250);
			});
        }
	});
	
});