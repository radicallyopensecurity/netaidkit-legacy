<?php

/* UNSAFE */
/* UNSAFE */
/* UNSAFE */
/* Please explain what is unsafe. Hardend a bit */


/* 
 * This class View($template, $params) load a template.phtml file
 * by including this file. It passes the params to _params, so 
 * they can be used by the view 
 */
class View
{
    protected $_template;
    protected $_params = array();

    public function __construct($template, $params = null)
    {
        /* We only expect a-z filename/template names */
        if (!preg_match("/^[a-zA-Z]*$/",$template)) {
            /* We throw a notfoundexception, so a catched break-in attempt 
             * does not differ from a genuine not found exception */
            throw new NotFoundException("View file does not exist.");
        }

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
