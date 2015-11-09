<?php

class UpdateController extends Page
{
    protected $_allowed_actions = array('index', 'do_upgrade', 'do_remind', 'download_status');

    public function init() {
        if ($_SESSION['logged_in'] != 1)
            $this->_redirect('/user/login');
    }

    public function index() {
        $updater = new Updater();
        if (!$updater->updateAvailable())
            $this->_redirect('/admin/index');

        $params = array();

        $view = new View('update', $params);
        return $view->display();
    }

    public function do_upgrade() {
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

    public function do_remind() {
        $reminder = UpdateReminder::get_reminder();
        try {
            $reminder->postpone();
            die('SUCCESS');
        } catch (Exception $e) {
            die('FAILURE');
        }
    }

    public function download_status() {
        die($this->_get_image_size());
    }

    protected function _get_image_size() {
        $image = '/tmp/latest_image';
        if (!file_exists($image))
            return '0';

        return floor((filesize($image) / 7471108) * 100);
    }

}
