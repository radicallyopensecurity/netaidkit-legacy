<?php

class Updater
{
    // TODO: read from config file
    protected $_latestVersionUrl = 'http://generated-atlas-93615.appspot.com/nak/latest_version';
    protected $_latestImageUrl = 'http://generated-atlas-93615.appspot.com/nak/latest_image';
    protected $_localImagePath = '/tmp/latest_image.bin';
    protected $_versionFile = '/etc/nak-release';
    protected $_pubKeyFile = '/etc/update-key';

    public function getCurrentVersion()
    {
        if (!file_exists($this->_versionFile))
            throw new Exception('Version file not found.');
        
        $timestamp = trim(file_get_contents($this->_versionFile));
            
        return $timestamp;
    }
    
    public function getLatestVersion()
    {
        @$latest_timestamp = trim(file_get_contents($this->_latestVersionUrl));
        
        return $latest_timestamp;
    }

    public function updateAvailable()
    {
        $current = $this->getCurrentVersion();
        $latest  = $this->getLatestVersion();
        
        if (intval($current) < intval($latest))
            return true;
        else
            return false;
    }
    
    public function downloadLatest()
    {
        file_put_contents($this->_localImagePath, fopen($this->_latestImageUrl, 'r'));
        
        if (!file_exists($this->_localImagePath))
            throw new Exception('Could not download latest image.');
    }
    
    public function validateSignature()
    {
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
    
    public function performUpdate()
    {   
        NetAidManager::do_update($this->_localImagePath);
        
        return true;
    }
}
