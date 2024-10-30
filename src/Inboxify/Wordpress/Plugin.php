<?php

/**
 * @package Inboxify\Wordpress
 */
namespace Inboxify\Wordpress;

/**
 * Wordpress Plug-in API Implementation
 * @package Inboxify\Wordpress
 */
class Plugin
{
    /**
     * @var integer Minimal allowed name length
     */
    const MIN_NAME_LEN = 2;
    /**
     * @var integer default action priority
     */
    const PRIORITY = 10;
    
    /**
     * Self instance for singleton pattern implementation
     * @var Plugin self 
     */
    protected static $instance;
    
    /**
     * @var array associative array of all actions of this plug-in
     */
    protected $actions = array(
        'actions' => array(), 'admin' => array(), 'front' => array()
    );
    
    /**
     * @var AdminMenu Instance of WP API Admin Menu Implementation 
     */
    protected $adminMenu;
    
    /**
     * @var Assets Instance of WP API Assets Implementation 
     */
    protected $assets;
    
    /**
     * @var Button Instance of TinyMCE button implementation
     */
    protected $button;
    
    /**
     * @var Captcha 3 most used Captcha plug-ins integration
     */
    protected $captcha;
    
    /**
     * @var Client Inboxify PHP API Client Wrapper
     */
    protected $client;
    
    /**
     * @var array associative array of all filters of this plug-in
     */
    protected $filters = array(
        'admin' => array(), 'filters' => array(), 'front' => array()
    );
    
    /**
     * @var boolean if client is configured = true, otherwise false
     */
    protected $isConfigured = false;
    
    /**
     * @var Settings Instance of WP API Settings Implementation 
     */
    protected $settings;
    
    /**
     * @var Shortcode Instance of Shortcode component
     */
    protected $shortcode;
    
    /**
     * @var array associative array of post params for subscription
     */
    protected $subscribeData = array(
        'captcha' => FILTER_SANITIZE_STRING,
        'captcha_prefix' => FILTER_SANITIZE_STRING,
        'email' => FILTER_SANITIZE_EMAIL,
        'company' => FILTER_SANITIZE_STRING,
        'first_name' => FILTER_SANITIZE_STRING,
        'middle_name' => FILTER_SANITIZE_STRING,
        'last_name' => FILTER_SANITIZE_STRING,
        'list_id' => FILTER_SANITIZE_STRING,
        'post_id' => FILTER_SANITIZE_NUMBER_INT,
        'type' => FILTER_SANITIZE_STRING,
        'widget_id' => FILTER_SANITIZE_NUMBER_INT,
        'sex' => FILTER_SANITIZE_NUMBER_INT,
        'telephone' => FILTER_SANITIZE_STRING,
        'mobile' => FILTER_SANITIZE_STRING,
        'address' => FILTER_SANITIZE_STRING,
        'postalCode' => FILTER_SANITIZE_STRING,
        'city' => FILTER_SANITIZE_STRING,
        'countryCode' => FILTER_SANITIZE_STRING,
        'custom_date' => FILTER_SANITIZE_STRING
    );
    
    /**
     * @var array associative array of post params for api validation
     */
    protected $validateData = array(
        'end_point' => FILTER_SANITIZE_STRING,
        'key' => FILTER_SANITIZE_STRING,
        'secret' => FILTER_SANITIZE_STRING,
        'ttl' => FILTER_SANITIZE_NUMBER_INT
    );
    
    /**
     * Create singlton instance
     * @return Plugin self
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        
        return self::$instance;
    }
    
    /**
     * Protected singleton constructor.
     * 
     * Method initiate the plug-in, by creating WP API Implementation Instances,
     * and hooking up the actions, filters and other WP API calls.
     */
    protected function __construct()
    {
        $this->init();
        $this->actions();
        $this->hook();
        
        register_activation_hook(WPIY_PLUGIN, array($this, 'onActivation'));
    }
    
    /**
     * Create instances of WP API Implementation Classes. Only selected optional 
     * features will be initiated.
     */
    protected function init()
    {
        $this->button = new Button();
        $this->captcha = Captcha::getInstance();
        $this->settings = new Settings($this->captcha);
        
        // if is captcha plugin set
        if ($this->settings->getOption(Settings::CAPTCHA)) {
            $this->captcha->setPlugin($this->settings->getOption(Settings::CAPTCHA));
        }
        
        $this->assets = new Assets();
        $this->adminMenu = new AdminMenu();
        
        try {
            $this->client = Client::getInstance($this->settings->getClientConfigData());
            $this->isConfigured = true;
        } catch (\UnexpectedValueException $e) {
            $this->isConfigured = false;
        }
        
        // optional feature: shortcode
        if ($this->settings->hasFeature(Settings::SHORTCODE)) {
            $this->shortcode = new Shortcode();
        }
    }
    
