<?php

namespace Inboxify\Wordpress;

/**
 * Wordpress Settings API Implementation
 * @package Inboxify\Wordpress
 */
class Settings
{
    /**
     * @var string list option name
     */
    const _LIST = 'list';
    /**
     * @var string api endpoint option name
     */
    const API_ENDPOINT = 'api_end_point';
    /**
     * @var string default api endpoint
     */
    const API_ENDPOINT_DEFAULT = 'https://api.inboxify.nl/';
    /**
     * @var string api key option name
     */
    const API_KEY = 'api_key';
    /**
     * @var string api secret option name
     */
    const API_SECRET = 'api_secret';
    
    /**
     * @var string captcha option name
     */
    const CAPTCHA = 'captcha';
    
    /**
     * @var string comments feature name
     */
    const COMMENTS = 'comments';
    
    /**
     * @var integer default time for inboxify api client
     */
    const DEFAULT_TIMEOUT = 10;
    /**
     * @var string features option name
     */
    const FEATURES = 'features';
    
    /**
     * @var string settings group name
     */
    const GROUP = 'inboxify';
    
    /**
     * @var string settings group name of budgetmailer sign up form
     */
    const GROUP_BM = 'budgetmailer';
    
    /**
     * @var string subscription checkbox title
     */
    const LABEL_CHECKBOX_TITLE = 'label_checkbox_title';
    /**
     * @var string subscription checkbox label
     */
    const LABEL_CHECKBOX_LABEL = 'label_checkbox_label';
    
    /**
     * @var string settings page slug
     */
    const PAGE_SLUG = 'inboxify';
    
    /**
     * @var string profile feature name
     */
    const PROFILE = 'profile';
    
    /**
     * @var string registration feature name
     */
    const REGISTRATION = 'registration';
    
    /**
     * @var string general settings section name
     */
    const SECTION_GENERAL = 'general';
    /**
     * @var string advanced settings section name
     */
    const SECTION_ADVANCED = 'advanced';
    
    /**
     * @var string shortcode feature name
     */
    const SHORTCODE = 'shortcode';
    
    /**
     * @var string status field name
     */
    const STATUS = 'status';
    
    /**
     * @var string general settings section title
     */
    const TITLE_GENERAL = 'General settings';
    /**
     * @var string advanced settings section title
     */
    const TITLE_ADVANCED = 'Advanced settings';
    
    /**
     * @var string tags option name
     */
    const TAGS = 'tags';
    
    /**
     * @var string ttl option name
     */
    const TTL = 'ttl';
    
    /**
     * @var string widget feature name
     */
    const WIDGET = 'widget';

