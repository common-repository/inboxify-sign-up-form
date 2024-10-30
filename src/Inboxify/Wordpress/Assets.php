<?php

/**
 * @package Inboxify\Wordpress
 */
namespace Inboxify\Wordpress;

/**
 * Wordpress CSS/JS API enqueue implementation
 * @package Inboxify\Wordpress
 */
class Assets
{
    const ADMIN = 'admin';
    const FRONT = 'front';
    /**
     * @var prefix of scripts and styles in order to avoid name conflicts
     */
    const PREFIX = 'inboxify';
    
    /**
     * @var array list of allowed environment keys
     */
    protected $allowedKeys = array(
        self::ADMIN, self::FRONT
    );
    
    /**
     * @var array assoc. array of scripts
     */
    protected $scripts = array(
        self::ADMIN => array(
            self::ADMIN => array(
                'deps' => array('jquery', 'jquery-ui-sortable'),
                'file' => '/assets/admin.js',
                'l10n' => 'localizeBackend'
            )
        ),
        self::FRONT => array(
            self::FRONT => array(
                'deps' => array('jquery'),
                'file' => '/assets/front.js',
                //'l10n' => 'localizeFront' // INFO not applicable in this case (because every inboxify form widget has it's own messages)
            )
        ),
        'scripts' => array()
    );
    
    /**
     * @var array assoc. array of styles
     */
    protected $styles = array(
        self::ADMIN => array(
            self::ADMIN => array(
                'file' => '/assets/admin.css'
            )
        ),
        self::FRONT => array(
            self::FRONT => array(
                'file' => '/assets/front.css'
            )
        ),
        'styles' => array()
    );
    
    /**
     * Check if the given key is allowed
     * @param type $key
     * @throws \RuntimeException
     */
    protected function checkKey($key)
    {
        if (!in_array($key, $this->allowedKeys)) {
            throw new \RuntimeException( sprintf( L10n::__( 'Asset set %s not found.' ), $key ) );
        }
    }
    
    /**
     * Enqueue scripts and styles for current environment key (admin|front)
     * @param string $key environment key (admin|front)
     */
    protected function enqueue($key)
    {
        $this->checkKey($key);
        
        $scripts = array_merge($this->scripts['scripts'], $this->scripts[$key]);
        $styles = array_merge($this->styles['styles'], $this->styles[$key]);
        
        foreach($scripts as $id => $script) {
            $deps = isset($script['deps']) ? $script['deps'] : array();
            $id = self::PREFIX . '-' . $id;
            
            wp_register_script( $id, plugins_url( $script['file'], WPIY_PLUGIN ), $deps );
            
            if (isset($script['l10n'])) {
                $method = $script['l10n'];
                wp_localize_script($id, self::PREFIX, $this->$method());
            }
            
            wp_enqueue_script( $id );
        }
        
        foreach($styles as $id => $style) {
            $deps = isset($style['deps']) ? $script['deps'] : array();
            $id = self::PREFIX . '-' . $id;
            
            wp_register_style( $id, plugins_url( $style['file'], WPIY_PLUGIN ) );
            wp_enqueue_style( $id );
        }
    }
    
    /**
     * Enqueue default and admin scripts.
     */
    public function onAdminEnqueueScripts()
    {
        $this->enqueue('admin');
    }
    
    /**
     * Enqueue default and admin styles.
     */
    public function onEnqueueScripts()
    {
        $this->enqueue('front');
    }
    
    /*protected function localizeFront()
    {
        return array(
            'message_invalid' => L10n::__( 'This field is required.' ),
            'message_invalid_form' => L10n::__( 'Please correct the Form Errors.' ),
            'message_success' => L10n::__( 'You have been successfully signed-up to our Newsletter.' ),
            'message_error' => L10n::__( 'We are terribly sorry, but we couldn\'t sign You up to your Newsletter due to unexpected Error.' )
        );
    }*/
    
    protected function localizeBackend()
    {
        return array(
            'button_label' => L10n::__('Inboxify Shortcode Generator'),
            'error_message' => L10n::__('Validating API credentials failed.'),
            'no_credentials' => L10n::__('No API credentials.'),
            'select_list' => L10n::__('Select contact list'),
            'ok' => L10n::__('OK')
        );
    }
}
