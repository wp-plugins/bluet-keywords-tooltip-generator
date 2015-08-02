<?php
defined('ABSPATH') or die("No script kiddies please!");

//common functions
function bluet_kttg_tooltip_layout($term_title,$dfn,$img,$id,$bluet_kttg_show_glossary_link = null,$bluet_kttg_glossary_page = null,$glossary_link_label=null){
	global $is_kttg_glossary_page;
//generates the HTML code of the tooltip model

$kttg_title_layout='';

//check if the hide title setting is checked to decide wether to show the title or not
	$kttg_tmp_title_setting=get_option( 'bluet_kw_settings' );
	if(empty($kttg_tmp_title_setting['bt_kw_hide_title'])){
		$kttg_title_layout='<span class="bluet_title_on_block">'.$term_title.'</span>';
	}
	
	$kttg_footer='';
	
	if(!$is_kttg_glossary_page and $bluet_kttg_show_glossary_link=="on" and $bluet_kttg_glossary_page!=""){

		//add a note at the footer of the tooltip	
		$kttg_footer='<a href="'.$bluet_kttg_glossary_page.'">'.$glossary_link_label.'</a>';
	}
	
		$layout_ret='<span class="bluet_block_to_show" data-tooltip="'.$id.'">'
						.'<img src="'.plugin_dir_url(__FILE__).'assets/close.png" class="bluet_hide_tooltip_button" />'
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
	 return true;
 }

function kttg_get_related_keywords($my_post_id){
	//return an array of related keywords of the current post
		//delete this function for optimization !
	
	global $more;

	//fetch terms
	$all_kw_titles=array(); //will contains keywords names with IDs ready for preg_match
	
	$kw_args =array(
		'post_type'=>'my_keywords', //to receive only keywords
		'posts_per_page'=>-1
	);
	$the_kw_query=get_posts($kw_args);
	//$the_kw_query = new WP_Query( $kw_args );	

	// The Loop to get all the keywords and its syns of the site in $all_kw_titles
		foreach($the_kw_query as $kw_post){
			//
			$syn=get_post_meta($kw_post->ID,'bluet_synonyms_keywords',true);
			
			//verify if prefix
			$is_prefix=false;
			$kw_after='';
			
			if(function_exists('bluet_prefix_metabox')){
				if(get_post_meta($kw_post->ID,'bluet_prefix_keywords',true)){
					$is_prefix=true;
				}
			}
			
			if($is_prefix){ $kw_after='\w*'; }
			
			if(!empty($syn)){
				$syn='|'.$syn.''.$kw_after;
			}	

			//change unmatchable apostrophe			
			//$term_title=str_replace("&#8217;","'",get_the_title());

			$term_title=$kw_post->post_title;
			/*test japanese and chinese*/
			$text_sep="(\W)";
			$japanese_chinese="/[\x{3000}-\x{303F}]|[\x{3040}-\x{309F}]|[\x{30A0}-\x{30FF}]|[\x{FF00}-\x{FFEF}]|[\x{4E00}-\x{9FAF}]|[\x{2605}-\x{2606}]|[\x{2190}-\x{2195}]|\x{203B}/u";
			
			if(preg_match($japanese_chinese,$term_title)==1){
				//change pattern if japanese or chinese text
				$text_sep=""; //no separator for japanese and chinese
			}

			$all_kw_titles[$kw_post->ID]='/'.$text_sep.'('.$term_title.''.$kw_after.''.$syn.')'.$text_sep.'/iu';				
		}
	

	/*get all posts (but not post type keywords)*/	
	//$posttypes_to_match=array('post','page');//initial posttypes to match
	
	//$posttypes_to_match=apply_filters('bluet_kttg_posttypes_to_match',$posttypes_to_match);

	//init the post keywords related to zero
	$post_have_kws=array();
	$content_to_check='';
	//set terms for each post , it can be changed to custom fields
	foreach($all_kw_titles as $term_id=>$term){		
		$more=1; //to make the <!--more--> tag return the hole content of the post

		//look for the $term in the content (### do something here to support custom fields)
		$content_to_check=' '.get_post($my_post_id)->post_content;

		if(function_exists('bluet_kttg_add_meta_to_check')){
			$content_to_check.=bluet_kttg_add_meta_to_check($my_post_id);
		}
		
		$content_to_check=strip_tags($content_to_check);//strip_tags eliminates HTML tags before passing in pregmatch

		
		if(preg_match($term,$content_to_check)){ 
			$post_have_kws[]=$term_id;
		}										
	}

	return $post_have_kws;
}

function kttg_get_related_posts($my_post_id){
	//return an array of related posts of the current keyword
}
function elim_apostrophes($chaine){
	//pour éliminé les apostrophes unicode echanger par apostrophe ascii	
	$resultat=str_replace("&#8217;","'",$chaine);

	return $resultat;
}