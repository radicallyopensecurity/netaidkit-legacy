<?php

class NetAidManager
{
    static public function setup_ap($ssid, $key)
    {
        if (empty($ssid) || empty($key))
            return false;
        
        $ssid = escapeshellarg($ssid);
        $key  = escapeshellarg($key);
        
        $output = shell_exec("/usr/bin/sbox apconfig $ssid $key");
        
        return true;
    }
    
    static public function scan_wifi()
    {
        $output = shell_exec("/usr/bin/sbox wifiscan");
        preg_match_all("/ESSID: \"(.*)\"/", $output, $ssids);
        
        return $ssids[1];
    }
    
    static public function setup_wan($ssid, $key)
    {
        if (empty($ssid))
            return false;
            
        $ssid = escapeshellarg($ssid);
        $key  = escapeshellarg($key);
        
        $output = shell_exec("/usr/bin/sbox wificonn $ssid $key");
        
        return true;
    }
    
    static public function set_adminpass($adminpass)
    {
        if (empty($adminpass))
            return false;
        
        $adminpass = escapeshellarg($adminpass);
        
        $output = shell_exec("/usr/bin/sbox adminpwd $adminpass");
        
        return true;
    }
    
    static public function get_stage()
    {
        return shell_exec("/usr/bin/sbox getstage");
    }
    
    static public function set_stage($stage)
    {
        if (empty($stage))
            return false;
            
        $stage = escapeshellarg($stage);
        
        $output = shell_exec("/usr/bin/sbox setstage $stage");
        
        return true;
    }
    
    static public function toggle_tor()
    {
        $cur_stage = self::get_stage();
        if ($cur_stage == STAGE_TOR) {
            $mode = 'off';
            self::set_stage(STAGE_ONLINE);
        } elseif ($cur_stage == STAGE_ONLINE) {
            $mode = 'on';
            self::set_stage(STAGE_TOR);
        } else {
            return false;
        }
            
        $output = shell_exec("/usr/bin/sbox stagetor $mode");
        
        return true;
    }
    
    static public function toggle_vpn()
    {
        $cur_stage = self::get_stage();
        if ($cur_stage == STAGE_VPN) {
            $mode = 'off';
            self::set_stage(STAGE_ONLINE);
        } elseif ($cur_stage == STAGE_ONLINE) {
            $mode = 'on';
            self::set_stage(STAGE_VPN);
        } else {
            return false;
        }
            
        $output = shell_exec("/usr/bin/sbox stagevpn $mode");
        
        return true;
    }
    
    static public function wan_ssid()
    {
        $output = shell_exec("/usr/bin/sbox wlaninfo wlan0");

        preg_match_all("/ESSID: \"(.*)\"/", $output, $ssids);

        return $ssids[1][0];
    }
}
