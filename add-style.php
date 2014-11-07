<?php
defined('ABSPATH') or die("No script kiddies please!");

function bluet_kw_custom_style(){
	$style_options=get_option('bluet_kw_style');
	
	$tooltip_color=$style_options['bt_kw_tt_color'];
	$tooltip_bg_color=$style_options['bt_kw_tt_bg_color'];
	$bt_kw_on_background=$style_options['bt_kw_on_background'];
	
	$desc_color=$style_options['bt_kw_desc_color'];
	$desc_bg_color=$style_options['bt_kw_desc_bg_color'];
	
	$desc_font_size=(empty($style_options['bt_kw_desc_font_size'])? 17 : $style_options['bt_kw_desc_font_size']);
	
	?>
	<style>
	/*block to show*/
	.bluet_block_to_show{
	  display:none;	
	  max-width:400px;
	  z-index:9999;
	}
	
	/*for alt images tooltips*/
	.bluet_tooltip_alt{
		max-width: 250px;
		padding: 1px 5px;
		text-align: center;
		color: <?php echo $desc_color; ?> !important;
		background-color: <?php echo $desc_bg_color; ?> !important;
		position: absolute;
		border-radius: 4px;
	}
	
	/*generic style*/
	.bluet_tooltip {
		display: inline;
		text-decoration: none; 
		color: <?php echo $tooltip_color; ?> !important;
		<?php
		if(!$bt_kw_on_background){
			echo("background: ".$tooltip_bg_color." !important;");
		}
		?>
		padding: 1px 7px 4px 7px;
		font-size: 1em;
		border-radius: 18px;
	}
	
	.bluet_tooltip:hover
	{
			box-shadow: 12px 5px 25px 0px rgba(83, 69, 50, 0.67);
			bottom: 2px;
	}
	
	.bluet_block_to_show img
	{
			float:left;
			margin:0px 8px 8px 0;
			border: none !important;
			border-radius: 5px;
			height: auto;
	}
	
	.bluet_block_to_show
	{
		color: <?php echo $desc_color; ?> !important;
		background: <?php echo $desc_bg_color; ?> !important;
		border: 1px solid #6D6D6D;
		height: auto;
		border-radius: 6px;
		box-shadow: 4px 4px 10px #6A7563;
		padding: 13px;
		font-size:<?php echo $desc_font_size; ?>px !important;
		line-height: normal;
		font-weight: normal;
	}
	

	.bluet_block_to_show img
	{
		width:270px;
		
	}
	
	.bluet_block_to_show {
		  position: absolute;
		  width:300px;
	}
	
	.bluet_block_to_show:before {
	  content: '';
	  position: absolute;
	  bottom: 100%;
	  left: 50%;
	  margin-left: -12px;
	  width: 0; height: 0;
	  border-bottom: 9px solid #6D6D6D;
	  border-right: 12px solid transparent;
	  border-left: 12px solid transparent;
	}
	
	.bluet_block_to_show:after {
	  content: '';
	  position: absolute;
	  bottom: 100%;
	  left: 50%;
	  margin-left: -8px;
	  width: 0; height: 0;
	  border-bottom: 7px solid <?php echo $desc_bg_color; ?>;
	  border-right: 8px solid transparent;
	  border-left: 8px solid transparent;
	}
	
	.bluet-hide-excluded{
		display:none;
	}
	
	</style>
	<?php
}