    /**
     * Configuratino options definition.
     * @var array associative array of sections and their fields
     */
    protected $config = array(
        self::SECTION_GENERAL => array(
            'callback' => self::SECTION_GENERAL,
            'fields' => array(
                self::API_ENDPOINT => array(
                    'attributes' => array('required' => 'required', 'type' => 'url'),
                    'default' => 'https://api.inboxify.nl/',
                    'description' => 'Inboxify API endpoint. Please don\'t change the URL unless you are instructed to.',
                    'tag' => 'input',
                    'title' => 'API endpoint',
                ),
                self::API_KEY => array(
                    'attributes' => array('required' => 'required', 'type' => 'text'),
                    'default' => '',
                    'description' => 'Insert your Inboxify API key.',
                    'tag' => 'input',
                    'title' => 'API key'
                ),
                self::API_SECRET => array(
                    'attributes' => array('required' => 'required', 'type' => 'text'),
                    'default' => '',
                    'description' => 'Insert your Inboxify API secret.',
                    'tag' => 'input',
                    'title' => 'API secret'
                ),
                self::_LIST => array(
                    'default' => false,
                    'description' => 'Please select the Inboxify list you want to use with WordPress.',
                    'tag' => 'select',
                    'title' => 'Default contact list',
                ),
                self::STATUS => array(
                    'default' => true,
                    'tag' => 'p',
                    'title' => 'API Status',
                )
            ),
            'page' => self::PAGE_SLUG,
            'title' => self::TITLE_GENERAL
        ),
        self::SECTION_ADVANCED => array(
            'callback' => self::SECTION_ADVANCED,
            'fields' => array(
                self::CAPTCHA => array(
                    'default' => false,
                    'description' => 'Please select one of the supported CAPTCHA plugins.',
                    'tag' => 'select',
                    'title' => 'CAPTCHA plugin'
                ),
                self::FEATURES => array(
                    'attributes' => array('multiple' => 'multiple'),
                    'default' => null,
                    'description' => 'Select the features you wish to enable this plugin for. Hold CTRL to enable multiple features.',
                    'tag' => 'select',
                    'title' => 'Supported features'
                ),
                self::LABEL_CHECKBOX_TITLE => array(
                    'attributes' => array('required' => 'required', 'type' => 'text'),
                    'default' => 'Newsletter subscription',
                    'description' => 'This text will be displayed as a title above subscription checkbox in the comment and registration forms.',
                    'tag' => 'input',
                    'title' => 'Subscription checkbox title'
                ),
                self::LABEL_CHECKBOX_LABEL => array(
                    'attributes' => array('required' => 'required', 'type' => 'text'),
                    'default' => 'Subscribe',
                    'description' => 'This text will be displayed as a label of the subscription checkbox.',
                    'tag' => 'input',
                    'title' => 'Subscription checkbox label'
                ),
                self::TAGS => array(
                    'attributes' => array('type' => 'text'),
                    'default' => '',
                    'description' => 'Comma separated list of default tags, that will be used to tag user during registration.',
                    'tag' => 'input',
                    'title' => 'Tags'
                ),
                self::TTL => array(
                    'default' => 3600,
                    'description' => 'Time to live of local contact data for both modes. Please do not change this value unless you understand how this works.',
                    'tag' => 'input',
                    'title' => 'Cache TTL'
                ),
            ),
            'page' => self::PAGE_SLUG,
            'title' => self::TITLE_ADVANCED
        )
    );
    
    /**
     * Used only for gettext...
     * @see TITLE_GENERAL, TITLE_ADVANCED, $config
     */
    private function translationWorkaround()
    {
        L10n::__('General settings');
        
        
        L10n::__('API endpoint');
        L10n::__('Inboxify API endpoint. Please don\'t change the URL unless you are instructed to.');
        
        L10n::__('API key');
        L10n::__('Insert your Inboxify API key.');
        
        L10n::__('API secret');
        L10n::__('Insert your Inboxify API secret.');
        
        L10n::__('Default contact list');
        L10n::__('Please select the Inboxify list you want to use with WordPress.');
        
        L10n::__('API Status');
        
        
        L10n::__('Advanced settings');
        
        
        L10n::__('CAPTCHA plugin');
        L10n::__('Please select one of the supported CAPTCHA plugins.');
        
        L10n::__('Supported features');
        L10n::__('Select the features you wish to enable this plugin for. Hold CTRL to enable multiple features.');
        
        L10n::__('Subscription checkbox title');
        L10n::__('Newsletter subscription');
        L10n::__('This text will be displayed as a title above subscription checkbox in the comment and registration forms.');
        
        L10n::__('Subscription checkbox label');
        L10n::__('Subscribe');
        L10n::__('This text will be displayed as a label of the subscription checkbox.');
        
        L10n::__('Tags');
        L10n::__('Comma separated list of default tags, that will be used to tag user during registration.');
        
        L10n::__('Cache TTL');
        L10n::__('Time to live of local contact data for both modes. Please do not change this value unless you understand how this works.');
    }
    
    /**
     * @var array assoc. array of supported features
     */
    protected $features = array(
        self::COMMENTS => 'Comment form',
        self::PROFILE => 'User profile form',
        self::REGISTRATION => 'Registration form',
        self::SHORTCODE => 'Shortcode',
        self::WIDGET => 'Widget'
    );
    /**
     * @var array assoc. array of current settings
     */
    protected $options = array();
    
    /**
     * Create new instantance of Settings API Implementation
     * @param Captcha $captcha Captcha instance
     */
    public function __construct(Captcha $captcha)
    {
        $this->captcha = $captcha;
        
        $this->init();
    }
    
