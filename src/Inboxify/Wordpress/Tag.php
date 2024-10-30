<?php

/**
 * @package Inboxify\Wordpress
 */
namespace Inboxify\Wordpress;

/**
 * Simple HTML tag renderer
 * @package Inboxify\Wordpress
 */
class Tag
{
    /**
     * @var array associative array of tags attributes
     */
    protected $attributes;
    /**
     * @var string tag content
     */
    protected $content;
    /**
     * @var string tag name
     */
    protected $tag;
    
    /**
     * Create new instance of this class
     * @param string $tag tag name
     * @param array $attributes associative array of tags attributes
     * @param string $content tag content
     */
    public function __construct($tag, array $attributes = array(), $content = null)
    {
        $this->attributes = $attributes;
        $this->content = $content;
        $this->tag = $tag;
        
        if ('textarea' == $this->tag) {
            if (isset($this->attributes['value'])) {
                $this->content = $this->attributes['value']; unset($this->attributes['value']);
            } else {
                $this->content = '';
            }
        }
    }
    
    /**
     * Convert tag to string (e.g. render)
     * @return string rendedred template or error string
     */
    public function __toString()
    {
        try {
            $rs = ('wysiwyg' == $this->tag) ? $this->renderWysiwyg() : $this->render();
        } catch (\Exception $e) {
            $rs = L10n::__('Generating Tag failed.');
        }
        
        return $rs;
    }
    
    /**
     * To string alias
     * @return string
     */
    public function toHtml()
    {
        return $this->__toString();
    }
    
    /**
     * Render tag
     * @return string
     */
    protected function render()
    {
        $html = '<' . $this->tag;
        
        foreach($this->attributes as $k => $v) {
            $html .= ' ' . $k . '="' . $v . '"';
        }
        
        if (is_null($this->content)) {
            $html .= '/>';
        } else {
            $html .= '>' . $this->content . '</' . $this->tag . '>';
        }
        
        return $html;
    }
    
    /**
     * Render WP WYSIWYG editor
     * @return string 
     */
    protected function renderWysiwyg()
    {
        ob_start();
        wp_editor($this->content, $this->attributes['id']);
        return ob_get_clean();
    }
}
