<?php
defined('ABSPATH') or die("No script kiddies please!");

# Adds a shortcode called 'kttg_glossary'.
add_shortcode('kttg_glossary', 'bluet_kttg_glossary');

function bluet_kttg_glossary(){
	global $tooltip_post_types;
	global $is_kttg_glossary_page;
	global $wpdb;
	 
	$glossary_options = get_option( 'bluet_glossary_options' );

    $tooltipy_glossary_show_thumb=$glossary_options['bluet_kttg_glossary_show_thumb'];
	 
    $is_kttg_glossary_page=true;
	
	//Begin -- glossary permalink page option
	if(!get_option('bluet_kttg_glossary_page')){
		//attribute glossary page permalink
		add_option('bluet_kttg_glossary_page',get_the_permalink());
	}
	if(get_option('bluet_kttg_glossary_page')!=get_the_permalink()){
		//update glossary page permalink if different
		update_option('bluet_kttg_glossary_page',get_the_permalink());
	}
	//End -- glossary permalink page option
    // next_posts_link() usage with max_num_pages
	if(!empty($glossary_options['kttg_glossary_text']['kttg_glossary_text_all']) and $glossary_options['kttg_glossary_text']['kttg_glossary_text_all']!=""){
		$text_all=$glossary_options['kttg_glossary_text']['kttg_glossary_text_all'];
	}else{
		$text_all=__('ALL','bluet-kw');
	}
	
	$current_letter_class='';
	if(empty($_GET["letter"])){
			$current_letter_class='bluet_glossary_current_letter';
	}
	$all_link='javascript: document.location.href=removeUrlParam("letter",document.location.href)';

	/*dropdown*/
	/*begin*/
		$ret="<div class='kttg_glossary_div'>";
		$ret.="<div class='kttg_glossary_families'><label>".__('Select a family','bluet-kw')." : </label><select name='kttg-glossary-family' onchange='document.location.href=changeQueryStringParameter(document.location.href,\"cat\",this.options[this.selectedIndex].value);'>";
	 	$ret.="<option value='all_families'>".__('All families','bluet-kw')."</option>";

		$families = get_categories(array(
	  		'taxonomy'=>'keywords_family'
	  	)) ;
		foreach ($families as $family) {
			$selected_family='';

			if(!empty($_GET['cat']) and $_GET['cat']==$family->category_nicename){
				$selected_family='selected';
			}

			$ret.='<option value="'.$family->category_nicename.'" '.$selected_family.'>';
			$ret.= $family->cat_name;
			$ret.= ' ('.$family->category_count.')';
			$ret.= '</option>';
		}

		$ret.="</select></div>";
  	/*end*/

   $ret.='<div class="kttg_glossary_header"><span class="bluet_glossary_all '.$current_letter_class.'"><a href=\''.$all_link.'\'>'.$text_all.'</a></span> - ';
 
   /*get chars array*/
   $chars_count=array();
     
    // the query
	    $args=array(
			'post_type'     => $tooltip_post_types,
            'order'         => 'ASC',
            'orderby'       => 'title',
			'posts_per_page'=>-1
		);

	$current_family="";

	if(!empty($_GET['cat']) and $_GET['cat']!='all_families'){
   		$current_family=$_GET['cat'];

   		$args['tax_query']=array(
				array(
					'taxonomy' => 'keywords_family',
					'field'    => 'slug',
					'terms'    => $current_family,
				),
			);
	} 
	
    $the_query = new WP_Query($args); 
							
   // The Loop
	if ( $the_query->have_posts() ){
			while ( $the_query->have_posts() ){
                $the_query->the_post();
				$my_char=strtoupper(mb_substr(get_the_title(),0,1,'utf-8'));
				if(!empty($chars_count[$my_char])){
					$chars_count[$my_char]++;
				}else{
					$chars_count[$my_char]=1;
				}
			}
	}
    // clean up after our query
    wp_reset_postdata(); 
	/**/

	foreach($chars_count as $chara=>$nbr){ 

 		$found_letter_class='bluet_glossary_found_letter';
		$current_letter_class='';

        $link_to_the_letter_page='javascript: document.location.href=changeQueryStringParameter(document.location.href,"letter","'.$chara.'")';
        //add_query_arg( array('letter' => $chara), get_the_permalink());
		if(!empty($_GET["letter"]) and $_GET["letter"]==$chara){
			$current_letter_class='bluet_glossary_current_letter';
		}
 
       $ret.=' <span class="bluet_glossary_letter '.$found_letter_class.' '.$current_letter_class.'"><a href=\''.$link_to_the_letter_page.'\'>'.$chara.'<span class="bluet_glossary_letter_count">'.$nbr.'</span></a></span>';
	}
   
   $ret.='</div>';
   
   $postids=array();
   
   $chosen_letter=null;
   if(!empty($_GET["letter"]) and $_GET["letter"]){
       $chosen_letter=$_GET["letter"];
	   
	   $postids=$wpdb->get_col($wpdb->prepare("
												SELECT      ID
												FROM        $wpdb->posts
												WHERE       SUBSTR($wpdb->posts.post_title,1,1) = %s
													AND $wpdb->posts.post_type='my_keywords'
													AND $wpdb->posts.post_status = 'publish'
												ORDER BY    $wpdb->posts.post_title"
											,$chosen_letter)); 
   }

   // set the "paged" parameter (use 'page' if the query is on a static front page)
    $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	
	$showposts=-1;
	if($glossary_options['kttg_kws_per_page']!=""){
		$showposts=$glossary_options['kttg_kws_per_page'];
	}
	
    // the query
	    $args=array(
			'post__in'		=>$postids,
			'post_type'     => $tooltip_post_types,
            'order'         => 'ASC',
            'orderby'       => 'title',
			'showposts'		=>$showposts,
			'paged'			=>$paged
    );
	
	if(!empty($_GET['cat']) and $_GET['cat']!='all_families'){
   		$current_family=$_GET['cat'];

   		$args['tax_query']=array(
				array(
					'taxonomy' => 'keywords_family',
					'field'    => 'slug',
					'terms'    => $current_family,
				),
			);
	} 

    $the_query = new WP_Query( $args); 
							
   // The Loop
	if ( $the_query->have_posts() ) : 
        $ret.='<div class="kttg_glossary_content"><ul>';
			while ( $the_query->have_posts() ) :
                $the_query->the_post();

				$families_list = wp_get_post_terms(get_the_id(), 'keywords_family', array("fields" => "names"));
				$families_string="";
				foreach ($families_list as $family_name) {
					$families_string.=$family_name." ";
				}
				$tooltipy_glossary_thumb='';
				if (
						!empty($tooltipy_glossary_show_thumb)
						and
						$tooltipy_glossary_show_thumb=="on"
						and
						has_post_thumbnail()
					) {
						$tooltipy_glossary_thumb='<div class="kttg_glossary_element_thumbnail">'.get_the_post_thumbnail().'</div>';					
					}
							

                //echo(substr(get_the_title(),0,1).'<br>');
                if((strtoupper(mb_substr(get_the_title(),0,1,'utf-8'))==$chosen_letter) or $chosen_letter==null){                    
                	$fam_action='javascript:document.location.href=changeQueryStringParameter(document.location.href,"cat","'.trim($families_string).'");';
                    $ret.='<li class="kttg_glossary_element" style="list-style-type: none;">
							<h2 class="kttg_glossary_element_title">'.get_the_title()." ".(count($families_list)>0 ? "<sub>[<a href=".$fam_action.">".$families_string."</a>]</sub>":"" ).'</h2>
							<div class="kttg_glossary_element_content">
							'.$tooltipy_glossary_thumb.get_the_content().'</div>
						</li>';
                }
                
			endwhile;
        $ret.='</ul></div>';
	
    // next_posts_link() usage with max_num_pages
	if(!empty($glossary_options['kttg_glossary_text']['kttg_glossary_text_next']) and $glossary_options['kttg_glossary_text']['kttg_glossary_text_next']!=""){
		$text_next=$glossary_options['kttg_glossary_text']['kttg_glossary_text_next'];
	}else{
		$text_next=__('Next','bluet-kw');
	}
	
	if(!empty($glossary_options['kttg_glossary_text']['kttg_glossary_text_previous']) and $glossary_options['kttg_glossary_text']['kttg_glossary_text_previous']!=""){
		$text_previous=$glossary_options['kttg_glossary_text']['kttg_glossary_text_previous'];
	}else{
		$text_previous=__('Previous','bluet-kw');
	}	
	
    $ret.=get_previous_posts_link( '<span class="kttg_glossary_nav prev">'.$text_previous.'</span>' );
	$ret.=" ";
    $ret.=get_next_posts_link( '<span class="kttg_glossary_nav next">'.$text_next.'</span>', $the_query->max_num_pages );
	$ret.="</div>";
    // clean up after our query
    wp_reset_postdata(); 

	else:  
	?><p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p><?php 
	endif;	
	
   return $ret;
}