    /**
     * Initiate settings object... get current options, and setup features
     */
    public function init()
    {
        $this->options = get_option( self::GROUP );
    }
    
    public function onPluginsLoaded()
    {
        $this->features = array(
            self::COMMENTS => L10n::__('Comment form'),
            self::PROFILE => L10n::__('User profile form'),
            self::REGISTRATION => L10n::__('Registration form'),
            self::SHORTCODE => L10n::__('Shortcode'),
            self::WIDGET => L10n::__('Widget')
        );
    }
    
    /**
     * This method will run on admin init action and implement settings sections and fields.
     */
    public function onAdminInit(  )
    {
        register_setting( self::GROUP, self::GROUP, array( $this, 'sanitize' ) );
        
        foreach( $this->config as $section_id => $section ) {
            add_settings_section(
                $section_id, L10n::__( $section['title'] ), 
                $this->getSectionCallback( $section_id ), self::PAGE_SLUG
            );
            
            foreach( $section['fields'] as $field_id => $field_def ) {
                add_settings_field(
                    $field_id, L10n::__( $field_def['title'] ),
                    $this->getFieldCallback( $section_id, $field_id ),
                    self::PAGE_SLUG, $section_id
                );
            }
        }
    }
    
    /**
     * This method will run on admin_notices action and display sanitization 
     * error messages.
     */
    public function onAdminNotices()
    {
        settings_errors( self::GROUP );
    }
    
    /**
     * Callback for general section info.
     */
    public function sectionGeneral()
    {
        //print self::TITLE_GENERAL;
    }
    
    /**
     * Render option field
     */
    public function fieldGeneralApiEndPoint()
    {
        $this->fieldHelper(self::SECTION_GENERAL, self::API_ENDPOINT);
    }
    
    /**
     * Render option field
     */
    public function fieldGeneralApiKey()
    {
        $this->fieldHelper(self::SECTION_GENERAL, self::API_KEY);
    }
    
    /**
     * Render option field
     */
    public function fieldGeneralApiSecret()
    {
        $this->fieldHelper(self::SECTION_GENERAL, self::API_SECRET);
    }
    
    /**
     * Render option field
     */
    public function fieldGeneralList()
    {
        $this->fieldHelper(self::SECTION_GENERAL, self::_LIST);
    }
    
    /**
     * Render option field
     */
    public function fieldGeneralStatus()
    {
        $this->fieldHelper(self::SECTION_GENERAL, self::STATUS);
    }
    
    /**
     * Get list of contact lists in inboxify api
     * @throws \RuntimeException in case it's impossible to get the list of contact lists from api
     */
    public function fieldGeneralListValues()
    {
        global $wp_settings_errors;
        
        $options = array(
            0 => L10n::__( 'Select contact list' )
        );
    
        if ($this->getFieldValue(self::SECTION_GENERAL, self::API_ENDPOINT)
            && $this->getFieldValue(self::SECTION_GENERAL, self::API_KEY)
            && $this->getFieldValue(self::SECTION_GENERAL, self::API_SECRET)
        ) {
            try {
                $c = Client::getInstance();
                $lists = $c->getLists();

                foreach( $lists as $list ) {
                    $options[$list->id] = $list->list;
                }
            } catch ( \Exception $e ) {
                $message = sprintf( L10n::__('Getting list of Contact Lists from Inboxify API failed (%s).'), $e->getMessage() );
                
                if (is_callable('add_settings_error')) {
                    add_settings_error( self::GROUP, self::_LIST, $message );
                }
            }
        }
        
        return $options;
    }

    /**
     * Advanced section info callback.
     */
    public function sectionAdvanced()
    {
        //print 'Advanced Section Info';
    }
    
    /**
     * Render option field
     */
    public function fieldAdvancedCaptcha()
    {
        $this->fieldHelper(self::SECTION_ADVANCED, self::CAPTCHA);
    }
    
    /**
     * Render option field
     */
    public function fieldAdvancedCaptchaValues()
    {
        return $this->captcha->getActiveCaptchas();
    }
    
