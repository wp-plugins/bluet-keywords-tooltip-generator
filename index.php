<?php
/*
Plugin Name: BleuT inline tooltip generator
Description: this plugin lets you put keywords in a dictionary of terms tooltip (automatic generation) ...
Author: Jamel Zarga
Version: 1.0
Author URI: https://www.facebook.com/jameleddine.zarga
*/

require_once dirname( __FILE__ ) . '/mes_fonctions.php';
require_once dirname( __FILE__ ) . '/admin-settings.php';
require_once dirname( __FILE__ ) . '/style.php';

if ( is_admin() )
	require_once dirname( __FILE__ ) . '/admin.php';

//pour inserer le style dans le head
add_action('wp_head','lebleut_ttg_tooltip_style');

//pour generer les termes utilisés lors de la sauvegarde
add_action('save_post','lebleut_ttg_cherche_termes');

//pour charger les termes de l'article en cours
add_filter('the_content','lebleut_ttg_add_keywords_tooltip');


//pour inserer les termes dans la base lors de l'activation
register_activation_hook(__FILE__,'lebleut_ttg_default_options');

function lebleut_ttg_default_options(){
	//charger lestermes
	require_once('my_terms.php');
	$mes_termes=$les_termes_et_definitions;
	$mes_params=array('#ffffff','#F5A45A','#ffffff','#333333');
	
	if ( get_option( 'lebleut_ttg_dico_terms' ) === false ) {
		add_option( 'lebleut_ttg_dico_terms', $mes_termes );
	}
	
	if ( get_option( 'lebleut_ttg_settings' ) === false ) {
		add_option( 'lebleut_ttg_settings', $mes_params );
	}
}

function lebleut_ttg_cherche_termes($post_id){

	//charger la liste des termes voulu
	//quire_once('my_terms.php');
	$les_termes_et_definitions=array();
	
	if ( get_option( 'lebleut_ttg_dico_terms' ) !== false ) {
		$les_termes_et_definitions=get_option( 'lebleut_ttg_dico_terms' );
	}
	
	$les_termes_de_cuisine=array();
	$les_definitions=array();
		
	foreach($les_termes_et_definitions as $terme=>$def){
		$les_termes_de_cuisine[]=esc_attr($terme);
		$les_definitions[]=esc_attr($def);
	}
	
	//chercher dans le content
	$sujet=get_post($post_id)->post_content;

	$resultat=substr_list_array($sujet,$les_termes_de_cuisine,$les_definitions); 
	
	//chercher l'option dans la base et l'associer
	if(get_option('lebleut_ttg_posts_terms')===false){
		add_option('lebleut_ttg_posts_terms','');
	}	
	$option_tmp=get_option('lebleut_ttg_posts_terms');
	$option_tmp[$post_id]=$resultat;

	update_option('lebleut_ttg_posts_terms',$option_tmp);
	// - Update the post's metadata.
}

function lebleut_ttg_add_keywords_tooltip($cont){
	$post_id=get_the_ID();
	
	//charger les termes de l'article de la base
	if(get_option('lebleut_ttg_posts_terms')!==false){
		$tout_les_termes=get_option('lebleut_ttg_posts_terms');
		//si les termes de cet article sont générés
		if($tout_les_termes[$post_id]!=false){
		
			foreach($tout_les_termes[$post_id] as $trm=>$dfn){
			
				$cont=str_ireplace($trm,'<span class="tooltip" title="'.$dfn.'"><span title="?">'.$trm.'</span></span>',$cont);
			}
		}
	}
	//changer le content modifié
	
	//retourner le nouveau content
	return $cont;
}