    /**
     * Initiate actions implemented by this plug-in. Only selected optional features 
     * will be implemented.
     */
    protected function actions()
    {
        if (!$this->isConfigured) {
            
            $this->actions['actions'] = array(
                'plugins_loaded' => array($this, 'onPluginsLoaded'),
                'wp_ajax_inboxify_api_validate' => array($this, 'onAjaxApiValidate'),
            );

            $this->actions['admin'] = array(
                'admin_enqueue_scripts' => array($this->assets, 'onAdminEnqueueScripts'),
                'admin_init' => array($this->settings, 'onAdminInit'),
                'admin_menu' => array($this->adminMenu, 'onAdminMenu'),
                'admin_notices' => array($this->settings, 'onAdminNotices')
            );

            return;
        }
        
        $this->actions['actions'] = array(
            'plugins_loaded' => array($this, 'onPluginsLoaded'),
            'wp_ajax_inboxify_api_validate' => array($this, 'onAjaxApiValidate'),
            'wp_ajax_inboxify_subscribe' => array($this, 'onAjaxSubscribe'),
            'wp_ajax_inboxify_tinymce' => array($this, 'onAjaxTinymce'),
            //'wp_ajax_inboxify_tinymce_sg' => array($this, 'onAjaxTinymceSg'),
            'wp_ajax_nopriv_inboxify_subscribe' => array($this, 'onAjaxSubscribe'),
        );
        
        $this->actions['admin'] = array(
            'admin_enqueue_scripts' => array($this->assets, 'onAdminEnqueueScripts'),
            'admin_init' => array($this->settings, 'onAdminInit'),
            'admin_menu' => array($this->adminMenu, 'onAdminMenu'),
            'admin_notices' => array($this->settings, 'onAdminNotices')
        );
        
        $this->actions['front'] = array(
            'wp_enqueue_scripts' => array($this->assets, 'onEnqueueScripts'),
            'wp_head' => array($this, 'onWpHead')
        );
        
        // conditional actions
        
        // optional feature: comment form
        if ($this->settings->hasFeature(Settings::COMMENTS)) {
            $this->actions['actions']['wp_insert_comment'] = array('argc' => 2, 'callback' => array($this, 'onWpInsertComment'));
            
            $this->filters['front'] = array(
                'comment_form_submit_button' => array($this, 'filterCommentFormSubmitButton')
            );
        }
        
        // optional feature: registration
        if ($this->settings->hasFeature(Settings::REGISTRATION)) {
            $this->actions['actions']['register_form'] = array($this, 'onRegisterForm');
            $this->actions['actions']['user_register'] = array($this, 'onUserRegister');
        }
        
        // optional feature: user profile (both self / others) forms
        if ($this->settings->hasFeature(Settings::PROFILE)) {
            $this->actions['actions']['edit_user_profile'] = array($this, 'onEditUserProfile');
            $this->actions['actions']['profile_update'] = array('argc' => 2, 'callback' => array($this, 'onUserUpdate'));
            $this->actions['actions']['show_user_profile'] = array($this, 'onEditUserProfile');
        }
        
        // optional feature: widget
        if ($this->settings->hasFeature(Settings::WIDGET)) {
            $this->actions['actions']['widgets_init'] = array($this, 'onWidgetsInit');
        }
    }
    
    /**
     * Add tags to contact
     * @param \stdClass $contact contact object
     * @param array|string $tags string comma separated tags or array of tags (or empty) 
     * @param array allowed tags (if empty all allowed, otherwise only in array allowed)
     */
    protected function addTags(\stdClass $contact, $tags, $allowed = [])
    {
        if (is_string($tags)) {
            $tags = trim($tags);
        }
        
        if (empty($tags)) {
            return null;
        }
        
        if (!isset($contact->tags) || !is_array($contact->tags)) {
            $contact->tags = array();
        }
        
        $tags = is_array($tags) ? $tags : self::strToArray($tags);
        
        if (count($allowed)) {
            $tmp = [];
            
            foreach($tags as $tag) {
                if (in_array($tag, $allowed)) {
                    $tmp[] = $tag;
                }
            }
            
            $tags = $tmp;
        }
        
        $contact->tags = array_merge($contact->tags, $tags);
        $contact->tags = array_map('trim', $contact->tags);
    }
    
