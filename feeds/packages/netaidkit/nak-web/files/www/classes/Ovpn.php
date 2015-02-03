<?php

class Ovpn {
    /* TODO: Should be from some config file */
    var $ovpn_root = '/netaidkit/ovpn/';


    /* Returns a list of found files */
    public function getOptions() {

        $ovpn = $this->ovpn_root;
        $files = array();

        /* Open ovpn dir, and on success, concat all found .ovpn files to 
           the return array */
        if (is_dir($ovpn)) {
            if($dh = opendir($ovpn)) {
                while (false !== ($file = readdir($dh))) {
                    if (preg_match("/^(.+)\.ovpn$/", $file, $matches)) {
                        $title = $matches[1];
                        $files[] = array("title" => $title, "file" => $file);

                    }
                }
            }
        }
        return $files;

    }


    public function handleUpload() {
        if (isset($_FILES['vpnfile'])) {
            $name = $_FILES['vpnfile']['name'];
            $tmp_file = $_FILES['vpnfile']['tmp_name'];

            if (preg_match("/^[a-zA-Z0-9\ -_\.]+.ovpn/", $name)) {        
                $destination = $this->ovpn_root . "/" . $name;
                move_uploaded_file($tmp_file, $destination);
                return true;
            }
            else {
                unlink($tmp_file);
                return false;
            }
        }

    }
}

?>
