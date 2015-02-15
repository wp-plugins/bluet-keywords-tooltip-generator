<?php

# Adds a shortcode called 'kttg_glossary'.
function bluet_kttg_glossary() {
    global $is_kttg_glossary_page;
    $is_kttg_glossary_page=true;
 
    // The Query
    $args=array(
           'post_type'      =>'my_keywords',
           'posts_per_page' =>-1,	//to retrieve all keywords
            'order'         => 'ASC',
            'orderby'       => 'title',
    );
   
   $ret='<p>* <span class="bluet_glossary_all"><a href="'.get_the_guid().'">ALL</a></span> * ';
   $chara='A';
   for($i=0;$i<26;$i++){ 
       $ret.='-<span class="bluet_glossary_letter"><a href="'.get_the_guid().'&letter='.$chara.'">'.$chara.'</a></span>';
       $chara++;
   }
   
   $ret.='</p>';
   
   $chosen_letter=null;
   if($_GET["letter"]){
       $chosen_letter=$_GET["letter"];
   }
   
   $the_query = new WP_Query( $args );

   // The Loop
   if ( $the_query->have_posts() ) {
        $ret.='<ul>';
           while ( $the_query->have_posts() ) {
                $the_query->the_post();

                //echo(substr(get_the_title(),0,1).'<br>');
                if((strtoupper(substr(get_the_title(),0,1))==$chosen_letter) or $chosen_letter==null){                    
                    $ret.='<li>'.get_the_title().'</li>';
                }
                
           }
        $ret.='</ul>';
        
      
   } else {
           // no posts found
         $ret='No keyWords found';
   }
   /* Restore original Post Data */
   wp_reset_postdata();
   
   
   return $ret;

}
add_shortcode('kttg_glossary', 'bluet_kttg_glossary');  

