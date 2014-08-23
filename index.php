<?php
/*
Plugin Name: BleuT advanced inline tooltip generator
Description: this plugin lets you put illustrated keywords in a dictionary of terms tooltip (automatic generation) ...
Author: Jamel Zarga
Version: 1.5
Author URI: https://www.facebook.com/jameleddine.zarga
*/

require_once dirname( __FILE__ ) . '/keyword-posttype.php'; //contain the class that handles the new custom post



//pour inserer le style dans le head
wp_enqueue_style('bluet_style',plugins_url('assets/style.css',__FILE__));

//test	
add_action('init',function(){
	new bluet_keyword();
});

add_filter('the_content',function($cont){	
	if(is_single()){ //only in single posts
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
			
			
				$term_replace='<b>$0</b>';
				
				/*tow loops montioned here to avoid overlapping (chevauchement) */
				foreach($my_keywords_terms as $id=>$arr){
					$term=$arr['term'];
					$img=$arr['img'];
					$dfn=$arr['dfn'];
					
					$cont=preg_replace("#([\" ])(".$term.")([ ,.])#i",'$1__$2$3',$cont,1);
				}
				
				foreach($my_keywords_terms as $id=>$arr){
					$term=$arr['term'];
					$img=$arr['img'];
					$dfn=$arr['dfn'];
					
					$cont=preg_replace("#([\" ])(__(".$term."))([ ,.])#i",'
					$1<span class="bluet_tooltip" >$3<span>'.$img.'
							<b>$3</b><br>'.$dfn.'</span></span>$4',$cont,1);
				}

		}
	}
	return $cont;
});

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
	echo('<h2>print_r($all_kw_titles) : </h2>');

	echo('<pre>');
		print_r($all_kw_titles);
	echo('</pre>');
	
	/* Restore original Post Data */
	wp_reset_postdata();

	$post_have_kws=array(); //the variable to save earlier
	/*get all posts (but not post type keywords)*/
	
	$args =array(
		'post_type'=>'post', //to recieve only posts
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
					if(preg_match($term,get_the_content())){			 //to fetch only in content here by get_the_content()
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
