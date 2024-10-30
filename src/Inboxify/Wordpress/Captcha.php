<?php

/**
 * @package Inboxify\Wordpress
 */
namespace Inboxify\Wordpress;

/**
 * Implementation of the 3 most used Captcha Plug-ins for WP
 * @package Inboxify\Wordpress
 */
class Captcha
{
    /**
     * @var string really simple captcha id
     */
    const CAPTCHA_RSC = 'really-simple-captcha';
    /**
     * @var string si captcha for wordpress id
     */
    const CAPTCHA_SI = 'si-captcha-for-wordpress';
    /**
     * @var string recaptcha for wp id
     */
    const CAPTCHA_RE = 'wp-recaptcha';
    /**
     * @var string invisible recaptcha for wp id
     */
    const CAPTCHA_IRE = 'invisible-recaptcha';
    /**
     * @var string catpcha input id
     */
    const ID = 'wpiy-captcha';
    /**
     * @var string catpcha input name
     */
    const NAME = 'wpiy-captcha';
    
    /**
     * Singleton instance
     * @var Captcha self
     */
    protected static $instance;
    
    /**
     * @var array auto-detected captchas
     */
    protected $active = array();
    
    /**
     * @var array assoc. array of captcha definitions
     */
    protected $captchas = array(
        self::CAPTCHA_RSC => array(
            'enabled' => false,
            'name' => 'Really Simple Captcha',
            'plugin' => 'really-simple-captcha/really-simple-captcha.php'
        ),
        self::CAPTCHA_SI => array(
            'enabled' => false,
            'name' => 'SI Captcha for Wordpress',
            'plugin' => 'si-captcha-for-wordpress/si-captcha.php'
        ),
        self::CAPTCHA_RE => array(
            'enabled' => false,
            'name' => 'WP ReCaptcha Integration',
            'plugin' => 'wp-recaptcha-integration/wp-recaptcha-integration.php'
        ),
        self::CAPTCHA_IRE => array(
            'enabled' => false,
            'name' => 'Invisible ReCaptcha',
            'plugin' => 'invisible-recaptcha/invisible-recaptcha.php'
        )
    );
    
    /**
     * @var string selected captcha plug-in
     */
    protected $plugin;
    
    /**
     * @var string really simple captcha prefix
     */
    protected $rscPrefix;
    
    /**
     * Create singleton instance of this class
     * @return Captcha
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        
        return self::$instance;
    }
    
    /**
     * Protected constructor
     */
    protected function __construct()
    {
        // INFO include this file, because we need is_plugin_active() function
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        foreach($this->captchas as $k => $captcha) {
            $this->captchas[$k]['enabled'] = is_plugin_active($captcha['plugin']);
        }
    }
    
    /**
     * Set current Captcha plug-in
     * @param string selected captcha plug-in
     */
    public function setPlugin($plugin)
    {
        $this->plugin = $this->checkPlugin($plugin) ? $plugin : null;
    }
    
    /**
     * Check if plug-in was set
     * @return boolean
     */
    public function hasPlugin()
    {
        return !empty($this->plugin) && $this->checkPlugin($this->plugin);
    }
    
    /**
     * Will return assoc. array of active Captcha plug-ins
     * @param boolean $noCaptcha if true first item will be no captcha
     * @return array assoc. array of active Captcha plug-ins
     */
    public function getActiveCaptchas($noCaptcha = true)
    {
        if (!count($this->active)) {
            if ($noCaptcha) {
                $this->active[0] = L10n::__( 'No CAPTCHA' );
            }

            foreach( $this->captchas as $k => $captcha ) {
                if ( $captcha['enabled'] ) {
                    $this->active[$k] = $captcha['name'];
                }
            }
        }
        
        return $this->active;
    }
    
    /**
     * Render selected Captcha plug-in
     * @param string $id captcha input id
     * @param string $name captcha input name
     * @param boolean $print if true, captcha will print, otherwise return
     * @return boolean|string
     */
    public function render($id = self::ID, $name = self::NAME, $print = false)
    {
        if (!$this->hasPlugin()) {
            return null;
        }
        
        if (self::CAPTCHA_IRE == $this->plugin) {
            $label = null;
        } else if ('grecaptcha' == \WP_reCaptcha::instance()->get_option('recaptcha_flavor')) {
            $label = L10n::__('Captcha Challenge');
        } else {
            $label = L10n::__('What code is in the image?');
        }
        
        $t = new Template(
            'captcha', array(
                'captcha' => $this->renderPlugin($id, $name),
                'id' => $id,
                'label' => $label,
                'name' => $name
            )
        );
        
        return $print ? print $t : (string) $t;
    }
    
