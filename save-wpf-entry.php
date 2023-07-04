<?php
/*
Plugin Name: Save WPF Entry 
Plugin URI: 
Description: This is a plugin that saves WPForms entries into the database
Author: Noman Akram
Text Domain: save-wpf-entry
Version: 1.0.0
*/

define( 'SWPFE_VERSION', '1.0.0' );

define( 'SWPFE_PLUGIN_PATH', dirname( __FILE__ ) );

define( 'SWPFE_PLUGIN_BASENAME',  basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );

define( 'SWPFE_PLUGIN_URL', plugins_url( '', SWPFE_PLUGIN_BASENAME ) );

define( 'SWPFE_CONTROLLER_PATH', SWPFE_PLUGIN_PATH  . DIRECTORY_SEPARATOR . 'controller' );

define( 'SWPFE_LIB_PATH', SWPFE_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'lib' );



require_once SWPFE_CONTROLLER_PATH .
    DIRECTORY_SEPARATOR .
    'SWPFEController.php';

require_once SWPFE_CONTROLLER_PATH .
    DIRECTORY_SEPARATOR .
    'SWPFEDataController.php';
// ==========================================================================
// = All app initialization is done in SWPFE_Controller __constructor. =
// ==========================================================================
$swpfe_controller = new SWPFEController();