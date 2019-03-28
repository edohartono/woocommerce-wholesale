<?php
/*
Plugin Name: WooCommerce Wholesale
Plugin URI: http://github.com/edohartono/woocommerce-wholesale
description: Wordpress & WooCommerce plugin that help you to make wholesale pricing easily to your product pricing of your product.
Version: 1.0.0
Author: Edo Hartono
Author URI: https://profiles.wordpress.org/edoha
?>
*/

if(!defined('ABSPATH')) exit;
if (!defined('VEDOWS_PLUGIN_DIR_PATH'))

define('VEDOWS_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));

if(!defined('VEDOWS_PLUGIN_URL'))

define('VEDOWS_PLUGIN_URL', plugins_url().'/'.basename(dirname(__FILE__)));

require_once( VEDOWS_PLUGIN_DIR_PATH. 'functions.php');
