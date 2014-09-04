<?php

require_once dirname( __FILE__ ) . '/add-style.php';

add_action('wp_head','bluet_kw_custom_style');
add_action('admin_head','bluet_kw_custom_style');

function theme_name_scripts() {
	wp_enqueue_script( 'angular-script', plugins_url('angular.min.js',__FILE__), array(), false, true );
	wp_enqueue_script( 'angular-app', plugins_url('app.js',__FILE__), array(), false, true );

}

add_action( 'admin_head', 'theme_name_scripts' );


add_action( 'admin_enqueue_scripts', 'bluet_kw_add_color_picker' );
function bluet_kw_add_color_picker($hook) {
 
    if( is_admin() ) { 
     
        wp_enqueue_style( 'wp-color-picker' ); 
         
        wp_enqueue_script( 'custom-script-handle', plugins_url( 'assets/custom-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true ); 
    }
}


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
		'style_section',				
		__('Customise the tooltip style :','bluet-kw'),					
		'bluet_kw_style_display',		
		'my_keywords_style'				
	);
	
/******************* fields */

	add_settings_field( 
		'bt_kw_in_concern_field', 		
		__('Posts / Pages','bluet-kw'), 
		'bt_kw_in_concern_display', 	
		'my_keywords_settings',			
		'concern_section'				
	);
		
	add_settings_field( 
		'bt_kw_tt_colour', 					
		__('Keyword style','bluet-kw'), 		
		'bt_kw_tt_colour_display', 		
		'my_keywords_style',			
		'style_section'					
	);
	
	add_settings_field( 
		'bt_kw_desc_colour', 					
		__('Description tooltip style','bluet-kw'), 			
		'bt_kw_desc_colour_display', 		
		'my_keywords_style',				
		'style_section'					
	);
	
/******************* registrations */
	//for settings options
	register_setting(
		'settings_group',					
		'bluet_kw_settings'					
	);
	
	//for style options
	register_setting(
		'settings_group',					
		'bluet_kw_style'				
	);
	
}); 


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

function bt_kw_desc_colour_display(){
	//colour field render function	
	$options = get_option( 'bluet_kw_style' );
	?>
		<?php _e('Description Background Colour','bluet-kw'); ?> : <br><input id="aaa" type="text" class="color-field" name="bluet_kw_style[bt_kw_desc_bg_color]" value="<?php echo $options['bt_kw_desc_bg_color']; ?>">
		<br><?php _e('Description font Colour','bluet-kw'); ?> :<br> <input type="text" class="color-field" name="bluet_kw_style[bt_kw_desc_color]" value="<?php echo $options['bt_kw_desc_color']; ?>">

	<?php
}

function bt_kw_tt_colour_display(){
	//colour field render function	
	$options = get_option( 'bluet_kw_style' );
	?>
		<?php _e('Background Colour','bluet-kw'); ?> : <br><input  type="text" class="color-field" name="bluet_kw_style[bt_kw_tt_bg_color]" value="<?php echo $options['bt_kw_tt_bg_color']; ?>">
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
 
/************************/
function bluet_kw_render_settings_page() {
	?>
		<div id="bluet-general" class="wrap" ng-app="myModule" ng-init="tabul='style'; descbgcol='#fff145'">
			<h2><?php _e('KeyWords Settings','bluet-kw'); ?></h2>
			<?php settings_errors();?>				
				<h2 class="nav-tab-wrapper">
					<a class="nav-tab" ng-click="tabul = 'style'" ng-class="{'nav-tab-active': tabul == 'style'}">Style</a>
					<a class="nav-tab" ng-click="tabul = 'settings'" ng-class="{'nav-tab-active': tabul == 'settings'}"><?php _e('Settings','bluet-kw'); ?></a>

				</h2>
			<form method="post" action="options.php">
			<?php

				settings_fields( 'settings_group' );
				
				echo('<div class="bluet-section" id="bluet-section-settings" ng-show="tabul===\'settings\'">');
				do_settings_sections( 'my_keywords_settings' );		
				echo('</div>');
				
				echo('<div class="bluet-section" id="bluet-section-style" ng-show="tabul===\'style\'">');
				?>
					<div id="bluet_kw_preview">
						<h3><?php _e('Preview','bluet-kw'); ?> :</h3>
						<?php _e('Pass your mouse over the word','bluet-kw'); ?> <span class="bluet_tooltip">Bluet Keywords Tooltip Generator<span><img height="auto !important" width="300" src="http://plugins.svn.wordpress.org/bluet-keywords-tooltip-generator/assets/banner-772x250.png" class="attachment-medium wp-post-image" alt="wp-hooks-guide"><b>Bluet Keywords Tooltip Generator</b> : <?php _e('this plugin allows you easely create tooltips for your technical keywords in order to explain them for your site visitors making surfing more comfortable.','bluet-kw'); ?><br>
						<?php _e('Click','bluet-kw'); ?> <a target="_blank" href="http://wordpress.org/support/view/plugin-reviews/bluet-keywords-tooltip-generator"><?php _e('Here','bluet-kw'); ?></a> <?php _e('to rate our plugin if you appreciate it','bluet-kw'); ?> ;) ..</span></span> <?php _e('to test the tooltip layout.','bluet-kw'); ?>
					</div>				
				<?php
				
				do_settings_sections( 'my_keywords_style' );			
				echo('</div>');
				
			?>
			
				<?php submit_button( __('Save Settings','bluet-kw'), 'primary'); ?> 

			</form>			
		</div>
<?php 
}
	

