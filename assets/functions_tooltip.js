jQuery(document).ready(function(){
	bluet_placeTooltips(".bluet_tooltip");
	
	function bluet_placeTooltips(inlineClass){
	  jQuery(inlineClass).each(function(i){
		jQuery(this).mouseenter(function(){
			
		  var tooltipBlock=jQuery(this).children().first();//
		  
			if(tooltipBlock){	
				//Calculate the new Position
				var xTop=(jQuery(this).offset().top+jQuery(this).outerHeight(true)+8);
				var yLeft=(jQuery(this).offset().left+(jQuery(this).outerWidth(true)/2)-tooltipBlock.outerWidth(true)/2);
				
				//to prevent to be before the left side of the doc
				if(yLeft<0){
					yLeft=0;
				}
				
				//to prevent to be before the right side of the doc
				if( jQuery(document).outerWidth() < (yLeft+tooltipBlock.outerWidth(true)) ){
					yLeft=yLeft-(yLeft+tooltipBlock.outerWidth(true)-jQuery(document).outerWidth())
				}
				
				tooltipBlock.slideDown();
				tooltipBlock.offset({"top":xTop,"left":yLeft});
		
			}
		});//end mouseenter
		
		jQuery(this).mouseleave(function(){
		  var tooltipBlock=jQuery(this).children().first();
		   if(tooltipBlock){
			   tooltipBlock.slideUp();
		   }
		});
	  });//end each
	}
})