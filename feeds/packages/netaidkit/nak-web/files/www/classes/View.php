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
    
    protected function _displayMessages()
    {
        $flashMessager = new FlashMessager();
        $messages = $flashMessager->getMessages();
        
        if (!empty($messages)) {
            foreach ($messages as $message) {
                echo "<p class=\"{$message['type']}\">{$message['text']}</p>";
            }
        }    
    }
}
