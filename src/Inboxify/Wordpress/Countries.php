<?php

/**
 * @package Inboxify\Wordpress
 */
namespace Inboxify\Wordpress;

class Countries
{
    const COUNTRIES = 'assets/countries.csv';
    
    public static function select($class, $dataName, $id, $name, $required)
    {
        $countries = self::getCountries();
        $required = $required ? ' required="required"' : null;
        $html = '<select class="' . esc_attr($class) . '" data-name="' . esc_attr($dataName) . '" id="' . esc_attr($id) . '" name="' . esc_attr($name) . '"' . $required . '>';
        
        foreach($countries as $value => $label) {
            $html .= '<option value="' . esc_attr($value) . '">' . esc_html($label) . '</option>';
        }
        
        $html .= '</select>';
        
        print $html;
    }
    
    protected static function getCountries()
    {
        $countries = array(
            '' => L10n::__('Select Country')
        );
        $csv = plugin_dir_path( WPIY_PLUGIN ) . self::COUNTRIES;
        $f = fopen($csv, 'r');
        
        if (!$f) {
            return $countries;
        }
        
        $index = 'nl_NL' == get_locale() ? 1 : 0;
        
        while (false != ($line = fgetcsv($f))) {
            $countries[$line[2]] = $line[$index];
        }
        
        fclose($f);
        
        return $countries;
    }
}
