<?php
defined('ABSPATH') or die("No script kiddies please!");

/*place metabox after the Title*/ 
add_action('edit_form_after_title', function() {
    global $post, $wp_meta_boxes,$post_type;
    
	do_meta_boxes($post_type,'after_title',$post);
	//echo("<pre>");print_r($post_type);echo("</pre>");
	
});
/**/

add_action('do_meta_boxes',function(){
//for keywords
		add_meta_box(
		'bluet_kw_settings_meta',
		__('Keyword Settings','bluet-kw'),
		'bluet_keyword_settings_render',
		'my_keywords',
		'after_title',
		'high'
		);
		
//for posts
		add_meta_box(
		'bluet_kw_post_related_keywords_meta',
		__('Keywords related','bluet-kw').' (KTTG)',
		'bluet_keywords_related_render',
		'post',
		'side',
		'high'
		);
		
//for pages		
		add_meta_box(
		'bluet_kw_page_related_keywords_meta',
		__('Keywords related','bluet-kw').' (KTTG)',
		'bluet_keywords_related_render',
		'page',
		'side',
        'high'
		);
});

function bluet_keyword_settings_render(){
	?>
	<p>
	<Label for="bluet_synonyms_id"><?php _e('Synonyms','bluet-kw');?></label><input id="bluet_synonyms_id" name="bluet_synonyms_name" type="text" value="<?php echo(get_post_meta(get_the_id(),'bluet_synonyms_keywords',true));?>" placeholder="<?php _e("Type here the keyword's Synonyms separated with '|'","bluet-kw");?>" style=" width:100%;" />
	</p>
	
	<p>
		<label for='bluet_case_sensitive_id'><?php _e('Make this keyword <b>Case Sensitive</b>','bluet-kw');?>  </label>
		<input id="bluet_case_sensitive_id" name="bluet_case_sensitive_name" type="checkbox" <?php if(get_post_meta(get_the_id(),'bluet_case_sensitive_word',true)) echo('checked');?> />
	</p>
	<?php
	if(function_exists('bluet_prefix_metabox')){
		bluet_prefix_metabox();
	}
	
	if(function_exists('bluet_video_metabox')){
		bluet_video_metabox();
	}
	
}

function bluet_keywords_related_render(){
//exclude checkbox to exclode the current post from being matched
	global $post;

	$current_post_id=$post->ID;
	$exclude_me = get_post_meta($current_post_id,'bluet_exclude_post_from_matching',true);
	?>
	<div>
		<h3><?php _e('Exclude this post from being matched','bluet-kw'); ?></h3>
		<input type="checkbox" id="bluet_kw_admin_exclude_post_from_matching_id" onClick="hideIfChecked('bluet_kw_admin_exclude_post_from_matching_id','bluet_kw_admin_div_terms')" name="bluet_exclude_post_from_matching_name" <?php if($exclude_me) echo "checked"; ?>/>
		<span style="color:red;"><?php _e('Exclude this post','bluet-kw'); ?></span>
	
	<?php
//show keywords list related
	
	$my_kws=array();
	
	$my_kws=kttg_get_related_keywords($current_post_id);
	
	//echo('<pre>');print_r($my_kws);echo('</pre>');

	$bluet_matching_keywords_field=get_post_meta($current_post_id,'bluet_matching_keywords_field',true);
	
	?>
	
		<div id="bluet_kw_admin_div_terms">
		<?php

	if(!empty($my_kws)){
		?>		
			<h3><?php _e('Keywords related','bluet-kw');?></h3>
		<?php
		echo('<ul style="list-style: initial; padding-left: 20px;">');
			foreach($my_kws as $kw_id){
				echo('<li style="color:green;"><i>'.get_the_title($kw_id).'</i></li>'); 
			}
		echo('</ul>');
	}else{
		echo('<p>'.__('No KeyWords found for this post','bluet-kw').'</p>');
	}
	
	echo('<a href="'.get_admin_url().'edit.php?post_type=my_keywords">');
	echo(__('Manage KeyWords','bluet-kw').' >>');
	echo('</a>');
		echo('</div>');
	echo "</div>";
}


add_action('save_post',function(){
	//saving synonyms
	if(!empty($_POST['post_type']) and $_POST['post_type']=='my_keywords'){
		//do sanitisation and validation
		
		//synonyms
		//editpost to prevent quick edit problems
		if($_POST['action'] =='editpost'){
			$syns_save=$_POST['bluet_synonyms_name'];		
			
			$kttg_case=$_POST['bluet_case_sensitive_name'];
			
			//replace ||||||| by only one
			$syns_save=preg_replace('(\|{2,100})','|',$syns_save);
			
			//eliminate spaces special caracters
			$syns_save=preg_replace('(^\||\|$|[\s]{2,100})','',$syns_save);
			update_post_meta($_POST['post_ID'],'bluet_synonyms_keywords',$syns_save);
			
			update_post_meta($_POST['post_ID'],'bluet_case_sensitive_word',$kttg_case);		
			
			//prefixes if exists
			if(function_exists('bluet_prefix_save')){
				bluet_prefix_save();
			}
			
			//prefixes if exists
			if(function_exists('bluet_video_save')){
				bluet_video_save();
			}
		}
		
		
	}else{
		if(!empty($_POST['action']) and $_POST['action'] =='editpost'){
			$exclude_me=$_POST['bluet_exclude_post_from_matching_name'];
			
			//save exclude post from matching
			update_post_meta($_POST['post_ID'],'bluet_exclude_post_from_matching',$exclude_me);

			$matchable_keywords=$_POST['matchable_keywords'];
			$arr_match=array();

			if(!empty($matchable_keywords)){
				foreach($matchable_keywords as $k=>$matchable_kw_id){
					$arr_match[$matchable_kw_id]=$matchable_kw_id;
				}
			}else{
				//
			}
			update_post_meta($_POST['post_ID'],'bluet_matching_keywords_field',$arr_match);
		}	
	}
}); 
