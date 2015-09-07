//once keywords fetched (highlihted)
jQuery(document).on("keywordsFetched",function() {
	var keyw=[];
	jQuery("body .bluet_tooltip").each(function(){
		keyw.push(jQuery(this).data('tooltip'));
	});
	
	jQuery.post(
		kttg_ajax_load,
		{
			'action': 'kttg_load_keywords',
			'keyword_ids': keyw
		},
		function(response){
			jQuery('#tooltip_blocks_to_show .bluet_block_to_show').remove(':not(#loading_tooltip)');

		
			jQuery('#tooltip_blocks_to_show').append(response);
			
			jQuery.event.trigger("keywordsLoaded");
		}
	);
});

jQuery(document).on("keywordsLoaded",function() {
	jQuery('#loading_tooltip').remove();

	//to prevent empty div on the top
	/*if(jQuery("#tooltip_blocks_to_show").find(".bluet_block_to_show").length==0){
		jQuery("#tooltip_blocks_to_show").remove();
	}*/
});