    /**
     * Render option field
     */
    public function fieldAdvancedFeatures()
    {
        $this->fieldHelper(self::SECTION_ADVANCED, self::FEATURES);
    }
    
    /**
     * Get features field values.
     */
    public function fieldAdvancedFeaturesValues()
    {
        return $this->features;
    }
    
    /**
     * Render option field
     */
    public function fieldAdvancedTags()
    {
        $this->fieldHelper(self::SECTION_ADVANCED, self::TAGS);
    }
    
    /**
     * Render option field
     */
    public function fieldAdvancedTtl()
    {
        $this->fieldHelper(self::SECTION_ADVANCED, self::TTL);
    }
    
    /**
     * Render checkbox title field
     */
    public function fieldAdvancedLabelCheckboxTitle()
    {
        $this->fieldHelper(self::SECTION_ADVANCED, self::LABEL_CHECKBOX_TITLE);
    }
    
    /**
     * Render checkbox title label
     */
    public function fieldAdvancedLabelCheckboxLabel()
    {
        $this->fieldHelper(self::SECTION_ADVANCED, self::LABEL_CHECKBOX_LABEL);
    }
    
    /**
     * Sanitize input... 
     * @param array assoc. array of submitted options
     * @return array assoc. array of sanitized options
     */
    public function sanitize( $input )
    {
        // create default config
        if (!is_array($input) || !count($input)) {
            $input = array();
            
            foreach($this->config as $section_id => $section) {
                foreach($section['fields'] as $field_id => $field) {
                    $input[$field_id] = isset($field['default']) ? L10n::__( $field['default'] ) : null;
                }
            }
        // sanitize fields: use current valid option value, or use default one
        } else {
            foreach($this->config as $section_id => $section) {
                foreach($section['fields'] as $field_id => $field) {
                    if (!isset($input[$field_id])) {
                        $input[$field_id] = L10n::__( $field['default'] );
                    } else {
                        $input[$field_id] = $this->sanitizeField($field_id, $input[$field_id]);
                    }
                }
            }
        }
        
        return $input;
    }
    
    /**
     * Sanitize single setting field.
     * @param string $field_id field id
     * @param mixed $value new field value
     * @see sanitize()
     */
    public function sanitizeField($field_id, $value)
    {
        switch($field_id) {
            case self::API_ENDPOINT:
                $parsed = parse_url($value);
                
                if (!is_array($parsed) || !isset($parsed['host']) || empty($parsed['host'])) {
                    
                    if (is_callable('add_settings_error')) {
                        add_settings_error(self::GROUP, self::API_ENDPOINT, L10n::__( 'Invalid End-Point URL.' ) );
                    }
                    
                    $value = $this->getOption($field_id);
                }
                break;
            case self::API_KEY:
            case self::API_SECRET:
                $value = trim($value);
                break;
            case self::CAPTCHA:
                if (!$this->captcha->checkPlugin($value)) {
                    $value = null;
                }
                break;
            case self::FEATURES:
                $tmp = array();
                
                if (is_array($value) && count($value)) {
                    foreach($value as $feature) {
                        if (isset($this->features[$feature])) {
                            $tmp[] = $feature;
                        }
                    }
                }
                
                $value = $tmp; unset($tmp);
                
                break;
            case self::TTL:
                $value = (int)$value;
                
                if ($value < 0) {
                    $value = 0;
                }
                break;
        }
        
        return $value;
    }
    
    /**
     * Get config data for Inboxify API PHP Client
     */
    public function getClientConfigData()
    {
        $cacheDir = WP_CONTENT_DIR . '/inboxify/';
        
        return array(
            'cache' => is_dir($cacheDir) && is_writable($cacheDir),
            'cacheDir' => $cacheDir,
            'endPoint' => $this->getOption(self::API_ENDPOINT),
            'key' => $this->getOption(self::API_KEY),
            'list' => $this->getOption(self::_LIST),
            'secret' => $this->getOption(self::API_SECRET),
            'timeOutSocket' => self::DEFAULT_TIMEOUT,
            'timeOutStream' => self::DEFAULT_TIMEOUT,
            self::TTL => $this->getOption(self::TTL)
        );
    }
    
