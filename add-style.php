<?php
defined('ABSPATH') or die("No script kiddies please!");

function bluet_kw_custom_style(){
	$style_options=get_option('bluet_kw_style');
	
	$tooltip_color=$style_options['bt_kw_tt_color'];
	$tooltip_bg_color=$style_options['bt_kw_tt_bg_color'];
	
	$desc_color=$style_options['bt_kw_desc_color'];
	$desc_bg_color=$style_options['bt_kw_desc_bg_color'];
	
	?>
	<style>
	@media screen and (min-width: 640px) {
		.bluet_tooltip span img
		{
			width:270px;
			
		}
		
		.bluet_tooltip span {
			  position: absolute;
			  width:300px;
			  opacity:0;
			-webkit-transition: opacity 0.4s linear;
			-moz-transition: opacity 0.4s linear;
			-ms-transition: opacity 0.4s linear;
			-o-transition: opacity 0.4s linear;
			transition:opacity 0.4s;

		}
		
		span:hover.bluet_tooltip span {
		  visibility: visible;
		  opacity: 0.95;
		  top: 35px;
		  left: 50%;
		  margin-left: -76px;
		  z-index: 999;
		}
		
		.bluet_tooltip span:before {
		  content: '';
		  position: absolute;
		  bottom: 100%;
		  left: 25%;
		  margin-left: -12px;
		  width: 0; height: 0;
		  border-bottom: 12px solid #6D6D6D;
		  border-right: 12px solid transparent;
		  border-left: 12px solid transparent;
		}
		
		.bluet_tooltip span:after {
		  content: '';
		  position: absolute;
		  bottom: 100%;
		  left: 25%;
		  margin-left: -8px;
		  width: 0; height: 0;
		  border-bottom: 8px solid <?php echo $desc_bg_color; ?>;
		  border-right: 8px solid transparent;
		  border-left: 8px solid transparent;
		}
		
		.bluet-hide-excluded{
			display:none;
		}
	}
	
	/*im mobile case*/
	@media screen and (max-width: 640px) {
		.bluet_tooltip span img
		{
				width:100%;
		}
			
		.bluet_tooltip span {
			max-width:400px;
			position: fixed;
			top:60px;
			right:20px;
			left:20px;
		}
		span:hover.bluet_tooltip span {
			  visibility: visible;
			  opacity: 0.95;
			  z-index: 999999;
		}
			
		.bluet-hide-excluded{
				display:none;
		}

	}
	
	/*generic style*/
	.bluet_tooltip {
			position: relative;
			display: inline;
			text-decoration: none; 
			position: relative;
			color: <?php echo $tooltip_color; ?>;
			background: <?php echo $tooltip_bg_color; ?>;
			padding-right: 8px;
			padding-left: 8px;
			padding-bottom: 3px;
			font-size: 1em;
			border-radius: 18px;
		}
		
	.bluet_tooltip:hover
		{
				box-shadow: 12px 5px 25px 0px rgba(83, 69, 50, 0.67);
				bottom: 2px;
		}
		
		.bluet_tooltip span img
		{
				float:left;
				margin:0px 8px 8px 0;
				border: none !important;
				border-radius: 5px;
				height: auto;
		}
		
		.bluet_tooltip span
		{
			color: <?php echo $desc_color; ?>;
			  background: <?php echo $desc_bg_color; ?>;
			  border: 1px solid #6D6D6D;
			  height: auto;
			  visibility: hidden;
			  border-radius: 6px;
			  box-shadow: 4px 4px 10px #6A7563;
  			padding: 13px;

		  }
	</style>
	<?php
}