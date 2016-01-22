<?php
define ('MAX_MSG_LEN', 262144);

/* TODO ADD EXCEPTIONS */
class NakdClient {
    protected $_socketFile = 'unix:///run/nakd/nakd.sock';
    protected $_conn;

    public function __construct() {
        $this->_conn = $this->_connect($this->_socketFile);
        if (!$this->_conn) {
            $view = new View('booting');
            $view->display();
            die();
        }
    }

    public function __destruct() {
        $this->_disconnect();
    }

    protected function _connect($socketFile) {
        return @stream_socket_client($socketFile);
    }

    protected function _disconnect() {
        @fclose($this->_conn);
    }

    /* From http://php.net/feof */
    protected function _safe_feof($fp, &$start = NULL) {
        $start = microtime(true);
        return feof($fp);
    }

    protected function _sendCommand($command) {
        fwrite($this->_conn, $command);
        $response = '';
        while (!feof($this->_conn)) {
            $response .= fread($this->_conn, MAX_MSG_LEN - strlen($response));
            $stream_meta_data = stream_get_meta_data($this->_conn);
            if ($stream_meta_data['unread_bytes'] <= 0) break;
        }

        return $response;
    }

    public function doCommand($cmdString, $args = array()) {
        $command = new CommandMessage($cmdString, $args);
        $response = $this->_sendCommand($command);
		$json = json_decode($response, true);

        return $json["result"];
    }

}
