<?php
date_default_timezone_set('Europe/Istanbul');
defined('BASEPATH') or exit('No direct script access allowed');


require_once(APPPATH . '../vendor/autoload.php');
Predis\Autoloader::register();

//date_default_timezone_set('Europe/Istanbul');




class Rtls extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        //$this->load->model("users_model");


    }


    public function index(Type $var = null)
    {
        $data = array(
            'id'    =>  0,
        );

        $this->load->view('rtls', $data);
    }


    public function rtlsdashboard(Type $var = null)
    {
        $client = new Predis\Client([
            //'scheme' => 'tcp',
            'host'   => $this->config->item('redis_host'),
            //'port'   => $this->config->item('redis_port'),
            'password' => $this->config->item('redis_password')
        ]);

        $data = array(
            'id'    =>  0,
        );
        $userkeys = $client->keys("user:*");
        $gatewaylist = $client->get("gateway:list");

        header('Content-Type: application/json');
        echo json_encode($gatewaylist);
    }


    public function rtlstrdevices(Type $var = null)
    {
        $client = new Predis\Client([
            //'scheme' => 'tcp',
            'host'   => $this->config->item('redis_host'),
            //'port'   => $this->config->item('redis_port'),
            'password' => $this->config->item('redis_password')
        ]);
        
        $userkeys = $client->keys("user:*");

        //header('Content-Type: application/json');
        try {
            foreach ($userkeys as $key => $value) {

                $userlist = json_decode($client->get("users:list"), true);
                $gatewaylist = json_decode($client->get("gateway:list"), true);

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
                //print_r($gatewaylist);
                echo   "<tr>";
                



                if ($timefark >= 20) {
                    echo "<td>" ."<b style='color:red;'>&#11044;</b>"."</td>";
                    echo "<td> ". date("d/m H:i:s", $epoch) . "</td>";
                    foreach ($userlist as $ukey => $uvalue) {
                        if (str_replace("user:", "", $value) == $uvalue["mac"]) {
                            echo   "<td><a href='"."rtls/userlog?mac=".$uvalue["mac"]."'>" . $uvalue["name"] . "</a></td>";
                        }
                    }
                    foreach ($gatewaylist as $gwkey => $gwvalue) {
                        if ($gwkey == $userget["gateway"]) {
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
                    echo   "<td>" . "" . "</td>";
                    echo   "<td>" . "" . "</td>";
                } else {
                    echo   "<td>" ."<b style='color:green;'>&#11044;</b>"."</td>";
                    echo "<td> ". date("H:i:s", $epoch-10800) . "</td>";
                    foreach ($userlist as $ukey => $uvalue) {
                        if (str_replace("user:", "", $value) == $uvalue["mac"]) {
                            echo   "<td><a href='"."rtls/userlog?mac=".$uvalue["mac"]."'>" . $uvalue["name"] . "</a></td>";
                        }
                    }
                    foreach ($gatewaylist as $gwkey => $gwvalue) {
                        if ($gwkey == $userget["gateway"]) {
                            echo   "<td>" . $gwvalue["name"] . "</td>";
                        }
                    }
                    echo   "<td>" . $userget["battery"] . "</td>";
                    echo   "<td>" . $userget["rssi"] . "</td>";
                    echo   "<td>" . "" . "</td>";
                    echo   "<td>" . "" . "</td>";
                    echo   "<td>" . $userget["x"] . "</td>";
                    echo   "<td>" . $userget["y"] . "</td>";
                    echo   "<td>" . $userget["z"] . "</td>";
                    echo   "<td>" . "" . "</td>";
                    echo   "<td>" . "" . "</td>";
                }
                echo   "</tr>";
            }
        } catch (Exception $e) {
            echo 'Error ',  $e->getMessage(), "\n";
        }
    }

    
}
