<?php

/* Format is %d:%s\n
 * %d is the counter (1-4)
 * %s is the timestamp of expiration
*/

class UpdateReminder {
    protected static $_ctrFile = ROOT_DIR . '/data/remind_counter';

    protected $_counter;
    protected $_expirationTimestamp;

    public static function get_reminder() {
        if (!file_exists(self::$_ctrFile) || filesize(self::$_ctrFile) == 0) {
            return new self('0:0');
        }

        return new self(file_get_contents(self::$_ctrFile));
    }

    public function __toString() {
        return $this->getCounter() . ':' . $this->getExpirationTimestamp();
    }

    public function __construct($line) {
        $data = explode(':', trim($line));

        $this->_counter = $data[0];
        $this->_expirationTimestamp = $data[1];
    }

    public function isExpired() {
        if (time() > $this->getExpirationTimestamp())
            return true;

        return false;
    }

    public function postpone() {
        if ($this->getCounter() >= UPDATE_REMIND_MAX)
            throw new Exception("You have to update now.");

        $this->_counter = $this->getCounter() + 1;
        $this->_expirationTimestamp = @strtotime('+1 hour');
        $this->_save();
    }

    protected function _save() {
        return file_put_contents($this->getCtrFile(), $this);
    }

    public function getCounter() {
        return $this->_counter;
    }

    public function getExpirationTimestamp() {
        return $this->_expirationTimestamp;
    }

    public function getCtrFile() {
        return self::$_ctrFile;
    }
}
