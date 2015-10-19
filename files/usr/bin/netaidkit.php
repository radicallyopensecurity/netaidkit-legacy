#!/usr/bin/php-cgi

<?php
require_once '/nak/webapp/classes/NakMessage.php';
require_once '/nak/webapp/classes/CommandMessage.php';
require_once '/nak/webapp/classes/ReplyMessage.php';
require_once '/nak/webapp/classes/NakdClient.php';

$command = $argv[1];
$args = array_slice($argv, 2);

$client = new NakdClient();
echo $client->doCommand($command, $args);
