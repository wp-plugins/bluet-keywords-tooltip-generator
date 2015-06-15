<?php
/*
Plugin Name: BleuT KeyWords ToolTip Generator
Description: This plugin allows you automatically create tooltip boxes for your technical keywords in order to explain them for your site visitors making surfing more comfortable.
Author: Jamel Zarga
Version: 2.6.2.1
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

//pour traiter les termes lors de l'activation de l'ajout d'un nouveau terme ou nouveau post (keyword) publish_{my_keywords}

add_action('init',function(){	
	/**** localization ****/
	load_plugin_textdomain('bluet-kw', false, dirname( plugin_basename( __FILE__ ) ).'/languages/');
	//create posttype for keywords	
	new bluet_keyword();

});

add_action('wp_enqueue_scripts', 'bluet_kw_load_scripts_front' );

add_action('wp_footer','bluet_kttg_place_tooltips');
add_action('admin_footer','bluet_kttg_place_tooltips');

function bluet_kttg_place_tooltips(){
	$kttg_sttings_options=get_option('bluet_kw_settings');
	$kttg_tooltip_position=$kttg_sttings_options["bt_kw_position"];
	if(!empty($kttg_sttings_options["bt_kw_animation_type"])){
		$animation_type=$kttg_sttings_options["bt_kw_animation_type"];
	}else{
		$animation_type="flipInX";
	}
	?>
	<script type="text/javascript">
		jQuery(document).ready(function(){
			bluet_placeTooltips(".bluet_tooltip, .bluet_img_tooltip","<?php echo($kttg_tooltip_position); ?>");	 
			animation_type="<?php echo($animation_type);?>";
			moveTooltipElementsTop(".bluet_block_to_show");
		})
	</script>
	<?php
}
	//call add filter for all hooks in need
	//you can pass cutom hooks you've done
	//(### do something here to support custom fields)
add_action('wp_head',function(){
	
	$contents_to_filter=array(
							array('the_content'),	//contents to filter to the post
							array('the_content')	//contents to filter to the page
						);
	
	/*get all posts (but not post type keywords)*/	
	$posttypes_to_match=array();//initial posttypes to match	
	$option_settings=get_option('bluet_kw_settings');
	
	if($option_settings['bt_kw_for_posts']){
		$posttypes_to_match[]='post';
	}
	
	if($option_settings['bt_kw_for_pages']){
		$posttypes_to_match[]='page';
	}
	if(function_exists('bluet_kttg_pro_addon')){//if pro addon activated
		$contents_to_filter=apply_filters('bluet_kttg_dustom_fields_hooks',$contents_to_filter);
		$posttypes_to_match=apply_filters('bluet_kttg_posttypes_to_match',$posttypes_to_match);
	}

	foreach($posttypes_to_match as $k=>$the_posttype_to_match){
		if(!empty($contents_to_filter[$k]) and $contents_to_filter[$k]!=null){
			bluet_kttg_filter_any_content($the_posttype_to_match,$contents_to_filter[$k]);
		}
	}

}); //'other content hook' if needed


//Functions


