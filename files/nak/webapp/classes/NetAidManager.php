<?php

class NetAidManager
{
    static public function setup_ap($ssid, $key)
    {
        if (empty($ssid) || empty($key))
            return false;

        $client = new NakdClient();
        $output = $client->doCommand('apconfig', array($ssid, $key));

        return true;
    }

    static public function scan_wifi()
    {
        $client = new NakdClient();
        $output = $client->doCommand('wifiscan');

        preg_match_all("/ESSID: \"(.+?)\".+?Encryption: (.+?) ?[\(|\n]/s", $output, $wifi_info);

        $wifi_list = array();
        foreach($wifi_info[1] as $i => $wifi) {
            $ssid = $wifi_info[1][$i];
            $enctype = $wifi_info[2][$i];

            $enctype = preg_replace('/mixed | PSK| 802.1X/', '', $enctype);
            if ($enctype == 'none')
                $enctype = 'Open';

            $wifi_list[$ssid] = $enctype;
        }

        asort($wifi_list);

        return $wifi_list;
    }

    static public function setup_wan($ssid, $key)
    {
        if (empty($ssid))
            return false;

        $client = new NakdClient();
        $output = $client->doCommand('wificonn', array($ssid, $key));

        return true;
    }

    static public function go_online()
    {
        $client = new NakdClient();
        $output = $client->doCommand('goonline');

        return true;
    }

    static public function set_adminpass($adminpass)
    {
        if (empty($adminpass) || strlen($adminpass) < 8)
            return false;

        $passfile = ROOT_DIR . '/data/pass';

        $admin_hash = password_hash($adminpass, PASSWORD_BCRYPT);

        return file_put_contents($passfile, $admin_hash);
    }

    static public function check_adminpass($loginpass)
    {
        $passfile = ROOT_DIR . '/data/pass';

        if (!file_exists($passfile))
            throw new Exception('Password file missing.');

        $admin_hash = file_get_contents($passfile);

        return password_verify($loginpass, $admin_hash);
    }

    static public function get_stage()
    {
        $client = new NakdClient();
        $output = $client->doCommand('getstage');

        return $output;
    }

    static public function get_inetstat()
    {
        $client = new NakdClient();
        $output = $client->doCommand('inetstat');

        return ($output == "0") ? true : false;
    }

    static public function set_stage($stage)
    {
        if (empty($stage))
            return false;

        $client = new NakdClient();
        $output = $client->doCommand('setstage', array($stage));

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

        $client = new NakdClient();
        $output = $client->doCommand('stagetor', array($mode));

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

        $client = new NakdClient();
        $output = $client->doCommand('stagevpn', array($mode));

        return true;
    }

    static public function wan_ssid()
    {
        $client = new NakdClient();
        $output = $client->doCommand('wlaninfo', array("wlan0"));

        preg_match_all("/Mode: (Master|Client) /", $output, $mode);
        preg_match_all("/ESSID: \"(.*)\"/", $output, $ssids);

        if ($mode[1][0] == 'Master')
            return 'Wired connection';
        else
            return $ssids[1][0];
    }

    static public function do_update($image_file)
    {

        $pid = pcntl_fork();
        if ($pid == -1) {
             die('could not fork');
        } else if ($pid) {
            //parent
        } else {
            $client = new NakdClient();
            $output = $client->doCommand('doupdate', array($image_file));
        }
    }

    static public function toggle_routing($mode)
    {
        if ($mode != 'on')
            $mode = 'off';

        $client = new NakdClient();
        $output = $client->doCommand('nrouting', array($mode));

        return true;
    }

    static public function toggle_broadcast($mode)
    {
        if ($mode != 'on')
            $mode = 'off';

        $client = new NakdClient();
        $output = $client->doCommand('broadcst', array($mode));

        return true;
    }

    static public function routing_status()
    {
        $setting = shell_exec('uci show firewall.@forwarding[0].enabled');
        $mode = substr($setting, -3, 1);
        return $mode;
    }

    static public function broadcast_hidden_status()
    {
        $setting = shell_exec('uci show wireless.@wifi-iface[1].hidden');
        $mode = substr($setting, -3, 1);
        return $mode;
    }

    static public function detect_portal() {
        $client = new NakdClient();
        $output = $client->doCommand('isportal', array($mode));

        return (trim($output) == "yes" ? true : false);
    }

}