    /**
     * Render captcha plug-in
     * @param string $id captcha input id
     * @param string $name captcha input name
     * @return string rendered captcha
     */
    public function renderPlugin($id, $name)
    {
        $method = $this->renderPluginMethod();
        return $this->$method($id, $name);
    }
    
    /**
     * Generate plug-in render method
     * @param null|string $plugin null or plugin
     * @return string method name
     */
    public function renderPluginMethod($plugin = null)
    {
        if (is_null($plugin)) {
            $plugin = $this->plugin;
        }
        
        return 'renderPlugin' . str_replace(' ', '', ucwords( str_replace('-', ' ', $plugin) ) );
    }
    
    /**
     * Render Invisible recaptcha plug-in
     * @param string $id captcha input id
     * @param string $name captcha input name
     * @return string rendered captcha
     */
    public function renderPluginInvisibleRecaptcha($id, $name)
    {
        $html = '<div id="iy-ire-wrapper"></div>';
        
        //ob_start();
        //do_action('google_invre_render_widget_action');
        //$html = ob_get_flush();
        
        return $html;
    }
    
    /**
     * Render Really Simple Captcha plug-in
     * @param string $id captcha input id
     * @param string $name captcha input name
     * @return string rendered captcha
     */
    public function renderPluginReallySimpleCaptcha($id, $name)
    {
        $c = new \ReallySimpleCaptcha();
        $html = null;

        $prefix = mt_rand();
        $image = $c->generate_image( $prefix, $c->generate_random_word() );

        if ( $image ) {
            $html = '<img src="' . get_site_url( null, 'wp-content/plugins/really-simple-captcha/tmp/' . $image ) . '" /><br/>';
            $html .= '<input class="inboxify-input" id="' . $id . '-prefix" name="inboxify[' . $name . ']" type="hidden" value="' . $prefix . '" data-name="captcha_prefix" />';
            $html .= '<input class="inboxify-input" id="' . $id . '" name="inboxify[' . $name . ']" type="text" required="required" data-name="captcha" /><br/>';
            $html .= '<small>' . L10n::__('This question is for testing whether or not you are a human visitor and to prevent automated spam submissions.') . '</small>';
        } else {
            $html = L10n::__( 'Generating Really Simple CAPTCHA image failed.' );
        }

        return $html;
    }
    
    /**
     * Render SI Captcha for WP plug-in
     * @param string $id captcha input id
     * @param string $name captcha input name
     * @return string rendered captcha
     */
    public function renderPluginSiCaptchaForWordpress($id, $name)
    {
        global $si_captcha;

        if ( isset( $si_captcha ) && $si_captcha instanceof \siCaptcha ) {
            $prefix = substr(md5(time()), 0, 16);
            $html = $si_captcha->si_captcha_captcha_html('si_image', $prefix, true);
            $html .= '<input class="inboxify-input" id="' . $id . '-prefix" name="inboxify[' . $name . ']" type="hidden" value="' . $prefix . '" data-name="captcha_prefix" />';
            $html .= '<input class="inboxify-input" id="' . $id . '" name="inboxify[' . $name . ']" type="text" required="required" data-name="captcha" /><br/>';
            $html .= '<small>' . L10n::__('This question is for testing whether or not you are a human visitor and to prevent automated spam submissions.') . '</small>';
            
            return $html;
        }

        return null;
    }
    
    /**
     * Render WP Re-Captcha plug-in
     * @param string $id captcha input id
     * @param string $name captcha input name
     * @return string rendered captcha
     */
    public function renderPluginWpRecaptcha($id, $name)
    {
        if ( class_exists('\WP_reCaptcha') ) {
            
            ob_start();
            do_action('recaptcha_print' , array());
            
            return ob_get_clean();
        }

        return null;
    }

