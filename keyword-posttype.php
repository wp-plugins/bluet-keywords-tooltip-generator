<?php

class bluet_keyword{
	function __construct(){
		$this->register_my_post_type();
		$this->add_picture_to_columns();
	}
	
	public function register_my_post_type(){
		$args=array(
			'labels'=>array(
				'name'=>'KeyWords',
				'singular_name'=>'KeyWord',
				'menu_name'=>'My KeyWords',
				'name_admin_bar'=>'My KeyWords',
				'all_items'=>'All my KeyWords',
				'add_new' =>'Add new one',
				'add_new_item'=>'New KeyWord',
				'edit_item'=>'Edit KeyWord',
				'new_item'=>'New KeyWord',
				'view_item'=>'View KeyWord',
				'search_items'=>'Search for KeyWords',
				'not_found'=>'KeyWords not found',
				'not_found_in_trash'=>'KeyWords not found in trash',
				'parent_item_colon' =>'Parent KeyWords colon'
				),
			'public'=>true,
			'supports'=>array('title','editor','thumbnail'),
			'menu_icon'=>plugins_url('assets/ico_16x16.png',__FILE__),
		);
		register_post_type('my_keywords',$args);		
	}
	public function add_picture_to_columns(){
		
		// add the picture among columns
		add_filter('manage_my_keywords_posts_columns', function($defaults){
			$defaults['the_picture'] = 'Picture';
			return $defaults;
		});
		
		add_action('manage_my_keywords_posts_custom_column', function($column_name,$post_id){
			if ($column_name == 'the_picture') {
				// show content of 'directors_name' column
				the_post_thumbnail('thumbnail');
			}
		},10,2); //10 priority, 2 arguments
	}
}

?>