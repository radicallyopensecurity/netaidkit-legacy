<?php

if (array_key_exists("connect", $_POST)) {
        if (array_key_exists("password", $_POST) && !empty($_POST['password'])) { 
                $output = shell_exec("/usr/bin/sbox wificonn");
                echo "CONNECTED";
        }                                            
}                                            
                          
$output = shell_exec("/usr/bin/sbox wifiscan");
preg_match_all("/ESSID: \"(.*)\"/", $output, $ssids);
                                                  
$networks = $ssids[1];                            
                                                  
?>                                                
<html>                                            
        <head>                                         
                <title>SBox - WiFi connect</title>       
        </head>                                                                                        
                                                                                                       
        <body>                                                                                         
                <h1>Welcome</h1>                                                                       
                <form action="index.php" method="POST">                                                
                <?php foreach ($networks as $network): ?>                                              
                        <input type="radio" name="ssid" value="<?= $network ?>" /><?= $network ?><br />
                <?php endforeach; ?>                             
                                                                 
                        <input type="password" name="password" />
                                                               
                        <input type="submit" name="connect" value="Connect" />
                </form>
        </body>
</html>