    /**
     * "Hook" actions and filters 
     */
    protected function hook()
    {
        $key = is_admin() ? 'admin' : 'front';
        $actions = array_merge($this->actions['actions'], $this->actions[$key]);
        
        foreach($actions as $action => $callback) {
            if (isset($callback['callback'])) {
                $tmp = $callback;
                $callback = $callback['callback'];
                $priority = isset($tmp['priority']) ? $tmp['priority'] : self::PRIORITY;
                $argc = isset($tmp['argc']) ? $tmp['argc'] : 1;
            } else {
                $priority = self::PRIORITY;
                $argc = 1;
            }
            
            add_action($action, $callback, $priority, $argc);
        }
        
        $filters = array_merge($this->filters['filters'], $this->filters[$key]);
        
        foreach($filters as $filter => $callback) {
            add_filter($filter, $callback);
        }
    }
    
    /**
     * Get post parameter containing value of subscribe checkbox.
     * @return integer 
     */
    protected function getSubscribe()
    {
        return filter_input(INPUT_POST, 'inboxify_subscribe', FILTER_SANITIZE_NUMBER_INT);
    }
    
    /**
     * Get settings component
     * @return Settings
     */
    public function getSettings()
    {
        return $this->settings;
    }
    
    /**
     * Handle subscription of an email to Inboxify API
     * @param string $email email to subscribe
     * @param boolean $subscribe yes / no
     * @param boolean $tags true = set default tags
     * @param null|string $email_old email before changed
     * @return boolean|\stdClass new contact or update result
     */
    protected function handleSubscribe($email, $subscribe = true, $tags = true, $email_old = null)
    {
        $contact = $this->client->getContact($email_old ? $email_old : $email);
        $defaultTags = $this->settings->getOption(Settings::TAGS);
        
        // create contact
        if ( is_null( $contact ) ) {
            $newContact = new \stdClass();
            $newContact->email = $email;
            $newContact->unsubscribed = !$subscribe;
            
            if ( $tags ) {
                $this->addTags( $newContact, $defaultTags );
            }

            $rs = $this->client->postContact( $newContact );
        // update contact
        } else {
            $newContact = clone $contact;
            
            if ($email_old) {
                $newContact->email = $email;
            }
            
            $newContact->unsubscribed = !$subscribe;
            
            if ( $tags ) {
                $this->addTags( $newContact, $defaultTags );
            }

            $rs = $this->client->putContact(
                $email_old ? $email_old : $email,
                $newContact
            );
        }
        
        return $rs;
    }

    /**
     * Render subscribe checkbox
     * @param \stdClass $user wordpress user instance, or null for current user
     * @param boolean $subscribed current subscription status
     * @param boolean $print true = print, false = return
     * @return integer|string 
     */
    protected function subscribeCheckbox($user, $subscribed = false, $print = true)
    {
        if ( ! $user ) {
            $user = wp_get_current_user();
        }
        
        $t = new Template(
            'checkbox', array('checked' => $subscribed ? ' checked="checked"' : null, 'label' => $this->settings->getOption(Settings::LABEL_CHECKBOX_LABEL), 'title' => $this->settings->getOption(Settings::LABEL_CHECKBOX_TITLE))
        );
        
        return $print ? print $t : (string)$t;
    }
    
    /**
     * Get AJAX Settings from either post or widget instance.
     * @param null|integer $widget_id null or widget instance id
     * @param null|integer $post_id null or widget instance id
     * @return array associative array of settings
     */
    protected function getAjaxSubscribeSettings($widget_id, $post_id = null)
    {
        if ($widget_id) {
            $widget = new \Inboxify\Wordpress\Widget\Subscribe;
            $widget->id = $widget->id_base . '-' . $widget_id;
            $settings = $widget->getSettings();
        } else if ($post_id) {
            $settings = $this->shortcode->getSettingsFromPost($post_id);
        }
        
        $settings['captcha'] = $this->settings->getOption(Settings::CAPTCHA);
        
        return $settings;
    }

    /**
     * Get subscribe data
     * @return array associative array of submitted data
     */
    protected function getAjaxSubscribeData()
    {
        $data = filter_input_array(INPUT_POST, $this->subscribeData);
        
        $data['tags'] = is_array($_POST['tags']) ? $_POST['tags'] : self::strToArray($_POST['tags']);
        
        if (count($data['tags'])) {
            foreach($data['tags'] as $i => $tag) {
                $data['tags'][$i] = filter_var($tag, FILTER_SANITIZE_STRING);
            }
        }
        
        return $data;
    }
    
    public function getClient()
    {
        return $this->client;
    }
    
