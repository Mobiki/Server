<?php
date_default_timezone_set('Europe/Istanbul');
defined('BASEPATH') or exit('No direct script access allowed');


require_once(APPPATH . '../vendor/autoload.php');
Predis\Autoloader::register();



class Dashboard extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model("Dashboard_model");
        $this->load->model("Devices_model");
        $this->load->model("Alert_model");
        $this->load->model("Users_model");
        $this->load->model("Personnel_model");
        $this->load->model("Assets_model");
        $this->load->model("Gateways_model");
    }

    public function redis()
    {
        if ($this->config->item('redis_status') == TRUE) {
            $client = new Predis\Client([
                'scheme' => $this->config->item('redis_scheme'),
                'host'   => $this->config->item('redis_host'),
                'port'   => $this->config->item('redis_port'),
                'password' => $this->config->item('redis_auth')
            ]);
            return $client;
        }
    }

    public function index()
    {
        $client = $this->redis();

        $gwjson = [];
        $gwkeys = $client->keys("gw:*");
        foreach ($gwkeys as $key => $value) {
            $gwset = json_decode($client->get($value), true);
            $gwjson[$gwset["mac"]] = $gwset["devices"];
        }

        $gateway_list = $this->Dashboard_model->gateway_list();
        $device_list = $this->Dashboard_model->device_list();

        $data = array(
            'pageId'    =>  1,
            'pageName' => 'Dashboard',
            'device_list' => $device_list,
            'gwjson' => $gwjson,

        );

        $this->load->view('dashboard', $data);
    }

    public function gateways()
    {
        $client = $this->redis();

        //$gwkeys = $client->keys("gw:*");
        $gateways = json_decode($client->get("gateways"), true);

        foreach ($gateways as $key => $gwvalue) {
            $gwinfo = json_decode($client->get("gw:" . $gwvalue["mac"]), true);
            $timefark = time() - $gwinfo["epoch"];
            if ($timefark > 30) {
                $device_num = 0;
            } else {
                $device_num = $gwinfo["devices"];
            }
            echo "<li><a href='gateways/detail?mac=" . $gwvalue["mac"] . "'>" . $gwvalue["name"] . " (" . $device_num . ") </a></li>";
        }
        echo "<li><a href='gateways'>All Gateways</a></li>";
    }


    public function rtls()
    {
        $client = $this->redis();

        //öenmli
        //$devices = $client->keys("devices:*"); diye çekilecek
        $userkeys = $client->keys("user:*");//devices olarak değiştir  $device_keys = $client->keys("devices:*");
        $sensorkeys = $client->keys("sensor:*");
        $lightkeys = $client->keys("light:*");

        //$personnel = json_decode($client->get("personnel"), true);
        //$assets = json_decode($client->get("assets"), true);

        $gateways = json_decode($client->get("gateways"), true); //tüm gateway leri json olarak çektiği yer.
        $devices = json_decode($client->get("devices"), true); //tüm devices leri json olarak çektiği yer.
        //users:
        //header('Content-Type: application/json');
        try {
            foreach ($userkeys as $key => $value) {

                $userlist = json_decode($client->get("users:list"), true);

                $userget = json_decode($client->get($value), true);

                $users = array(
                    'usermac' => str_replace($value, "user:", ""),
                    'userdata' => $userget

                );
                //echo json_encode($users);
                if (isset($userget["epoch"])) {
                    $epoch = $userget["epoch"] + 10800;
                } else {
                    $epoch = 0;
                }
                $timefark = (time() + 10800) - $epoch;

                echo   "<tr>";

                if ($timefark >= 30) {
                    echo "<td>" . "<b style='color:red;'>&#11044;</b>" . "</td>";
                    echo "<td> " . date("d/m H:i:s", $userget["epoch"]) . "</td>";


                    foreach ($devices as $key => $dvalue) {
                        if ($dvalue["mac"] == str_replace("user:", "", $value)) {
                            echo   "<td><a href='" . "history/userlog?mac=" . $dvalue["mac"] . "'>" . $dvalue["name"] . "</a></td>";
                        }
                    }

                    foreach ($gateways as $gwkey => $gwvalue) {
                        if ($gwvalue["mac"] == $userget["gateway"]) {
                            echo   "<td>" . $gwvalue["name"] . "</td>";
                        }
                    }
                    echo   "<td>" . "" . "</td>";
                    echo   "<td>" . "" . "</td>";
                    echo   "<td>" . "" . "</td>";
                    echo   "<td>" . "" . "</td>";
                    echo   "<td>" . "" . "</td>";
                    echo   "<td>" . "" . "</td>";
                    echo   "<td>" . "" . "</td>";
                } else {
                    echo   "<td>" . "<b style='color:green;'>&#11044;</b>" . "</td>";
                    echo "<td> " . date("H:i:s", $epoch - 10800) . "</td>";
                    foreach ($devices as $key => $dvalue) {
                        if ($dvalue["mac"] == str_replace("user:", "", $value)) {
                            echo   "<td><a href='" . "history/userlog?mac=" . $dvalue["mac"] . "'>" . $dvalue["name"] . "</a></td>";
                        }
                    }
                    foreach ($gateways as $gwkey => $gwvalue) {
                        if ($gwvalue["mac"] == $userget["gateway"]) {
                            echo   "<td>" . $gwvalue["name"] . "</td>";
                        }
                    }
                    echo   "<td>" ?>
                    <div class="progress progress-sm mr-2">
                        <div class="progress-bar bg-info" title="<?php echo $userget["battery"]; ?>%" role="progressbar" style="width: <?php echo $userget["battery"]; ?>%" aria-valuenow="<?php echo $userget["battery"]; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                <?php echo "</td>";
                                    echo   "<td>" . $userget["rssi"] . "</td>";
                                    echo   "<td>" . "" . "</td>";
                                    echo   "<td>ACC(" . $userget["x"] . "," . $userget["y"] . "," . $userget["z"] . ")</td>";
                                    echo   "<td>" . "" . "</td>";
                                    echo   "<td>" . "" . "</td>";
                                    echo   "<td>" . $userget["click"] . "</td>";
                                }
                                echo   "</tr>";
                            }
                        } catch (Exception $e) {
                            echo 'Error ',  $e->getMessage(), "\n";
                        }

                        //sensor:
                        try {
                            foreach ($sensorkeys as $key => $value) {

                                $userget = json_decode($client->get($value), true);

                                //header('Content-Type: application/json');
                                //echo json_encode($users);
                                if (isset($userget["epoch"])) {
                                    $epoch = $userget["epoch"] + 10800;
                                } else {
                                    $epoch = 0;
                                }
                                $timefark = (time() + 10800) - $epoch;
                                echo   "<tr>";

                                if ($timefark >= 30) {
                                    echo "<td>" . "<b style='color:red;'>&#11044;</b>" . "</td>";
                                    echo "<td> " . date("d/m H:i:s", $userget["epoch"]) . "</td>";

                                    foreach ($devices as $key => $dvalue) {
                                        if ($dvalue["mac"] == str_replace("sensor:", "", $value)) {
                                            echo   "<td><a href='" . "history/userlog?mac=" . $dvalue["mac"] . "'>" . $dvalue["name"] . "</a></td>";
                                        }
                                    }

                                    if (isset($userget["gateway"])) {
                                        foreach ($gateways as $gwkey => $gwvalue) {

                                            if ($gwvalue["mac"] == $userget["gateway"]) {
                                                echo   "<td>" . $gwvalue["name"] . "</td>";
                                            }
                                        }
                                    } else {
                                        "<td>" . "" . "</td>";
                                    }
                                    echo   "<td>" . "" . "</td>";
                                    echo   "<td>" . "" . "</td>";
                                    echo   "<td>" . "" . "</td>";
                                    echo   "<td>" . "" . "</td>";
                                    echo   "<td>" . "" . "</td>";
                                    echo   "<td>" . "" . "</td>";
                                } else {
                                    echo   "<td>" . "<b style='color:green;'>&#11044;</b>" . "</td>";
                                    echo "<td> " . date("H:i:s", $epoch - 10800) . "</td>";
                                    /*foreach ($userlist as $ukey => $uvalue) {
                        if (str_replace("user:", "", $value) == $uvalue["mac"]) {
                            echo   "<td><a href='"."history/userlog?mac=".$uvalue["mac"]."'>" . $uvalue["name"] . "</a></td>";
                        }
                    }*/
                                    foreach ($devices as $key => $dvalue) {
                                        if ($dvalue["mac"] == str_replace("sensor:", "", $value)) {
                                            echo   "<td><a href='" . "history/userlog?mac=" . $dvalue["mac"] . "'>" . $dvalue["name"] . "</a></td>";
                                        }
                                    }
                                    foreach ($gateways as $gwkey => $gwvalue) {
                                        if ($gwvalue["mac"] == $userget["gateway"]) {
                                            echo   "<td>" . $gwvalue["name"] . "</td>";
                                        }
                                    }
                                    echo   "<td>" ?>
                    <div class="progress progress-sm mr-2">
                        <div class="progress-bar bg-info" title="<?php echo $userget["battery"]; ?>%" role="progressbar" style="width: <?php echo $userget["battery"]; ?>%" aria-valuenow="<?php echo $userget["battery"]; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                <?php echo "</td>";
                                    echo   "<td>" . $userget["rssi"] . "</td>";
                                    echo   "<td>" . "" . "</td>";
                                    echo   "<td>" . "" . "</td>";
                                    echo   "<td>" . $userget["temperature"] . " &#176;C</td>";
                                    echo   "<td>" . $userget["humidity"] . " % rH</td>";
                                    echo   "<td>" . "" . "</td>";
                                }
                                echo   "</tr>";
                            }
                        } catch (Exception $e) {
                            echo 'Error ',  $e->getMessage(), "\n";
                        }

                        //lightkeys:
                        try {
                            foreach ($lightkeys as $key => $value) {

                                $userget = json_decode($client->get($value), true);

                                //header('Content-Type: application/json');
                                //echo json_encode($users);
                                if (isset($userget["epoch"])) {
                                    $epoch = $userget["epoch"] + 10800;
                                } else {
                                    $epoch = 0;
                                }
                                $timefark = (time() + 10800) - $epoch;
                                echo   "<tr>";

                                if ($timefark >= 30) {
                                    echo "<td>" . "<b style='color:red;'>&#11044;</b>" . "</td>";
                                    echo "<td> " . date("d/m H:i:s", $userget["epoch"]) . "</td>";
                                    foreach ($devices as $key => $dvalue) {
                                        if ($dvalue["mac"] == str_replace("light:", "", $value)) {
                                            echo   "<td><a href='" . "history/userlog?mac=" . $dvalue["mac"] . "'>" . $dvalue["name"] . "</a></td>";
                                        }
                                    }
                                    foreach ($gateways as $gwkey => $gwvalue) {
                                        if ($gwvalue["mac"] == $userget["gateway"]) {
                                            echo   "<td>" . $gwvalue["name"] . "</td>";
                                        }
                                    }
                                    echo   "<td>" . "" . "</td>";
                                    echo   "<td>" . "" . "</td>";
                                    echo   "<td>" . "" . "</td>";
                                    echo   "<td>" . "" . "</td>";
                                    echo   "<td>" . "" . "</td>";
                                    echo   "<td>" . "" . "</td>";
                                    echo   "<td>" . "" . "</td>";
                                } else {
                                    echo   "<td>" . "<b style='color:green;'>&#11044;</b>" . "</td>";
                                    echo "<td> " . date("H:i:s", $epoch - 10800) . "</td>";

                                    foreach ($devices as $key => $dvalue) {
                                        if ($dvalue["mac"] == str_replace("light:", "", $value)) {
                                            echo   "<td><a href='" . "history/userlog?mac=" . $dvalue["mac"] . "'>" . $dvalue["name"] . "</a></td>";
                                        }
                                    }
                                    foreach ($gateways as $gwkey => $gwvalue) {
                                        if ($gwvalue["mac"] == $userget["gateway"]) {
                                            echo   "<td>" . $gwvalue["name"] . "</td>";
                                        }
                                    }
                                    echo   "<td>" ?>
                    <div class="progress progress-sm mr-2">
                        <div class="progress-bar bg-info" title="<?php echo $userget["battery"]; ?>%" role="progressbar" style="width: <?php echo $userget["battery"]; ?>%" aria-valuenow="<?php echo $userget["battery"]; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <?php echo "</td>";
                                        echo   "<td>" . $userget["rssi"] . "</td>";
                                        echo   "<td>" . $userget["light"] . "</td>";
                                        echo   "<td>" . "" . "</td>";
                                        echo   "<td>" . "" . "</td>";
                                        echo   "<td>" . "" . "</td>";
                                        echo   "<td>" . "" . "</td>";
                                    }
                                    echo   "</tr>";
                                }
                            } catch (Exception $e) {
                                echo 'Error ',  $e->getMessage(), "\n";
                            }
                        }
                        //###############alert##################################################################################

                        public function alert()
                        {
                            $client = $this->redis();
                            $alert_rules = json_decode($client->get("alert_rules"), true);
                            $devices = json_decode($client->get("devices"), true);
                            $device_types = json_decode($client->get("device_type"), true);
                            $gateways = json_decode($client->get("gateways"), true);
                            $alertkeys = $client->keys("alertset:*");
                            foreach ($alertkeys as $key => $value) {
                                $alert = json_decode($client->get($value), true);
                                if (isset($alert["status"]) && $alert["status"] == 1) {
                                    $alert_key = $alertkeys[$key];
                                    $mac = $alert["mac"];
                                    $epoch = $alert["epoch"];
                                    $gateway_mac = $alert["gateway"];
                                    $status = $alert["status"];
                                    $alert_value = $alert["alert"];
                                    $device_id = "";
                                    $device_name = "";
                                    $alert_id = "";
                                    $alert_name = "";
                                    $device_type_name = "";
                                    $device_type_id = "";
                                    $gateway_name = "";
                                    $gateway_id = "";
                                    $gateway_location = "";
                                    //find device id and type_id
                                    foreach ($devices as $devices_key => $device) {
                                        if ($device["mac"] == $mac) {
                                            $device_id = $device["id"];
                                            $device_name = $device["name"];
                                            $device_type_id = $device["type_id"];
                                            break;
                                        }
                                    }
                                    //find device type name
                                    foreach ($device_types as $device_types_key => $device_type) {
                                        if ($device_type["id"] == $device_type_id) {
                                            $device_type_name = $device_type["name"];
                                            break;
                                        }
                                    }
                                    //find gateway information
                                    foreach ($gateways as $gateways_key => $gateway) {
                                        if ($gateway["mac"] == $gateway_mac) {
                                            $gateway_name = $gateway["name"];
                                            $gateway_id = $gateway["id"];
                                            $gateway_location = $gateway["lat"] . ", " . $gateway["lng"];
                                            break;
                                        }
                                    }

                                    //find alert name all devices or one device
                                    foreach ($alert_rules as $alert_rules_key => $alert_rule) {
                                        if ($alert_rule["device_id"] == 0) {
                                            //ALL devices
                                            if ($alert_rule["device_type"] == $device_type_id) {
                                                if ($alert_rule["equation"] == "0") {//equation 0 "=" for sensor_value
                                                    if ($alert_rule["sensor_value"] == $alert_value) {
                                                        $alert_name = $alert_rule["name"];
                                                        $alert_id = $alert_rule["id"];
                                                        $this->alert_parse($alert_name,$epoch,$device_name,$gateway_location,$gateway_name,$alert_key,$alert_id,$device_id,$gateway_id);
                                                    }
                                                }
                                                if ($alert_rule["equation"] == "1") {//equation 1 ">" for sensor_value
                                                    if ($alert_value > $alert_rule["sensor_value"]) {
                                                        $alert_name = $alert_rule["name"];
                                                        $alert_id = $alert_rule["id"];
                                                        $this->alert_parse($alert_name,$epoch,$device_name,$gateway_location,$gateway_name,$alert_key,$alert_id,$device_id,$gateway_id);
                                                    }
                                                }
                                                if ($alert_rule["equation"] == "2") {//equation 2 "<" for sensor_value
                                                    if ($alert_value < $alert_rule["sensor_value"]) {
                                                        $alert_name = $alert_rule["name"];
                                                        $alert_id = $alert_rule["id"];
                                                        $this->alert_parse($alert_name,$epoch,$device_name,$gateway_location,$gateway_name,$alert_key,$alert_id,$device_id,$gateway_id);
                                                    }
                                                }
                                            }
                                        } else {
                                            //one devices
                                            if ($alert_rule["device_id"] == $device_id) {
                                                if ($alert_rule["equation"] == "0") {//equation 0 "=" for sensor_value
                                                    if ($alert_rule["sensor_value"] == $alert_value) {
                                                        $alert_name = $alert_rule["name"];
                                                        $alert_id = $alert_rule["id"];
                                                        $this->alert_parse($alert_name,$epoch,$device_name,$gateway_location,$gateway_name,$alert_key,$alert_id,$device_id,$gateway_id);
                                                    }
                                                }
                                                if ($alert_rule["equation"] == "1") {//equation 1 ">" for sensor_value
                                                    if ($alert_value > $alert_rule["sensor_value"]) {
                                                        $alert_name = $alert_rule["name"];
                                                        $alert_id = $alert_rule["id"];
                                                        $this->alert_parse($alert_name,$epoch,$device_name,$gateway_location,$gateway_name,$alert_key,$alert_id,$device_id,$gateway_id);
                                                    }
                                                }
                                                if ($alert_rule["equation"] == "2") {//equation 2 "<" for sensor_value
                                                    if ($alert_value < $alert_rule["sensor_value"]) {
                                                        $alert_name = $alert_rule["name"];
                                                        $alert_id = $alert_rule["id"];
                                                        $this->alert_parse($alert_name,$epoch,$device_name,$gateway_location,$gateway_name,$alert_key,$alert_id,$device_id,$gateway_id);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

    public function alert_parse($alert_name,$epoch,$device_name,$gateway_location,$gateway_name,$alert_key,$alert_id,$device_id,$gateway_id)
    {
        echo '<tr>';
        echo '<td>' . $alert_name . '</td>';
        echo '<td>' . date("d/m H:i:s", $epoch) . '</td>';
        echo '<td>' . $device_name . '</td>';
        echo '<td><a href="' . $gateway_location . '">' . $gateway_name . '</a></td>';
        echo '<td>';
        echo '<div class="custom-control custom-switch">';?>
        <input type="checkbox" class="custom-control-input" onClick="$('#stopalarm').load('alert/suspend_alert?alert_key=<?php echo $alert_key;?>&alert_rules_id=<?php echo $alert_id;?>&device_id=<?php echo $device_id;?>&gateway_id=<?php echo $gateway_id;?>');" id="cs" />
        <?php
        echo '<label class="custom-control-label" for="cs"> Suspend Alarm</label>';
        echo '</div>';
        echo '</td>';
        echo '</tr>';
    }



    public function get_all_suspended_alerts()
    {
        $suspended_alerts = $this->Alert_model->get_all_suspended_alerts();
        $alert_rules = $this->Alert_model->get_all_alert_rules();
        $users = $this->Users_model->get_all();
        $devices = $this->Devices_model->get_all();
        $personnel = $this->Personnel_model->get_all();
        $assets = $this->Assets_model->get_all();
        $gateways = $this->Gateways_model->get_all();

        foreach ($suspended_alerts as $key => $suspended_alert) {
            $alert_log_id = $suspended_alert["id"];
            $alert_key = $suspended_alert["alert_key"];
            $alert_rules_id=$suspended_alert["alert_rules_id"];
            $device_id=$suspended_alert["device_id"];
            $gateway_id=$suspended_alert["gateway_id"];
            $suspended_user_id=$suspended_alert["suspended_user_id"];
            $suspend_date=$suspended_alert["suspend_date"];
            $user_name="";
            $alert_name="";
            $device_name="";
            $personnel_name="";
            $asset_name="";
            $gateway_name="";
            foreach ($users as $key => $user) {
                if ($suspended_alert["suspended_user_id"] == $user["id"]) {
                $user_name = $user["name"];
                break;
                }
            }
            foreach ($alert_rules as $key => $alert_rule) {
                if ($alert_rule["id"]==$alert_rules_id) {
                    $alert_name=$alert_rule["name"];
                    break;
                }
            }
            foreach ($devices as $key => $device) {
                if ($device["id"]==$device_id) {
                    $device_type=$device["type_id"];
                    $device_name=$device["name"];
                    break;
                }
            }
            foreach ($gateways as $gateways_key => $gateway) {
                if ($gateway["id"] == $gateway_id) {
                    $gateway_name = $gateway["name"];
                    $gateway_location = $gateway["lat"] . ", " . $gateway["lng"];
                    break;
                }
            }

                
                    echo '<tr>';
                    echo '<td>' . $alert_name . "</td>";
                    echo '<td>' . $suspend_date . '</td>';
                    echo '<td>' . $device_name . '</td>';
                    echo '<td>' . $gateway_name . '</td>';
                    echo '<td>' . $user_name . '</td>';
                    echo '<td>'; ?>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" onClick="$('#stopalarm').load('alert/close_alert?alert_key=<?php echo $alert_key; ?>&alert_log_id=<?php echo $alert_log_id; ?>');" id="css<?php echo $alert_log_id; ?>" ><?php
                    echo '<label class="custom-control-label" for="css'.$alert_log_id.'"> Close</label>';
                    echo '</div>';
                    echo '</td>';
                    echo '</tr>';
        }
    }

    public function get_all_closed_alerts()
    {
        $close_alerts = $this->Alert_model->get_all_closed_alerts();
        $alert_rules = $this->Alert_model->get_all_alert_rules();
        $users = $this->Users_model->get_all();
        $devices = $this->Devices_model->get_all();
        $personnel = $this->Personnel_model->get_all();
        $assets = $this->Assets_model->get_all();
        $gateways = $this->Gateways_model->get_all();
        foreach ($close_alerts as $key => $close_alert) {
            $alert_log_id = $close_alert["id"];
            $alert_key = $close_alert["alert_key"];
            $alert_rules_id=$close_alert["alert_rules_id"];
            $device_id=$close_alert["device_id"];
            $gateway_id=$close_alert["gateway_id"];
            $closed_user_id=$close_alert["closed_user_id"];
            $close_date=$close_alert["close_date"];
            $user_name="";
            $alert_name="";
            $device_name="";
            $personnel_name="";
            $asset_name="";
            $gateway_name="";
            foreach ($users as $key => $user) {
                if ($close_alert["closed_user_id"] == $user["id"]) {
                $user_name = $user["name"];
                break;
                }
            }
            foreach ($alert_rules as $key => $alert_rule) {
                if ($alert_rule["id"]==$alert_rules_id) {
                    $alert_name=$alert_rule["name"];
                    break;
                }
            }
            foreach ($devices as $key => $device) {
                if ($device["id"]==$device_id) {
                    $device_type=$device["type_id"];
                    $device_name=$device["name"];
                    break;
                }
            }
            foreach ($gateways as $gateways_key => $gateway) {
                if ($gateway["id"] == $gateway_id) {
                    $gateway_name = $gateway["name"];
                    $gateway_location = $gateway["lat"] . ", " . $gateway["lng"];
                    break;
                }
            }
            echo '<tr>';
            echo '<td>' . $alert_name . "</td>";
            echo '<td>' . $close_date . '</td>';
            echo '<td>' . $device_name . '</td>';
            echo '<td>' . $gateway_name . '</td>';
            echo '<td>' . $user_name . '</td>';
            echo '<td> Closed </td>';
            echo '</tr>';
        }
    }
}