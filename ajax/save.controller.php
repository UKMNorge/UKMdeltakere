<?php

global $current_user;
get_currentuserinfo();  

require_once('UKM/logger.class.php'); 

UKMlogger::setID( 'wordpress', $current_user->ID, get_option('pl_id') );


$DATA = [];
foreach( $_POST['formData'] as $field ) {
	$DATA[ $field['name'] ] = $field['value'];
}

switch( $_POST['do'] ) {
	case 'save':
		require_once( plugin_dir_path( __FILE__ ) .'../save/innslag.save.php' );
		break;
}