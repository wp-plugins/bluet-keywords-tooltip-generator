<?php
defined('ABSPATH') or die("No script kiddies please!");

//common functions

function bluet_kttg_tooltip_layout($name,$dfn,$img,$id){
//generates the HTML code of the tooltip model

	$layout_ret='<span class="bluet_block_to_show" data-tooltip="'.$id.'">'
					.'<span class="bluet_block_container">'
						.'<span class="bluet_img_in_tooltip">'.$img.'</span>'
						.'<span class="bluet_title_on_block">'
						.$name
						.'</span>'
						.$dfn
					.'</span>'
				.'</span>';
				
	return $layout_ret;
}