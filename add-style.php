<?php
defined('ABSPATH') or die("No script kiddies please!");

function bluet_kw_custom_style(){
	
	if(function_exists('bluet_kttg_pro_addon')){//if pro addon activated
	
		$adv_options=get_option('bluet_kw_advanced');
		$apply_custom_style_sheet=$adv_options['bt_kw_adv_style']['apply_custom_style_sheet'];
		
		/*
			If apply custom sheet is activated so don't load this style file
			
		*/
		if($apply_custom_style_sheet){
			return false;
		}
		
	}
	
	$style_options=get_option('bluet_kw_style');
	
	$tooltip_color=$style_options['bt_kw_tt_color'];
	$tooltip_bg_color=$style_options['bt_kw_tt_bg_color'];
	$bt_kw_on_background=$style_options['bt_kw_on_background'];
	
	$desc_color=$style_options['bt_kw_desc_color'];
	$desc_bg_color=$style_options['bt_kw_desc_bg_color'];
	
	$desc_font_size=(empty($style_options['bt_kw_desc_font_size'])? 17 : $style_options['bt_kw_desc_font_size']);
	
	$is_important="";
	
	if(!is_admin()){ 
		$is_important=" !important";
	}
	?>
	<style>

	/*for alt images tooltips*/
	.bluet_tooltip_alt{
		max-width: 250px;
		padding: 1px 5px;
		text-align: center;
		color: <?php echo $desc_color; ?> <?php echo($is_important)?>;
		background-color: <?php echo $desc_bg_color; ?> <?php echo($is_important)?>;
		position: absolute;
		border-radius: 4px;
		z-index:9999;
	}
	
	.bluet_tooltip{
		text-decoration: none; 
		color: <?php echo $tooltip_color; ?> <?php echo($is_important)?>;
		
		<?php
		if(!$bt_kw_on_background){
			echo("background: ".$tooltip_bg_color." ".$is_important.";");
			
			echo("padding: 1px 5px 3px 5px;");
			echo("font-size: 1em;");
		}else{
			echo("border-bottom: 1px dotted;");
			echo("border-bottom-color: ".$tooltip_color.";");
		}
		?>
	}
	
	.bluet_block_to_show{
		display:none;	
		max-width:300px;
		z-index:9999;
		padding:10px;
		
		position: absolute;
		height: auto;
	}
	.bluet_block_container{		  
		color: <?php echo $desc_color; ?> <?php echo($is_important)?>;
		background: <?php echo $desc_bg_color; ?> <?php echo($is_important)?>;
		border-radius: 2px;
		box-shadow: 0px 0px 10px #717171 <?php echo($is_important)?>;
		font-size:<?php echo $desc_font_size; ?>px <?php echo($is_important)?>;
		line-height: normal;
		font-weight: normal;
		display:inline-block;
		width:inherit;
	}
	
	.bluet_img_in_tooltip{	
		border-radius: inherit;
	}

	.bluet_img_in_tooltip img
	{
		float:left;
		margin-bottom:8px;
		border: none !important;
		border-radius: inherit;
		width:100%;
		height: auto;
		margin-bottom: 0px;
	}
	
	.bluet_text_content{
		padding:6px 10px 6px 10px;		
	}
	.bluet_block_to_show:after {
	  content: '';
	  position: absolute;
	  left: 50%;
	  margin-left: -8px;
	  width: 0;
	  height: 0;
	  border-right: 8px solid transparent;
	  border-left: 8px solid transparent;
	}
	
	.kttg_arrow_show_bottom:after{
		top:3px;
		border-bottom: 7px solid <?php echo $desc_bg_color; ?>;
	}
	
	.kttg_arrow_show_top:after{
		bottom: 5px;
		border-top: 7px solid <?php echo $desc_bg_color; ?>;
	}
	
	.kttg_arrow_show_right:after{
		bottom: 5px;
		border-top: 7px solid <?php echo $desc_bg_color; ?>;
	}
	
	.kttg_arrow_show_left:after{
		bottom: 5px;
		border-top: 7px solid <?php echo $desc_bg_color; ?>;
	}
	
	.bluet-hide-excluded{
		display:none;
	}
	
	.bluet_title_on_block{
		text-transform: capitalize;
		font-weight: bold;
	}
	
	/* Glossary style */
	span.bluet_glossary_letter a {
		text-decoration: none;
		padding: 3px;
		background-color: beige;
		border-radius: 3px;
	}
	
	.bluet_glossary_all a {
		text-decoration: none;
		padding: 3px;
		background-color: bisque;
		font-weight: bold;
		border-radius: 3px;
	}
	.bluet_glossary_letter_count {
		vertical-align: super;
		font-size: 70%;
		color: crimson;
		padding-left: 2px;
	}
	.bluet_glossary_found_letter{
		font-weight: bold;
	}
        span.kttg_glossary_nav {
                background-color: bisque;
                padding: 5px;
                margin-left: 5px;
        }
	
	/* hide button */
	.bluet_hide_tooltip_button{
		width: 25px;
		height: 25px;
		position: absolute;
		right: -4px;
		top: -2px;
	}
	</style>
	<?php
}