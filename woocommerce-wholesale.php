<?php
/*
Plugin Name: Woocommerce Wholesale
Plugin URI: http://github.com/edohartono/woocommerce-wholesale
description: Woocommerce Wholesale is plugin that help you to make wholesale pricing of your product.
Version: 1.0.0
Author: Edo Hartono
Author URI: http://github.com/edohartono
?>
*/

if(!defined('ABSPATH')) exit;
if (!defined('WS_PLUGIN_DIR_PATH'))

define('WS_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));

if(!defined('WS_PLUGIN_URL'))

define('WS_PLUGIN_URL', plugins_url().'/'.basename(dirname(__FILE__)));

require_once( WS_PLUGIN_DIR_PATH. 'functions.php');
