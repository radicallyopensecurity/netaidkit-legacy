<?php

/* 
 * This class View($template, $params) load a template.phtml file
 * by including this file. It passes the params to _params, so 
 * they can be used by the view.
 */
class View
{
    protected $_template;
    protected $_params = array();

    public function __construct($template, $params = null)
    {
        /* Validate template name. */
        if (!preg_match("/^[a-zA-Z\_]*$/",$template)) {
            /* We throw a notfoundexception, so a caught break-in attempt 
             * does not differ from a genuine not found exception. */
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
                $type = htmlspecialchars($message['type']);
                $text = htmlspecialchars($message['text']);
                echo "<p class=\"$type\">$text</p>";
            }
        }    
    }
}
