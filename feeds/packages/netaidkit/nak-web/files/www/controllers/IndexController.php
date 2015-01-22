<?php

class IndexController extends Page
{
    protected $_allowed_actions = array('index');
    
    public function index()
    {
        $this->_redirect('/setup/index');
    }
}
