<?php

/**
 * @package Inboxify\Wordpress
 */
namespace Inboxify\Wordpress;

/**
 * WP Shortcode API Implementation
 * @package Inboxify\Wordpress
 */
class Shortcode
{
    const SUBSCRIBE = 'inboxify_subscribe';
    
    protected $map = array(
        't' => 'title',
        'e' => 'email',
        'l' => 'list',
        
        'cl' => 'company_label',
        'cd' => 'company_displayed',
        'cr' => 'company_required',
        
        'fnl' => 'first_name_label',
        'fnd' => 'first_name_displayed',
        'fnr' => 'first_name_required',

        'mnl' => 'middle_name_label',
        'mnd' => 'middle_name_displayed',
        'mnr' => 'middle_name_required',

        'lnl' => 'last_name_label',
        'lnd' => 'last_name_displayed',
        'lnr' => 'last_name_required',

        'sel' => 'sex_label',
        'sed' => 'sex_displayed',
        'ser' => 'sex_required',
        
        'tel' => 'telephone_label',
        'ted' => 'telephone_displayed',
        'ter' => 'telephone_required',
        
        'mol' => 'mobile_label',
        'mod' => 'mobile_displayed',
        'mor' => 'mobile_required',
        
        'adl' => 'address_label',
        'add' => 'address_displayed',
        'adr' => 'address_required',
        
        'zil' => 'zip_label',
        'zid' => 'zip_displayed',
        'zir' => 'zip_required',
        
        'cil' => 'city_label',
        'cid' => 'city_displayed',
        'cir' => 'city_required',
        
        'col' => 'country_label',
        'cod' => 'country_displayed',
        'cor' => 'country_required',
        
        'sl' => 'submit_label',

        'ta' => 'tags',
        'taa' => 'tags_allowed',
        'tal' => 'tags_label',
        'tad' => 'tags_displayed',
        'tar' => 'tags_required',
        'ta_in' => 'tags_input',
        
        'cdl' => 'custom_date_label',
        'cdd' => 'custom_date_displayed',
        'cdr' => 'custom_date_required',
        
        'hb' => 'html_before',
        'ha' => 'html_after',

        'mi' => 'message_invalid',
        'mif' => 'message_invalid_form',
        'ms' => 'message_success',
        'me' => 'message_error'
    );
    
    /**
     * @var array associative array of implemented shortcodes
     */
    protected $shortcodes = array(
        self::SUBSCRIBE => 'shortcodeSubscribe'
    );
    
    /**
     * Create new shortcode instance
     */
    public function __construct()
    {
        $this->init();
    }
    
    /**
     * Add all shortcodes to WP API
     */
    protected function init()
    {
        foreach($this->shortcodes as $id => $method) {
            add_shortcode($id, array($this, $method));
        }
    }
    
    /**
     * Subscription form shortcode
     * @param array $atts associative array of shortcode attributes
     * @return string rendered form
     */
    public function shortcodeSubscribe($atts)
    {
        if (isset($atts['widget_id'])) {
            return $this->shortcodeSubscribeWidget($atts);
        }
        
        $widget = new Widget\Subscribe();
        $settings = $this->attsToSettings($atts, $widget->getDefaults());
        
        $args = array(
            'after_title' => '</h2>',
            'after_widget' => '',
            'before_title' => '<h2>',
            'before_widget' => '',
        );
        
        return $widget->widget($args, $settings, $settings, false);
    }
    
    /**
     * Subscription form shortcode - widget mode
     * @param array $atts associative array of shortcode attributes
     * @return string rendered form
     */
    public function shortcodeSubscribeWidget($atts = array())
    {
        if (!isset($atts['widget_id'])) {
            return L10n::__('Shortcode attribute widget_id is not set.');
        }
        
        $widget_id = $atts['widget_id'];
        $widget = new Widget\Subscribe();
        $widget->_set($widget_id);
        $settings = $widget->get_settings();
        
        if (!isset($settings[$widget_id])) {
            return sprintf( L10n::__('Widget id %d doesn\'t exist.'), $widget_id );
        }
        
        $settings = $settings[$widget_id];
        
        $args = array(
            'after_title' => '</h2>',
            'after_widget' => '',
            'before_title' => '<h2>',
            'before_widget' => '',
        );
        
        return $widget->widget($args, $settings, null, false);
    }
    
    /**
     * Converts shortcode attributes to widget settings
     * @param array $atts assoc. array of attributes
     * @param array $defaults assoc. array of default configuration values
     * @return array converted attributes or default settings
     */
    protected function attsToSettings($atts, $defaults)
    {
        $settings = array();
        
        foreach($this->map as $k => $kk) {
            $settings[$kk] = isset($atts[$k]) ? $atts[$k] : $defaults[$kk];
        }
        
        $settings['tags_allowed_array'] = Plugin::strToArray($settings['tags_allowed']);
        
        // INFO allow html
//        $settings['html_before'] = html_entity_decode($settings['html_before']);
//        $settings['html_after'] = html_entity_decode($settings['html_after']);
        
        return $settings;
    }
    
    /**
     * Get map widget / shortcode
     */
    public function getMap()
    {
        return $this->map;
    }
    
    /**
     * Detects a shortcode in post and grab attributes (this is for ajax)
     * INFO this doesn't support multiple shortcodes
     * @param integer $post_id post id
     * @return array assoc. array of settings
     */
    public function getSettingsFromPost($post_id)
    {
        $atts = array();
        $post = get_post($post_id);
        
        if ($post && $post->ID) {
            $pattern = get_shortcode_regex();
            
            if (   preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches )
                && array_key_exists( 2, $matches )
                && in_array( self::SUBSCRIBE, $matches[2] )
            ) {
                $k = null;
                
                foreach($matches[2] as $k => $v) {
                    if (self::SUBSCRIBE == $v) {
                        break;
                    }
                }
                
                $atts = !is_null($k) ? shortcode_parse_atts($matches[3][$k]) : array();
            }
        }
        
        $widget = new Widget\Subscribe();
        $settings = $this->attsToSettings($atts, $widget->getDefaults());
        
        return $settings;
    }
}
