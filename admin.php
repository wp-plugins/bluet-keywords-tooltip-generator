<?php

require_once dirname( __FILE__ ) . '/mes_fonctions.php';
require_once dirname( __FILE__ ) . '/admin-settings.php';
require_once dirname( __FILE__ ) . '/style.php';


//lors de la creation des menus de l'admin
add_action( 'admin_menu', 'lebleut_ttg_menu' );

//admin_init is triggered before any other hook when a user accesses the admin area
add_action( 'admin_init', 'lebleut_ttg_admin_init' );

if($_GET['term']!=null){
		
	//recevoir les param
	$the_term_to_delete=$_GET['term'];
	$the_terms=get_option('lebleut_ttg_dico_terms');
	$new_terms=array();
	
	//supprimer 
	foreach($the_terms as $a_term=>$def){	
		if($a_term!=$the_term_to_delete){
			$new_terms[$a_term]=$def;
		}
	}
	update_option('lebleut_ttg_dico_terms',$new_terms);
	$msg='The term <b>'.$the_term_to_delete.'</b> deleted successfully.';	
	$_GET['message']=$msg;
}

function lebleut_ttg_menu() {

	//main menu
	add_menu_page(
	'blueT Inline KeyWords Tooltip', //title
	'Inline ToolTips', //menu item name
	'manage_options', //capability
	'lebleut_ttg_main_slug', //url slug
	'lebleut_ttg_main_menu',  //function handling
	plugins_url('img/ico_16x16.png',__file__), //icon
	30	//position
	);
	
	//submenu 1
	add_submenu_page(
	'lebleut_ttg_main_slug',//menu parent	
	'blueT inline ToolTips Settings', 
	'Settings', 
	'manage_options', 
	'lebleut_ttg_settings_slug',
	'lebleut_ttg_settings_menu'	
	);
}

