<?php
define ('MAX_MSG_LEN', 4096);

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

    protected function _sendCommand($command) {
        fwrite($this->_conn, $command);
        $response = fread($this->_conn, MAX_MSG_LEN);

        return $response;
    }

    public function doCommand($cmdString, $args = array()) {
        $command = new CommandMessage($cmdString, $args);
        $response = $this->_sendCommand($command);
		$json = json_decode($response, true);

        return $json["result"];
    }

}
