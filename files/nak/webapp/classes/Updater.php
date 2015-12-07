<?php

class Updater
{
    // TODO: read from config file
    protected $_appName = 'generated-atlas-93615.appspot.com';
    protected $_latestVersionUrl = 'https://google.com/nak/latest_version';
    protected $_latestImageUrl = 'https://google.com/nak/latest_image';
    protected $_localImagePath = '/tmp/latest_image.bin';
    protected $_versionFile = '/etc/nak-release';
    protected $_pubKeyFile = '/etc/update-key';
    protected $_streamContext;
    protected $_release;

    public function __construct() {
        if (!file_exists($this->_versionFile))
            throw new Exception('Version file not found.');

        $opts = array('http'=>array('header'=>"Host: " . $this->_appName ."\r\n"));
        $this->_streamContext = stream_context_create($opts);
        $this->_release = explode('-', trim(file_get_contents($this->_versionFile)));

        if ($this->getBuildType() == 'dev') {
            $this->_latestVersionUrl .= '?build=dev';
            $this->_latestImageUrl .= '?build=dev';
        }
    }

    public function getCurrentVersion() {
        return $timestamp = $this->_release[0];
    }

    public function getBuildType() {
        return $timestamp = $this->_release[1];
    }

    public function getLatestVersion() {
        $latest = trim(@file_get_contents($this->_latestVersionUrl, false, $this->_streamContext));
        return $latest;
    }

    public function updateAvailable() {
        $reminder = UpdateReminder::get_reminder();
        if (!($reminder->isExpired()))
            return false;

        $current = $this->getCurrentVersion();
        $latest  = $this->getLatestVersion();

        if (intval($current) < intval($latest))
            return true;
        else
            return false;
    }

    public function downloadLatest() {
        @file_put_contents($this->_localImagePath, fopen($this->_latestImageUrl, 'r', false, $this->_streamContext));

        if (!file_exists($this->_localImagePath))
            throw new Exception('Could not download latest image.');
    }

    public function validateSignature() {
        if (!file_exists($this->_pubKeyFile))
            throw new Exception('Public key file not found.');

        $pubkey = file_get_contents($this->_pubKeyFile);

        $data = file_get_contents($this->_localImagePath);
        $signature = substr($data, -64);
        $data = substr($data, 0, -64);
        $status = openssl_verify($data, $signature, $pubkey);

        if ($status == 1)
            return true;

        return false;
    }

    public function performUpdate() {
        NetAidManager::do_update($this->_localImagePath);

        return true;
    }
}
