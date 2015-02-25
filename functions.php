<?php
defined('ABSPATH') or die("No script kiddies please!");

//common functions

function bluet_kttg_tooltip_layout($name,$dfn,$img,$id){
//generates the HTML code of the tooltip model

	$layout_ret='<span class="bluet_block_to_show" data-tooltip="'.$id.'">'
					.'<div class="bluet_block_container">'
						.'<div class="bluet_img_in_tooltip">'.$img.'</div>'
						.'<div class="bluet_text_content">'
							.'<span class="bluet_title_on_block">'
							.$name
							.'</span>'
							.wpautop($dfn)
						.'</div>'
					.'</div>'
				.'</span>';
				
	return $layout_ret;
}

function kttg_length_compare( $a, $b ) {
    return strlen($a)-strlen($b) ;
}
