<?php

/* UNSAFE */
/* UNSAFE */
/* UNSAFE */

class View
{
    protected $_template;
    protected $_params = array();

    public function __construct($template, $params = null)
    {
        $filename = VIEW_DIR . "/$template.phtml";
        if (!file_exists($filename))
            throw new NotFoundException("View file does not exist.");
            
        $this->_template = $filename;
        $this->_params = $params;
    }
    
    public function display()
    {
        return include($this->_template);
    }
}
