<?php
function kttg_load_keywords_js() {
	wp_enqueue_script( 'kttg_load_keywords_script', plugins_url('assets/ajax/load-keywords.js',__FILE__), array('jquery'), '1.0', true );

	// pass Ajax Url to script.js
	wp_localize_script('kttg_load_keywords_script', 'kttg_ajax_load', admin_url( 'admin-ajax.php' ) );
}
add_action('wp_enqueue_scripts', 'kttg_load_keywords_js');

////
add_action( 'wp_ajax_kttg_load_keywords', 'kttg_load_keywords' );
add_action( 'wp_ajax_nopriv_kttg_load_keywords', 'kttg_load_keywords' );

function kttg_load_keywords() {
	global $tooltip_post_types;
	// récupération du mot tapé dans la recherche

	if(empty($_POST['keyword_ids'])){
		return;
	}else{
		$keyword_ids = $_POST['keyword_ids'];
	}

	$args = array(
	    'post__in' => $keyword_ids,
		'post_type' => $tooltip_post_types,
		'posts_per_page'=>-1
	);
	$options = get_option( 'bluet_kw_settings' );	

	if(!empty($options['bt_kw_hide_title']) and $options['bt_kw_hide_title']=='on'){
		$hide_title=true;
	}else{
		$hide_title=false;
	}
	
	$ajax_query = new WP_Query($args);

	if ( $ajax_query->have_posts() ) {
		while ( $ajax_query->have_posts() ){
			$ajax_query->the_post();
			?>
			<span class="bluet_block_to_show" data-tooltip="<?php echo(get_the_id()); ?>">
				<img src="<?php echo(plugins_url('assets',__FILE__)); ?>/close_button.png" class="bluet_hide_tooltip_button">
				<div class="bluet_block_container">
				<?php
				if(get_post_meta(get_the_id(),'bluet_youtube_video_id',true)==""){		
				?>				
					<div class="bluet_img_in_tooltip">
						<?php echo(get_the_post_thumbnail(get_the_id(),'medium')); ?>
					</div>
					<?php
				}else{
					?>				
					<div class="bluet_img_in_tooltip">
						<iframe src="https://www.youtube.com/embed/<?php echo(get_post_meta(get_the_id(),'bluet_youtube_video_id',true)); ?>?rel=0&showinfo=0" frameborder="0" allowfullscreen>
						</iframe>						
					</div>
					<?php
				}
				?>
						<div class="bluet_text_content">
						
							<?php
							if(!$hide_title){
								echo('<span class="bluet_title_on_block">'.get_the_title().'</span>');
							}
							the_content(); 
							?>
						</div>
					<div class="bluet_block_footer">
					</div>
				</div>
			</span>
			<?php
		}
	}
	?>
	<script type="text/javascript">
		//add listeners to tooltips 
		jQuery(".bluet_block_to_show").mouseover(function(){
			//.show()
			jQuery(this).show();
		});
		jQuery(".bluet_block_to_show").mouseout(function(){
			//leave it like that .css("display","none"); for Safari navigator issue
			jQuery(this).css("display","none");
		});
		
		jQuery(".bluet_hide_tooltip_button").click(function(){
			//leave it like that .css("display","none"); for Safari navigator issue
			jQuery(".bluet_block_to_show").css("display","none");
			//jQuery(".bluet_block_to_show");
		});
	</script>
	<?php

	die();
}