<?php

add_action('edit_form_after_title', function() {
    global $post, $wp_meta_boxes,$post_type;
    
	do_meta_boxes($post_type,'after_title',$post);
	
});

add_action('do_meta_boxes',function(){
		add_meta_box(
		'bluet_kw_synonyms_meta',
		__('Synonyms','bluet-kw'),
		'bluet_synonyms_render',
		'my_keywords',
		'after_title',
		'high'
		);

		add_meta_box(
		'bluet_kw_related_posts_meta',
		__('Posts in concern','bluet-kw').' * (BlueT)',
		'bluet_posts_related_render',
		'my_keywords',
		'side',
		'high'
		);
		
		add_meta_box(
		'bluet_kw_related_keywords_meta',
		__('Keywords related','bluet-kw').' * (BlueT)',
		'bluet_keywords_related_render',
		'post',
		'side',
		'high'
		);
		
		add_meta_box(
		'bluet_keywords_related_pages_meta',
		__('Keywords Related','bluet-kw').' * (BlueT)',
		'bluet_keywords_related_render',
		'page',
		'side',
        'high'
		);
});
function bluet_posts_related_render(){
	$nbr=get_post_meta(get_the_id(),'bluet_posts_in_concern',true);	
	if(empty($nbr)){
		$nbr=0;
	}
	echo($nbr);
}
function bluet_synonyms_render(){
	?>
	<p>
	<input id="bluet_synonyms_name" name="bluet_synonyms_name" type="text" value="<?php echo(get_post_meta(get_the_id(),'bluet_synonyms_keywords',true));?>" placeholder="<?php _e("Type here the keyword's Synonyms separated with '|'","bluet-kw");?>" style=" width:100%;" />
	</p>
	<?php
}
function bluet_keywords_related_render(){

	$my_kws=get_post_meta(get_the_id(),'bluet_matched_keywords',true);
	if(!empty($my_kws)){
	echo('<ul><b>');
		foreach($my_kws as $kw_id){
			echo('<li>'.get_the_title($kw_id).'</li>'); 
		}
	echo('</b></ul>');
	}else{
		echo(__('No KeyWords found for this post','bluet-kw').'<br>');
	}
	
	echo('<a href="'.get_admin_url().'edit.php?post_type=my_keywords">');
	echo(__('Manage KeyWords','bluet-kw').' >>');
	echo('</a>');
}

add_action('save_post',function(){
	if($_POST['post_type']=='my_keywords'){
	
	$syns_save=$_POST['bluet_synonyms_name'];
	
	$syns_save=preg_replace('(\|{2,100})','|',$syns_save);
	
	$syns_save=preg_replace('(^\||\|$|[\s]{2,100})','',$syns_save);
		update_post_meta($_POST['post_ID'],'bluet_synonyms_keywords',$syns_save);
	}
});