    /**
     * Validate captcha code
     * @param string $str captcha code
     * @param string $prefix captcha prefix
     * @return null|boolean
     */
    public function validate($str, $prefix = null)
    {
        if (!$this->hasPlugin()) {
            return null;
        }
        
        return $this->validatePlugin($str, $prefix);
    }
    
    /**
     * Validate captcha code using selected plug-in
     * @param string $str captcha code
     * @param string $prefix captcha prefix
     * @return null|boolean
     */
    public function validatePlugin($str, $prefix = null)
    {
        $method = $this->validatePluginMethod();
        return $this->$method($str, $prefix);
    }
    
    /**
     * Generate plug-in render method
     * @param null|string $plugin null or plugin
     * @return string method name
     */
    public function validatePluginMethod($plugin = null)
    {
        if (is_null($plugin)) {
            $plugin = $this->plugin;
        }
        
        return 'validatePlugin' . str_replace(' ', '', ucwords( str_replace('-', ' ', $plugin) ) );
    }
    
    /**
     * Validate captcha code using Invisible reaptcha
     * @param string $str captcha code
     * @param string $prefix captcha prefix
     * @return null|boolean
     */
    public function validatePluginInvisibleRecaptcha($str, $prefix)
    {
        return apply_filters('google_invre_is_valid_request_filter', true);
    }
    
    /**
     * Validate captcha code using Really Simple Captcha
     * @param string $str captcha code
     * @param string $prefix captcha prefix
     * @return null|boolean
     */
    public function validatePluginReallySimpleCaptcha($str, $prefix)
    {
        $c = new \ReallySimpleCaptcha();
        return $c->check( $prefix, $str );
    }
    
    /**
     * Validate captcha code using SI Captcha for WP
     * @param string $str captcha code
     * @param string $prefix captcha prefix
     * @return null|boolean
     */
    public function validatePluginSiCaptchaForWordpress($str, $prefix)
    {
        global $si_captcha;
        $_POST['si_captcha_code'] = $str;

        if ( isset( $si_captcha ) && $si_captcha instanceof \siCaptcha ) {
            return 'valid' == $si_captcha->si_captcha_validate_code($prefix);
        }
        
        return false;
    }
    
    /**
     * Validate captcha code using WP Re-Captcha
     * @param string $str captcha code
     * @param string $prefix captcha prefix
     * @return null|boolean
     */
    public function validatePluginWpRecaptcha($str)
    {
        if ( class_exists('\WP_reCaptcha') ) {
            $private_key = \WP_reCaptcha::instance()->get_option( 'recaptcha_privatekey' );
            $remote_ip = $_SERVER['REMOTE_ADDR'];
            $user_response = isset( $_REQUEST['captcha'] ) ? $_REQUEST['captcha'] : false;
            $user_response2 = isset( $_REQUEST['captcha_response'] ) ? $_REQUEST['captcha_response'] : false;
            
            if ($user_response2) {
                \WP_reCaptcha_ReCaptcha::instance(); // trigger class loading
                $last_result = recaptcha_check_answer( $private_key, $remote_ip, $user_response, $user_response2 );
                
                return $last_result->is_valid === true;
            } elseif ( $user_response !== false ) {
                $url = "https://www.google.com/recaptcha/api/siteverify?secret=$private_key&response=$user_response&remoteip=$remote_ip";
                $response = wp_remote_get( $url );

                if ( ! is_wp_error($response) ) {
                    $response_data = wp_remote_retrieve_body( $response );
                    $this->_last_result = json_decode($response_data);
                } else {
                    $this->_last_result = (object) array( 'success' => false , 'wp_error' => $response );
                }

                return $this->_last_result->success;
            }
        }
        
        return null;
    }

    /**
     * Check if plug-in is valid catpcha plug-in
     * @return null
     */
    public function checkPlugin($plugin)
    {
        // INFO this can throw exception in inconvenient places 
        // (e.g. wrong captcha set -> won't render settings anymore)
        if (!isset($this->captchas[$plugin]) || !$this->captchas[$plugin]['enabled']) {
            return false;
            //throw new \RuntimeException(L10n::__('Invalid CAPTCHA plugin.'));
        }
        
        return true;
    }
}
