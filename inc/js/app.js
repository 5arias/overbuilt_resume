jQuery(document).ready(function($){
	$(document).foundation();
	
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