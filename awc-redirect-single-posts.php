<?php
/*
Plugin Name: AWC Redirect Single Posts
Plugin URI:  https://getlab.ca
Description: Simple Plugin to redirect single posts
Version:     0.1
Author:      Alex Coleman
Author URI:  https://alexcoleman.io
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: awc
*/

if ( ! defined( 'ABSPATH' ) ) exit;

$dir = plugin_dir_path( __FILE__ );

include_once( $dir . 'lib/awc-redirect-setup.php' );