<?php
defined('ABSPATH') or die("No script kiddies please!");

//common functions

function bluet_kttg_tooltip_layout($term_title,$dfn,$img,$id){
//generates the HTML code of the tooltip model

$kttg_title_layout='';

//check if the hide title setting is checked to decide wether to show the title or not
	$kttg_tmp_title_setting=get_option( 'bluet_kw_settings' );
	$kttg_hide_title_setting=$kttg_tmp_title_setting['bt_kw_hide_title'];

	if($kttg_hide_title_setting!='on'){
		//dont make additional spaces inside this html tag 
		$kttg_title_layout='<span class="bluet_title_on_block">'.$term_title.'</span>';
	}

		$layout_ret='<span class="bluet_block_to_show" data-tooltip="'.$id.'">'
					.'<div class="bluet_block_container">'
						.'<div class="bluet_img_in_tooltip">'.$img.'</div>'
						.'<div class="bluet_text_content">'
							.$kttg_title_layout
							.wpautop($dfn)
						.'</div>'
					.'</div>'
				.'</span>';
				
	return $layout_ret;
}

function kttg_length_compare( $a, $b ) {
    return strlen($a)-strlen($b) ;
}
