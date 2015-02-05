<?php

class Ovpn {
    /* TODO: Should be from some config file */
    var $ovpn_root = '/netaidkit/ovpn/';


    /* Returns a list of found files */
    public function getOptions() {

        $ovpn = $this->ovpn_root . '/upload/';
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

    public function removeFile($file) {
        if (!preg_match("/^[0-9a-zA-Z\_\-\.]*$/", $file))
            return false;
    
        $path = "{$this->ovpn_root}/upload/$file";
        
        if (file_exists($path)) {
            unlink($path);
            return true;
        } else {
            return false;
        }
    }

    public function handleUpload() {
        if (isset($_FILES['vpnfile'])) {
            $name = $_FILES['vpnfile']['name'];
            $tmp_file = $_FILES['vpnfile']['tmp_name'];

            if (preg_match("/^[a-zA-Z0-9\ -_\.]+.ovpn/", $name)) {        
                $destination = $this->ovpn_root . "/upload/" . $name;
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
