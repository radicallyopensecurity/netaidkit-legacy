<?php

class UpdateController extends Page
{
    protected $_allowed_actions = array('index', 'do_upgrade');
    
    public function init()
    {
        if ($_SESSION['logged_in'] != 1)
            $this->_redirect('/user/login');
    }
    
    public function index()
    {
        $updater = new Updater();
        if (!$updater->updateAvailable())
            $this->_redirect('/admin/index');
        
        $params = array();
    
        $view = new View('update', $params);
        return $view->display();
    }
    
    public function do_upgrade()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $updater = new Updater();
            if ($updater->updateAvailable()) {
                $image_data = $updater->downloadLatest();

                if (!$updater->validateSignature($image_data))
                    die('INVALID SIGNATURE');
                    
                if ($updater->performUpdate($image_data))
                    die('Upgrading, please wait for the device to reboot.');
                else
                    die('FAILED');
            }
        }
    }
}
