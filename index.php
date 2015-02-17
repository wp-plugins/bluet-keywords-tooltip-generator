<?php
/*
Plugin Name: BleuT KeyWord ToolTip Generator
Description: this plugin lets you put illustrated keywords in a dictionary of terms tooltip (automatic generation) ...
Author: Jamel Zarga
Version: 2.3.3
Author URI: http://www.blueskills.net/about-us
*/
defined('ABSPATH') or die("No script kiddies please!");

require_once dirname( __FILE__ ) . '/functions.php';
require_once dirname( __FILE__ ) . '/keyword-posttype.php'; //contain the class that handles the new custom post
require_once dirname( __FILE__ ) . '/settings-page.php';
require_once dirname( __FILE__ ) . '/widget.php';
require_once dirname( __FILE__ ) . '/meta-boxes.php';
require_once dirname( __FILE__ ) . '/glossary-shortcode.php';



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
	if(is_admin()){
		new bluet_keyword();
	}
});

//call add filter for all hooks in need
	//you can pass cutom hooks you've done
	//(### do something here to support custom fields)
bluet_kttg_filter_any_content(array('the_content')); //'other content hook' if needed

//pour traiter les termes lors de l'activation de l'ajout d'un nouveau terme ou nouveau post (keyword) publish_{my_keywords}
add_action('publish_my_keywords','bluet_kttg_regenerate_keywords');
add_action('publish_post','bluet_kttg_regenerate_keywords');
add_action('publish_page','bluet_kttg_regenerate_keywords');
add_action('trashed_post','bluet_kttg_regenerate_keywords');