    /**
     * Run submitted data validation
     * @param array $data submitted data
     * @param array $settings ajax settings
     * @return array associative array of invalid elements (or empty array)
     */
    protected function validateAjaxData(array $data, array $settings)
    {
        $invalid = array();
        $captcha = $settings['captcha'];
        $fnr = isset($settings['first_name_required']) && $settings['first_name_required'];
        $mnr = isset($settings['middle_name_required']) && $settings['middle_name_required'];
        $lnr = isset($settings['last_name_required']) && $settings['last_name_required'];
        $cdr = isset($settings['custom_date_required']) && $settings['custom_date_required'];
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $invalid[] = 'email';
        }
        
        if ($fnr && strlen($data['first_name']) < self::MIN_NAME_LEN) {
            $invalid[] = 'first_name';
        }
        
        if ($mnr && strlen($data['middle_name']) < self::MIN_NAME_LEN) {
            $invalid[] = 'middle_name';
        }
        
        if ($lnr && strlen($data['last_name']) < self::MIN_NAME_LEN) {
            $invalid[] = 'last_name';
        }
        
        if ($cdr) {
            $expected = date('Y-m-d', strtotime($data['custom_date']));
            
            if ($expected != $data['custom_date']) {
                $invalid[] = 'custom_date';
            }
        }
        
        if ($captcha && !$this->captcha->validate($data['captcha'], $data['captcha_prefix'])) {
            $invalid[] = 'captcha';
        }
        
