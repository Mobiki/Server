<?php

require_once(APPPATH . '../vendor/autoload.php');
Predis\Autoloader::register();

class Widgets extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function redis()
    {
        $client = new Predis\Client([
            'scheme' => $this->config->item('redis_scheme'),
            'host'   => $this->config->item('redis_host'),
            'port'   => $this->config->item('redis_port'),
            'password' => $this->config->item('redis_auth')
        ]);
        return $client;
    }

    //----------------------------------------------------------------------------------------

    public function gateway_count()
    {
        $client = $this->redis();
        $gateways = json_decode($client->get("gateways"), true);
        $t = 0;
        $a = 0;
        foreach ($gateways as $key => $gateway) {
            if ($gateway["status"] == "1") {
                $a = $a + 1;
            }
            $t = $t + 1;
        }
        echo "<span class='small' style='color:green' title='Active Gateways'>" . $a . "</span>";
        echo " / <span style='color:dodgerblue' title='ALL Registered Gateways'>" . $t . "</span>";
    }

    //----------------------------------------------------------------------------------------
    public function device_count()
    {
        $client = $this->redis();
        $devices = json_decode($client->get("devices"), true);
        $t = 0;
        $a = 0;
        foreach ($devices as $key => $device) {
            if ($device["status"] == "1") {
                $a = $a + 1;
            }
            $t = $t + 1;
        }
        echo "<span class='small' style='color:green' title='Active Gateways'>" . $a . "</span>";
        echo " / <span style='color:dodgerblue' title='ALL Registered Gateways'>" . $t . "</span>";
    }

    //----------------------------------------------------------------------------------------

    public function rtls()
    {
        $client = $this->redis();

        $devices_keys = $client->keys("rtls:device:*");
        $rtls_devices = $client->mget($devices_keys); //With MGET we put "keys" and get the values 

        $card_keys = $client->keys("rtls:device:card:*");
        $card_devices = $client->mget($card_keys); //With MGET we put "keys" and get the values 
        $sensor_keys = $client->keys("rtls:device:sensor:*");
        $sensor_devices = $client->mget($sensor_keys);
        $light_keys = $client->keys("rtls:device:light:*");
        $light_devices = $client->mget($light_keys);

        $personnel = json_decode($client->get("personnel"), true);
        $gateways = json_decode($client->get("gateways"), true); //tüm gateway leri json olarak çektiği yer.
        $devices = json_decode($client->get("devices"), true); //tüm devices leri json olarak çektiği yer.
        $assets = json_decode($client->get("assets"), true); //tüm devices leri json olarak çektiği yer.

        foreach ($rtls_devices as $key => $rtls_device) {
            $device_json = json_decode($rtls_device, true);
            $device_mac = $device_json["mac"];
            $gateway_mac = $device_json["gateway"];

            foreach ($devices as $devices_key => $device) {
                if ($device["mac"] == $device_mac) {
                    $device_id = $device["id"];
                    $device_name = $device["name"];
                    $name = $device["name"];
                }
            }
            foreach ($personnel as $key => $person) {
                if ($person["device_id"] == $device_id) {
                    $name = $person["name"];
                }
            }
            foreach ($assets as $key => $asset) {
                if ($asset["device_id"] == $device_id) {
                    $name = $asset["name"];
                }
            }
            foreach ($gateways as $key => $gateway) {
                if ($gateway["mac"] == $gateway_mac) {
                    $location = $gateway["name"];
                }
            }
            $epoch = $device_json["epoch"];
            $battery = $device_json["battery"];
            $rssi = $device_json["rssi"];

            if ($device_json["click"]) {
                @$click = "<p style='margin:0px;'>Click : " . @$device_json["click"] . "</p>";
            } else {
                @$click = "";
            }
            if ($device_json["x"]) {
                @$x = $device_json["x"];
                @$y = $device_json["y"];
                @$z = $device_json["z"];
                @$acc = "<p style='margin:0px;'>ACC : (" . $x . "," . $y . "," . $z . ")</p>";
            } else {
                @$acc = "";
            }
            if ($device_json["light"]) {
                @$light = "<p style='margin:0px;'>Light : " . @$device_json["light"] . "</p>";
            } else {
                @$light = "";
            }
            if ($device_json["temperature"]) {
                @$temperature = "<p style='margin:0px;'>T : " . @$device_json["temperature"] . " &#8451;</p>";
            } else {
                @$temperature = "";
            }
            if (@$device_json["humidity"]) {
                @$humidity = "<p style='margin:0px;'>H : " . @$device_json["humidity"] . " %rh</p>";
            } else {
                @$humidity = "";
            }
            $time_dif = time() - $epoch;
            $time_dif_value = 30;

            echo "<tr>";
            if ($time_dif >= $time_dif_value) {
                echo "<td>" . "<b title='0' style='color:red;'>&#11044;</b>" . "</td>";
            } else {
                echo "<td>" . "<b  title='1' style='color:green;'>&#11044;</b>" . "</td>";
            }
            echo "<td>" . date("H:i:s", @$epoch) . "</td>";
            echo "<td><a href='" . $device_mac . "' title='" . $device_mac . "' >" . @$name . "</a></td>";
            echo "<td>" . $location . "</td>";
            if ($time_dif >= $time_dif_value) {
                echo   "<td></td>";
                echo   "<td></td>";
                echo   "<td></td>";
            } else {
                echo   "<td><div class='progress progress-sm mr-2'>
                <div class='progress-bar bg-info' title='" . $battery . "%' style='width:" . $battery . "%' aria-valuenow='" . $battery . "' aria-valuemin='0' aria-valuemax='100'></div>
            </div></td>";
                echo   "<td>" . $rssi . "</td>";
                echo   "<td>" . @$acc . @$click . @$light . @$temperature . @$humidity . "</td>";
            }
            echo   "</tr>";
        }
    }
}
