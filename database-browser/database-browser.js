jQuery(document).ready(function(){
	jQuery(".hider").hide();
	jQuery("#wherelabel").click(function(){
		jQuery("#where").toggle();
	});
	jQuery("#orderbylabel").click(function(){
		jQuery("#orderby").toggle();
	});
	jQuery("#queryheader").click(function(){
		jQuery("#query").toggle();
	});
	// set up main objects
	var exportlinks = jQuery("#databasebrowser .export");
	// when an export link is clicked, export the data using AJAX
	exportlinks.live("click", function(event){
		// get the format
		var format = this.id;
		if (bike.length) {
			jQuery.post(
				ajaxurl,
				{
					action : 'databasebrowser-export',
					format : format
				},
				function(response) {
					jQuery("#bike").html(response);
				}
			);
		}
		event.preventDefault();
	});
});