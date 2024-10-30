<?php

/**
 * @package Inboxify\Wordpress\Widget
 */
namespace Inboxify\Wordpress\Widget;

use Inboxify\Wordpress\Captcha;
use Inboxify\Wordpress\L10n;
use Inboxify\Wordpress\Plugin;
use Inboxify\Wordpress\Tag;
use Inboxify\Wordpress\Template;

/**
 * Inboxify Subscription Widget
 * @package Inboxify\Wordpress\Widget
 */
class Subscribe extends \WP_Widget
{
    protected static $defaults = array();
    
    protected $fields = array(
        'email' => array(
            'attributes' => array('class' => 'bmsortables', 'required' => 'required', 'type' => 'text'),
            'default' => 'Email',
            'tag' => 'input', 
            'title' => ''
        ),
        'email_displayed' => array(
            'attributes' => array('checked' => 'checked', 'disabled' => 'disabled', 'type' => 'checkbox'),
            'default' => false,
            'tag' => 'input',
            'title' => ''
        ),
        'email_required' => array(
            'attributes' => array('checked' => 'checked', 'disabled' => 'disabled', 'type' => 'checkbox'),
            'default' => false,
            'tag' => 'input',
            'title' => ''
        ),
        
        'company_label' => array(
            'attributes' => array('class' => 'bmsortables', 'required' => 'required', 'type' => 'text'),
            'default' => 'Company',
            'tag' => 'input',
            'title' => '',
        ),
        'company_displayed' => array(
            'attributes' => array('type' => 'checkbox'),
            'default' => false,
            'tag' => 'input',
            'title' => ''
        ),
        'company_required' => array(
            'attributes' => array('type' => 'checkbox'),
            'default' => false,
            'tag' => 'input',
            'title' => ''
        ),

        'first_name_label' => array(
            'attributes' => array('class' => 'bmsortables', 'required' => 'required', 'type' => 'text'),
            'default' => 'First name',
            'tag' => 'input',
            'title' => '',
        ),
        'first_name_displayed' => array(
            'attributes' => array('type' => 'checkbox'),
            'default' => false,
            'tag' => 'input',
            'title' => ''
        ),
        'first_name_required' => array(
            'attributes' => array('type' => 'checkbox'),
            'default' => false,
            'tag' => 'input',
            'title' => ''
        ),

        'middle_name_label' => array(
            'attributes' => array('class' => 'bmsortables', 'required' => 'required', 'type' => 'text'),
            'default' => 'Middle name',
            'tag' => 'input',
            'title' => '',
        ),
        'middle_name_displayed' => array(
            'attributes' => array('type' => 'checkbox'),
            'default' => false,
            'tag' => 'input',
            'title' => ''
        ),
        'middle_name_required' => array(
            'attributes' => array('type' => 'checkbox'),
            'default' => false,
            'tag' => 'input',
            'title' => ''
        ),
        
        'last_name_label' => array(
            'attributes' => array('class' => 'bmsortables', 'required' => 'required', 'type' => 'text'),
            'default' => 'Last name',
            'tag' => 'input',
            'title' => '',
        ),
        'last_name_displayed' => array(
            'attributes' => array('type' => 'checkbox'),
            'default' => false,
            'tag' => 'input',
            'title' => ''
        ),
        'last_name_required' => array(
            'attributes' => array('type' => 'checkbox'),
            'default' => false,
            'tag' => 'input',
            'title' => ''
        ),
        
        'sex_label' => array(
            'attributes' => array('class' => 'bmsortables', 'required' => 'required', 'type' => 'text'),
            'default' => 'Sex',
            'tag' => 'input',
            'title' => '',
        ),
        'sex_displayed' => array(
            'attributes' => array('type' => 'checkbox'),
            'default' => false,
            'tag' => 'input',
            'title' => ''
        ),
        'sex_required' => array(
            'attributes' => array('type' => 'checkbox'),
            'default' => false,
            'tag' => 'input',
            'title' => ''
        ),
        
        'telephone_label' => array(
            'attributes' => array('class' => 'bmsortables', 'required' => 'required', 'type' => 'text'),
            'default' => 'Telephone',
            'tag' => 'input',
            'title' => '',
        ),
        'telephone_displayed' => array(
            'attributes' => array('type' => 'checkbox'),
            'default' => false,
            'tag' => 'input',
            'title' => ''
        ),
        'telephone_required' => array(
            'attributes' => array('type' => 'checkbox'),
            'default' => false,
            'tag' => 'input',
            'title' => ''
        ),
        
        'mobile_label' => array(
            'attributes' => array('class' => 'bmsortables', 'required' => 'required', 'type' => 'text'),
            'default' => 'Mobile',
            'tag' => 'input',
            'title' => '',
        ),
        'mobile_displayed' => array(
            'attributes' => array('type' => 'checkbox'),
            'default' => false,
            'tag' => 'input',
            'title' => ''
        ),
        'mobile_required' => array(
            'attributes' => array('type' => 'checkbox'),
            'default' => false,
            'tag' => 'input',
            'title' => ''
        ),
        
        'address_label' => array(
            'attributes' => array('class' => 'bmsortables', 'required' => 'required', 'type' => 'text'),
            'default' => 'Address',
            'tag' => 'input',
            'title' => '',
        ),
        'address_displayed' => array(
            'attributes' => array('type' => 'checkbox'),
            'default' => false,
            'tag' => 'input',
            'title' => ''
        ),
        'address_required' => array(
            'attributes' => array('type' => 'checkbox'),
            'default' => false,
            'tag' => 'input',
            'title' => ''
        ),
        
        'zip_label' => array(
            'attributes' => array('class' => 'bmsortables', 'required' => 'required', 'type' => 'text'),
            'default' => 'Postal Code',
            'tag' => 'input',
            'title' => '',
        ),
        'zip_displayed' => array(
            'attributes' => array('type' => 'checkbox'),
            'default' => false,
            'tag' => 'input',
            'title' => ''
        ),
        'zip_required' => array(
            'attributes' => array('type' => 'checkbox'),
            'default' => false,
            'tag' => 'input',
            'title' => ''
        ),
        
        'city_label' => array(
            'attributes' => array('class' => 'bmsortables', 'required' => 'required', 'type' => 'text'),
            'default' => 'City',
            'tag' => 'input',
            'title' => '',
        ),
        'city_displayed' => array(
            'attributes' => array('type' => 'checkbox'),
            'default' => false,
            'tag' => 'input',
            'title' => ''
        ),
        'city_required' => array(
            'attributes' => array('type' => 'checkbox'),
            'default' => false,
            'tag' => 'input',
            'title' => ''
        ),
        
        'country_label' => array(
            'attributes' => array('class' => 'bmsortables', 'required' => 'required', 'type' => 'text'),
            'default' => 'Country',
            'tag' => 'input',
            'title' => '',
        ),
        'country_displayed' => array(
            'attributes' => array('type' => 'checkbox'),
            'default' => false,
            'tag' => 'input',
            'title' => ''
        ),
        'country_required' => array(
            'attributes' => array('type' => 'checkbox'),
            'default' => false,
            'tag' => 'input',
            'title' => ''
        ),
        
        'custom_date_label' => array(
            'attributes' => array('class' => 'bmsortables', 'required' => 'required', 'type' => 'text'),
            'default' => 'Custom Date',
            'tag' => 'input',
            'title' => '',
        ),
        'custom_date_displayed' => array(
            'attributes' => array('type' => 'checkbox'),
            'default' => false,
            'tag' => 'input',
            'title' => ''
        ),
        'custom_date_required' => array(
            'attributes' => array('type' => 'checkbox'),
            'default' => false,
            'tag' => 'input',
            'title' => ''
        ),
        
        'tags_label' => array(
            'attributes' => array('class' => 'bmsortables', 'required' => 'required', 'type' => 'text'),
            'default' => 'Tags',
            'tag' => 'input',
            'title' => '',
        ),
        'tags_displayed' => array(
            'attributes' => array('type' => 'checkbox'),
            'default' => false,
            'tag' => 'input',
            'title' => ''
        ),
        'tags_required' => array(
            'attributes' => array('type' => 'checkbox'),
            'default' => false,
            'tag' => 'input',
            'title' => ''
        ),
        
        'title' => array(
            'attributes' => array('required' => 'required', 'type' => 'text'),
            'default' => 'Newsletter subscription',
            'tag' => 'input',
            'title' => 'Title'
        ),
        
        'list' => array(
            'attributes' => array('required' => 'required', 'type' => 'text'),
            'default' => null,
            'options' => array(),
            'tag' => 'select',
            'title' => 'Contact list',
        ),
        
        'submit_label' => array(
            'attributes' => array('required' => 'required', 'type'=>'text'),
            'default' => 'Sign up',
            'tag' => 'input',
            'title' => 'Submit label'
        ),
        
        'tags' => array(
            'attributes' => array('type' => 'text'),
            'default' => '',
            'description' => 'Comma separated list of default tags, that will be used to tag user during registration.',
            'tag' => 'input',
            'title' => 'Tags'
        ),
        
        'tags_allowed' => array(
            'attributes' => array('type' => 'text'),
            'default' => '',
            'description' => 'Comma separated list of allowed tags for tags input.',
            'tag' => 'input',
            'title' => 'Allowed Tags'
        ),

        'tags_input' => array(
            'attributes' => array('type' => 'select'),
            'default' => '',
            'description' => 'Select how will users input tags.',
            'tag' => 'select',
            'title' => 'Tags Input',
            'options' => array(
                'input' => 'Text',
                'select' => 'Select',
                'checkbox' => 'Checkboxes',
                'radio' => 'Radio Buttons'
            )
        ),

        'html_before' => array(
            'attributes' => array(),
            'default' => '',
            'tag' => 'textarea', 
            'title' => 'HTML before sign up form'
        ),
        'html_after' => array(
            'attributes' => array(),
            'default' => '',
            'tag' => 'textarea',
            'title' => 'HTML after sign up form'
        ),

        'message_invalid' => array(
            'attributes' => array('required' => 'required', ),
            'default' => 'This field is required.',
            'tag' => 'textarea',
            'title' => 'Empty input field error'
        ),
        
        'message_invalid_form' => array(
            'attributes' => array('required' => 'required', ),
            'default' => 'Please correct the form errors.',
            'tag' => 'textarea',
            'title' => 'Not valid form error'
        ),
        
        'message_success' => array(
            'attributes' => array('required' => 'required', ),
            'default' => 'You have been successfully signed up to our newsletter.',
            'tag' => 'textarea',
            'title' => 'Subscription success message'
        ),
        
        'message_error' => array(
            'attributes' => array('required' => 'required', ),
            'default' => 'We are terribly sorry, but due to an unexpected error we couldn\'t sign you up for our newsletter.',
            'tag' => 'textarea',
            'title' => 'Subscription error message'
        )
    );
    
