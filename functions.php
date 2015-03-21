<?php
defined('ABSPATH') or die("No script kiddies please!");

//common functions
function bluet_kttg_tooltip_layout($term_title,$dfn,$img,$id,$bluet_kttg_show_glossary_link = null,$bluet_kttg_glossary_page = null){
	global $is_kttg_glossary_page;
//generates the HTML code of the tooltip model

$kttg_title_layout='';

//check if the hide title setting is checked to decide wether to show the title or not
	$kttg_tmp_title_setting=get_option( 'bluet_kw_settings' );
	$kttg_hide_title_setting=$kttg_tmp_title_setting['bt_kw_hide_title'];
	$button_prop='none';
	
	if(is_i_device()){
		$button_prop='block';
	}
	if($kttg_hide_title_setting!='on'){
		//dont make additional spaces inside this html tag 
		$kttg_title_layout='<span class="bluet_title_on_block">'.$term_title.'</span>';
	}
	
	$kttg_footer='';
	
	if(!$is_kttg_glossary_page and $bluet_kttg_show_glossary_link=="on" and $bluet_kttg_glossary_page!=""){
		//add a note at the footer of the tooltip	
		$kttg_footer='<a href="'.$bluet_kttg_glossary_page.'">'.__('View glossary','bluet-kw').'</a>';
	}
	
		$layout_ret='<span class="bluet_block_to_show" data-tooltip="'.$id.'">'
						.'<img src="'.plugin_dir_url(__FILE__).'assets/close.png" class="bluet_hide_tooltip_button" style="display:'.$button_prop.';"/>'
						.'<div class="bluet_block_container">'
							.'<div class="bluet_img_in_tooltip">'.$img.'</div>'
							.'<div class="bluet_text_content">'
								.$kttg_title_layout
								.wpautop($dfn)
							.'</div>'
							.'<div class="bluet_block_footer">'.$kttg_footer.'</div>'
						.'</div>'
				.'</span>';
				
	return $layout_ret;
}

function kttg_length_compare( $a, $b ) {
    return strlen($a)-strlen($b) ;
}


function is_i_device(){ 
//verify if the client is on a mac iphone ipod or ipad

	$user_agent     =   $_SERVER['HTTP_USER_AGENT'];
    $ret    =   false;
    $os_array       =   array(
                            '/iphone/i',
                            '/ipod/i',
                            '/ipad/i',
							'/android/i'
                        );

    foreach ($os_array as $regex) {
        if (preg_match($regex, $user_agent)) {
            $ret    =   true;
        }
    }   
    return $ret;
}
