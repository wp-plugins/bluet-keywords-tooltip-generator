<?php
defined('ABSPATH') or die("No script kiddies please!");

/*adding custom style*/
require_once dirname( __FILE__ ) . '/add-style.php';
require_once dirname( __FILE__ ) . '/importer.php';
require_once dirname( __FILE__ ) . '/functions.php';

add_action('wp_head','bluet_kw_custom_style');
add_action('admin_head','bluet_kw_custom_style');

/* enqueue js functions  for test only*/
function bluet_kw_load_scripts() {
	//
	wp_enqueue_script( 'kttg-settings-functions-script', plugins_url('assets/settings-functions.js',__FILE__), array(), false, true );
	
	//
	wp_enqueue_script( 'kttg-admin-tooltips-functions-script', plugins_url('assets/kttg-tooltip-functions.js',__FILE__), array(), false, true );
}
add_action( 'admin_head', 'bluet_kw_load_scripts' );

/** implementing colorPicker wordpress api **/

add_action( 'admin_enqueue_scripts', 'bluet_kw_add_color_picker' );

function bluet_kw_add_color_picker($hook) {
 
    if( is_admin() ) { //if in admin pages
     
        // Add the color picker css file       
        wp_enqueue_style( 'wp-color-picker' ); 
         
        // Include our custom jQuery file with WordPress Color Picker dependency
        wp_enqueue_script( 'kttg-colorpicker-custom-script', plugins_url( 'assets/colorpicker-custom-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true ); 
    }
}

/**end ColorPicker Implementation**/

/*-----------------------------------------------*
 * Sections, Settings, and Fields
/*-----------------------------------------------*

/**
 * Registers a new settings field on the 'General Settings' page of the WordPress dashboard.
 */
add_action( 'admin_init',function () {

/******************* sections */
	// 1st section
	add_settings_section(
		'concern_section',					
		__('Post Types in concern :','bluet-kw'),					
		'bluet_kw_sttings_display',		
		'my_keywords_settings'				
	);
	
	// 2nd section
	add_settings_section(
		'style_section',					// The ID to use for this section in attribute tags
		__('Customise the tooltip style :','bluet-kw'),					// The title of the section rendered to the screen
		'bluet_kw_style_display',		// The function used to render the options for this section
		'my_keywords_style'				// The ID of the page on which this section is rendered
	);
	
	
/******************* fields */

	// Define the in concern settings field
	add_settings_field( 
		'bt_kw_in_concern_field', 					
		__('Posts / Pages','bluet-kw'), 			
		'bt_kw_in_concern_display', 		
		'my_keywords_settings',				
		'concern_section'					
	);
	
	// Define the match all settings field
	add_settings_field( 
		'bt_kw_match_all_field', 					
		__('Match once or all occurrences','bluet-kw'), 			
		'bt_kw_match_all_display', 		
		'my_keywords_settings',				
		'concern_section'					
	);

	// Define the hide title settings field
	add_settings_field( 
		'bt_kw_hide_title', 					
		__('Tooltip title','bluet-kw'), 			
		'bt_kw_hide_title_display', 		
		'my_keywords_settings',				
		'concern_section'					
	);
	
	// Define the position settings field
	add_settings_field( 
		'bt_kw_position', 					
		__('Tooltip position','bluet-kw'), 			
		'bt_kw_position_display', 		
		'my_keywords_settings',				
		'concern_section'					
	);
	
	// Define the settings field for tooltip font color
	add_settings_field( 
		'bt_kw_tt_colour', 					// The ID (or the name) of the field
		__('Keyword style','bluet-kw'), 			// The text used to label the field
		'bt_kw_tt_colour_display', 		// The callback function used to render the field
		'my_keywords_style',				// The page on which we'll be rendering this field
		'style_section'					// The section to which we're adding the setting
	);
	
	add_settings_field( 
		'bt_kw_desc_colour', 					
		__('Description tooltip style','bluet-kw'), 			
		'bt_kw_desc_colour_display', 		
		'my_keywords_style',				
		'style_section'					
	);

	add_settings_field( 
		'bt_kw_desc_font_size', 					
		__('Description tooltip Font size','bluet-kw'), 			
		'bt_kw_desc_font_size_display', 		
		'my_keywords_style',				
		'style_section'					
	);

	add_settings_field( 
		'bt_kw_alt_img', 					
		__('Activate tooltips for images ?','bluet-kw'),
		'bt_kw_alt_img_display',
		'my_keywords_style',			
		'style_section'					
	);
/******************* registrations */
	//for settings options
	register_setting(
		'settings_group',					// The name of the group of settings
		'bluet_kw_settings'					// The name of the actual option (or setting)
	);
	
	//for style options
	register_setting(
		'settings_group',					// The name of the group of settings
		'bluet_kw_style'					// The name of the actual option (or setting)
	);
	
}); // end bluet_kw_settings_registration


//create submenu
add_action('admin_menu',function(){
	add_submenu_page(
		'edit.php?post_type=my_keywords',
		__('KeyWords Settings','bluet-kw'), 
		__('Settings','bluet-kw'), 
		'manage_options', 
		'my_keywords_settings', 
		'bluet_kw_render_settings_page'
	);
	
});
/*-----------------------------------------------*
 * Callbacks
/*-----------------------------------------------*

/**
 * Renders the content of the options page for the 	
*/
function bt_kw_alt_img_display(){
	//img atl tooltip render function	
	$options = get_option( 'bluet_kw_style' );
	?>
	<input type="checkbox" 	id="bt_kw_alt_img" 	name="bluet_kw_style[bt_kw_alt_img]" <?php if($options['bt_kw_alt_img']) echo 'checked'; ?>/><?php _e("alt property of the images will be displayed as a tooltip",'bluet-kw'); ?><br><?php
}
function bt_kw_desc_font_size_display(){
//font size field render function	
	$options = get_option( 'bluet_kw_style' );
	?>
			<input id="bt_kw_desc_font_size_id" type="number" min="1" max="50" name="bluet_kw_style[bt_kw_desc_font_size]" value="<?php echo $options['bt_kw_desc_font_size']; ?>"> pixels
	<?php
}
function bt_kw_desc_colour_display(){
	//colour field render function	
	$options = get_option( 'bluet_kw_style' );
	?>
		<?php _e('Description Background Colour','bluet-kw'); ?> : <br>
			<input id="aaa" type="text" class="color-field" name="bluet_kw_style[bt_kw_desc_bg_color]" value="<?php echo $options['bt_kw_desc_bg_color']; ?>">
		<br><?php _e('Description font Colour','bluet-kw'); ?> :<br>
			<input type="text" class="color-field" name="bluet_kw_style[bt_kw_desc_color]" value="<?php echo $options['bt_kw_desc_color']; ?>">
	<?php
}

function bt_kw_tt_colour_display(){
	//colour field render function	
	$options = get_option( 'bluet_kw_style' );
	?>
		<?php _e('Background Colour','bluet-kw'); ?> : <br><p><input id="bluet_kw_no_background" type="checkbox" name="bluet_kw_style[bt_kw_on_background]" <?php if($options['bt_kw_on_background']) echo 'checked'; ?>/><label for="bluet_kw_no_background" style="border-bottom: black 1px dotted;"><?php _e('No background (Dotted style)','bluet-kw'); ?></label></p><div id="bluet_kw_bg_hide"><input  type="text" class="color-field" name="bluet_kw_style[bt_kw_tt_bg_color]" value="<?php echo $options['bt_kw_tt_bg_color']; ?>"></div>
		<br><?php _e('Font Colour','bluet-kw'); ?> : <br><input  type="text" class="color-field" name="bluet_kw_style[bt_kw_tt_color]" value="<?php echo $options['bt_kw_tt_color']; ?>">

	<?php
}

function bluet_kw_sttings_display(){
	echo('<div id="keywords-settings">'.__('Choose either Posts or/and Pages in concern','bluet-kw').'.</div>');
}
function bluet_kw_style_display(){
	echo(__('Make your own style.','bluet-kw'));
}
function bt_kw_in_concern_display(){
	$options = get_option( 'bluet_kw_settings' );
?>
	<input type="checkbox" 	id="bt_kw_for_posts_id" 	name="bluet_kw_settings[bt_kw_for_posts]" <?php if($options['bt_kw_for_posts']) echo 'checked'; ?>/><?php _e('Posts','bluet-kw'); ?><br>
	<input type="checkbox" 	id="bt_kw_for_pages_id" 	name="bluet_kw_settings[bt_kw_for_pages]" <?php if($options['bt_kw_for_pages']) echo 'checked'; ?>/><?php _e('Pages','bluet-kw'); ?>
<?php

 }
 
function bt_kw_match_all_display(){
	$options = get_option( 'bluet_kw_settings' );
?>
	<input type="checkbox" 	id="bt_kw_match_all_id" 	name="bluet_kw_settings[bt_kw_match_all]" <?php if($options['bt_kw_match_all']) echo 'checked'; ?>/><?php _e('Match all occurrences','bluet-kw'); ?><br>
<?php

 }

function bt_kw_hide_title_display(){
	$options = get_option( 'bluet_kw_settings' );
?>
	<input type="checkbox" 	id="bt_kw_hide_title_id" 	name="bluet_kw_settings[bt_kw_hide_title]" <?php if($options['bt_kw_hide_title']) echo 'checked'; ?>/><?php _e('Hide the tooltips title','bluet-kw'); ?><br>
<?php
	 
}

function bt_kw_position_display(){
	$options = get_option( 'bluet_kw_settings' );
?>
	<input type="radio"	name="bluet_kw_settings[bt_kw_position]" value="top" <?php if($options['bt_kw_position']=="top") echo 'checked'; ?>/><?php _e('Top','bluet-kw'); ?><br>
	<input type="radio"	name="bluet_kw_settings[bt_kw_position]" value="bottom" <?php if($options['bt_kw_position']=="bottom") echo 'checked'; ?>/><?php _e('Bottom','bluet-kw'); ?><br>
	<input type="radio"	name="bluet_kw_settings[bt_kw_position]" value="right" <?php if($options['bt_kw_position']=="right") echo 'checked'; ?>/><?php _e('Right','bluet-kw'); ?><br>
	<input type="radio"	name="bluet_kw_settings[bt_kw_position]" value="left" <?php if($options['bt_kw_position']=="left") echo 'checked'; ?>/><?php _e('Left','bluet-kw'); ?><br>
<?php
	 
 }
/************************/
function bluet_kw_render_settings_page() {
	?>
		<div id="bluet-general" class="wrap" >
			
			<?php
				$kttg_infos=get_plugin_data(dirname(__FILE__).'/index.php');
				$kttg_name=$kttg_infos['Name'];
				$kttg_version=$kttg_infos['Version'];
			?>
			<h2><?php _e('KeyWords Settings','bluet-kw'); ?></h2><span><?php echo('<b>'.$kttg_name.'</b> (v'.$kttg_version.')');?></span>
			
			<?php settings_errors();?>				
				<h2 class="nav-tab-wrapper">
					<a class="nav-tab" id="bluet_style_tab" data-tab="bluet-section-style"><?php _e('Style','bluet-kw'); ?></a>
					<a class="nav-tab" id="bluet_settings_tab" data-tab="bluet-section-settings"><?php _e('Settings','bluet-kw'); ?></a>
					<a class="nav-tab" id="bluet_excluded_tab" data-tab="bluet-section-excluded"><?php _e('Excluded posts','bluet-kw');?></a>
					<a class="nav-tab" target="_blank" style="background-color: antiquewhite;" href="https://wordpress.org/support/plugin/bluet-keywords-tooltip-generator" ><?php _e('Help ?','bluet-kw');?></a>
					<a class="nav-tab" target="_blank" style="background-color: antiquewhite;" href="http://www.blueskills.net/product/kttg-images-add-on" ><?php _e('Premium Add-Ons','bluet-kw');?></a>
					<a class="nav-tab" target="_blank" style="background-color: aliceblue;" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=LPKWCDNECSVWJ" ><?php _e('Donate','bluet-kw');?></a>

				</h2>
			<form method="post" action="options.php">
			<?php
				// Render the settings for the settings section identified as 'Footer Section'
				settings_fields( 'settings_group' );
				
				//render sections here	
			echo('<div id="bluet-sections-div">');
				echo('<div class="bluet-section" id="bluet-section-settings" >');
				do_settings_sections( 'my_keywords_settings' );		
				echo('</div>');
				
				echo('<div class="bluet-section" id="bluet-section-style" >');
				
			//the tooltip display
				//tooltip content vars
				$test_name='Keywords ToolTip Generator';
				$test_dfn= __('this plugin allows you easely create tooltips for your technical keywords.','bluet-kw')
							.'<br>'
							.__('Click','bluet-kw')
							.'<a target="_blank" href="http://wordpress.org/support/view/plugin-reviews/bluet-keywords-tooltip-generator">'
							.__('Here','bluet-kw')
							.'</a>'
							.__('to rate our plugin if you appreciate it','bluet-kw')
							.';) ..';
				$test_img='<img height="auto !important" width="300" src="http://plugins.svn.wordpress.org/bluet-keywords-tooltip-generator/assets/banner-772x250.png" onError="this.src=\'http://plugins.svn.wordpress.org/bluet-keywords-tooltip-generator/assets/banner-772x250.jpg\'" class="attachment-medium wp-post-image" alt="wp-hooks-guide">';
				$test_id=111;
				
				//displaying the tooltip
				echo('<div id="tooltip_blocks_to_show">');
				echo(bluet_kttg_tooltip_layout($test_name,$test_dfn,$test_img,$test_id));
				echo('</div>');
				
				echo('<div id="tooltip_settings_sections">');
					do_settings_sections( 'my_keywords_style' );	
				echo('</div>');
				?>	
							
					<div id="bluet_kw_preview" style="background-color: rgb(211, 211, 211);  width: 75%;  padding: 15px;  border-radius: 10px;">
						<h3 style="margin-bottom: 12px;  margin-top: 0px;"><?php _e('Preview','bluet-kw'); ?> :</h3>
						<?php _e('Pass your mouse over the word','bluet-kw'); ?>
						<span class="bluet_tooltip" data-tooltip="111">KTTG</span> <?php _e('to test the tooltip layout.','bluet-kw'); ?>
					</div>				
				<?php
						
				echo('</div>');
				
				echo('<div class="bluet-section" id="bluet-section-excluded" >');
				?>				
					<div id="bluet_kw_excluded_posts">
						<h3><?php _e('Excluded posts','bluet-kw');?></h3>
						<p><?php _e('Posts which are excluded from being matched','bluet-kw');?></p>
						<?php
						$excluded_posts=bluet_kw_fetch_excluded_posts();
						
						if(empty($excluded_posts)){ 
							echo('<p style="color:red;">');
							_e('No posts or pages are excluded','bluet-kw');
							echo('</p>');
						}
						
						echo('<ul style="list-style: initial; padding-left: 25px;">');
						foreach($excluded_posts as $k=>$excluded_post){
							?>
							<li><a href="<?php echo $excluded_post['permalink']; ?>"><?php echo $excluded_post['title']; ?></a></li>
							<?php
						}
						echo("</ul>");
						?>
					</div>
				<?php
				echo('</div>');
				
			?>
			</div> <!-- end  bluet-sections-div -->
			
				<?php submit_button( __('Save Settings','bluet-kw'), 'primary'); ?> 

			</form>	

		</div>
<?php 

}


function bluet_kw_fetch_excluded_posts(){
//returns the list of the posts being excluded from keywords matching
	$arr_ret=array();
	
	//process
	$args =array(
		'post_type'=>array('post','page'),
		'posts_per_page'=>-1
	);
	$the_kw_query = new WP_Query( $args );
	
	
	// The Loop
	if ( $the_kw_query->have_posts() ) {
		while ( $the_kw_query->have_posts() ) {
			$the_kw_query->the_post();
			
			$exclude_me = get_post_meta(get_the_id(),'bluet_exclude_post_from_matching',true);
			//if excluded push it in the results
			if($exclude_me) array_push($arr_ret,array(
										'id'=>get_the_id(), //0
										'title'=>get_the_title(),			//1
										'permalink'=>get_the_permalink())		//2
										);
		}
	}	
	
	/* Restore original Post Data */
	wp_reset_postdata();
	
	
	return $arr_ret;
}