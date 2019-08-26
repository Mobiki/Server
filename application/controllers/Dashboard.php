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
    }

    public function redis(Type $var = null)
    {
        $client = new Predis\Client([
            'scheme' => $this->config->item('redis_scheme'),
            'host'   => $this->config->item('redis_host'),
            'port'   => $this->config->item('redis_port'),
            'password' => $this->config->item('redis_password')
        ]);
        return $client;
    }

    public function index(Type $var = null)
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
            'gateway_list' => $gateway_list,
            'device_list' => $device_list,
            'gwjson' => $gwjson,

        );

        $this->load->view('dashboard', $data);
    }

    public function gateways(Type $var = null)
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


    public function rtls(Type $var = null)
    {
        $client = $this->redis();

        $userkeys = $client->keys("user:*");
        $sensorkeys = $client->keys("sensor:*");
        $lightkeys = $client->keys("light:*");

        $gateways = json_decode($client->get("gateways"), true); //tüm gateway leri json olarak çektiği yer.
        $devices = json_decode($client->get("devices"), true); //tüm gateway leri json olarak çektiği yer.

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

                    /*print_r($userget);
die();*/
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
    //#################################################################################################

    public function alert(Type $var = null)
    {
        $client = $this->redis();

        $alert_rules = json_decode($client->get("alert_rules"), true);
        $devices = json_decode($client->get("devices"), true);
        $gateways = json_decode($client->get("gateways"), true);

        $lightkeys = $client->keys("alertset:*");

        foreach ($lightkeys as $key => $value) {
            $alert = json_decode($client->get($value));

            if (isset($alert->status)) {
                if ($alert->status == 1) {

                    echo   "<tr>";
                    echo   "<td>";
                    foreach ($devices as $key => $dvalue) {
                        if ($alert->mac == $dvalue["mac"]) {


                            foreach ($alert_rules as $key => $arvalue) {
                                if ($arvalue["device_id"] == $dvalue["id"] && $arvalue["device_id"] != 0) {
                                    echo $arvalue["name"];
                                }


                                if ($arvalue["equation"] == 0) { // 0 =

                                    if ($arvalue["device_id"] == 0 && $arvalue["device_type"] == $dvalue["type_id"]) {
                                        if ($arvalue["sensor_value"] == $alert->alert) {
                                            echo $arvalue["name"];
                                        }
                                    }
                                }

                                if ($arvalue["equation"] == 1) { // 1 >
                                    if ($arvalue["device_id"] == 0 && $arvalue["device_type"] == $dvalue["type_id"]) {
                                        if ($arvalue["sensor_value"] < $alert->alert) {
                                            echo $arvalue["name"];
                                        }
                                    }
                                }

                                if ($arvalue["equation"] == 2) { // 2 <
                                    if ($arvalue["device_id"] == 0 && $arvalue["device_type"] == $dvalue["type_id"]) {
                                        if ($arvalue["sensor_value"] > $alert->alert) {
                                            echo $arvalue["name"];
                                        }
                                    }
                                }
                            }
                        }
                    }

                    echo "</td>";
                    echo   "<td>" . date("d/m H:i:s", $alert->epoch) . "</td>";

                    foreach ($devices as $key => $dvalue) {

                        if ($dvalue["mac"] == $alert->mac) {
                            echo   "<td>" . $dvalue["name"] . "</td>";
                        }
                    }

                    foreach ($gateways as $key => $gwvalue) {
                        if ($gwvalue["mac"] == $alert->gateway) {
                            echo   "<td>" . $gwvalue["name"] . "</td>";
                        }
                    }

                    echo   "<td>" ?>

<div class="custom-control custom-switch">
    <input type="checkbox" class="custom-control-input" onClick="$('#stopalarm').load('dashboard/suspendalert?mac=<?php echo $lightkeys[$key]; ?>&msg=lighton&gateway=<?php echo $alert->gateway; ?>&epoch=<?php echo $alert->epoch; ?>');" data-type="light" data-mac="" id="cs<?php echo ""; ?>" checked>
    <label class="custom-control-label" for="cs<?php echo  ""; ?>"> Suspend</label>
</div>

<?php
                    echo "</td>";
                    echo "</tr>";
                    ?>
<audio id="myAudio" controls autoplay>
    <!--<source src="<?php //echo base_url("assets/sound/alert.mp3"); ?>" type="audio/mpeg"></audio>-->
<?php
                }
            }
        }
    }

    public function suspendalert(Type $var = null)
    {
        $client = $this->redis();

        $user_data = $this->session->userdata('userdata');

        $lightmac = $this->input->get("mac");
        $data = array(
            'device_mac' => $lightmac,
            'gateway' => $this->input->get("gateway"),
            'epoch' => $this->input->get("epoch"),
            'msg' => $this->input->get("msg"),
            'user_id' => $user_data["id"],
            'status' => 2,
            'suspend_date' => date('Y-m-d H:i:s'),
        );

        $this->Devices_model->addAlertLog($data);

        $lightinfo = json_decode($client->get($lightmac), true);
        $lightinfo["status"] = 2;
        $lightsuspend = json_encode($lightinfo);
        $client->set($lightmac, $lightsuspend);
    }

    public function getsuspendalert(Type $var = null)
    {
        $suspendAlert = $this->Alert_model->getSuspendAlerts();
        $usersList = $this->Users_model->users_get_all();

        foreach ($suspendAlert as $key => $avalue) {
            foreach ($usersList as $key => $uvalue) {

                if ($avalue["user_id"] == $uvalue["id"]) {
                    echo "<tr>";
                    echo "<td>" . "" . "</td>";
                    echo "<td>" . $avalue["suspend_date"] . "</td>";
                    echo "<td>" . $avalue["device_mac"] . "</td>";
                    echo "<td>" . "" . "</td>";
                    echo "<td>" . $uvalue["name"] . "</td>";
                    echo   "<td>" ?>
<div class="custom-control custom-switch">
    <input type="checkbox" class="custom-control-input" onClick="$('#stopalarm').load('dashboard/closealert?mac=<?php echo $avalue["device_mac"]; ?>&alertid=<?php echo $avalue["id"]; ?>');" data-type="light" data-mac="" id="css<?php echo $avalue["id"]; ?>" checked>
    <label class="custom-control-label" for="css<?php echo $avalue["id"]; ?>"> Close</label>
</div>
<?php
                    echo "</td>";

                    echo "</tr>";
                }
            }
        }
    }

    public function closeAlert(Type $var = null)
    {
        $alertid = $this->input->get("alertid");
        $devicemac = $this->input->get("mac");
        $this->Alert_model->alertClose($alertid, $devicemac);
    }


    public function getclosedalert(Type $var = null)
    {
        $getCloseAlerts = $this->Alert_model->getCloseAlerts();

        foreach ($getCloseAlerts as $key => $value) {
            echo "<tr>";
            echo "<td>" . "" . "</td>";
            echo "<td>" . $value["close_date"] . "</td>";
            echo "<td>" . $value["device_mac"] . "</td>";
            echo "<td>" . "" . "</td>";
            echo "<td>" . "" . "</td>";
            echo "<td>" . "Closed" . "</td>";
            //echo "<td>";
            //echo "</td>";

            echo "</tr>";
        }
    }
}