    /**
     * @var array list of element sets rendered in widget form 
     */
    protected $elements = array(
        'email' => array(
            'Email', 'email_displayed', 'email_required'
        ),
        'company_label' => array(
            'Company', 'company_displayed', 'company_required'
        ),
        'first_name_label' => array(
            'First name', 'first_name_displayed', 'first_name_required'
        ),
        'middle_name_label' => array(
            'Middle name', 'middle_name_displayed', 'middle_name_required'
        ),
        'last_name_label' => array(
            'Last name', 'last_name_displayed', 'last_name_required'
        ),
        'sex_label' => array(
            'Sex', 'sex_displayed', 'sex_required'
        ),
        'telephone_label' => array(
            'Telephone', 'telephone_displayed', 'telephone_required'
        ),
        'mobile_label' => array(
            'Mobile', 'mobile_displayed', 'mobile_required'
        ),
        'address_label' => array(
            'Address', 'address_displayed', 'address_required'
        ),
        'zip_label' => array(
            'Postal Code', 'zip_displayed', 'zip_required'
        ),
        'city_label' => array(
            'City', 'city_displayed', 'city_required'
        ),
        'country_label' => array(
            'Country', 'country_displayed', 'country_required'
        ),
        'custom_date_label' => array(
            'Custom Date', 'custom_date_displayed', 'custom_date_required'
        ),
        'tags_label' => array(
            'Tags', 'tags_displayed', 'tags_required'
        )
    );
    
