<?php

class UpdateController extends Page
{
    protected $_allowed_actions = array('index', 'download', 'do_upgrade',
                                          'do_remind', 'download_status');

    public function init() {
        if ($_SESSION['logged_in'] != 1)
            $this->_redirect('/user/login');
    }

    public function index() {
        $updater = new Updater();
        if (!$updater->updateAvailable())
            $this->_redirect('/admin/index');

        $network = NetAidManager::ap_name();
        $params = array('network' => $network);

        $view = new View('update', $params);
        return $view->display();
    }

    public function download() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $updater = new Updater();
            if ($updater->updateAvailable()) {
                $updater->downloadLatest();
                die('OK');
            }
        }
    }

    public function do_upgrade() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $updater = new Updater();
            if ($updater->updateAvailable()) {
                if (!$updater->validateSignature()) {
                    $updater->deleteImage();
                    die('INVALID SIGNATURE');
                }

                    //die('SUCCESS'); # ENABLE TO DEBUG!
                if ($updater->performUpdate())
                    die('SUCCESS');
                else
                    die('FAILURE');
            }
        }
    }

    public function do_remind() {
        $reminder = UpdateReminder::get_reminder();
        $updater = new Updater();
        try {
            $updater->stopDownload();
            $reminder->postpone();
            die('SUCCESS');
        } catch (Exception $e) {
            die('FAILURE');
        }
    }

    public function download_status() {
        $updater = new Updater();
        die($updater->get_percentage_downloaded());
    }
}