/* enqueue js functions for the front side*/
function bluet_kw_load_scripts_front() {
	$options = get_option( 'bluet_kw_settings' );
	$anim_type=$options['bt_kw_animation_type'];
	if(!empty($anim_type) and $anim_type!="none"){
		wp_enqueue_style( 'kttg-tooltips-animations-styles', plugins_url('assets/animate.css',__FILE__), array(), false);
	}
	//load jQuery once to avoid conflict
	wp_enqueue_script('jquery');

	//
	wp_enqueue_script( 'kttg-tooltips-functions-script', plugins_url('assets/kttg-tooltip-functionsv2.6.1.js',__FILE__), array(), false, true );
		
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
		'bt_kw_tt_color'=>'inherit',
		'bt_kw_tt_bg_color'=>'#0D45AA',
		
		'bt_kw_desc_color'=>'#ffffff',
		'bt_kw_desc_bg_color'=>'#5eaa0d',
		
		'bt_kw_desc_font_size'=>'14',
		
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

function bluet_kttg_filter_any_content($post_type_to_filter,$filter_hooks_to_filter){
	//this function filters a specific posttype with specific filter hooks
	$my_post_id=get_the_id();
	$exclude_me = get_post_meta($my_post_id,'bluet_exclude_post_from_matching',true);			

	//if the current post tells us to exclude from fetch
	//or the post type is not appropriate
	
	if($post_type_to_filter!=get_post_type($my_post_id)){
		return false;
	}
	if($post_type_to_filter=='post' and !is_single($my_post_id)){
		return false;
	}
	foreach($filter_hooks_to_filter as $hook){
		add_filter($hook,'kttg_filter_posttype',100000);//priority to 100 000 to avoid filters after it		
	}
}

function kttg_specific_plugins($cont){
	//specific modification so it can work for fields of "WooCommerce Product Addons"
	foreach($cont as $k=>$c_arr){
		//for description field
		$cont[$k]['description']=kttg_filter_posttype($c_arr['description']);
	}	
	return $cont;
}
		
function kttg_filter_posttype($cont){
	/*28-05-2015*/
	//specific modification so it can work for "WooCommerce Product Addons" and other addons
	if(is_array($cont)){
		$cont=kttg_specific_plugins($cont);		
		return $cont;
	}				
	/*28-05-2015 end*/

	$my_post_id=get_the_id();
	$exclude_me = get_post_meta($my_post_id,'bluet_exclude_post_from_matching',true);			
	
	if($exclude_me){
		return $cont;
	}

	//glossary settings
	$bluet_kttg_show_glossary_link=get_option('bluet_kw_settings');		
	if(!empty($bluet_kttg_show_glossary_link['bluet_kttg_show_glossary_link'])){
		$bluet_kttg_show_glossary_link=$bluet_kttg_show_glossary_link['bluet_kttg_show_glossary_link'];
	}else{
		$bluet_kttg_show_glossary_link=false;
	}
	
	$bluet_kttg_glossary_page=get_option('bluet_kttg_glossary_page');


	$option_settings=get_option('bluet_kw_settings');

	//var dans la quelle on cache les tooltips a afficher
	$html_tooltips_to_add='<div class="my_tooltips_in_block">';		

	$my_keywords_ids=kttg_get_related_keywords($my_post_id);
	
	//if user specifies keywords to match
	$bluet_matching_keywords_field=get_post_meta($my_post_id,'bluet_matching_keywords_field',true);
	if(!empty($bluet_matching_keywords_field)){
		$my_keywords_ids=$bluet_matching_keywords_field;
	}                                    
					
	global $is_kttg_glossary_page;
	$kttg_fetch_all_keywords=get_option('kttg_fetch_all_keywords');
	if(empty($kttg_fetch_all_keywords)){
		$kttg_fetch_all_keywords=false;
	}else if($kttg_fetch_all_keywords=="false"){
		$kttg_fetch_all_keywords=false;
	}else if($kttg_fetch_all_keywords=="true"){
		$kttg_fetch_all_keywords=true;
	}
	if(!empty($my_keywords_ids) OR $is_kttg_glossary_page OR $kttg_fetch_all_keywords){
		
		$my_keywords_terms=array(); 
							
		//looking in all occurences if glossary page
		if($is_kttg_glossary_page){
			$post_in=array();
		}else{
			$post_in=$my_keywords_ids;
		}
		if($kttg_fetch_all_keywords){
			$post_in=null;
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
						'youtube'=>get_post_meta(get_the_id(),'bluet_youtube_video_id',true),
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
				preg_match_all('#('.$regex.')#iu',$cont,$out);
				$cont=preg_replace('#('.$regex.')#i','**T_A_G**',$cont); //replace tags by **T_A_G**							
			//end
			
			$limit_match=($option_settings['bt_kw_match_all']=='on'? -1 : 1);
			
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
					$temr_occ=elim_apostrophes($temr_occ);
					$cont=elim_apostrophes($cont);
					
					$cont=preg_replace('#((\W)('.$temr_occ.''.$kw_after.')(\W))#u'.$kttg_case_sensitive,'$2__$3__$4',$cont,$limit_match);
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
				$video=$arr['youtube'];

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
				if($video!="" and function_exists('bluet_kttg_all_tooltips_layout')){
					$html_tooltips_to_add.=bluet_kttg_all_tooltips_layout(
			/*text=*/	$dfn,
			/*image=*/	'',
						$video,
						$arr["kw_id"]
					);
				}else{
					$html_tooltips_to_add.=bluet_kttg_tooltip_layout(
						$kttg_term_title 	//title
						,$dfn				//content def
						,$img				//image
						,$arr["kw_id"]		//id
						,$bluet_kttg_show_glossary_link	//show glossary link y/n
						,$bluet_kttg_glossary_page		//glossary page permalink
						);
				}

				
				//verify if case sensitive
				if($arr['case']){
					$kttg_case_sensitive='';
				}else{
					$kttg_case_sensitive='i';
				}								
				foreach($term_and_syns_array as $temr_occ){
					$temr_occ=elim_apostrophes($temr_occ);
					$cont=elim_apostrophes($cont);
					
					$cont=preg_replace('#(__('.$temr_occ.''.$kw_after.')__)#u'.$kttg_case_sensitive,$html_to_replace,$cont,-1);
				}
			}
			
			//Reinsert tag HTML elements
			foreach($out[0] as $id=>$tag){						
				$cont=preg_replace('#(\*\*T_A_G\*\*)#',$tag,$cont,1);
			}
			
			//prevent HTML Headings (h1 h2 h3) to be matched
			$regH='(<h[1-3]+>.*)(class="bluet_tooltip")(.*<\/h[1-3]+>)';						
			$cont=preg_replace('#('.$regH.')#iu','$2$4',$cont);					
	}			

	$html_tooltips_to_add=apply_filters('kttg_another_tooltip_in_block',$html_tooltips_to_add);
	$html_tooltips_to_add.="</div>";

	$cont=$html_tooltips_to_add.$cont;
	return do_shortcode($cont);//do_shortcode to return content after executing shortcodes
}
