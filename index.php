<?php
/*
Plugin Name: BleuT KeyWord ToolTip Generator
Description: this plugin lets you put illustrated keywords in a dictionary of terms tooltip (automatic generation) ...
Author: Jamel Zarga
Version: 2.0.2
Author URI: https://www.facebook.com/jameleddine.zarga
*/

require_once dirname( __FILE__ ) . '/keyword-posttype.php'; 
require_once dirname( __FILE__ ) . '/settings-page.php';
require_once dirname( __FILE__ ) . '/widget.php';
require_once dirname( __FILE__ ) . '/meta-boxes.php';

$bluet_kw_capability=apply_filters('bluet_kw_capability','manage_options');

register_activation_hook(__FILE__,'bluet_kw_activation');
function bluet_kw_activation(){
	$style_options=array();
	$style_options=array(
		'bt_kw_tt_color'=>'#fff',
		'bt_kw_tt_bg_color'=>'#ad7154',
		
		'bt_kw_desc_color'=>'#fff',
		'bt_kw_desc_bg_color'=>'#c0a599'
	);
	
	if(!get_option('bluet_kw_style')){
		add_option('bluet_kw_style',$style_options);
	}
	
	$consern_options=array();
	$consern_options=array(
		'bt_kw_for_posts'=>'on'
	);
	
	if(!get_option('bluet_kw_settings')){
		add_option('bluet_kw_settings',$consern_options);
	}
}

add_action('init',function(){
	load_plugin_textdomain('bluet-kw', false, dirname( plugin_basename( __FILE__ ) ).'/languages/');
});

//test	
add_action('init',function(){
	new bluet_keyword();
});
filter_any_content(array('the_content')); 

function filter_any_content($subject_hooks){
	foreach($subject_hooks as $hook){
		add_filter($hook,function($cont){
		
			$settings=get_option('bluet_kw_settings');
			
			if((is_single() and $settings['bt_kw_for_posts']) or (is_page() and $settings['bt_kw_for_pages'])){ 
				$my_keywords_ids=get_post_meta(get_the_id(),'bluet_matched_keywords',true);
				if(!empty($my_keywords_ids)){
					
					$my_keywords_terms=array();
					
					$wk_args=array(
						'post__in'=>$my_keywords_ids,
						'post_type'=>'my_keywords'
					);
					
					$the_wk_query = new WP_Query( $wk_args );

					if ( $the_wk_query->have_posts() ) {

						while ( $the_wk_query->have_posts() ) {
							$the_wk_query->the_post();
							
							$my_keywords_terms[]=array(
								'term'=>get_the_title(),
								'syns'=>get_post_meta(get_the_id(),'bluet_synonyms_keywords',true),
								'dfn'=>get_the_content(),
								'img'=>get_the_post_thumbnail(get_the_id(),'medium')
								);							
							
						}
						
					} else {
					}
					wp_reset_postdata();
						
							$regex='<\/?\w+((\s+\w+(\s*=\s*(?:".*?"|\'.*?\'|[^\'">\s]+))?)+\s*|\s*)\/?>';							
							$out=array();
							preg_match_all('#('.$regex.')#i',$cont,$out);
							$cont=preg_replace('#('.$regex.')#i','__tag__',$cont); //replace tags by __tag__							
						
						foreach($my_keywords_terms as $id=>$arr){
							$term=$arr['term'];
							
							if($arr['syns']!=""){
								$term.='|'.$arr['syns'];
							}

							$img=$arr['img'];
							$dfn=$arr['dfn'];
							
							$cont=preg_replace('#('.$term.')#i','__$1',$cont,1);
						}
						
						foreach($my_keywords_terms as $id=>$arr){
							$term=$arr['term'];
							
							if($arr['syns']!=""){
								$term.='|'.$arr['syns'];
							}

							$img=$arr['img'];
							$dfn=$arr['dfn'];
							
							$delimiter_1='<span class="bluet_tooltip" >$2<span>';
							$delimiter_2='<b>$2</b> : ';
							$delimiter_3='</span></span>';
							
							$cont=preg_replace('#(__('.$term.'))#i',$delimiter_1.''.$img.''.$delimiter_2.''.$dfn.''.$delimiter_3,$cont,1);
							$cont=preg_replace('(<img)','<img height="auto !important"',$cont);
						}
						
						foreach($out[0] as $id=>$tag){
							$cont=preg_replace('#(__tag__)#',$tag,$cont,1);
						}

					
				}
			}
			return $cont;
		});
	}
}

add_action('publish_my_keywords','regenerate_keywords');
add_action('publish_post','regenerate_keywords');
register_activation_hook( __FILE__,'regenerate_keywords');

function regenerate_keywords(){
	
	$all_kw_titles=array(); 
	
	$kw_args =array(
		'post_type'=>'my_keywords', 
		'posts_per_page'=>-1
	);
	$the_kw_query = new WP_Query( $kw_args );
	
	
	if ( $the_kw_query->have_posts() ) {
		while ( $the_kw_query->have_posts() ) {
			$the_kw_query->the_post();
			
			//
			$all_kw_titles[get_the_id()]='('.get_the_title().')i';
		}
	}	
	
	wp_reset_postdata();

	$post_have_kws=array(); 
	
	$args =array(
		'post_type'=>array('post','page'), 
		'posts_per_page'=>-1
	);
	$the_posts_query = new WP_Query( $args );
	
	
	if ( $the_posts_query->have_posts() ) {
		while ( $the_posts_query->have_posts() ) {
			$the_posts_query->the_post();			
				foreach($all_kw_titles as $term_id=>$term){
					$post_meta='les_ingredients';
					if(preg_match($term,strip_tags(get_the_content()))){ 
						$post_have_kws[get_the_id()][]=$term_id;
					}				
						
				}


		}
	}	
	wp_reset_postdata();	

		if(!get_option('bluet_post_have_kws')){ 
			add_option('bluet_post_have_kws');
		}
		
		update_option('bluet_post_have_kws',$post_have_kws);
		
		foreach($post_have_kws as $post_id=>$kws_ids){
			update_post_meta($post_id,'bluet_matched_keywords',$kws_ids);
		}
		
		$Kw_have_posts=bluet_Kw_posts_related();
		
		bluet_kw_reset_meta();
		foreach($Kw_have_posts as $kw_id=>$posts_ids){
			$nbr_posts_related=count($posts_ids);
			update_post_meta($kw_id,'bluet_posts_in_concern',$nbr_posts_related);
		}
}

function bluet_Kw_posts_related(){
	$post_have_kws=get_option('bluet_post_have_kws');
	$Kw_have_posts=array();
	$max_related=0;
	
	if(!empty($post_have_kws)){

		foreach($post_have_kws as $post_id=>$kws){
			foreach($kws as $kw){
				$Kw_have_posts[$kw][]=$post_id;
			}
		}
		
		foreach($Kw_have_posts as $kposts){
			$tmp_count=count($kposts);
			if($tmp_count>$max_related){
				$max_related=$tmp_count;
			}
		}
	}
	$Kw_have_posts['max_related_posts']=$max_related;
	
	return $Kw_have_posts;
}

function bluet_kw_reset_meta(){
	$kw_args =array(
		'post_type'=>'my_keywords', 
		'posts_per_page'=>-1
	);
	$the_kw_query = new WP_Query( $kw_args );
	
	
	if ( $the_kw_query->have_posts() ) {
		while ( $the_kw_query->have_posts() ) {
			$the_kw_query->the_post();
			
			update_post_meta(get_the_id(),'bluet_posts_in_concern',0); 
		}
	}	
	
	wp_reset_postdata();
}