    /**
     * Register widget with WordPress.
     */
    public function __construct()
    {
        parent::__construct(
            'inboxify_widget_subscribe',
            L10n::__( 'Inboxify Sign Up Form widget'),
            array(
                'description' => L10n::__( 'Inboxify Sign Up Form widget' )
            )
        );
        
        $this->fields['list']['options'] = $GLOBALS['wpiy_plugin']->getSettings()->fieldGeneralListValues();
    }
    
    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance, $settings = null, $print = true ) {
        if (is_null($settings)) {
            $settings = $this->getSettings();
            $type = 'widget';
            $post_id = null;
            $sort = isset($instance['sort']) && substr_count($instance['sort'], ',') ? explode(',', $instance['sort']) : false;
            $widget_id = $this->idToI($this->id);
        } else {
            $post_id = get_the_ID();
            $sort = false;
            $type = 'shortcode';
            $widget_id = null;
        }
        
        $list_id = isset($instance['list']) && $instance['list'] ? $instance['list'] : null;
        
        $html = $args['before_widget'];
        
        $html .= (string) new Template(
            'subscribe', array(
                'args' => $args,
                'captcha' => Captcha::getInstance()->render('wpiy-captcha-' . $post_id . '-' . $widget_id, 'captcha'),
                'captcha_type' => $GLOBALS['wpiy_plugin']->getSettings()->getOption('captcha'),
                'instance' => $instance,
                'list_id' => $list_id,
                'post_id' => $post_id, 
                'settings' => $settings,
                'sort' => $sort,
                'type' => $type, 
                'widget_id' => $widget_id
            )
        );
        
        $html .= $args['after_widget'];
        
        return $print ? print $html : $html;
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        $sort = isset($instance['sort']) && substr_count($instance['sort'], ',')
            ? explode(',', $instance['sort']) : false;
        
        $sort_name = $this->get_field_name('sort');
        $sort_id = $this->get_field_id('sort');
        
        $table_id = $this->get_field_id('bmsortable');
    ?>
<h3><?php L10n::_e('Form Settings') ?></h3>
<?php
        $continue = true;

        foreach($this->fields as $id => $field) {
            if ('title' == $id) { $continue = false; }
            if ($continue) { continue; }
            
            $field['attributes']['id'] = $id;
            $field['attributes']['value'] = isset($instance[$id]) ? $instance[$id] : L10n::__($field['default']);
            
            $this->element($field['tag'], $field['attributes'], $field['title'], isset($field['options']) ? $field['options'] : array(), isset($field['description']) ? $field['description'] : null);
        }
?>
<h3><?php L10n::_e('Form Fields') ?></h3>
<p><?php L10n::_e('Drag and drop the field to change their order.') ?></p>
<input id="<?php print $sort_id ?>" name="<?php print $sort_name ?>" type="hidden" value="<?php print isset($instance['sort']) ? $instance['sort'] : '' ?>"/>
<table id="<?php print $table_id ?>" class="bmsortable">
    <thead>
        <tr>
            <th>&nbsp;</th>
            <th><?php L10n::_e( 'Displayed' ) ?></th>
            <th><?php L10n::_e( 'Required' ) ?></th>
            <th><?php L10n::_e( 'Label' ) ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($sort) {
            uksort($this->elements, function($a, $b) use ($sort) {
                $ak = (int)array_search($a, $sort);
                $bk = (int)array_search($b, $sort);
                
                return $ak - $bk;
            });
        }
        
        foreach($this->elements as $key => $element) {
            $this->elements($instance, L10n::__($element[0]), $element[1], $element[2], $key);
        }
        ?>
    </tbody>
</table>
<?php if ($this->number > 0): ?>
<p><?php L10n::_e('To use this widget as shortcode use the following code:') ?> <code>[inboxify_subscribe widget_id=<?php print $this->number ?>]</code></p>
<?php endif; ?>
<script type="text/javascript">bmsortable("<?php print $sort_id ?>", "<?php print $table_id ?>")</script>
<?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();

        foreach($this->fields as $id => $field) {
            $instance[$id] = isset($new_instance[$id]) ? $new_instance[$id] : $field['default'];
            
            if ( ! in_array( $id, array( 'html_after', 'html_before' ) ) ) {
                $instance[$id] = strip_tags( $instance[$id] );
            }
        }
        
        $instance['sort'] = $new_instance['sort'];
        
        return $instance;
    }
    
    protected function element($tag, array $attributes, $label, $options = array(), $description = null, $print = true)
    {
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' widefat' : 'widefat';
        $attributes['data-name'] = $attributes['id'];
        $attributes['name'] = $this->get_field_name($attributes['id']);
        $attributes['id'] = $this->get_field_id($attributes['id']);
        
        $isCheckbox = isset($attributes['type']) && 'checkbox' == $attributes['type'];
        $label = L10n::__( $label );
        
        if (!$isCheckbox && strlen($label)) {
            $label .= ':';
        } else if ($isCheckbox) {
            if ($attributes['value']) {
                $attributes['checked'] = 'checked';
            }
            $attributes['value'] = 1;
        }
        
        if ('select' == $tag && $options) {
            $content = null;
            
            foreach($options as $option_value => $option_label) {
                $option_attributes = $option_value == $attributes['value'] ? array('selected' => 'selected') : array();
                $option_attributes['value'] = $option_value;
                
                $content .= (string)new Tag('option', $option_attributes, $option_label);
            }
        } else {
            $content = null;
        }
        
        $input = (string) new Tag($tag, $attributes, $content);
        $label = (string) new Tag('label', array('for' => $attributes['id']), $label);
        
        $return = $isCheckbox ? $input . $label : $label . '<br>' . $input;
        
        if ($description) {
            $return .= '<p class="description inboxify-description">' . $description . '</p>';
        }
        
        $return = (string) new Tag('p', array(), $return);
        
        return $print ? print $return : $return;
    }
    
    protected function elements($instance, $title, $element1, $element2, $element3)
    {
        $id = $element1;
        $displayed = $this->fields[$id];
        $displayed['attributes']['id'] = $id;
        $displayed['attributes']['value'] = isset($instance[$id]) ? $instance[$id] : L10n::__($displayed['default']);

        $id = $element2;
        $required = $this->fields[$id];
        $required['attributes']['id'] = $id;
        $required['attributes']['value'] = isset($instance[$id]) ? $instance[$id] : L10n::__($required['default']);

        $id = $element3;
        $label = $this->fields[$id];
        $label['attributes']['id'] = $id;
        $label['attributes']['value'] = isset($instance[$id]) ? $instance[$id] : L10n::__($label['default']);
?>
        <tr>
            <th scope="row">
                <?php print $title ?>
            </th>
            <td>
                <?php $this->element($displayed['tag'], $displayed['attributes'], $displayed['title']); ?>
            </td>
            <td>
                <?php $this->element($required['tag'], $required['attributes'], $required['title']); ?>
            </td>
            <td>
                <?php $this->element($label['tag'], $label['attributes'], $label['title']); ?>
            </td>
        </tr>
<?php
    }
    
    public function getOption( $key )
    {
        $settings = $this->getSettings();
        
        if (!isset($settings[$key])) {
            throw new \RuntimeException( L10n::__( 'Settings not found.' ) );
        }
        
        return $settings[$key];
    }
    
    /**
     * Get this wigets settings or first configured widget settings.
     * @return array widget settings as an associative array
     */
    public function getSettings(array $settings = null)
    {
        if (!$this->id) {
            throw new \RuntimeException( L10n::__( 'Uninitialized widget.' ) );
        }
        
        $settings = $this->get_settings();
        $i = $this->idToI();
        $settings = isset($settings[$i]) ? $settings[$i] : array();
        
        foreach($this->fields as $k => $v) {
            if (!isset($settings[$k])) {
                $settings[$k] =  $v['default'];
            }
        }
        
        $settings['tags_allowed_array'] = Plugin::strToArray($settings['tags_allowed']);
        
        return $settings;
    }
    
    protected function idToI($id = null)
    {
        if (is_null($id)) {
            $id = $this->id;
        }
        
        $matches = array();
        preg_match('/(\d+)$/', $id, $matches);
        
        return $matches[1];
    }
    
    public function getDefaults()
    {
        $d = array();
        
        foreach($this->fields as $k => $v) {
            $d[$k] = $v['default'];
        }
        
        return $d;
    }
    
    /**
     * Used only for gettext...
     * @see $fields, $elements
     */
    private function translationWorkaround()
    {
        L10n::__('Email');
        L10n::__('Company');
        L10n::__('First name');
        L10n::__('Middle name');
        L10n::__('Last name');
        L10n::__('Sex');
        L10n::__('Telephone');
        L10n::__('Mobile');
        L10n::__('Address');
        L10n::__('Postal Code');
        L10n::__('City');
        L10n::__('Country');
        L10n::__('Title');
        L10n::__('Newsletter subscription');
        L10n::__('Submit label');
        L10n::__('Sign up');
        L10n::__('Comma separated list of default tags, that will be used to tag user during registration.');
        L10n::__('Tags');
        L10n::__('HTML before sign up form');
        L10n::__('HTML after sign up form');
        L10n::__('This field is required.');
        L10n::__('Empty input field error');
        L10n::__('Please correct the form errors.');
        L10n::__('Not valid form error');
        L10n::__('You have been successfully signed up to our newsletter.');
        L10n::__('Subscription success message');
        L10n::__('We are terribly sorry, but due to an unexpected error we couldn\'t sign you up for our newsletter.');
        L10n::__('Subscription error message');
        
        L10n::__('Allowed Tags');
        L10n::__('Comma separated list of allowed tags for tags input.');
        L10n::__('Tags Input.');
        L10n::__('Select how will users input tags.');
        L10n::__('Custom Date');
    }
}
