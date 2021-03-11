<?php
date_default_timezone_set('Europe/Istanbul');
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

    public function rtls_widget()
    {
        $this->load->view("rtls_widget");
    }
    public function rtls()
    {
        $filter_gateway_mac = $this->input->get("gateway_mac");
        $filter_device_type_id = $this->input->get("device_type_id");
        $filter_personnel_id = $this->input->get("personnel_id");
        $filter_status = $this->input->get("status");

        $client = $this->redis();

        $devices_keys = $client->keys("rtls:device:*");
        $rtls_devices = $client->mget($devices_keys); //With MGET we put "keys" and get the values 

        /*$card_keys = $client->keys("rtls:device:card:*");
        $card_devices = $client->mget($card_keys); //With MGET we put "keys" and get the values 
        $sensor_keys = $client->keys("rtls:device:sensor:*");
        $sensor_devices = $client->mget($sensor_keys);
        $light_keys = $client->keys("rtls:device:light:*");
        $light_devices = $client->mget($light_keys);*/

        $personnel = json_decode($client->get("personnel"), true);
        $gateways = json_decode($client->get("gateways"), true); //tüm gateway leri json olarak çektiği yer.
        $devices = json_decode($client->get("devices"), true); //tüm devices leri json olarak çektiği yer.
        $assets = json_decode($client->get("assets"), true); //tüm devices leri json olarak çektiği yer.
        $zones = json_decode($client->get("zones"), true); //tüm devices leri json olarak çektiği yer.

        foreach ($rtls_devices as $key => $rtls_device) {
            $device_name = "";
            $device_json = json_decode($rtls_device, true);

            if (array_key_exists('mac', $device_json)) {
                @$device_mac = $device_json["mac"];
            } else {
                @$device_mac = "";
            }

            if (array_key_exists('gateway', $device_json)) {
                @$gateway_mac = $device_json["gateway"];
            } else {
                @$gateway_mac = "";
            }

            foreach ($devices as $devices_key => $device) {
                if ($device["mac"] == $device_mac) {
                    $device_id = $device["id"];
                    $device_type_id = $device["type_id"];
                    $device_name = $device["name"];
                    $name = $device["name"];
                    break;
                }
            }
            foreach ($personnel as $key => $person) {
                if ($person["device_id"] == $device_id) {
                    $name = $person["name"];
                    $person_id = $person["id"];
                    break;
                } else {
                    $person_id = 0;
                }
            }
            foreach (@$assets as $key => $asset) {
                if (@$asset["device_id"] == $device_id) {
                    @$name = $asset["name"];
                    break;
                }
            }
            foreach ($gateways as $key => $gateway) {
                if ($gateway["mac"] == $gateway_mac) {
                    $location = $gateway["name"];
                    break;
                }
            }

            if (array_key_exists('epoch', $device_json)) {
                @$epoch = $device_json["epoch"];
            } else {
                @$epoch = 0;
            }
            if (array_key_exists('battery', $device_json)) {
                @$battery = $device_json["battery"];
            } else {
                @$battery = 0;
            }

            switch ($battery) {
                case $battery > 75:
                    $battery_id = 5;
                    $battery_icon = "fa-battery-full";
                    $battery_color = "green";
                    break;
                case $battery <= 75 && $battery > 50:
                    $battery_id = 4;
                    $battery_icon = "fa-battery-three-quarters";
                    $battery_color = "blue";
                    break;
                case $battery <= 75 && $battery > 50:
                    $battery_id = 3;
                    $battery_icon = "fa-battery-half";
                    $battery_color = "yellow";
                    break;
                case $battery <= 50 && $battery > 25:
                    $battery_id = 2;
                    $battery_icon = "fa-battery-quarter";
                    $battery_color = "orange";
                    break;
                case $battery <= 25:
                    $battery_id = 1;
                    $battery_icon = "fa-battery-empty";
                    $battery_color = "red";
                    break;
            }

            if (array_key_exists('rssi', $device_json)) {
                @$rssi = $device_json["rssi"];
            } else {
                @$rssi = 0;
            }

            if (@$rssi >= -55) {
                $rssi_id = 4;
            }
            if ($rssi < -55 && $rssi >= -70) {
                $rssi_id = 3;
            }
            if ($rssi < -70 && $rssi >= -85) {
                $rssi_id = 2;
            }
            if ($rssi < -85) {
                $rssi_id = 1;
            }
            if (array_key_exists('click', $device_json)) {
                @$click = "<p style='margin:0px;'>Click : " . @$device_json["click"] . "</p>";
            } else {
                @$click = "";
            }
            if (array_key_exists('x', $device_json)) {
                @$x = $device_json["x"];
                @$y = $device_json["y"];
                @$z = $device_json["z"];
                @$acc = "<p style='margin:0px;'>ACC : (" . $x . "," . $y . "," . $z . ")</p>";
            } else {
                @$acc = "";
            }
            if (array_key_exists('light', $device_json)) {
                @$light = "<p style='margin:0px;'>Light : " . @$device_json["light"] . "</p>";
            } else {
                @$light = "";
            }
            if (array_key_exists('temperature', $device_json)) {
                @$temperature = "<p style='margin:0px;'>T : " . @$device_json["temperature"] . " &#8451;</p>";
            } else {
                @$temperature = "";
            }
            if (array_key_exists('humidity', $device_json)) {
                @$humidity = "<p style='margin:0px;'>H : " . @$device_json["humidity"] . " %rh</p>";
            } else {
                @$humidity = "";
            }

            if (array_key_exists('motion', $device_json)) {
                @$motion = "<p style='margin:0px;'>Motionless : " . $device_json["motion"] . "</p>";
            } else {
                @$motion = "";
            }

            $time_dif = time() - $epoch;
            $time_dif_value = 30;

            if ($filter_gateway_mac == $gateway_mac || !$filter_gateway_mac) {
                if ($filter_device_type_id == $device_type_id || !$filter_device_type_id) {
                    if ($filter_personnel_id == $person_id || !$filter_personnel_id) {
                        if ($filter_status == 2 || !$filter_status) {
                            echo "<tr>";
                            if ($time_dif >= $time_dif_value) {
                                echo "<td>" . "<b title='0' style='color:red;'>&#11044;</b>" . "</td>";
                                echo "<td>" . date("d/m H:i:s", @$epoch) . "</td>";
                                echo "<td><a title='" . @$device_name . "' >" . @$name . "</a></td>";
                                echo "<td>" . @$location . "</td>";
                                echo   "<td></td>";
                                echo   "<td><img title='0' src='" . base_url("assets/img/rssi/0-rssi.png") . "'/></td>";
                                echo   "<td></td>";
                            } else {
                                echo "<td>" . "<b  title='1' style='color:green;'>&#11044;</b>" . "</td>";
                                echo "<td>" . date("H:i:s", @$epoch) . "</td>";
                                echo "<td><a title='" . @$device_name . "' >" . @$name . "</a></td>";
                                echo "<td>" . @$location . "</td>";
                                echo   "<td><i style='color:" . $battery_color . ";' title='".$battery."' class='fa " . $battery_icon . " fa-lg' aria-hidden='true'></i></td>";
                                echo   "<td><img title='" . $rssi . "' src='" . base_url("assets/img/rssi/") . $rssi_id . "-rssi.png" . "'/></td>";
                                echo   "<td>" . @$acc . @$click . @$light . @$temperature . @$humidity . @$motion . "</td>";
                            }
                            echo   "</tr>";
                        } else {
                            if ($filter_status == 3) {
                                echo "<tr>";
                                if ($time_dif >= $time_dif_value) { } else {
                                    echo "<td>" . "<b  title='1' style='color:green;'>&#11044;</b>" . "</td>";
                                    echo "<td>" . date("H:i:s", @$epoch) . "</td>";
                                    echo "<td><a title='" . @$device_name . "' >" . @$name . "</a></td>";
                                    echo "<td>" . @$location . "</td>";
                                    echo "<td><i style='color:" . $battery_color . ";' title='".$battery."' class='fa " . $battery_icon . " fa-lg' aria-hidden='true'></i></td>";
                                    echo "<td><img title='" . $rssi . "' src='" . base_url("assets/img/rssi/") . $rssi_id . "-rssi.png" . "'/></td>";
                                    echo "<td>" . @$acc . @$click . @$light . @$temperature . @$humidity . @$motion . "</td>";
                                }
                                echo   "</tr>";
                            }
                            if ($filter_status == 4) {
                                echo "<tr>";
                                if ($time_dif >= $time_dif_value) {
                                    echo "<td>" . "<b title='0' style='color:red;'>&#11044;</b>" . "</td>";
                                    echo "<td>" . date("d/m H:i:s", @$epoch) . "</td>";
                                    echo "<td><a title='" . @$device_name . "' >" . @$name . "</a></td>";
                                    echo "<td>" . @$location . "</td>";
                                    echo "<td></td>";
                                    echo "<td><img title='0' src='" . base_url("assets/img/rssi/0-rssi.png") . "'/></td>";
                                    echo "<td></td>";
                                }
                                echo   "</tr>";
                            }
                        }
                    }
                }
            }
        }
    }

    public function live_map()
    {
        $client = $this->redis();

        $dosya = '151_0_p.xml';

        //$tum = __DIR__.'/../../../resources/mapAssets/xml/'.$dosya;
        $tum = __DIR__ . '/../../151/' . $dosya;

        $xml = file_get_contents($tum);

        //$yeniArr = $this->gatewaysInfo();
        $yeniArr = "";

        //return view('m2boyut', ['xml' => $xml,'gwList' => $yeniArr]);
        //$devices = json_decode($client->get("devices"), true);
        $yeniArr = json_decode($client->get("gateways"), true);

        $data = array(
            'id' => '',
            'pageName' => "Map",
            'xml' => $xml,
            'gwList' => $yeniArr,
            //'devices' => $devices,
        );
        $this->load->view('live_map_widget', $data);
    }

    public function zone_widget()
    {
        $client = $this->redis();
        $zones = json_decode($client->get("zones"), true);
        $devices = json_decode($client->get("devices"), true);
        $device_types = json_decode($client->get("device_type"), true);
        $gateways = json_decode($client->get("gateways"), true);
        $personnel = json_decode($client->get("personnel"), true);
        $assets = json_decode($client->get("assets"), true);

        $devices_keys = $client->keys("rtls:device:*");
        $rtls_devices = $client->mget($devices_keys);


        foreach ($devices_keys as $key) {
            $device_jsons = json_decode($client->get($key), true);
            foreach ($devices as $device_key => $device) {
                if ($device["mac"] == $device_jsons["mac"]) {
                    $device_jsons["info"] = $device;
                    $device_jsons["type_id"] = $device["type_id"];
                    foreach ($device_types as $dt_key => $device_type) {
                        if ($device_type["id"] == $device["type_id"]) {
                            $device_jsons["info"]["type"] = $device_type;
                        }
                    }
                    foreach ($personnel as $person_key => $person) {
                        if ($person["device_id"] == $device["id"]) {
                            $device_jsons["person"][] = $person;
                        }
                    }

                    foreach ($assets as $assets_key => $asset) {
                        if ($asset["device_id"] == $device["id"]) {
                            $device_jsons["assets"][] = $asset;
                        }
                    }
                }
            }

/*print_r($device_jsons["mac"]);
echo " <br>";
print_r($device_jsons["info"]["name"]);
echo " <br>";
print_r($device_jsons["gateway"]);
                echo " <br>";
                echo " <br>";
                echo " <br>";*/
            foreach ($gateways as $gwkey => $gateway) {
                
            
                if ($device_jsons["gateway"] == $gateway["mac"]) {
                    $gateways[$gwkey]["devices"][] = $device_jsons;
                }/*else{
                    $gateways[$gwkey]["devices"] = array('devices');
                }*/
            }
        }//die();

        foreach ($zones as $zone_key => $zone) {
            foreach ($gateways as $gateway_key => $gateway) {
                if ($zone["id"] == $gateway["zone_id"]) {
                    //echo "  ".$key."   test"."</br>";
                    $zones[$zone_key]["gateways"][] = $gateway;
                }
            }
        }
        $data = array(
            'zones' => $zones,
        );
        header('Content-Type: application/json');
        echo json_encode($zones);
        //$this->load->view("zone_widget", $data);
    }

    public function zone()
    {
        $this->load->view("zone_widget");
    }
}
