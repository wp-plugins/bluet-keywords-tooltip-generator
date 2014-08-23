<?php

require_once dirname( __FILE__ ) . '/mes_fonctions.php';
require_once dirname( __FILE__ ) . '/style.php';

function lebleut_ttg_settings_menu(){
lebleut_ttg_tooltip_style();

$style_col_pick='style="width: 30px; height: 30px; border: 0px;padding: 0px;"';

//charger les options de style
$opt=get_option('lebleut_ttg_settings');
	?>
	<div class='wrap'>
		<h2>Blue<img src="<?php echo(plugins_url('img/ico_40x40.png',__file__)); ?>"> Inline KeyWords Tooltip - <b>Settings</b></h2>
			<h3>(<a href="mailto:lebleut@gmail.com?Subject=wp%20inline%20tooltip">LeBleut@gmail.com</a>)</h3>
		<!--message d'affichage-->
		<?php
		if($_GET[erreur_message]==false){
			$msg_type='updated';
		}else{	
			$msg_type='error';
		}
		
		if($_GET['message']){?>
			<div class="<?php echo($msg_type);?>">
				<p><?php echo($_GET['message']); ?></p>
			</div>
		<?php } ?>
		<!--Fin message d'affichage-->

	<form method="post" action="admin-post.php">
		<input type="hidden" name="action" value="save_lebleut_ttg_settings">
		<!-- Adding security through hidden referrer field -->
		<?php wp_nonce_field( 'lebleut_ttg_nonce' ); ?>
		<span id="les_parametres">

			<h3>Terms style</h3>
				<p>Text :<input type="color" name="term_text_color" id="term_text_color" value="<?php echo($opt[0]); ?>" <?php echo($style_col_pick) ?>>
					<input type="hidden" id="chosen_term_text_color" value="<?php echo($opt[0]); ?>">
				Background :<input type="color" name="term_bg_color" id="term_bg_color" value="<?php echo($opt[1]); ?>" <?php echo($style_col_pick)?>>
					<input type="hidden" id="chosen_term_bg_color" value="<?php echo($opt[1]); ?>">
				</p>
			  
		<?php
		

	?>
			<h3>Tooltip style</h3>
				<p>Text :<input type="color" name="tooltip_text_color" id="tooltip_text_color"  value="<?php echo($opt[2]); ?>" <?php echo($style_col_pick) ?>>
					<input type="hidden" id="chosen_tooltip_text_color" value="<?php echo($opt[2]); ?>">
				Background :<input type="color" name="tooltip_bg_color" id="tooltip_bg_color" value="<?php echo($opt[3]); ?>" <?php echo($style_col_pick)?>>
					<input type="hidden" id="chosen_tooltip_bg_color" value="<?php echo($opt[3]); ?>">
				</p>
		</span>
		
		
		<div style="padding:30px 0px">
			<input type="submit" value="Save the new style" class="button-primary">
		</div>
		</form>
		
		<div id="aperçu">
		<h3>Demo :</h3>
		This is a plugin that allows you to highlight <span id="le_terme" title="It can be one or multiple terms, and this is a DEMO ;) -> IF the style is not ok, please save the new style first." class="tooltip"><span title="?">Terms or keywords</span></span> by associating tooltips.
		</div>
	</div>

	<?php
} 

function save_lebleut_ttg_settings(){
		// Check that user has proper security level
	if ( !current_user_can( 'manage_options' ) )
		wp_die( 'Not allowed' );
	
	// Check that nonce field created in configuration form
	// is present
	check_admin_referer( 'lebleut_ttg_nonce' );
	
	// Retrieve original plugin options array
	$options = array();
	

	// Cycle through all text form fields and store their values
	// in the options array
			$options[0]= $_POST['term_text_color'];
			$options[1]= $_POST['term_bg_color'];
			$options[2]= $_POST['tooltip_text_color'];
			$options[3]= $_POST['tooltip_bg_color'];
			
			$msg="New Style changed successfully.";
	// Store updated options array to database
	update_option( 'lebleut_ttg_settings', $options );
	
	
	
	// Redirect the page to the configuration form that was
	// processed
	wp_redirect( add_query_arg( 'page',	'lebleut_ttg_settings_slug',admin_url( 'admin.php?message='.$msg.'&erreur_message='.$errormsg )));
	
	exit;
}