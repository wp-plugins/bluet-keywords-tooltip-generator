<?php
/*
Plugin Name: BleuT KeyWords ToolTip Generator
Description: This plugin allows you automatically create tooltip boxes for your technical keywords in order to explain them for your site visitors making surfing more comfortable.
Author: Jamel Zarga
Version: 2.4.9.5
Author URI: http://www.blueskills.net/about-us
*/
defined('ABSPATH') or die("No script kiddies please!");

require_once dirname( __FILE__ ) . '/keyword-posttype.php'; //contain the class that handles the new custom post
require_once dirname( __FILE__ ) . '/settings-page.php';
require_once dirname( __FILE__ ) . '/widget.php';
require_once dirname( __FILE__ ) . '/meta-boxes.php';
require_once dirname( __FILE__ ) . '/glossary-shortcode.php';
require_once dirname( __FILE__ ) . '/functions.php';




$bluet_kw_capability=apply_filters('bluet_kw_capability','manage_options');

/*init settings*/
register_activation_hook(__FILE__,'bluet_kw_activation');
register_activation_hook( __FILE__,'bluet_kttg_regenerate_keywords');

/**** localization ****/
add_action('init',function(){
	load_plugin_textdomain('bluet-kw', false, dirname( plugin_basename( __FILE__ ) ).'/languages/');
	
	//load jQuery once to avoid conflict
	wp_enqueue_script('jquery');
});

add_action( 'wp_head', 'bluet_kw_load_scripts_front' );

//create posttype for keywords	
add_action('init',function(){
	//instenciate the post type
		new bluet_keyword();
});

add_action('wp_footer','bluet_kttg_place_tooltips');
add_action('admin_footer','bluet_kttg_place_tooltips');

function bluet_kttg_place_tooltips(){
	$kttg_sttings_options=get_option('bluet_kw_settings');
	$kttg_tooltip_position=$kttg_sttings_options["bt_kw_position"];
	?>
	<script type="text/javascript">
		jQuery(document).ready(function(){
			bluet_placeTooltips(".bluet_tooltip, .bluet_img_tooltip","<?php echo($kttg_tooltip_position); ?>");	 
			moveTooltipElementsTop(".bluet_block_to_show");
		})
	</script>
	<?php
}
	//call add filter for all hooks in need
	//you can pass cutom hooks you've done
	//(### do something here to support custom fields)
add_action('wp_head',function(){
	$contents_to_filter=array('the_content');
	
	$option=get_option('bluet_kw_advanced');
	
	if($option[bt_kw_match_custom_field]!=""){
		foreach(explode("|",$option[bt_kw_match_custom_field]) as $k=>$val){
			$contents_to_filter[]=$val;
		}
	}


	bluet_kttg_filter_any_content($contents_to_filter);
}); //'other content hook' if needed

//pour traiter les termes lors de l'activation de l'ajout d'un nouveau terme ou nouveau post (keyword) publish_{my_keywords}

add_action('publish_my_keywords','bluet_kttg_regenerate_keywords');
add_action('publish_post','bluet_kttg_regenerate_keywords');
add_action('publish_page','bluet_kttg_regenerate_keywords');
add_action('trashed_post','bluet_kttg_regenerate_keywords');

//Functions


/* enqueue js functions for the front side*/
function bluet_kw_load_scripts_front() {
	//
	wp_enqueue_script( 'kttg-tooltips-functions-script', plugins_url('assets/kttg-tooltip-functions.js',__FILE__), array(), false, true );
		
	$opt_tmp=get_option('bluet_kw_style');
	if($opt_tmp['bt_kw_alt_img']=='on'){
		//
		wp_enqueue_script( 'kttg-functions-alt-img-script', plugins_url('assets/img-alt-tooltip.js',__FILE__), array(), false, true );
	}
}

