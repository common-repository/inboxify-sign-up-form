<?php

/**
 * @package Inboxify\Wordpress
 */
namespace Inboxify\Wordpress;

/**
 * Simple PHP template engine
 * @package Inboxify\Wordpress
 */
class Template
{
    /**
     * @var string default template file suffix
     */
    const SUFFIX = '.php';
    
    /**
     * @var string template file (relative path without suffix)
     */
    protected $template;
    /**
     * @var array template variables (associative array)
     */
    protected $vars;
    
    /**
     * Create new Template instance
     * @var string template file (relative path without suffix)
     * @var array template variables (associative array)
     */
    public function __construct( $template, array $vars = array() )
    {
        $this->template = $template;
        $this->vars = $vars;
    }
    
    /**
     * Convert template to string (e.g. render)
     * @return string rendedred template or error string
     */
    public function __toString()
    {
        try {
            $rs = $this->render();
        } catch (\Exception $e) {
            $rs = sprintf( L10n::__( 'Converting template %s to string failed (%s).' ), $this->template, $e->getMessage() );
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
     * Get full template path
     * @param boolean $check if true, method will check existance of the file
     * @return string absolute template file path
     * @throws \RuntimeException in case the check is true an file doesn't exist
     */
    protected function getTemplatePath( $check = true )
    {
        $path = plugin_dir_path( WPIY_PLUGIN ) . 'view/' . $this->template . self::SUFFIX;
        
        if ( $check && ! is_readable( $path ) ) {
            throw new \RuntimeException( L10n::__( 'Template not found.' ) );
        }
        
        return $path;
    }
    
    /**
     * Render template
     * @return string
     * @throws \RuntimeException In case there is no input or including template file fails
     */
    public function render( )
    {
        extract( $this->vars );
        
        ob_start();
        $rs = include( $this->getTemplatePath() );
        $output = ob_get_clean();
        
        if ( ! $rs || ! $output ) {
            throw new \RuntimeException( L10n::__( 'Template processing failed.' ) );
        }
        
        return $output;
    }
}