        return $invalid;
    }
    
    /**
     * Render subscription checkbox in comment form
     * @param string $button comment button... which we don't actually change at all
     * @return string
     */
    public function filterCommentFormSubmitButton($button)
    {
        $contact = null;
        $subscribed = false;
        $user = wp_get_current_user();

        if ( is_object($user) && $user->ID > 0 ) {
            $contact = $this->client->getContact( $user->user_email );
            $subscribed = is_object($contact) && !$contact->unsubscribed;
        }

        if ( ! $subscribed ) {
            $this->subscribeCheckbox( $user, $subscribed );
        }

        return $button;
    }
    
    /**
     * This function is run upon plug-in activation. Try to import BudgetMailer
     * sign-up form settings.
     */
    public function onActivation()
    {
        $settings_bm = get_option(Settings::GROUP_BM);
        $settings_iy = get_option(Settings::GROUP);
        
        if ($settings_bm && !$settings_iy) {
            $settings_new = $settings_bm;
            $settings_new[Settings::API_ENDPOINT] 
                = Settings::API_ENDPOINT_DEFAULT;
            
            update_option(Settings::GROUP, $settings_new);
        }
    }
    
    /**
     * This is ajax subscribe action handler. It will create new contact 
     * in configured Inboxify contact list.
     */
    public function onAjaxSubscribe()
    {
        $response = new \stdClass();
        
        try {
            $data = $this->getAjaxSubscribeData();//var_dump($data);die();
            $settings = $this->getAjaxSubscribeSettings($data['widget_id'], $data['post_id']);
            $invalid = $this->validateAjaxData($data, $settings);
            
            $list_id = isset($data['list_id']) && $data['list_id'] ? $data['list_id'] : null;
            
            if (count($invalid)) {
                $response->invalid = $invalid;
                $response->success = false;
            } else {
                $contact = $this->client->getContact( $data['email'], $list_id );

                if ( ! $contact ) {
                    $contact = new \stdClass();
                    $new = true;
                } else {
                    $new = false;
                }

                $contact->email = $data['email'];
                $contact->companyName = $data['company'];
                $contact->firstName = $data['first_name'];
                $contact->insertion = $data['middle_name'];
                $contact->lastName = $data['last_name'];
                $contact->sex = $data['sex'];
                $contact->telephone = $data['telephone'];
                $contact->mobile = $data['mobile'];
                $contact->address = $data['address'];
                $contact->postalCode = $data['postalCode'];
                $contact->city = $data['city'];
                $contact->countryCode = $data['countryCode'];
                $contact->customDate = $data['custom_date'];

                foreach($contact as $k => $v) {
                    if (is_null($v)) {
                        unset($contact->{$k});
                    }
                }

                if ('' === $contact->sex) {
                    unset($contact->sex);
                }
                
                $contact->unsubscribed = false;

                $this->addTags($contact, $settings['tags']);
                $this->addTags($contact, $data['tags'], $settings['tags_allowed_array']);
                
                if ($new) {
                    $this->client->postContact( $contact, $list_id );
                    $response->success = true;
                } else {
                    $this->client->putContact( $contact->email, $contact, $list_id );
                    $response->success = true;
                }
            }
        } catch (\Exception $e) {
            $response->exception = $e->getMessage();
            $response->success = false;
        }

        wp_die( json_encode( $response ) );
    }
    
    public function onAjaxTinymce()
    {
        $vars = array(
            'instance' => array(),
            'map' => $this->shortcode->getMap()
        );
        
        print new Template('button', $vars);
        
        wp_die();
    }
    
    /**
     * AJAX callback for validation of API credentials and loading lists from API.
     */
    public function onAjaxApiValidate()
    {
        $response = new \stdClass();
        
        try {
            $data = filter_input_array(INPUT_POST, $this->validateData);
            $data['ttl'] = intval($data['ttl']); // php is stupid (filter number int returning string...)
            
            $config = $this->settings->getClientConfigData();
            
            $config_override = array(
                'cache' => false,
                'endPoint' => $data['end_point'],
                'key' => $data['key'],
                'list' => null,
                'secret' => $data['secret'],
                'ttl' => $data['ttl']
            );
            
            $config = array_merge($config, $config_override);
            $client = Client::getTemporaryClientInstance($config);
            
            $response->success = $client->isConnected();
            
            if ($response->success) {
                $response->list = $this->settings->getOption(Settings::_LIST);
                
                $lists = $client->getLists(false);
                
                foreach($lists as $list) {
                    $response->lists[$list->id] = $list->list;
                }
            } else {
                $response->list = null;
                $response->lists = array();
            }
        } catch (\Exception $e) {
            $response->exception = $e->getMessage();
            $response->success = false;
        }

        wp_die( json_encode( $response ) );
    }
    
    /**
     * Display subscription checkbox on user profile
     * @param \stdClass $user current user
     */
    public function onEditUserProfile($user = null)
    {
        $contact = $this->client->getContact($user->user_email);
        $this->subscribeCheckbox( $user, is_object($contact) && !$contact->unsubscribed );
    }
    
    /**
     * Helper method, call multiple on plug-ins loaded events
     */
    public function onPluginsLoaded()
    {
        \Inboxify\Wordpress\L10n::onPluginsLoaded();
        $this->settings->onPluginsLoaded();
    }
    
    /**
     * Display subscription checkbox on registration form
     */
    public function onRegisterForm( )
    {
        $this->subscribeCheckbox(false);
    }

    /**
     * Handle registration form data submission / subscription
     * @param int $user_id
     * @return mixed
     * @see handleSubscribe()
     */
    public function onUserRegister($user_id)
    {
        if ($this->getSubscribe()) {
            $user = get_userdata($user_id);
            
            return $this->handleSubscribe($user->user_email, $this->getSubscribe());
        }
        
        return null;
    }

    /**
     * Process subscription checkbox displayed on user profile.
     * @param integer $user_id user id
     * @param \stdClass $user_old old user data object
     * @return mixed
     * @see handleSubscribe()
     */
    public function onUserUpdate($user_id, $user_old)
    {
        $user = get_userdata($user_id);
        
        $email_old = null;
        
        if ($user->user_email != $user_old->user_email) {
            $email_old = $user_old->user_email;
        }
        
        return $this->handleSubscribe($user->user_email, $this->getSubscribe(), false, $email_old);
    }
    
    /**
     * Register subscription widget
     * @return null
     */
    public function onWidgetsInit()
    {
        return register_widget( 'Inboxify\Wordpress\Widget\Subscribe' );
    }
    
    /**
     * Make sure there is ajaxurl JS variable on front-end
     */
    public function onWpHead()
    {
        print new Template('head', array('url' => get_admin_url( null, 'admin-ajax.php' )));
    }
    
    /**
     * Handle subscription from comment form
     * @param integer $comment_id comment id
     * @param \stdClass $comment comment instance
     * @return mixed
     * @see handleSubscribe()
     */
    public function onWpInsertComment( $comment_id, $comment )
    {
        if ($comment && $this->getSubscribe()) {
            return $this->handleSubscribe($comment->comment_author_email, $this->getSubscribe());
        }
        
        return null;
    }
    
    public static function strToArray($string)
    {
        $array = [];
        
        $string = trim($string);
        
        if (!empty($string)) {
            if (substr_count($string, ',')) {
                $strings = explode(',', $string);
                
                foreach($strings as $string_part) {
                    $array[] = trim($string_part);
                }
            } else {
                $array[] = $string;
            }
        }
        
        return $array;
    }
    
    public static function arrayToStr($array)
    {
        $string = null;
        
        if (count($array)) {
            foreach($array as $string_part) {
                if (!is_null($string)) {
                    $string .= ', ';
                }
                
                $string .= trim($string_part);
            }
        }
        
        return $string;
    }
}
