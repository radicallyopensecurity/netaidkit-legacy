<?php

class LogsController extends Page
{
    protected $_allowed_actions = array('index', 'download_tor', 'download_vpn');

    protected $_torLogFile = '/var/log/tor/notices.log';
    protected $_vpnLogFile = '/var/log/openvpn.log';

    public function init()
    {
        if ($_SESSION['logged_in'] != 1)
            $this->_redirect('/user/login');
    }

    public function index()
    {
        $cur_stage = NetAidManager::get_stage();
        if ($cur_stage == STAGE_DEFAULT)
            $this->_redirect('/setup/ap');
        if ($cur_stage == STAGE_OFFLINE)
            $this->_redirect('/setup/wan');

        $torLog = $this->_getTorLog();
        $vpnLog = $this->_getVpnLog();

        $params = array('torLog' => $torLog, 'vpnLog' => $vpnLog);
        $view = new View('logs', $params);
        return $view->display();
    }

    public function download_tor()
    {
        header('Content-type: text/plain');
        header('Content-Disposition: attachment; filename="netaidkit_tor_log.txt"');
        echo $this->_getTorLog();
        exit;
    }

    public function download_vpn()
    {
        header('Content-type: text/plain');
        header('Content-Disposition: attachment; filename="netaidkit_vpn_log.txt"');
        echo $this->_getVpnLog();
        exit;
    }

    protected function _getTorLog()
    {
        if (file_exists($this->_torLogFile))
            return trim(file_get_contents($this->_torLogFile));

        return _('Tor log empty.');
    }

    protected function _getVpnLog()
    {
        if (file_exists($this->_vpnLogFile)) {
            $log = file_get_contents($this->_vpnLogFile);
            if (!empty($log))
                return trim($log);
        }

        return _('OpenVPN log empty.');
    }
}
