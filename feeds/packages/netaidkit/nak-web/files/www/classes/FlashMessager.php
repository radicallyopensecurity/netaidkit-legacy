<?php

class FlashMessager
{
    public function _construct()
    {
        if (!array_key_exists('messages', $_SESSION))
            $_SESSION['messages'] = array();
    }
    
    public function addMessage($type, $text)
    {
        $_SESSION['messages'][] = array('type' => $type, 'text' => $text);
    }
    
    public function getMessages()
    {
        if (empty($_SESSION['messages']))
            return false;
        
        $messages = $_SESSION['messages'];
        unset($_SESSION['messages']);
        
        return $messages;
    }
}
