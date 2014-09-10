<?php

class bluet_keyword_widget extends wp_widget{
	function __construct(){
		$params=array(
			'description'=>'contains keywords used in the current single post.',
			'name'=>'My keywords (BlueT)'
		);
		parent::__construct('my_keywords_widget','',$params);
	}
	
	public function widget( $args, $instance ) {
		if(!is_single()) return false; 
		extract($args);
		echo($before_widget);
			echo($before_title);
				echo $instance['title'];
			echo($after_title);
			echo('<ul>');
				$my_terms_ids=$my_keywords_ids=get_post_meta(get_the_id(),'bluet_matched_keywords',true);
				
				if(!empty($my_terms_ids)){ 
					foreach($my_terms_ids as $term_id){
					
					$wk_args=array(
						'p'=>$term_id,
						'post_type'=>'my_keywords'
					);
					
						$the_wk_query = new WP_Query( $wk_args );

					if ( $the_wk_query->have_posts() ) {

						while ( $the_wk_query->have_posts() ) {
							$the_wk_query->the_post();
							
								$trm=get_the_title();
								$dfn=get_the_excerpt();
								$img=get_the_post_thumbnail($term_id,'medium');
						}
						
					}

					wp_reset_postdata();
					
						$delimiter_1='<span class="bluet_tooltip" >'.$trm.'<span>';
						$delimiter_2='<b>'.$trm.'</b><br>';
						$delimiter_3='</span></span> ';
						
						echo('<li>'.$delimiter_1.''.$img.''.$delimiter_2.''.$dfn.''.$delimiter_3.'</li>');
					}
				}else{
					_e('no terms found for this post','bluet-kw');
				}
				echo('</ul>');

		echo($after_widget);
	}

	public function form( $instance ) {
	?>
		<label for="<?php echo $this->get_field_id('title'); ?>" >Title : </label>
		<input
			class="widefat"
			id="<?php echo $this->get_field_id('title'); ?>"
			name="<?php echo $this->get_field_name('title'); ?>"
			value="<?php if(isset($instance['title'])) echo esc_attr($instance['title']); ?>"
		/>
	<?php
	
	}
	
	public function register_widget(){
	}	
}

add_action('widgets_init',function(){
	register_widget('bluet_keyword_widget');
});

?>