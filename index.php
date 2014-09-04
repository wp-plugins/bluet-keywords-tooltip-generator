<?php
/*
Plugin Name: BleuT KeyWord ToolTip Generator
Description: this plugin lets you put illustrated keywords in a dictionary of terms tooltip (automatic generation) ...
Author: Jamel Zarga
Version: 2.0.1
Author URI: https://www.facebook.com/jameleddine.zarga
*/

require_once dirname( __FILE__ ) . '/keyword-posttype.php'; //contain the class that handles the new custom post
require_once dirname( __FILE__ ) . '/settings-page.php';
require_once dirname( __FILE__ ) . '/widget.php';

$bluet_kw_capability=apply_filters('bluet_kw_capability','manage_options');



/*init settings*/
register_activation_hook(__FILE__,'bluet_kw_activation');
function bluet_kw_activation(){
	$style_options=array();
	//initialise style option if empty
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
	//initialise style option if empty
	$consern_options=array(
		'bt_kw_for_posts'=>'on'
	);
	
	if(!get_option('bluet_kw_settings')){
		add_option('bluet_kw_settings',$consern_options);
	}
}

/**** localization ****/
add_action('init',function(){
	load_plugin_textdomain('bluet-kw', false, dirname( plugin_basename( __FILE__ ) ).'/languages/');
});

//test	
add_action('init',function(){
	new bluet_keyword();
});
//call add filter for all hooks in need
	//you can pass cutom hooks you'v done
filter_any_content(array('the_content')); //'other content hook' if needed

function filter_any_content($subject_hooks){
	foreach($subject_hooks as $hook){
		add_filter($hook,function($cont){
		
			$settings=get_option('bluet_kw_settings');
			
			if((is_single() and $settings['bt_kw_for_posts']) or (is_page() and $settings['bt_kw_for_pages'])){ 
				//depends on option settings 
				/*
				 * begin
				 */

				
				/*
				 * end
				 */
				if(get_option('bluet_post_have_kws')){
					$my_keywords_ids=get_option('bluet_post_have_kws');
					
					$my_keywords_ids=$my_keywords_ids[get_the_id()];
					
					$my_keywords_terms=array();
					
					// The Query
					$wk_args=array(
						'post__in'=>$my_keywords_ids,
						'post_type'=>'my_keywords'
					);
					
					$the_wk_query = new WP_Query( $wk_args );

					// The Loop
					if ( $the_wk_query->have_posts() ) {

						while ( $the_wk_query->have_posts() ) {
							$the_wk_query->the_post();
							
							$my_keywords_terms[]=array(
								'term'=>get_the_title(),
								'dfn'=>get_the_content(),
								'img'=>get_the_post_thumbnail(get_the_id(),'medium')
								);
						}
						
					} else {
						// no posts found
					}
					/* Restore original Post Data */
					wp_reset_postdata();
						
						// first preg replace to eliminate html tags 						
							$regex='<\/?\w+((\s+\w+(\s*=\s*(?:".*?"|\'.*?\'|[^\'">\s]+))?)+\s*|\s*)\/?>';							
							$out=array();
							preg_match_all('#('.$regex.')#i',$cont,$out);
							$cont=preg_replace('#('.$regex.')#i','__tag__',$cont); //replace tags by __tag__							
						//end
						
						/*tow loops montioned here to avoid overlapping (chevauchement) */
						foreach($my_keywords_terms as $id=>$arr){
							$term=$arr['term'];
							$img=$arr['img'];
							$dfn=$arr['dfn'];
							
							$cont=preg_replace('#('.$term.')#i','__$1',$cont,1);
						}
						
						foreach($my_keywords_terms as $id=>$arr){
							$term=$arr['term'];
							$img=$arr['img'];
							$dfn=$arr['dfn'];
							
							$delimiter_1='<span class="bluet_tooltip" >$2<span>';
							$delimiter_2='<b>$2</b> : ';
							$delimiter_3='</span></span>';
							
							$cont=preg_replace('#(__('.$term.'))#i',$delimiter_1.''.$img.''.$delimiter_2.''.$dfn.''.$delimiter_3,$cont,1);
							$cont=preg_replace('(<img)','<img height="auto !important"',$cont);
						}
						
						//rendre les tag HTML
						foreach($out[0] as $id=>$tag){
							$cont=preg_replace('#(__tag__)#',$tag,$cont,1);
						}

					
				}
			}
			return $cont;
		});
	}
}

//pour traiter les termes lors de l'ajout d'un nouveau terme ou nouveau post (keyword) publish_{my_keywords}
add_action('publish_my_keywords','regenerate_keywords');
add_action('publish_post','regenerate_keywords');

function regenerate_keywords(){
	/*do some thing here to refetch the terms (wp_query)*/
	
	//fetch terms
	$all_kw_titles=array(); //will contains keywords names with IDs ready for preg_match
	
	$kw_args =array(
		'post_type'=>'my_keywords', //to recieve only keywords
		'posts_per_page'=>-1
	);
	$the_kw_query = new WP_Query( $kw_args );
	
	
	// The Loop
	if ( $the_kw_query->have_posts() ) {
		while ( $the_kw_query->have_posts() ) {
			$the_kw_query->the_post();
			
			//
			$all_kw_titles[get_the_id()]='('.get_the_title().')i';
		}
	}	
	//test	
	
	/* Restore original Post Data */
	wp_reset_postdata();

	$post_have_kws=array(); //the variable to save earlier
	/*get all posts (but not post type keywords)*/
	
	$args =array(
		'post_type'=>array('post','page'), //to recieve only posts
		'posts_per_page'=>-1
	);
	$the_posts_query = new WP_Query( $args );
	
	
	// The Loop
	if ( $the_posts_query->have_posts() ) {
		while ( $the_posts_query->have_posts() ) {
			$the_posts_query->the_post();			
			//
				//set terms for each post , it can be changed to custom fields
				foreach($all_kw_titles as $term_id=>$term){
				//get_post_meta(get_the_id(), "les_ingredients",true);
					$post_meta='les_ingredients';
					// to test post meta :  or preg_match($term,get_post_meta(get_the_id(),$post_meta,true))){			 //to fetch only in content here by get_the_content()
					if(preg_match($term,strip_tags(get_the_content()))){ //strip_tags eliminates HTML tags before passing in pregmatch
						$post_have_kws[get_the_id()][]=$term_id;
					}				
						
				}


		}
	}	
	/* Restore original Post Data */
	wp_reset_postdata();	

		if(!get_option('bluet_post_have_kws')){ //option contains result ...
			add_option('bluet_post_have_kws');
		}
		
		update_option('bluet_post_have_kws',$post_have_kws);
}
