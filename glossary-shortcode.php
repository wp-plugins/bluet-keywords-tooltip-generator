<?php
defined('ABSPATH') or die("No script kiddies please!");

# Adds a shortcode called 'kttg_glossary'.
add_shortcode('kttg_glossary', 'bluet_kttg_glossary');

function bluet_kttg_glossary(){
	 global $is_kttg_glossary_page;
	 global $wpdb;
	 
	 $glossary_options = get_option( 'bluet_glossary_options' );
	 
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
   $ret='<div class="kttg_glossary_header"><span class="bluet_glossary_all"><a href="'.get_the_guid().'">'.$text_all.'</a></span> - ';
   /*get chars array*/
   $chars_count=array();
    // the query
	    $args=array(
			'post_type'     =>'my_keywords',
            'order'         => 'ASC',
            'orderby'       => 'title',
			'posts_per_page'=>-1,
		);
	
    $the_query = new WP_Query($args); 
							
   // The Loop
	if ( $the_query->have_posts() ){
			while ( $the_query->have_posts() ){
                $the_query->the_post();
				$my_char=strtoupper(mb_substr(get_the_title(),0,1,'utf-8'));
				if(empty($chars_count[$my_char])){
					$chars_count[$my_char]=0;
				}
				$chars_count[$my_char]++;
			}
	}
    // clean up after our query
    wp_reset_postdata(); 
	/**/

	foreach($chars_count as $chara=>$nbr){ 

 		$found_letter_class='bluet_glossary_found_letter';
		$current_letter_class='';

        $link_to_the_letter_page=add_query_arg( array('letter' => $chara), get_the_permalink());
		if(!empty($_GET["letter"]) and $_GET["letter"]==$chara){
			$current_letter_class='bluet_glossary_current_letter';
		}
 
       $ret.=' <span class="bluet_glossary_letter '.$found_letter_class.' '.$current_letter_class.'"><a href="'.$link_to_the_letter_page.'">'.$chara.'<span class="bluet_glossary_letter_count">'.$nbr.'</span></a></span>';
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
			'post_type'     =>'my_keywords',
            'order'         => 'ASC',
            'orderby'       => 'title',
			'showposts'		=>$showposts,
			'paged'			=>$paged
    );
	
    $the_query = new WP_Query( $args); 
							
   // The Loop
	if ( $the_query->have_posts() ) : 
        $ret.='<div class="kttg_glossary_content"><ul>';
			while ( $the_query->have_posts() ) :
                $the_query->the_post();

                //echo(substr(get_the_title(),0,1).'<br>');
                if((strtoupper(mb_substr(get_the_title(),0,1,'utf-8'))==$chosen_letter) or $chosen_letter==null){                    
                    $ret.='<li class="kttg_glossary_element" style="list-style-type: none;">'.get_the_title().'</li>';
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

    // clean up after our query
    wp_reset_postdata(); 

	else:  
	?><p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p><?php 
	endif;	
	
   return $ret;
}