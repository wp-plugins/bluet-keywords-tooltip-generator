<?php
// Check that code was called from WordPress with
// uninstallation constant declared
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit;
	
// Check if options exist and delete them if present
if ( get_option( 'lebleut_ttg_dico_terms' ) != false ) {
	delete_option( 'lebleut_ttg_dico_terms' );
}
?>