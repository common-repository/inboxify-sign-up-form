<?php

/**
 * @package Inboxify\Wordpress
 */
namespace Inboxify\Wordpress;

/**
 * Wordpress mce buttons and plug-ins implementation.
 * @package Inboxify\Wordpress
 */
class Button
{
    const ID = 'iysc';
    
    /**
     * Constructor: add admin init action
     */
    public function __construct()
    {
        add_action('admin_init', array($this, 'init'));
    }
    
    /**
     * Initialize editor button and plugin
     */
    public function init()
    {
        add_filter('mce_buttons', array($this, 'addButton'));
        add_filter('mce_external_plugins', array($this, 'addPlugin'));
    }
    
    /**
     * Add button to TinyMCE
     */
    public function addButton($buttons)
    {
        array_push($buttons, self::ID);
        
        return $buttons;
    }
    
    /**
     * Add plug-in to TinyMCE
     */
    public function addPlugin($plugin_array)
    {
        $plugin_array[self::ID] = plugins_url('/assets/button.js', WPIY_PLUGIN);
        
        return $plugin_array;
    }
}
