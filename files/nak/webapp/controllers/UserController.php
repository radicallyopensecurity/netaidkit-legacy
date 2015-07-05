<?php

class UserController extends Page
{
    protected $_allowed_actions = array('index', 'login', 'do_login', 'logout');

    public function init()
    {
        $cur_stage = NetAidManager::get_stage();
        if ($cur_stage == STAGE_DEFAULT)
            $this->_redirect('/setup/ap');
        if ($cur_stage == STAGE_OFFLINE)
            $this->_redirect('/setup/wan');
    }

    public function index()
    {
        $this->_redirect('/user/login');
    }

    public function login()
    {
        if ($_SESSION['logged_in'] == 1)
            $this->_redirect('/admin/index');

        $view = new View('login');
        return $view->display();
    }

    public function do_login()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $username = $request->postvar('username');
            $password = $request->postvar('password');

            if (empty($username) || empty($password)) {
                $this->_addMessage('error', 'All fields are required', 'login');
                $this->_redirect('/user/login');
            }

            if ($username == 'admin' && NetAidManager::check_adminpass($password)) {
                $_SESSION['token'] = md5(uniqid(rand(), true));
                $_SESSION['logged_in'] = 1;
                $this->_redirect('/admin/index');
            } else {
                $this->_addMessage('error', 'Username/password is incorrect.', 'login');
                $this->_redirect('/user/login');
            }
        }
    }

    public function logout()
    {
        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();

        $this->_redirect('/user/login');
    }
}
