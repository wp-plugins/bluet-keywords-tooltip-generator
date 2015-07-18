<?php
defined('ABSPATH') or die("No script kiddies please!");

if ( ! defined( 'BT_KW_BASE_FILE' ) )
    define( 'BT_KW_BASE_FILE', __FILE__ );
if ( ! defined( 'BT_KW_BASE_DIR' ) )
    define( 'BT_KW_BASE_DIR', dirname( BT_KW_BASE_FILE ) );
if ( ! defined( 'BT_KW_PLUGIN_URL' ) )
    define( 'BT_KW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

class bluet_keyword{
	
	function __construct(){
		$this->register_my_post_type();
		
		$this->add_columns();
	}
	
	public function register_my_post_type(){
	global $bluet_kw_capability;

		$args=array(
			'labels'=>array(
				'name'=>__('KeyWords','bluet-kw'),
				'singular_name'=>__('KeyWord','bluet-kw'),
				'menu_name'=>__('My KeyWords','bluet-kw'),
				'name_admin_bar'=>__('My KeyWords','bluet-kw'),
				'all_items'=>__('All my KeyWords','bluet-kw'),
				'add_new' =>__('Add new one','bluet-kw'),
				'add_new_item'=>__('New KeyWord','bluet-kw'),
				'edit_item'=>__('Edit KeyWord','bluet-kw'),
				'new_item'=>__('New KeyWord','bluet-kw'),
				'view_item'=>__('View KeyWord','bluet-kw'),
				'search_items'=>__('Search for KeyWords','bluet-kw'),
				'not_found'=>__('KeyWords not found','bluet-kw'),
				'not_found_in_trash'=>__('KeyWords not found in trash','bluet-kw'),
				'parent_item_colon' =>__('Parent KeyWords colon','bluet-kw')
				),
			'public'=>true,
			'supports'=>array('title','editor','thumbnail','author'),
			'menu_icon'=>plugins_url('assets/ico_16x16.png',__FILE__),
			
		);
		
		//modify capabilities if bluet_kw_capability hook has been called
		
		
		if($bluet_kw_capability!='manage_options'){
		$args['capabilities']=array(
		        'edit_post' => $bluet_kw_capability,
				'edit_posts' => $bluet_kw_capability,
				'publish_posts' => $bluet_kw_capability,
				'delete_post' => $bluet_kw_capability,
			);
			
		}		
	
		register_post_type('my_keywords',$args);		
	}
	public function add_columns(){
		
		// add the picture among columns
		add_filter('manage_my_keywords_posts_columns', function($defaults){		

			$defaults['the_picture']=__('Picture','bluet-kw');
			$defaults['is_prefix'] =__('Is Prefix ?','bluet-kw');
			$defaults['is_video'] =__('Video tooltip','bluet-kw');
			
			//we want to rearrange the columns apearance
			$reArr['cb']=$defaults['cb']; //checkBox column
			$reArr['the_picture']=$defaults['the_picture'];
			$reArr['title']=$defaults['title'];
			
			//is prefix ? if appropriate addon is activated
			if(function_exists('bluet_prefix_metabox')){
				$reArr['is_prefix']=$defaults['is_prefix'];
			}
			
			if(function_exists('bluet_video_metabox')){
				$reArr['is_video']=$defaults['is_video'];
			}
			//
			$reArr['date']=$defaults['date'];
			
			//return the rearranged array
			return $reArr;
		});
		
		add_action('manage_my_keywords_posts_custom_column', function($column_name,$post_id){

			if ($column_name == 'the_picture') {
				// show content of 'directors_name' column
				the_post_thumbnail(array(75,75));
			}elseif($column_name == 'is_prefix'){
				//if appropriate addon is activated
				if(function_exists('bluet_show_prefix_in_column')){
					bluet_show_prefix_in_column();
				}
			}elseif($column_name == 'is_video'){
				//if appropriate addon is activated
				if(function_exists('bluet_show_video_in_column')){
					bluet_show_video_in_column();
				}
			}
		},10,2); //10 priority, 2 arguments

	}
}

?>