function bluet_kw_activation(){
	$style_options=array();
	
	//initialise style option if bluet_kw_style is empty
	$style_options=array(
		'bt_kw_tt_color'=>'#dd3333',
		'bt_kw_tt_bg_color'=>'#67d3b3',
		
		'bt_kw_desc_color'=>'#fff',
		'bt_kw_desc_bg_color'=>'#5eaa0d',
		
		'bt_kw_desc_font_size'=>'17',
		
		'bt_kw_on_background' =>'on'
	);
	
	if(!get_option('bluet_kw_style')){
		add_option('bluet_kw_style',$style_options);
	}
	
	$settings_options=array();
	//initialise settings option if empty
	$settings_options=array(
		'bt_kw_for_posts'=>'on',
		'bt_kw_match_all'=>'on',
		'bt_kw_position'=>'bottom'		
	);
	
	if(!get_option('bluet_kw_settings')){
		add_option('bluet_kw_settings',$settings_options);
	}
}

function bluet_kttg_filter_any_content($subject_hooks){
	/////////////////////////////
	
	foreach($subject_hooks as $hook){
		add_filter($hook,function($cont){
			
		$bluet_kttg_show_glossary_link=get_option('bluet_kw_settings');
		
		$bluet_kttg_show_glossary_link=$bluet_kttg_show_glossary_link['bluet_kttg_show_glossary_link'];
		$bluet_kttg_glossary_page=get_option('bluet_kttg_glossary_page');

			$exclude_me = get_post_meta(get_the_id(),'bluet_exclude_post_from_matching',true);
			//if the current post tells us to exclude from fetch
			if($exclude_me) return $cont;
			
			$settings=get_option('bluet_kw_settings');

			//var dans la quelle on cache les tooltips à afficher
			$html_tooltips_to_add='<div class="my_tooltips_in_block">';
			
			//verifying custom postes to filter			
			if(function_exists('bluet_filter_this_custom_post_type')){
				$filter_this_custom_post_type=bluet_filter_this_custom_post_type(get_the_id());
			}else{
				$filter_this_custom_post_type=false;				
			}
			
			if( 
				(is_single() and (((get_post_type(get_the_id())=='post') and $settings['bt_kw_for_posts'])
									or ((get_post_type(get_the_id())!='post') and $filter_this_custom_post_type))
				)
				or(is_page() and $settings['bt_kw_for_pages'])
			){ 

				$my_keywords_ids=get_post_meta(get_the_id(),'bluet_matched_keywords',true);
				
				//if user specifies keywords to match
				$bluet_matching_keywords_field=get_post_meta(get_the_id(),'bluet_matching_keywords_field',true);
				if(!empty($bluet_matching_keywords_field)){
					$my_keywords_ids=$bluet_matching_keywords_field;
				}                                    
                                
                global $is_kttg_glossary_page;
                                    
				if(!empty($my_keywords_ids) OR $is_kttg_glossary_page){
					
					$my_keywords_terms=array(); 
                                        
					//looking in all occurences if glossary page
					if($is_kttg_glossary_page){
						$post_in=array();
					}else{
						$post_in=$my_keywords_ids;
					}
                                        
					// The Query                                                                          
					$wk_args=array(
						'post__in'=>$post_in,
						'post_type'=>'my_keywords',
						'posts_per_page'=>-1	//to retrieve all keywords
					);
					
					$the_wk_query = new WP_Query( $wk_args );

					// The Loop
					if ( $the_wk_query->have_posts() ) {

						while ( $the_wk_query->have_posts() ) {
							$the_wk_query->the_post();
							
							if(get_the_title()!=""){ //to prevent untitled keywords
								$tmp_array_kw=array(
									'kw_id'=>get_the_id(),
									'term'=>get_the_title(),
									'case'=>false,
									'pref'=>false,
									'syns'=>get_post_meta(get_the_id(),'bluet_synonyms_keywords',true),									
									'dfn'=>get_the_content(),
									'img'=>get_the_post_thumbnail(get_the_id(),'medium')
								);
								
								if(get_post_meta(get_the_id(),'bluet_case_sensitive_word',true)=="on"){
									$tmp_array_kw['case']=true;
								}
								
								//if prefix addon activated
								if(function_exists('bluet_prefix_metabox')){
									if(get_post_meta(get_the_id(),'bluet_prefix_keywords',true)=="on"){
										$tmp_array_kw['pref']=true;
									}
								}

								$my_keywords_terms[]=$tmp_array_kw;
							}							
							
						}
						
					}
					
					/* Restore original Post Data */
					wp_reset_postdata();
						
						// first preg replace to eliminate html tags 						
							$regex='<\/?\w+((\s+\w+(\s*=\s*(?:".*?"|\'.*?\'|[^\'">\s]+))?)+\s*|\s*)\/?>';							
							$out=array();
							preg_match_all('#('.$regex.')#i',$cont,$out);
							$cont=preg_replace('#('.$regex.')#i','**T_A_G**',$cont); //replace tags by **T_A_G**							
						//end
						
						$limit_match=($settings['bt_kw_match_all']=='on'? -1 : 1);
						
						/*tow loops montioned here to avoid overlapping (chevauchement) */
						foreach($my_keywords_terms as $id=>$arr){
							$term=$arr['term'];
							
							//concat synonyms if they are not empty
							if($arr['syns']!=""){
								$term.='|'.$arr['syns'];
							}

							$is_prefix=$arr['pref'];

							if(function_exists('bluet_prefix_metabox') and $is_prefix){
									$kw_after='\w*';
							}else{
								$kw_after='';
							}
							
							$term_and_syns_array=explode('|',$term);
		
							//sort keywords by string length in the array (to match them properly)
							usort($term_and_syns_array,'kttg_length_compare');
							
							//verify if case sensitive
							if($arr['case']){
								$kttg_case_sensitive='';
							}else{
								$kttg_case_sensitive='i';
							}							
							
							foreach($term_and_syns_array as $temr_occ){
								$cont=preg_replace('#((\W)('.$temr_occ.''.$kw_after.')(\W))#'.$kttg_case_sensitive,'$2__$3__$4',$cont,$limit_match);
							}					

						}
						foreach($my_keywords_terms as $id=>$arr){
							$term=$arr['term'];
							
							//concat synonyms if they are not empty
							if($arr['syns']!=""){
								$term.='|'.$arr['syns'];
							}

							$img=$arr['img'];
							$dfn=$arr['dfn'];
							$is_prefix=$arr['pref'];

							if(function_exists('bluet_prefix_metabox') and $is_prefix){
									$kw_after='\w*';
							}else{
								$kw_after='';
							}		
							
							if($dfn!=""){
								$dfn=$arr['dfn'];
							}
							
							$html_to_replace='<span class="bluet_tooltip" data-tooltip="'.$arr["kw_id"].'">$2</span>';
							
							$term_and_syns_array=explode('|',$term);

							$kttg_term_title=$term_and_syns_array[0];
						
							$html_tooltips_to_add.=bluet_kttg_tooltip_layout(
													$kttg_term_title 	//title
													,$dfn				//content def
													,$img				//image
													,$arr["kw_id"]		//id
													,$bluet_kttg_show_glossary_link	//show glossary link y/n
													,$bluet_kttg_glossary_page		//glossary page permalink
													);
							
							//verify if case sensitive
							if($arr['case']){
								$kttg_case_sensitive='';
							}else{
								$kttg_case_sensitive='i';
							}								
							foreach($term_and_syns_array as $temr_occ){
								$cont=preg_replace('#(__('.$temr_occ.''.$kw_after.')__)#'.$kttg_case_sensitive,$html_to_replace,$cont,-1);
							}
						}
						
						//Reinsert tag HTML elements
						foreach($out[0] as $id=>$tag){						
							$cont=preg_replace('#(\*\*T_A_G\*\*)#',$tag,$cont,1);
						}
						
						//prevent HTML Headings (h1 h2 h3) to be matched
						$regH='(<h[1-3]+>.*)(class="bluet_tooltip")(.*<\/h[1-3]+>)';						
						$cont=preg_replace('#('.$regH.')#i','$2$4',$cont);					
				}
			}			
			
			$html_tooltips_to_add.="</div>";
			
			$cont=$html_tooltips_to_add.$cont;
			return $cont;
		},100);
	}
}