//a associer au bouton convenable
function genere_post_terms(){

	// Check that user has proper security level
	if ( !current_user_can( 'manage_options' ) )
		wp_die( 'Not allowed' );
	
	// Check that nonce field created in configuration form
	// is present
	check_admin_referer( 'lebleut_ttg_nonce' );
	
	
//la fonction qui génère les nouveaux termes des postes
	$nbrPosts=wp_count_posts()->publish;	
	$mes_posts=get_posts(array('numberposts' => ($nbrPosts),'post_status'=> 'publish'));
	$mes_post_text=array();
	
	foreach($mes_posts as $mon_post){
		$mes_post_text[$mon_post->ID]=$mon_post->post_content;
	}
	// maintenant nous avons tout le contenune des postes dans - > $mes_post_text
		
	$mes_termes=get_option('lebleut_ttg_dico_terms');
	$nouv_termes=array();

	$les_termes_de_cuisine=array();
	foreach($mes_termes as $terme=>$def){
		$les_termes_de_cuisine[]=esc_attr($terme);
		$les_definitions[]=esc_attr($def);
	}
	
	foreach($mes_post_text as $id=>$text){//pour chaque article
		$nouv_termes[$id]=substr_list_array($text,$les_termes_de_cuisine,$les_definitions);
	}
	//mettre a jour la liste
	update_option('lebleut_ttg_posts_terms',$nouv_termes);
	//
	$msg='Terms successfully generated';
	
	//var_dump($nouv_termes);

	//redirection
	wp_redirect( add_query_arg( 'page',	'lebleut_ttg_main_slug',admin_url( 'admin.php?message='.$msg.'&erreur_message='.$errormsg  )));
	exit;

}

	
//la fonction qui fait le rendu le la page des option d'administration dans l'admin
function lebleut_ttg_main_menu(){

//genere_post_terms();

	$mes_termes=get_option('lebleut_ttg_dico_terms');
	
		?>
	<div class='wrap'>
	<h2>Blue<img src="<?php echo(plugins_url('img/ico_40x40.png',__file__)); ?>"> Inline KeyWords Tooltip - <b>Dico Terms</b></h2>
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

	<h3>Add a new term</h3>
	<!--Form ajout-->
	<form method="post" action="admin-post.php">
	<input type="hidden" name="action" value="save_lebleut_ttg_dico_terms">
	<!-- Adding security through hidden referrer field -->
	<?php wp_nonce_field( 'lebleut_ttg_nonce' ); ?>
	<table class="wp-list-table widefat fixed media-list-table" style="width:60%">
		<thead>
				<tr>
					<th class="manage-column">New Term</th>
					<th class="manage-column">Signification</th>
				</tr>
		</thead>
		<tbody id="the-list">
				<tr class="alternate status-inherit">
					<td class="post-title page-title column-title"><input type="text" name="elem_term" value=""></td>
					<td class="post-title page-title column-title"><textarea name="elem_def" rows="4"></textarea></td>
				</tr>
				<tr class="alternate status-inherit">
					<td class="post-title page-title column-title"><input type="submit" value="Add the term" class="button-primary"></td>
					<td class="post-title page-title column-title"></td>
				</tr>
		</tbody>
	</table>
	</form>	
	<!--fin Form ajout-->
	
	<h3>Terms list</h3>
	<!--tab debut-->
	<table class="wp-list-table widefat fixed media-list-table" style="width:60%">
		<thead>
			<tr>
				<th class="manage-column">Term</th>
				<th class="manage-column">Segnification</th>
				<th class="manage-column"></th>
			</tr>
		</thead>
		<tbody id="the-list">
			<?php
			foreach($mes_termes as $trm=>$def){
			?>
			<tr class="alternate status-inherit">
				<td class="post-title page-title column-title"><?php echo($trm); ?></td>
				<td class="post-title page-title column-title"><?php echo($def); ?></td>
				
				<td class="post-title page-title column-title">
					<span class="delete">
						<a href="admin.php?page=lebleut_ttg_main_slug&term=<?php echo($trm);?>" class="delete-tag">Delete</a>
					</span>
				</td>
			</tr>
			<?php
			}		
			?>
		</tbody>
	</table>
	<!--tab fin-->
	
	<h3>Generate the terms for all my posts</h3>
	<p>
	If you do not generate terms for all posts, <span style="color:red;">only new posts</span> will be affected.
	</p>
	<form method="post" action="admin-post.php">
		<input type="hidden" name="action" value="genere_post_termes">
		<!-- Adding security through hidden referrer field -->
		<?php wp_nonce_field( 'lebleut_ttg_nonce' ); ?>
		
		<input type="submit" value="Generate keyWords" class="button-primary">
	</form>
	
	
	</div>
<?php
}


function lebleut_ttg_admin_init() {
	add_action( 'admin_post_save_lebleut_ttg_dico_terms',
				'process_lebleut_ttg_dico_terms' );
				
	add_action( 'admin_post_genere_post_termes',
				'genere_post_terms' );

	add_action( 'admin_post_save_lebleut_ttg_settings',
				'save_lebleut_ttg_settings' );

}


function process_lebleut_ttg_dico_terms(){
	// Check that user has proper security level
	if ( !current_user_can( 'manage_options' ) )
		wp_die( 'Not allowed' );
	
	// Check that nonce field created in configuration form
	// is present
	check_admin_referer( 'lebleut_ttg_nonce' );
	
	// Retrieve original plugin options array
	$options = get_option( 'lebleut_ttg_dico_terms' );
	

	// Cycle through all text form fields and store their values
	// in the options array
		if (  $_POST['elem_term']!='' &&  $_POST['elem_def']!=''){
			$options[sanitize_text_field( $_POST['elem_term'] )]=sanitize_text_field( $_POST['elem_def'] );
			$msg="Term added successfully";
		}else{
				$msg="Warning! You must fill in both fields";
				$errormsg=true; //c.a.d  type erreur
		}
	// Store updated options array to database
	update_option( 'lebleut_ttg_dico_terms', $options );
	
	
	
	// Redirect the page to the configuration form that was
	// processed
	wp_redirect( add_query_arg( 'page',	'lebleut_ttg_main_slug',admin_url( 'admin.php?message='.$msg.'&erreur_message='.$errormsg )));
	
	exit;
}