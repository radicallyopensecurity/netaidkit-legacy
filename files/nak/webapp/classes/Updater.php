<?php

class Updater
{
    // TODO: read from config file
    protected $_appName = 'generated-atlas-93615.appspot.com';
    protected $_latestVersionUrl = 'https://google.com/nak/latest_version';
    protected $_latestImageUrl = 'https://google.com/nak/latest_image';
    protected $_localImagePath = '/tmp/latest_image.bin';
    // server-side HTTP header (Content-Length, etc.)
    protected $_imageHeaderFile = '/tmp/update_header';
    protected $_versionFile = '/etc/nak-timestamp';
    protected $_pubKeyFile = '/etc/update-key';
    protected $_streamContext;
    protected $_release;

    protected $_http_headers;

    public function __construct() {
        if (!file_exists($this->_versionFile))
            throw new Exception('Version file not found.');

        $this->_http_headers = array('http'=>array('header'=>"Host: " . $this->_appName));
        $this->_streamContext = stream_context_create($this->_http_headers);
        $this->_release = explode('-', trim(file_get_contents($this->_versionFile)));

        $this->_latestVersionUrl .= '?key=new';
        $this->_latestImageUrl .= '?key=new';
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
            return false;	# SET TO TRUE TO DEBUG!
    }

    public function downloadLatest() {
        // return if there's already a process writing
        // to the image file
        if ($this->_get_wget_pid($_localImagePath) != -1)
            return;

        // launch a wget instance in background
        $wget_cmdline = "wget -c -b -q -S -o {$this->_imageHeaderFile} \
            -O {$this->_localImagePath} {$this->_latestImageUrl} ";
        foreach($this->_http_headers as $field) {
            $text = $field['header'];
            $wget_cmdline .= "--header=\"$text\" ";
        $wget_cmdline .= "2>&1 >/dev/null";
        }
        system($wget_cmdline, $wget_ret);

        if ($wget_ret)
            throw new Exception('Could not download latest image.');
    }

    public function stopDownload() {
        $wget_pid = $this->_get_wget_pid($_localImagePath);
        if ($wget_pid != -1)
            posix_kill($wget_pid, SIGKILL);
    }

    public function deleteImage() {
        unlink($this->_localImagePath);
    }

    public function validateSignature() {
        if (!file_exists($this->_pubKeyFile))
            throw new Exception('Public key file not found.');

        $pubkey = file_get_contents($this->_pubKeyFile);

        $data = file_get_contents($this->_localImagePath);
        $signature = substr($data, -512);
        $data = substr($data, 0, -512);
        $status = openssl_verify($data, $signature, $pubkey, "sha1WithRSAEncryption");

        if ($status == 1)
            return true;

        return false;
    }

    public function performUpdate() {
        NetAidManager::do_update($this->_localImagePath);

        return true;
    }

    public function get_percentage_downloaded() {
        $current_size = $this->_get_current_size();
        $update_size = $this->_get_update_size();

        if ($current_size == $update_size)
            return "100";

        $percentage = (($current_size * 1.0) / $update_size) * 100.0;
        return sprintf('%.02f', $percentage);
    }

    protected function _get_update_size() {
        $http_headers = file('/tmp/update_header');
        foreach ($http_headers as $field) {
            $tuple = explode(':', trim($field));
            if (stristr($tuple[0], 'Content-Length') !== FALSE)
                return intval($tuple[1]);
        }
        return -1;
    }

    protected function _get_current_size() {
        $image = '/tmp/latest_image.bin';
        if (!file_exists($image))
            return '0';

        return filesize($image);
    }

    protected function _get_wget_pid($file) {
        $pgrep_cmdline = 'pgrep -fl wget"';
        $process_list = explode('\n', system($pgrep_cmdline));
        foreach($process_list as $process) {
            $tuple = explode(' ', $process);
            $pid = $tuple[0];
            if (strstr($process, "-O $file") !== FALSE)
                return intval($pid);
        }

        return -1;
    }
}
