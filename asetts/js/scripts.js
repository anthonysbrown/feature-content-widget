jQuery(document).ready(function($) {
	
	  $('.nice_select').select2({ width: '100%' , minimumResultsForSearch: Infinity});
			
			
	$("#fcw-problems-dropdown").on("change",function(){		
			var problem_id = $("#fcw-problems-dropdown").val();
			var data = {
				'action': 'fcw_ajax_solutions',
				'problem_id': problem_id     
			};
					
					
			if(problem_id != ''){
				$(".fcw-solutions").html('<div class="fcw-loading"><img src="' + fcw_vars.fcw_asetts + '/images/ring.gif"></div>');
				jQuery.post(fcw_vars.ajax_url, data, function(response) {
					
							$(".fcw-solutions").html(response);
				});
			}
	
	});
});