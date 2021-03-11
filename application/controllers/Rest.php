<?php

require_once(APPPATH . '../vendor/autoload.php');
Predis\Autoloader::register();

use Firebase\JWT\JWT;

class Rest extends CI_Controller
{

    private $requestMethod;
    private $surl;
    private $id;

    const PRIVATE_KEY = <<<EOD
    -----BEGIN RSA PRIVATE KEY-----
    MIICWwIBAAKBgF0uZPMUdMNwDRFdNolpPhGhtR3ZQ3nttrLc3nhtOIkEVAMRQVRO
    dvPp6JwLPz+hzn9TDlnDL/LZ8T2UyMChS1rv2HDDz3LqLJCH+pV3Gn/cWsDqxT4n
    JLFM79440/EmCbbT2jWO7xXCNXvwL0ZYv9wM+Ed8tQXgY1x1tzJHB6yvAgMBAAEC
    gYBXg+tsIhpINEURueouxJl3Fdl1X0jwi0K8WpTXpj0i8t20w9AHzmoKS/YcGLQe
    n2nCS89+nsO54tegbszdnp+WdlCdZim04LjqC264W7a+brzilntNiKPz4xdTL5GJ
    WmZMOJPpgMH0j9fs6gbIcycT0e7hXrrBKkXs1uahBCTHgQJBAKTdqGMO6akRY8ix
    17eLPMooZE8IbfaQEvZ4nlfATJP8jR6jnh3iHmGnJiyvqP7QNZN5me8Q4rGYE5uC
    LLd28gMCQQCQsJG3TE1R84kYFfOKQrFQXpvhBSGf+KaPYcu6mAfwjBqbU5jAyncj
    BCI/2zzAryOK2hj3qf5tyHXIn+1cKxDlAkEAokHVG8jthouq3TbKy8Wpiny+XFo7
    f1LElvaXQF3uACeq6+C0GU0WAZ30ID6x4Dciw4YGThccRRUbFw3C3L2f6QJAYuqT
    dACSC6i23OSE7szRc+R6JMfhSQAwvm1ZXmN5ahYeSnpIP+UqtaGp2IYFbqVNYyvf
    TdHFwz/8ZgAPwacfkQJAR68V5ZqQ6gDtR/pPHZ5z2YJ9es9Hn+61gFjUF7wrsbJE
    F8Ra5IGTsemOwjGBNduSSJpTfkBc4Lygc9jkh/hFIg==
    -----END RSA PRIVATE KEY-----
    EOD;

    const PUBLIC_KEY = <<<EOD
    -----BEGIN PUBLIC KEY-----
    MIGeMA0GCSqGSIb3DQEBAQUAA4GMADCBiAKBgF0uZPMUdMNwDRFdNolpPhGhtR3Z
    Q3nttrLc3nhtOIkEVAMRQVROdvPp6JwLPz+hzn9TDlnDL/LZ8T2UyMChS1rv2HDD
    z3LqLJCH+pV3Gn/cWsDqxT4nJLFM79440/EmCbbT2jWO7xXCNXvwL0ZYv9wM+Ed8
    tQXgY1x1tzJHB6yvAgMBAAE=
    -----END PUBLIC KEY-----
    EOD;

