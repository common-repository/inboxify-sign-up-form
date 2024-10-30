<?php

/**
 * Plugin Name: Inboxify Sign Up Form
 * Plugin URI: http://www.inboxify.nl
 * Description: Easily add Inboxify newsletter sign up forms to your WordPress website with the offical Inboxify Sign Up Form plugin.
 * Version: 1.0.4
 * Author: Inboxify
 * Author URI: http://www.inboxify.nl
 * License: MIT
 */

/**
 * @var string Inboxify Newsletter Sign Up Plug-in Name (File)
 */
define( 'WPIY_PLUGIN', __FILE__ );

// Required PSR-4 Autoloader
require_once 'src/AutoloaderPsr4.php';

// Initiate and configure PSR-4 Autoloader
$alpsr4 = new AutoloaderPsr4( array(
    'Inboxify\Api' => __DIR__ . '/vendor/inboxify-api-php/src/Inboxify/Api',
    'Inboxify\Wordpress' => __DIR__ . '/src/Inboxify/Wordpress'
) );

// Create new Instance of Inboxify WordPress Plugin
$GLOBALS['wpiy_plugin'] = \Inboxify\Wordpress\Plugin::getInstance();
