<?php

class CommandMessage extends NakMessage {
    protected $_command;
    protected $_args;

    public function __construct($command, $args = array()) {
        $this->setCommand($command);
        $this->setArgs($args);
    }

    public function __toString() {
        $data = $this->getCommand() . "\r\n";
        foreach ($this->_args as $arg) {
            $data .= $arg . "\r\n";
        }

        return $data;
    }

    public function setCommand($command) {
        $this->_command = $command;
    }

    public function getCommand() {
        return $this->_command;
    }

    public function setArgs($args) {
        $this->_args = $args;
    }

    public function getArgs() {
        return $this->_args;
    }
}
