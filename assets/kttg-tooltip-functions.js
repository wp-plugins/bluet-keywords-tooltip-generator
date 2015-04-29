function moveTooltipElementsTop(className){
//this function moves les tooltip elements after the tag BODY
		jQuery("body").prepend("<div id='tooltip_blocks_to_show'></div>");
		
		jQuery("#tooltip_blocks_to_show").prepend(jQuery(className));
		
		//remove repeated elements 
		jQuery("#tooltip_blocks_to_show").children().each(function(){
			id_post_type=jQuery(this).data("tooltip");
			if(jQuery("#tooltip_blocks_to_show").children("[data-tooltip="+id_post_type+"]").length>1){
				jQuery("#tooltip_blocks_to_show").children("[data-tooltip="+id_post_type+"]").each(function(index){
					if(index>0){
						jQuery(this).remove();
					}
				});
			}
		});
		
	//add listeners to tooltips 
	jQuery(".bluet_block_to_show").mouseover(function(){
		//.show()
		jQuery(this).show();
	})
	jQuery(".bluet_block_to_show").mouseout(function(){
		//leave it like that .css("display","none"); for Safari navigator issue
		jQuery(this).css("display","none");
	})
	
	jQuery(".bluet_hide_tooltip_button").click(function(){
		//leave it like that .css("display","none"); for Safari navigator issue
		jQuery(".bluet_block_to_show").css("display","none");
		//jQuery(".bluet_block_to_show");
	})

}

function bluet_placeTooltips(inlineClass,position){
	//add listeners to inline keywords on mouseover
	jQuery(inlineClass).mouseover(function(){
		//id of the posttype in concern

		id_post_type=jQuery(this).data("tooltip");
		var tooltipBlock=jQuery("#tooltip_blocks_to_show").children("[data-tooltip="+id_post_type+"]").first();
	  
		if(tooltipBlock){

			//Calculate the new Position
			
			//vertical offsets
			var xTop_show_middle=jQuery(this).offset().top+jQuery(this).outerHeight(true)-tooltipBlock.outerHeight(true)/2;	
			
			var xTop_show_bottom=(jQuery(this).offset().top+jQuery(this).outerHeight(true));			
			var xTop_show_top=jQuery(this).offset().top-tooltipBlock.outerHeight(true);
			
			//horizontal offsets
			var yLeft_show_center=jQuery(this).offset().left+(jQuery(this).outerWidth(false)/2)-tooltipBlock.outerWidth(true)/2;
			
			/*yLeft_show_center=jQuery(this).offset().left-tooltipBlock.css("padding-left").replace("px", ""); */

			var yLeft_show_left=jQuery(this).offset().left-tooltipBlock.outerWidth(true);			
			var yLeft_show_right=jQuery(this).offset().left+jQuery(this).outerWidth(true);
			
			//to prevent to be before the left side of the doc
			if(yLeft_show_center<0){
				yLeft_show_center=0;
			}
			
			//to prevent to be before the right side of the doc
			if( jQuery(document).outerWidth() < (yLeft_show_center+tooltipBlock.outerWidth(true)) ){
				yLeft_show_center=yLeft_show_center-(yLeft_show_center+tooltipBlock.outerWidth(true)-jQuery(document).outerWidth())
			}
			//.show()
			tooltipBlock.css("opacity","0").show();
			animation_margin=10;
			
			switch(position) {
				case "top":		
					xTop_show_top-=animation_margin;

					tooltipBlock.offset({"top":xTop_show_top,"left":yLeft_show_center});
					tooltipBlock.addClass("kttg_arrow_show_top");	

					tooltipBlock.animate({"opacity":"1","top":"+="+animation_margin},300);
					break;
				case "bottom":	
					xTop_show_bottom+=animation_margin;
		
					tooltipBlock.offset({"top":xTop_show_bottom,"left":yLeft_show_center});
					tooltipBlock.addClass("kttg_arrow_show_bottom");

					tooltipBlock.animate({"opacity":"1","top":"-="+animation_margin},300);

					break;
				case "right":
					yLeft_show_right+=animation_margin;

					tooltipBlock.offset({"top":xTop_show_middle,"left":yLeft_show_right});
					
					tooltipBlock.animate({"opacity":"1","left":"-="+animation_margin},300);

					//tooltipBlock.addClass("kttg_arrow_show_right");
					break;
					
				case "left":
					yLeft_show_left-=animation_margin;

					tooltipBlock.offset({"top":xTop_show_middle,"left":yLeft_show_left});
					
					tooltipBlock.animate({"opacity":"1","left":"+="+animation_margin},300);

					//tooltipBlock.addClass("kttg_arrow_show_left");
					break;
				default:
					xTop_show_bottom-=animation_margin;

					tooltipBlock.offset({"top":xTop_show_bottom,"left":yLeft_show_center});
					tooltipBlock.addClass("kttg_arrow_show_bottom");
					
					tooltipBlock.animate({"opacity":"1","bottom":"+="+animation_margin},300);

					break;
			}
		}
	});
	
	//on mouseout
	jQuery(inlineClass).mouseout(function(){
		id_post_type=jQuery(this).data("tooltip");
		var tooltipBlock=jQuery("#tooltip_blocks_to_show").children("[data-tooltip="+id_post_type+"]").first();

	   if(tooltipBlock){
		   //leave it like that .css("display","none"); for Safari navigator issue
		tooltipBlock.css("display","none");
		//tooltipBlock;
		   
	   }
	});
}