    function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Authorization, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }

        parent::__construct();
        $this->load->model("Users_model");

        $this->requestMethod = $_SERVER["REQUEST_METHOD"];
        $this->surl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->surl = explode('/', $this->surl);

        if (sizeof($this->surl) == 4) {
            try {
                $this->id = $this->surl[3];
            } catch (\Throwable $th) {
                $this->id = 0;
            }
        } else {
            $this->id = 0;
        }
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

    public function login()
    {
        //header("Access-Control-Allow-Origin: *");
        //header("Access-Control-Allow-Methods: POST");
        //header("Access-Control-Max-Age: 3600");
        //header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        //$all_headers = getallheaders();
        //print_r($all_headers);

        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, TRUE);

        $error = array(
            'login' => false,
            'message' => 'Access denied',
            'error_description' => 'Invalid email or password combination.',
            'jwt' => null,
        );
        $email = @$input["email"];
        $password = @$input["password"];

        $result = $this->Users_model->login_check($email, $password);
        if ($result['auth'] == 'auth1') {
            $result['expiry_time'] = time() + (5 * 24 * 60 * 60);
            $jwt_result = array(
                'login' => true,
                'message' => 'Access',
                'jwt' => JWT::encode($result, Rest::PRIVATE_KEY, 'RS256'),
            );
            echo json_encode($jwt_result);
            header("HTTP/1.1 200 OK");
        } else {
            echo json_encode($error);
            header("HTTP/1.1 401 Unauthorized");
            exit;
        }
    }

    public function auth()
    {
        //header("Access-Control-Allow-Origin: *");
        //header("Content-Type: application/json; charset=UTF-8");
        //header("Access-Control-Allow-Methods: POST");
        //header("Access-Control-Max-Age: 3600");
        //header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        $all_headers = getallheaders();
        $data = array();
        if (array_key_exists('Authorization', $all_headers)) {
            //auth key var
            $auth_header = $all_headers['Authorization'];
            $jwt = str_replace('Bearer ', '', $auth_header);
            $error = array(
                'error' => 'Wrong token key',
            );
            try { //decode
                $decoded = JWT::decode($jwt, Rest::PUBLIC_KEY, array('RS256'));
                //expiry_time if
                $data['data'] = $decoded;
                echo json_encode($data);
                header("HTTP/1.1 200 OK");
                exit;
            } catch (\Throwable $th) { //error
                echo json_encode($error);
                header("HTTP/1.1 401 Unauthorized");
                exit;
            }
        } else {
            //auth key yok
            $error = array(
                'error' => 'Authorization key not exists',
            );
            echo json_encode($error);
            header("HTTP/1.1 401 Unauthorized");
            exit;
        }
    }

    public function auth_header_check($all_headers)
    {
        if (array_key_exists('Authorization', $all_headers)) {
            //auth key exists
            $auth_header_text = $all_headers['Authorization'];
            try {
                $jwt = str_replace('Bearer ', '', $auth_header_text);
                $result = $this->decode_jwt($jwt);
                return $result;
            } catch (\Throwable $th) {
                $error = array(
                    'error' => 'Authorization key not exists',
                );
                echo json_encode($error);
                header("HTTP/1.1 401 Unauthorized");
                exit;
            }
        } else {
            //auth key not exists
            $error = array(
                'error' => 'Authorization key not exists',
            );
            echo json_encode($error);
            header("HTTP/1.1 401 Unauthorized");
            exit;
        }
    }

    public function decode_jwt($jwt)
    {
        try {
            $decode = JWT::decode($jwt, Rest::PUBLIC_KEY, array('RS256'));
            //print_r($decode);
            if ($decode->auth) {
                return true;
            } else {
                $error = array(
                    'error' => 'Unauthorized',
                );
                echo json_encode($error);
                header("HTTP/1.1 401 Unauthorized");
                exit;
            }
        } catch (\Throwable $th) {
            $error = array(
                'error' => 'Wrong token key',
            );
            echo json_encode($error);
            header("HTTP/1.1 401 Unauthorized");
            exit;
        }
    }

    //alert_logs -- 
    //alert_rules --
    //assets -- 
    //asset_type -- 
    //buildings -- 
    //company -- 
    //departments -- 
    //devices -- 
    //devices_type -- 
    //gateways -- 
    //gateway_type -- 
    //logs -- 
    //personnel -- 
    //personnel_type -- 
    //users -- 
    //users_role -- 
    //work_shift -- 
    //zones -- 

    //Devices-------------------------------------------------------------------------------------
    //Devices-Gateways------------------------------------------------------------------------------------
    public function gateways()
    {
        if ($this->auth_header_check(getallheaders())) {


            $this->load->model("Gateways_model");
            $this->load->model("Zones_model");

            try {
                $id = $this->surl[3];
            } catch (\Throwable $th) {
                $id = 0;
            }

            switch ($this->requestMethod) {
                case 'GET':
                    if ($id == 0) {
                        $response = $this->Gateways_model->get_all();
                    } else {
                        $response = $this->Gateways_model->get_by_id($id);
                    }
                    header("HTTP/1.1 200 OK");
                    echo json_encode($response);
                    break;
                case 'POST':
                    //$response = $this->createUserFromRequest();
                    break;
                case 'PUT':
                    //$response = $this->updateUserFromRequest($this->userId);
                    break;
                case 'DELETE':
                    //$response = $this->deleteUser($this->userId);
                    break;
                default:
                    //$response = $this->notFoundResponse();
                    break;
            }
        }
    }

    public function return_message($code, $message)
    {
        $response = array(
            'data' => $message,
        );
        header("HTTP/1.1 " . $code);
        echo json_encode($response);
        exit;
    }

    public function gateway_type()
    {
        if ($this->auth_header_check(getallheaders())) {


            $this->load->model("Gateway_type_model");

            //print_r(sizeof($this->surl));



            $response = array(
                'data' => null,
            );

            switch ($this->requestMethod) {
                case 'GET':
                    if ($this->id == 0) {
                        $data = $this->Gateway_type_model->get();
                    } else {
                        $data = $this->Gateway_type_model->get_by_id($this->id);
                    }
                    $response['data'] = $data;
                    header("HTTP/1.1 200 OK");
                    echo json_encode($response);
                    break;
                case 'POST':
                    $inputJSON = file_get_contents('php://input');
                    $input = json_decode($inputJSON, TRUE);
                    $data = $this->Gateway_type_model->insert($input);
                    if ($data) {
                        $this->return_message('200 OK', 'Ok');
                    } else {
                        $this->return_message('506 Error', 'DB Error');
                    }
                    break;
                case 'PUT':
                    $inputJSON = file_get_contents('php://input');
                    $input = json_decode($inputJSON, TRUE);
                    $data = $this->Gateway_type_model->update($this->id, $input);
                    if ($data) {
                        $this->return_message('200 OK', 'Ok');
                    } else {
                        $this->return_message('506 Error', 'DB Error');
                    }
                    break;
                case 'DELETE':
                    if ($this->id > 0) {
                        $data = $this->Gateway_type_model->delete($this->id);
                        if ($data) {
                            $this->return_message('200 OK', 'Ok');
                        } else {
                            $this->return_message('506 Error', 'DB Error');
                        }
                    } else {
                        $this->return_message('506 Error', 'ID Error');
                    }
                    break;
                default:
                    $data = $this->notFoundResponse();
                    break;
            }
        }
    }
    //Devices-Devices------------------------------------------------------------------------------------

    //RTLS-------------------------------------------------------------------------------------
    public function rtls()
    {
        //header("Access-Control-Allow-Origin: *");
        //header("Content-Type: application/json; charset=UTF-8");
        //header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
        //header("Access-Control-Max-Age: 3600");
        //header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


        $all_headers = getallheaders();
        if (array_key_exists('Authorization', $all_headers)) {
            //auth key var
            $auth_header = $all_headers['Authorization'];
            $jwt = str_replace('Bearer ', '', $auth_header);
        }

        $response = array(
            'data' => null,
            'extra' => null,
        );

        $data = array();
        $extra = array();

        $response['data'] = $data;

        $response['extra'] = $extra;
        $response['uri'] = $this->surl;
        $response['requestMethod'] = $this->requestMethod;


        if (sizeof($this->surl) == 4) {
            try {
                $request = $this->surl[3];
            } catch (\Throwable $th) {
                $request = "";
            }
        } else {
            $request = "";
        }

        switch ($request) {
            case '': //rtls/
                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                $response['data'] = $this->get_rtls();
                break;
            case 'gateways': //rtls/gateways
                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                //$response['data'] = $this->get_rtls_gateways();
                break;
            case 'devices': //rtls/gateways
                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                //$response['data'] = $this->get_rtls_devices();
                break;
            case 'zones': //rtls/zones
                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                //$response['data'] = $this->get_rtls_zones();
                break;
            case 'location': //rtls/location
                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                //$response['data'] = $this->get_rtls_location();
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);

        //rtls/gateway_type
        //rtls/devices
        //rtls/devices_type
        //rtls/zones
        //rtls/assets
        //rtls/asset_type
        //rtls/location

        //$response['status_code_header'] = 'HTTP/1.1 201 Created';
        //$response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        //$response['status_code_header'] = 'HTTP/1.1 404 Not Found';

        echo json_encode($response);
    }

    public function get_rtls()
    {
        $client = $this->redis();
        $devices_keys = $client->keys("rtls:device:*");
        $rtls_devices = $client->mget($devices_keys); //With MGET we put "keys" and get the values 
        //$card_keys = $client->keys("rtls:device:card:*");
        //$card_devices = $client->mget($card_keys); //With MGET we put "keys" and get the values 
        //$sensor_keys = $client->keys("rtls:device:sensor:*");
        //$sensor_devices = $client->mget($sensor_keys);
        //$light_keys = $client->keys("rtls:device:light:*");
        //$light_devices = $client->mget($light_keys);
        $personnel = json_decode($client->get("personnel"), true);
        $gateways = json_decode($client->get("gateways"), true); //tüm gateway leri json olarak çektiği yer.
        $devices = json_decode($client->get("devices"), true); //tüm devices leri json olarak çektiği yer.
        $assets = json_decode($client->get("assets"), true); //tüm devices leri json olarak çektiği yer.
        $zones = json_decode($client->get("zones"), true); //tüm devices leri json olarak çektiği yer.

        $data = array();
        foreach ($rtls_devices as $rtls_key => $rtls_device) {
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
                    $lat = $gateway["lat"];
                    $lng = $gateway["lng"];
                    $gw_mac = $gateway["mac"];
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

            $time_dif = time() - $epoch;
            $time_dif_value = 30;

            if ($time_dif >= $time_dif_value) {
                $data[$rtls_key]['device']['id'] = null;
                $data[$rtls_key]['device']['name'] = null;
                $data[$rtls_key]['device']['mac'] = null;
                $data[$rtls_key]['status_color'] = 'red';
                $data[$rtls_key]['date'] = date("d/m H:i:s", @$epoch);
                $data[$rtls_key]['name'] = @$device_name . " " . $name;
                $data[$rtls_key]['location']['name'] = @$location;
                $data[$rtls_key]['location']['zone'] = null;
                $data[$rtls_key]['location']['gateway'] = $gw_mac;
                $data[$rtls_key]['location']['lat'] = $lat;
                $data[$rtls_key]['location']['lng'] = $lng;
                $data[$rtls_key]['battery'] = null;
                $data[$rtls_key]['rssi']['value'] = 0;
                $data[$rtls_key]['rssi']['icon'] = "assets/img/rssi/0-rssi.png";
                $data[$rtls_key]['detail'] = null;
            } else {
                $data[$rtls_key]['device']['id'] = null;
                $data[$rtls_key]['device']['name'] = null;
                $data[$rtls_key]['device']['mac'] = null;
                $data[$rtls_key]['status_color'] = 'green';
                $data[$rtls_key]['date'] = date("d/m H:i:s", @$epoch);
                $data[$rtls_key]['name'] = @$device_name . " " . $name;
                $data[$rtls_key]['location']['name'] = @$location;
                $data[$rtls_key]['location']['zone'] = null;
                $data[$rtls_key]['location']['gateway'] = $gw_mac;
                $data[$rtls_key]['location']['lat'] = $lat;
                $data[$rtls_key]['location']['lng'] = $lng;
                $data[$rtls_key]['battery']['color'] = $battery_color;
                $data[$rtls_key]['battery']['icon'] = $battery_icon;
                $data[$rtls_key]['battery']['value'] = $battery;
                $data[$rtls_key]['rssi']['value'] = @$device_json["rssi"];
                $data[$rtls_key]['rssi']['icon'] = base_url("assets/img/rssi/") . $rssi_id . "-rssi.png";
                $data[$rtls_key]['detail']['acc']['x'] = @$device_json["x"];
                $data[$rtls_key]['detail']['acc']['y'] = @$device_json["y"];
                $data[$rtls_key]['detail']['acc']['z'] = @$device_json["z"];
                $data[$rtls_key]['detail']['click'] = @$device_json["click"];
                $data[$rtls_key]['detail']['light'] = @$device_json["light"];
                $data[$rtls_key]['detail']['temperature'] = @$device_json["temperature"];
                $data[$rtls_key]['detail']['humidity'] = @$device_json["humidity"];
                $data[$rtls_key]['detail']['motion'] = $device_json["motion"];
            }
        }
        return $data;
    }


    //Customers------------------------------------------------------------------------------------
    //Customer-Zone-----------------------------------------------------------------------------------
    //Customer-Zone-----------------------------------------------------------------------------------

    public function devices()
    {
        if ($this->auth_header_check(getallheaders())) {

            $this->load->model("Devices_model");

            $response = array(
                'data' => null,
            );

            switch ($this->requestMethod) {
                case 'GET':
                    if ($this->id == 0) {
                        $data = $this->Devices_model->get_all();
                    } else {
                        $data = $this->Devices_model->get_by_id($this->id);
                    }
                    $response['data'] = $data;
                    header("HTTP/1.1 200 OK");
                    echo json_encode($response);
                    break;
                case 'POST':
                    $inputJSON = file_get_contents('php://input');
                    $input = json_decode($inputJSON, TRUE);
                    $data = $this->Devices_model->insert($input);
                    if ($data) {
                        $this->return_message('200 OK', 'Ok');
                    } else {
                        $this->return_message('506 Error', 'DB Error');
                    }
                    break;
                case 'PUT':
                    $inputJSON = file_get_contents('php://input');
                    $input = json_decode($inputJSON, TRUE);
                    $data = $this->Devices_model->update($this->id, $input);
                    if ($data) {
                        $this->return_message('200 OK', 'Ok');
                    } else {
                        $this->return_message('506 Error', 'DB Error');
                    }
                    break;
                case 'DELETE':
                    if ($this->id > 0) {
                        $data = $this->Devices_model->delete($this->id);
                        if ($data) {
                            $this->return_message('200 OK', 'Ok');
                        } else {
                            $this->return_message('506 Error', 'DB Error');
                        }
                    } else {
                        $this->return_message('506 Error', 'ID Error');
                    }
                    break;
                default:
                    $data = $this->notFoundResponse();
                    break;
            }
        }
    }

    public function personnel()
    {
        if ($this->auth_header_check(getallheaders())) {
            $this->load->model("Personnel_model");

            $response = array(
                'data' => null,
            );

            switch ($this->requestMethod) {
                case 'GET':
                    if ($this->id == 0) {
                        $data = $this->Personnel_model->get_all();
                    } else {
                        $data = $this->Personnel_model->get_by_id($this->id);
                    }
                    $response['data'] = $data;
                    header("HTTP/1.1 200 OK");
                    echo json_encode($response);
                    break;
                case 'POST':
                    $inputJSON = file_get_contents('php://input');
                    $input = json_decode($inputJSON, TRUE);
                    $data = $this->Personnel_model->insert($input);
                    if ($data) {
                        $this->return_message('200 OK', 'Ok');
                    } else {
                        $this->return_message('506 Error', 'DB Error');
                    }
                    break;
                case 'PUT':
                    $inputJSON = file_get_contents('php://input');
                    $input = json_decode($inputJSON, TRUE);
                    $data = $this->Personnel_model->update($this->id, $input);
                    if ($data) {
                        $this->return_message('200 OK', 'Ok');
                    } else {
                        $this->return_message('506 Error', 'DB Error');
                    }
                    break;
                case 'DELETE':
                    if ($this->id > 0) {
                        $data = $this->Personnel_model->delete($this->id);
                        if ($data) {
                            $this->return_message('200 OK', 'Ok');
                        } else {
                            $this->return_message('506 Error', 'DB Error');
                        }
                    } else {
                        $this->return_message('506 Error', 'ID Error');
                    }
                    break;
                default:
                    $data = $this->notFoundResponse();
                    break;
            }
        }
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}