function bluet_kttg_regenerate_keywords(){
	
	//fetch terms
	$all_kw_titles=array(); //will contains keywords names with IDs ready for preg_match
	
	$kw_args =array(
		'post_type'=>'my_keywords', //to receive only keywords
		'posts_per_page'=>-1
	);
	$the_kw_query = new WP_Query( $kw_args );
	
	
	// The Loop to get all the keywords and its syns of the site in $all_kw_titles
	if ( $the_kw_query->have_posts() ) {
		while ( $the_kw_query->have_posts() ) {
			$the_kw_query->the_post();			

			//
			$syn=get_post_meta(get_the_id(),'bluet_synonyms_keywords',true);
			
			//verify if prefix
			$is_prefix=false;
			$kw_after='';
			
			if(function_exists('bluet_prefix_metabox')){
				if(get_post_meta(get_the_id(),'bluet_prefix_keywords',true)){
					$is_prefix=true;
				}
			}
			
			if($is_prefix){ $kw_after='\w*'; }
			
			if(!empty($syn)){
				$syn='|'.$syn.''.$kw_after;
			}			
			
			$all_kw_titles[get_the_id()]='((\W)('.get_the_title().''.$kw_after.''.$syn.')(\W))i';			
		}
	}	
	
	/* Restore original Post Data */
	wp_reset_postdata();

	$post_have_kws=array(); //the variable to save earlier
	
	/*get all posts (but not post type keywords)*/	
	$posttypes_to_match=array('post','page');//initial posttypes to match
	
	$posttypes_to_match=apply_filters('bluet_kttg_posttypes_to_match',$posttypes_to_match);

	$args =array(
		'post_type'=>$posttypes_to_match, //to receive only posts and pages (### do something here to support custom post types)
		'posts_per_page'=>-1
	);
	$the_posts_query = new WP_Query( $args );
		
	// The Loop
	if ( $the_posts_query->have_posts() ) {
		while ( $the_posts_query->have_posts() ) {
			$the_posts_query->the_post();			
			
			//init the post keywords related to zero
			$post_have_kws[get_the_id()]=array();
			
				//set terms for each post , it can be changed to custom fields
				foreach($all_kw_titles as $term_id=>$term){
				//get_post_meta(get_the_id(), "les_ingredients",true);
				
					//look for the $term in the content (### do something here to support custom fields)
					$content_to_check=' '.get_the_content();

					if(function_exists('bluet_kttg_add_meta_to_check')){
						$content_to_check.=bluet_kttg_add_meta_to_check();
					}
					$content_to_check=strip_tags($content_to_check);//strip_tags eliminates HTML tags before passing in pregmatch

					if(preg_match($term,$content_to_check)){ 
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
		
		/* begin -- save in post meta bluet_matched_keywords */
		foreach($post_have_kws as $post_id=>$kws_ids){
			update_post_meta($post_id,'bluet_matched_keywords',$kws_ids);
		}
		/*end*/
		
		/* begin -- save in post meta bluet_matched_keywords */
		$Kw_have_posts=bluet_Kw_posts_related();
		
		//init every post_meta 'bluet_posts_in_concern' to zero
		bluet_kw_reset_meta();
		
		//process
		foreach($Kw_have_posts as $kw_id=>$posts_ids){
			$nbr_posts_related=count($posts_ids);
			update_post_meta($kw_id,'bluet_posts_in_concern',$nbr_posts_related);
		}
		/*end*/
}
					
function bluet_Kw_posts_related(){
	//returns an array of posts related with the current keyword
	$post_have_kws=get_option('bluet_post_have_kws');
	$Kw_have_posts=array();
	$max_related=0;
	
	//calculate the posts related
	if(!empty($post_have_kws)){

		foreach($post_have_kws as $post_id=>$kws){
			foreach($kws as $kw){
				$Kw_have_posts[$kw][]=$post_id;
			}
		}
		
		//look for max related
		foreach($Kw_have_posts as $kposts){
			$tmp_count=count($kposts);
			if($tmp_count>$max_related){
				$max_related=$tmp_count;
			}
		}
	}
	//to prevent zero devision
	if($max_related==0){
		$max_related=1;
	}		
				
	$Kw_have_posts['max_related_posts']=$max_related;
	
	return $Kw_have_posts;
}

function bluet_kw_reset_meta(){
	$kw_args =array(
		'post_type'=>'my_keywords', //to recieve only keywords
		'posts_per_page'=>-1
	);
	$the_kw_query = new WP_Query( $kw_args );
	
	
	// The Loop
	if ( $the_kw_query->have_posts() ) {
		while ( $the_kw_query->have_posts() ) {
			$the_kw_query->the_post();
			
			update_post_meta(get_the_id(),'bluet_posts_in_concern',0); //init by zero every post_meta
		}
	}	
	
	/* Restore original Post Data */
	wp_reset_postdata();
}