    /**
     * Get option or default settings value
     * @param string $name option name
     * @param mixed $default default value or null (in case null default option value will be used)
     * @return type
     */
    public function getOption($name, $default = null)
    {
        if (is_null($default)) {
            if (isset($this->config[self::SECTION_GENERAL][$name])) {
                $default = $this->config[self::SECTION_GENERAL][$name]['default'];
            } else if (isset($this->config[self::SECTION_ADVANCED][$name])) {
                $default = $this->config[self::SECTION_ADVANCED][$name]['default'];
            }
            
            $default = L10n::__($default);
        }
        
        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }
    
    /**
     * Get field callback
     * @param string $section_id section id
     * @param string $field_id field id
     * @return array callback
     */
    protected function getFieldCallback( $section_id, $field_id )
    {
        return array(
            $this, $this->getFieldName( $section_id, $field_id )
        );
    }
    
    /**
     * Get field name
     * @param string $section_id section id
     * @param string $field_id field id
     * @return array field name
     */
    protected function getFieldName( $section_id, $field_id )
    {
        return 'field' . ucfirst( $section_id ) . str_replace( ' ', '', ucwords( str_replace( '_', ' ', $field_id) ) );
    }
    
    /**
     * Get section callback
     * @param string $section_id section id
     * @param string $field_id field id
     * @return array callback
     */
    protected function getSectionCallback( $section_id )
    {
        return array(
            $this, 'section' . ucfirst( $section_id )
        );
    }
    
    /**
     * This helper method render option input field with label, and print or return it.
     * @param string $section_id section id
     * @param string $field_id field id
     * @param boolean $print if true, method will print the input field
     * @return mixed
     */
    protected function fieldHelper($section_id, $field_id, $print = true)
    {
        $content = null;
        $field = $this->config[$section_id]['fields'][$field_id];
        $name = self::GROUP . '[' . $field_id . ']';
        $value = $this->getFieldValue($section_id, $field_id);
        $attributes = array_merge(
            isset( $field['attributes'] ) ? $field['attributes'] : array(), 
            array(
                'id' => $field_id, 
                'name' => $name,
                'value' => $value
            )
        );
        
        if ('select' == $field['tag']) {
            if (isset($attributes['multiple'])) {
                $name .= '[]';
                $attributes['name'] = $name;
                unset($attributes['value']);
            }
            
            $callback = $this->getValuesMethod($section_id, $field_id);
            $values = method_exists($this, $callback) ? $this->$callback() : array();
            
            foreach($values as $k => $v) {
                if (is_array($value)) {
                    $a = in_array($k, $value) ? array('selected' => 'selected') : array();
                } else {
                    $a = $value == $k ? array('selected' => 'selected') : array();
                }
                $a['value'] = $k;
                
                $content .= (string)new Tag('option', $a, $v);
            }
        }
        
        $html = (string) new Tag($field['tag'], $attributes, $content);
        
        if (isset($field['description'])) {
            $html .= (string) new Tag('p', array('class' => 'description'), L10n::__( $field['description'] ) );
        }
        
        return $print ? print $html : $html;
    }
    
    /**
     * Get field value
     * @param string $section_id section id
     * @param string $field_id field id
     * @return mixed field value
     */
    protected function getFieldValue( $section_id, $field_id )
    {   
        return isset( $this->options[$field_id] ) 
            ? $this->options[$field_id] 
            : L10n::__( $this->config[$section_id]['fields'][$field_id]['default'] );
    }
    
    /**
     * Get values method for field
     * @param string $section_id section id
     * @param string $field_id field id
     * @return string method name
     */
    protected function getValuesMethod($section_id, $field_id)
    {
        return $this->getFieldName($section_id, $field_id) . 'Values';
    }
    
    /**
     * Check if plug-in has enabled feature
     * @param string $feature feature id
     * @return boolean true - yes, false - no
     */
    public function hasFeature($feature)
    {
        if (isset($this->features[$feature])) {
            $features = $this->getOption(self::FEATURES);
            
            return is_array($features) && in_array($feature, $features);
        }
        
        return false;
    }
}
