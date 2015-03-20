<?php
defined('ABSPATH') or die("No script kiddies please!");

# Adds a shortcode called 'kttg_glossary'.
add_shortcode('kttg_glossary', 'bluet_kttg_glossary');

function bluet_kttg_glossary(){
	 global $is_kttg_glossary_page;
	 global $wpdb;
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

   $ret='<div class="kttg_glossary_header">* <span class="bluet_glossary_all"><a href="'.get_the_guid().'">'.__('ALL','bluet-kw').'</a></span> * ';
   $chara='A';
   for($i=0;$i<26;$i++){ 
   
   	   $kttg_posts_num=$wpdb->get_var($wpdb->prepare("
												SELECT      COUNT(*)
												FROM        $wpdb->posts
												WHERE       SUBSTR($wpdb->posts.post_title,1,1) = %s
													AND $wpdb->posts.post_type='my_keywords'
													AND $wpdb->posts.post_status = 'publish'
												ORDER BY    $wpdb->posts.post_title"
											,$chara)); 
		$found_letter_class='';
		
		if($kttg_posts_num==0){
			$kttg_posts_num='';
			$link_to_the_letter_page="";
		}else{
			$found_letter_class='bluet_glossary_found_letter';
			$link_to_the_letter_page='href="'.get_the_guid().'&letter='.$chara.'"';
		}
       $ret.=' <span class="bluet_glossary_letter '.$found_letter_class.'"><a '.$link_to_the_letter_page.'>'.$chara.'<span class="bluet_glossary_letter_count">'.$kttg_posts_num.'</span></a></span>';
       $chara++;
   }
   
   $ret.='</div>';
   
   $postids=array();
   
   $chosen_letter=null;
   if($_GET["letter"]){
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

    // the query
	    $args=array(
			'post__in'		=>$postids,
			'post_type'     =>'my_keywords',
            'order'         => 'ASC',
            'orderby'       => 'title',
			'showposts'		=>10,
			'paged'			=>$paged
    );
	
    $the_query = new WP_Query( $args); 
							
   // The Loop
    ?>

    <?php if ( $the_query->have_posts() ) : 
        $ret.='<div class="kttg_glossary_content"><ul>';
			while ( $the_query->have_posts() ) :
                $the_query->the_post();

                //echo(substr(get_the_title(),0,1).'<br>');
                if((strtoupper(substr(get_the_title(),0,1))==$chosen_letter) or $chosen_letter==null){                    
                    $ret.='<li class="kttg_glossary_element" style="list-style-type: none;">'.get_the_title().'</li>';
                }
                
			endwhile;
        $ret.='</ul></div>';
	
    // next_posts_link() usage with max_num_pages
    $ret.=get_previous_posts_link( '<span style="">Prev --   </span>' );
    $ret.=get_next_posts_link( '<span style="">   -- Next</span>', $the_query->max_num_pages );

    ?>

    <?php 
    // clean up after our query
    wp_reset_postdata(); 
    ?>

    <?php else:  ?>
    <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
	<?php 
	endif;	
	
   return $ret;
}